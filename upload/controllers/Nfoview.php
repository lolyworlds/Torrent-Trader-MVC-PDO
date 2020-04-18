<?php
  class Nfoview extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
		// Set Current User
		// $curuser = $this->userModel->setCurrentUser();
		// Set Current User
		// $db = new Database;
dbconn();
global $site_config, $CURUSER;
// check access and rights
if ($site_config["MEMBERSONLY"]){
	loggedinonly();

	if($CURUSER["view_torrents"]=="no")
		show_error_msg(T_("ERROR"), "You do not have permission to view nfo's", 1);
}


$id = (int)$_GET["id"];

if (!$id)
	show_error_msg(T_("ID_NOT_FOUND"), T_("ID_NOT_FOUND_MSG_VIEW"), 1);

stdhead(T_('NFO_VIEW'));

$query = DB::run("SELECT name, nfo FROM torrents WHERE id=?", [$id]);
$res = $query->fetch(PDO::FETCH_ASSOC);

if ($res["nfo"] != "yes")
  show_error_msg(T_("ERROR"), T_("NO_NFO"), 1);

if($res["nfo"] == "yes"){
    $char1 = 55; //cut length (cutname func is in header.php)
    $shortname = CutName(htmlspecialchars($res["name"]), $char1);
    
	$nfo_dir = $site_config["nfo_dir"];

    $nfofilelocation = "$nfo_dir/$id.nfo";
    $filegetcontents = file_get_contents($nfofilelocation);
    $nfo = htmlspecialchars($filegetcontents);
    
    
    if ($nfo) {
		$nfo = my_nfo_translate($nfo);
		if($CURUSER["edit_torrents"]=="yes")
            begin_frame(T_("NFO_FILE_FOR").": <a href='".$site_config["SITEURL"]."/torrentsdetails?id=$id'>$shortname</a> - <a href='/nfoedit?id=$id'>".T_("NFO_EDIT")."</a>");
        else
            begin_frame(T_("NFO_FILE_FOR").": $shortname");

		print("<textarea class='nfo' style=\"width:98%;height:100%;\" rows='50' cols='20' readonly='readonly'>".stripslashes($nfo)."</textarea>");

        end_frame();
    }

}//has nfo

stdfoot();
}
  }