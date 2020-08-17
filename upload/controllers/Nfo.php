<?php
  class Nfo extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    public function index(){
        // $this->userModel = $this->model('User');
    }

public function read(){
dbconn();
global $config;
// check access and rights
if ($config["MEMBERSONLY"]){
	loggedinonly();

	if($_SESSION["view_torrents"]=="no")
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
    
	$nfo_dir = $config["nfo_dir"];

    $nfofilelocation = "$nfo_dir/$id.nfo";
    $filegetcontents = file_get_contents($nfofilelocation);
    $nfo = htmlspecialchars($filegetcontents);
    
    
    if ($nfo) {
		$nfo = my_nfo_translate($nfo);
		if($_SESSION["edit_torrents"]=="yes")
            begin_frame(T_("NFO_FILE_FOR").": <a href='".$config["SITEURL"]."/torrents/read?id=$id'>$shortname</a> - <a href='$config[SITEURL]/nfo/edit?id=$id'>".T_("NFO_EDIT")."</a>");
        else
            begin_frame(T_("NFO_FILE_FOR").": $shortname");

		print("<textarea class='nfo' style=\"width:98%;height:100%;\" rows='50' cols='20' readonly='readonly'>".stripslashes($nfo)."</textarea>");

        end_frame();
    }

}//has nfo

stdfoot();
}

public function edit(){
dbconn();
global $config;
loggedinonly();

error_reporting(0);
                       
if ($_SESSION["edit_torrents"] == "no")
    show_error_msg(T_("ERROR"), T_("NFO_PERMISSION"), 1);

$id = ( int ) cleanstr($_REQUEST["id"]); 
$do = $_POST["do"];

$nfo = $config["nfo_dir"] . "/$id.nfo";

if ($do == "update") { 
                                                                 
    if ( is_file( $nfo ) )  
    {
         file_put_contents( $nfo, $_POST['content'] );
         
         write_log("NFO ($id) was updated by $_SESSION[username].");
      
         show_error_msg(T_("NFO_UPDATED"), T_("NFO_UPDATED"), 1);
    }
}

if ($do == "delete") {   
    
    $reason = htmlspecialchars($_POST["reason"]);

    if (get_row_count("torrents", "WHERE `nfo` = 'yes' AND `id` = $id"))
    {
        unlink($nfo);
        write_log("NFO ($id) was deleted by $_SESSION[username] $reason");
        DB::run("UPDATE `torrents` SET `nfo` = 'no' WHERE `id` = $id");
        show_error_msg(T_("NFO_DELETED"), T_("NFO_DELETED"), 1);
    }
    
    show_error_msg(T_("ERROR"), sprintf(T_("NFO_NOT_EXIST"), $id), 1);
} 

if ((!is_valid_id($id)) || (!$contents = file_get_contents($nfo))) {
     show_error_msg(T_("ERROR"), T_("NFO_NOT_FOUND"), 1);
}

stdhead(T_("NFO_EDITOR"));
begin_frame(T_("NFO_EDIT"));
?>

<center>
<form method="post" action="nfo/edit">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="hidden" name="do" value="update" />
<textarea class="nfo" name="content" cols="100%" rows="80"><?php echo htmlspecialchars(stripslashes($contents)); ?></textarea><br />
<input type="reset" value="<?php echo T_("RESET"); ?>" />
<button type='submit' class='btn btn-sm btn-primary'><?php echo T_("SAVE"); ?></button>
</form>
</center>

<?php
end_frame();

begin_frame(T_("NFO_DELETE"));
?>

<center>
<form method="post" action="/nfo/edit">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="hidden" name="do" value="delete" />
<b><?php echo T_("NFO_REASON"); ?>:</b> <input type="text" name="reason" size="40" />
<button type='submit' class='btn btn-sm btn-primary'><?php echo T_("DEL"); ?></button>
</form>
</center>

<?php
end_frame();

stdfoot();
}

  }