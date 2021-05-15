<?php
class Download extends Controller
{

    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
    }

    public function index()
    {
        // Ban Download
        $subbanned = DB::run("SELECT id FROM users WHERE id=? AND downloadbanned=? LIMIT 1", [$_SESSION['id'], 'no']);
        if ($subbanned->rowCount() < 1) {
            Session::flash('info', "You are banned from downloading please contact staff if you feel this is a mistake !", URLROOT."/index");
        }
        if ($_SESSION["can_download"] == "no") {
            Session::flash('info', Lang::T("NO_PERMISSION_TO_DOWNLOAD"), URLROOT."/index");
        }
        $id = (int) $_GET["id"];
        if (!$id) {
            Session::flash('info', Lang::T("ID_NOT_FOUND_MSG_DL"), URLROOT."/index");
        }
        
        $fn = TORRENTDIR."/$id.torrent";
        $res = DB::run("SELECT filename, banned, external, announce, owner, vip FROM torrents WHERE id =" . intval($id));
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $vip = $row['vip'];
        if ($_SESSION['class'] < _VIP && $vip == "yes") {
            Session::flash('info', "<b>You can not download, you have to be VIP</b>", $_SERVER['HTTP_REFERER']);
        }
        if (!$row) {
            Session::flash('info', Lang::T("ID_NOT_FOUND"), $_SERVER['HTTP_REFERER']);
        }
        if ($row["banned"] == "yes") {
            Session::flash('info', Lang::T("BANNED_TORRENT"), $_SERVER['HTTP_REFERER']);
        }
        if (!is_file($fn)) {
            Session::flash('info', Lang::T("FILE_NOT_FILE"), $_SERVER['HTTP_REFERER']);
        }
        if (!is_readable($fn)) {
            Session::flash('info', Lang::T("FILE_UNREADABLE"), $_SERVER['HTTP_REFERER']);
        }
        $name = $row['filename'];
        $friendlyurl = str_replace("http://", "", URLROOT);
        $friendlyname = str_replace(".torrent", "", $name);
        $friendlyext = ".torrent";
        $name = $friendlyname . "[" . $friendlyurl . "]" . $friendlyext;
        DB::run("UPDATE torrents SET hits = hits + 1 WHERE id = $id");
        // LIKE MOD
        if (FORCETHANKS) {
            if ($_SESSION["id"] != $row["owner"] && FORCETHANKS) {
            $data = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$id, 'torrent', $_SESSION['id']]);
            $like = $data->fetch(PDO::FETCH_ASSOC);
                if (!$like) {
                    Session::flash('info', Lang::T("PLEASE_THANK"), $_SERVER['HTTP_REFERER']);
                }
            }
        }
        
        // if user dont have a passkey generate one, only if current member, note - it was membersonly
        if ($_SESSION['loggedin'] == true) {
            if (strlen($_SESSION['passkey']) != 32) {
                $rand = array_sum(explode(" ", microtime()));
                $_SESSION['passkey'] = md5($_SESSION['username'] . $rand . $_SESSION['secret'] . ($rand * mt_rand()));
                DB::run("UPDATE users SET passkey=? WHERE id=?", [$_SESSION['passkey'], $_SESSION['id']]);
            }
        }
        
        // if not external and current member, note - it was membersonly
        if ($row["external"] != 'yes' && $_SESSION['loggedin'] == true) { // local torrent so add passkey
            // Bencode
            $dict = Bencode::decode(file_get_contents($fn));
            $dict['announce'] = sprintf(PASSKEYURL, $_SESSION["passkey"]);
            unset($dict['announce-list']);
            $data = Bencode::encode($dict);
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header("Content-Type: application/x-bittorrent");
            print $data;
        } else { // external torrent so no passkey needed
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Content-Length: ' . filesize($fn));
            header("Content-Type: application/x-bittorrent");
            readfile($fn);
        }
    }

    public function attachment()
    {
        $id = (int) $_GET["id"];
        $hash = $_GET["hash"];
        $filename = $hash;
        $fn = TORRENTDIR . "/attachment/$filename.data";
        $sql = DB::run("SELECT * FROM attachments WHERE  id=?", [$id])->fetch(PDO::FETCH_ASSOC);
        $extension = substr($sql['filename'], -3);
        if (!file_exists($fn)) {
             echo "The file $filename does not exists";
        } else {
        header('Content-Disposition: attachment; filename="' . $sql['filename'] . '"');
        header('Content-Length: ' . filesize($fn));
        header("Content-Type: application/$extension");
        readfile($fn);
       }
    }
    
}