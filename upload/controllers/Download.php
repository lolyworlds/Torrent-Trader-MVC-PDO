<?php
  class Download extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
dbconn();
global $site_config, $CURUSER;
if ($_GET["passkey"]) {
	$CURUSER = DB::run("SELECT * FROM users INNER JOIN groups ON users.class=groups.group_id WHERE passkey=? AND enabled=? AND status=?", [$_GET["passkey"], 'yes', 'confirmed'])->fetch();
}

//check permissions
if ($site_config["MEMBERSONLY"]){
	loggedinonly();
	
	if($CURUSER["can_download"]=="no")
		show_error_msg(T_("ERROR"), T_("NO_PERMISSION_TO_DOWNLOAD"), 1);
}

    // LIKE MOD
    if($site_config["forcethanks"]) {
    $data = DB::run("SELECT user FROM thanks WHERE thanked = ? & type = ? & user = ?", [$id, 'torrent', $CURUSER['id']]);
    $like = $data->fetch(PDO::FETCH_ASSOC);
    if(!$like){
        show_error_msg(T_("ERROR"), T_("PLEASE_THANK"), 1);
    }
    }

$id = (int)$_GET["id"];

if (!$id)
	show_error_msg(T_("ID_NOT_FOUND"), T_("ID_NOT_FOUND_MSG_DL"), 1);

$res = DB::run("SELECT filename, banned, external, announce FROM torrents WHERE id =".intval($id));
$row = $res->fetch(PDO::FETCH_ASSOC);

$torrent_dir = $site_config["torrent_dir"];

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
$friendlyurl = str_replace("http://","",$site_config["SITEURL"]);
$friendlyname = str_replace(".torrent","",$name);
$friendlyext = ".torrent";
$name = $friendlyname ."[". $friendlyurl ."]". $friendlyext;

DB::run("UPDATE torrents SET hits = hits + 1 WHERE id = $id");

require_once("classes/BEcode.php");

// if user dont have a passkey generate one, only if current member, note - it was membersonly
if ($CURUSER){
	if (strlen($CURUSER['passkey']) != 32) {
		$rand = array_sum(explode(" ", microtime()));
		$CURUSER['passkey'] = md5($CURUSER['username'].$rand.$CURUSER['secret'].($rand*mt_rand()));
		DB::run("UPDATE users SET passkey=? WHERE id=?", [$CURUSER['passkey'], $CURUSER['id']]);
	}
}
// if not external and current member, note - it was membersonly
if ($row["external"]!='yes' && $CURUSER){// local torrent so add passkey
	$dict = BDecode(file_get_contents($fn));
	$dict['announce'] = sprintf($site_config["PASSKEYURL"], $CURUSER["passkey"]);
	unset($dict['announce-list']);

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