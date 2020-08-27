<?php
  class Download extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
dbconn();
global $config;

// Bann Download
////////////////////////bann download ///////////////////////////////////////////////////
$subbanned = DB::run("SELECT id FROM users WHERE id=? AND downloadbanned=? LIMIT 1", [$_SESSION['id'], 'no']);
if ($subbanned->rowCount() < 1){
	autolink(TTURL."/index", "You are banned from downloading please contact staff if you feel this is a mistake !");
}

if ($_GET["passkey"]) {
	// todo $_SESSION = DB::run("SELECT * FROM users INNER JOIN groups ON users.class=groups.group_id WHERE passkey=? AND enabled=? AND status=?", [$_GET["passkey"], 'yes', 'confirmed'])->fetch();
}

//check permissions
if ($config["MEMBERSONLY"]){
	loggedinonly();
	
	if($_SESSION["can_download"]=="no")
		show_error_msg(T_("ERROR"), T_("NO_PERMISSION_TO_DOWNLOAD"), 1);
}

$id = (int)$_GET["id"];

if (!$id)
	show_error_msg(T_("ID_NOT_FOUND"), T_("ID_NOT_FOUND_MSG_DL"), 1);

$res = DB::run("SELECT filename, banned, external, announce, owner FROM torrents WHERE id =".intval($id));
$row = $res->fetch(PDO::FETCH_ASSOC);

    // LIKE MOD
    if($_SESSION["id"] != $row["owner"] && $config["forcethanks"]) {
    $data = DB::run("SELECT user FROM thanks WHERE thanked = ? & type = ? & user = ?", [$id, 'torrent', $_SESSION['id']]);
    $like = $data->fetch(PDO::FETCH_ASSOC);
    if(!$like){
        show_error_msg(T_("ERROR"), T_("PLEASE_THANK"), 1);
    }
    }

$torrent_dir = $config["torrent_dir"];

$fn = "$torrent_dir/$id.torrent";

if (!$row)
	show_error_msg(T_("FILE_NOT_FOUND"), T_("ID_NOT_FOUND"),1);
if ($row["banned"] == "yes")
	show_error_msg(T_("ERROR"), T_("BANNED_TORRENT"), 1);
if (!is_file($fn))
	show_error_msg(T_("FILE_NOT_FOUND"), T_("FILE_NOT_FILE"), 1);
if (!is_readable($fn))
	show_error_msg(T_("FILE_NOT_FOUND"), T_("FILE_UNREADABLE"), 1);

$name = $row['filename'];
$friendlyurl = str_replace("http://","",$config["SITEURL"]);
$friendlyname = str_replace(".torrent","",$name);
$friendlyext = ".torrent";
$name = $friendlyname ."[". $friendlyurl ."]". $friendlyext;

DB::run("UPDATE torrents SET hits = hits + 1 WHERE id = $id");

// if user dont have a passkey generate one, only if current member, note - it was membersonly
if ($_SESSION['loggedin']  == true){
	if (strlen($_SESSION['passkey']) != 32) {
		$rand = array_sum(explode(" ", microtime()));
		$_SESSION['passkey'] = md5($_SESSION['username'].$rand.$_SESSION['secret'].($rand*mt_rand()));
		DB::run("UPDATE users SET passkey=? WHERE id=?", [$_SESSION['passkey'], $_SESSION['id']]);
	}
}

require_once("classes/BDecode.php");
require_once("classes/BEncode.php");

// if not external and current member, note - it was membersonly
if ($row["external"]!='yes' && $_SESSION['loggedin']  == true){// local torrent so add passkey
	// BDe Class
	$dict = BDecode(file_get_contents($fn));
	$dict['announce'] = sprintf($config["PASSKEYURL"], $_SESSION["passkey"]);
	unset($dict['announce-list']);

	// BEn Class
	$data = BEncode($dict);
    
	header('Content-Disposition: attachment; filename="'.$name.'"');

    //header('Content-Length: ' . strlen($data)); 
    
	header("Content-Type: application/x-bittorrent");

	print $data; 

}else{// external torrent so no passkey needed
   
	header('Content-Disposition: attachment; filename="'.$name.'"');

    header('Content-Length: ' . filesize($fn));  
    
	header("Content-Type: application/x-bittorrent");

	readfile($fn); 
}
	}
}