<?php
  class Torrents extends Controller {
    
    public function __construct(){
        $this->torrentModel = $this->model('Torrent');
    }
    
	    public function index(){
			// for now just to prevent display warning
		}
	
    public function browse(){

dbconn();
global $site_config, $CURUSER, $pdo;
//check permissions
if ($site_config["MEMBERSONLY"]){
    loggedinonly();

    if($CURUSER["view_torrents"]=="no")
        show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
}

//get http vars
$addparam = "";
$wherea = array();
$wherea[] = "visible = 'yes'";
$thisurl = "torrents/browse?";

if ($_GET["cat"]) {
    $wherea[] = "category = " . sqlesc($_GET["cat"]);
    $addparam .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
    $thisurl .= "cat=".urlencode($_GET["cat"])."&amp;";
}

if ($_GET["parent_cat"]) {
    $addparam .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&amp;";
    $thisurl .= "parent_cat=".urlencode($_GET["parent_cat"])."&amp;";
    $wherea[] = "categories.parent_cat=".sqlesc($_GET["parent_cat"]);
}

$parent_cat = $_GET["parent_cat"];
$category = (int) $_GET["cat"];

$where = implode(" AND ", $wherea);
$wherecatina = array();
$wherecatin = "";
$res =$this->torrentModel->getCatById ();
while($row = $res->fetch(PDO::FETCH_LAZY)){
    if ($_GET["c$row[id]"]) {
        $wherecatina[] = $row["id"];
        $addparam .= "c$row[id]=1&amp;";
        $thisurl .= "c$row[id]=1&amp;";
    }
    $wherecatin = implode(", ", $wherecatina);
}

if ($wherecatin)
    $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";

if ($where != "")
    $where = "WHERE $where";

if ($_GET["sort"] || $_GET["order"]) {

    switch ($_GET["sort"]) {
        case 'name': $sort = "torrents.name"; $addparam .= "sort=name&amp;"; break;
        case 'times_completed':    $sort = "torrents.times_completed"; $addparam .= "sort=times_completed&amp;"; break;
        case 'seeders':    $sort = "torrents.seeders"; $addparam .= "sort=seeders&amp;"; break;
        case 'leechers': $sort = "torrents.leechers"; $addparam .= "sort=leechers&amp;"; break;
        case 'comments': $sort = "torrents.comments"; $addparam .= "sort=comments&amp;"; break;
        case 'size': $sort = "torrents.size"; $addparam .= "sort=size&amp;"; break;
        default: $sort = "torrents.id";
    }

    if ($_GET["order"] == "asc" || ($_GET["sort"] != "id" && !$_GET["order"])) {
        $sort .= " ASC";
        $addparam .= "order=asc&amp;";
    } else {
        $sort .= " DESC";
        $addparam .= "order=desc&amp;";
    }

    $orderby = "ORDER BY $sort";

    }else{
        $orderby = "ORDER BY torrents.id DESC";
        $_GET["sort"] = "id";
        $_GET["order"] = "desc";
    }

//Get Total For Pager
$count = $this->torrentModel->getCatwhere($where);

//get sql info
if ($count) {
    list($pagertop, $pagerbottom, $limit) = pager(20, $count, "torrents/browse?" . $addparam);
    $query = "SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
    $res = $pdo->run($query);
}else{
    unset($res);
}

stdhead(T_("BROWSE_TORRENTS"));
begin_frame(T_("BROWSE_TORRENTS"));

// get all parent cats
echo "<center><b>".T_("CATEGORIES").":</b> ";
$catsquery = $this->torrentModel->getCatByParent () ;
echo " - <a href='$site_config[SITEURL]/torrents/browse'>".T_("SHOW_ALL")."</a>";
while($catsrow = $catsquery->fetch(PDO::FETCH_ASSOC)){
        echo " - <a href='$site_config[SITEURL]/torrents/browse/?parent_cat=".urlencode($catsrow['parent_cat'])."'>$catsrow[parent_cat]</a>";
}

?>
<br /><br />
<form method="get" action="torrents/browse">
<table align="center">
<tr align='right'>
<?php
$i = 0;

$cats = $this->torrentModel->getCatByParentName () ;
while ($cat = $cats->fetch(PDO::FETCH_ASSOC)) {
    $catsperrow = 5;
    print(($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : "");
    print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a href='$site_config[SITEURL]/torrents/browse?cat={$cat["id"]}'>".htmlspecialchars($cat["parent_cat"])." - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) || $_GET["cat"] == $cat["id"] ? "checked='checked' " : "") . "value='1' /></td>\n");
    $i++;
}
echo "</tr><tr align='center'><td colspan='$catsperrow' align='center'><input type='submit' value='".T_("GO")."' /></td></tr>";
echo "</table></form>";

//if we are browsing, display all subcats that are in same cat
if ($parent_cat){
    $thisurl .= "parent_cat=".urlencode($parent_cat)."&amp;";
    echo "<br /><br /><b>".T_("YOU_ARE_IN").":</b> <a href='torrents/browse?parent_cat=".urlencode($parent_cat)."'>".htmlspecialchars($parent_cat)."</a><br /><b>".T_("SUB_CATS").":</b> ";
    $subcatsquery = $pdo->run("SELECT id, name, parent_cat FROM categories WHERE parent_cat=".sqlesc($parent_cat)." ORDER BY name");
    while($subcatsrow = $subcatsquery->fetch(PDO::FETCH_ASSOC)){
        $name = $subcatsrow['name'];
        echo " - <a href='$site_config[SITEURL]/torrents/browse?cat=$subcatsrow[id]'>$name</a>";
    }
}

if (is_valid_id($_GET["page"]))
    $thisurl .= "page=$_GET[page]&amp;";

echo "</center><br /><br />";//some spacing

// New code (TorrentialStorm)
    echo "<div align='right'><form id='sort' action=''>".T_("SORT_BY").": <select name='sort' onchange='window.location=\"{$thisurl}sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
    echo "<option value='id'" . ($_GET["sort"] == "id" ? " selected='selected'" : "") . ">".T_("ADDED")."</option>";
    echo "<option value='name'" . ($_GET["sort"] == "name" ? " selected='selected'" : "") . ">".T_("NAME")."</option>";
    echo "<option value='comments'" . ($_GET["sort"] == "comments" ? " selected='selected'" : "") . ">".T_("COMMENTS")."</option>";
    echo "<option value='size'" . ($_GET["sort"] == "size" ? " selected='selected'" : "") . ">".T_("SIZE")."</option>";
    echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") . ">".T_("COMPLETED")."</option>";
    echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? " selected='selected'" : "") . ">".T_("SEEDERS")."</option>";
    echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? " selected='selected'" : "") . ">".T_("LEECHERS")."</option>";
    echo "</select>&nbsp;";
    echo "<select name='order' onchange='window.location=\"{$thisurl}order=\"+this.options[this.selectedIndex].value+\"&amp;sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value'>";
    echo "<option selected='selected' value='asc'" . ($_GET["order"] == "asc" ? " selected='selected'" : "") . ">".T_("ASCEND")."</option>";
    echo "<option value='desc'" . ($_GET["order"] == "desc" ? " selected='selected'" : "") . ">".T_("DESCEND")."</option>";
    echo "</select>";
    echo "</form></div>";

// End

if ($count) {
    torrenttable($res);
    print($pagerbottom);
}else {
    
     print("<div class='f-border'>");
     print("<div class='f-cat' width='100%'>".T_("NOTHING_FOUND")."</div>");
     print("<div>");
     print T_("NO_UPLOADS");
     print("</div>");
     print("</div>");
    
}

if ($CURUSER)
    $pdo->run("UPDATE users SET last_browse=? WHERE id=?", [gmtime(), $CURUSER['id']]);

end_frame();
stdfoot();
}

    public function needseed(){
 dbconn();
global $site_config, $CURUSER, $pdo;
 // Check permissions
 if ($site_config["MEMBERSONLY"]) {
     loggedinonly();
     
     if ($CURUSER["view_torrents"] == "no")
         show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
 }  
 
 $res = $pdo->run("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size, torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, users.username FROM torrents LEFT JOIN users ON torrents.owner = users.id WHERE torrents.banned = 'no' AND torrents.leechers > 0 AND torrents.seeders <= 1 ORDER BY torrents.seeders");
 
 if ($res->rowCount() == 0)
     show_error_msg(T_("ERROR"), T_("NO_TORRENT_NEED_SEED"), 1);
     
     stdhead(T_("TORRENT_NEED_SEED"));
     begin_frame(T_("TORRENT_NEED_SEED"));
     
     echo T_("TORRENT_NEED_SEED_MSG");
     
     ?>

     <div class='table-responsive'><table class='table table-striped'>
     <thead><tr>
         <th><?php echo T_("TORRENT_NAME"); ?></th>
         <th><?php echo T_("UPLOADER"); ?></th>
         <th><?php echo T_("LOCAL_EXTERNAL"); ?></th>
         <th><?php echo T_("SIZE"); ?></th>
         <th><?php echo T_("SEEDS"); ?></th>
         <th><?php echo T_("LEECHERS"); ?></th>
         <th><?php echo T_("COMPLETE"); ?></th>
         <th><?php echo T_("ADDED"); ?></th>
     </tr></thead>
     
     <?php 
     
     while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        
        $type = ($row["external"] == "yes") ? T_("EXTERNAL") : T_("LOCAL"); 

        if ($row["anon"] == "yes" && ($CURUSER["edit_torrents"] == "no" || $CURUSER["id"] != $row["owner"]))
            $owner = T_("ANONYMOUS");
        elseif ($row["username"])
            $owner = "<a href='$site_config[SITEURL]/accountdetails?id=".$row["owner"]."'>" . class_user($row["username"]) . "</a>";
        else
            $owner = T_("UNKNOWN_USER");

        ?>
        
        <tbody><tr>
           <td><a href="<?php echo $site_config['SITEURL'] ?>/torrents/details?id=<?php echo $row["id"]; ?>"><?php echo CutName(htmlspecialchars($row["name"]), 40) ?></a></td>
           <td><?php echo $owner; ?></td>
           <td><?php echo $type; ?></td>
           <td><?php echo mksize($row["size"]); ?></td>
           <td><?php echo number_format($row["seeders"]); ?></td>
           <td><?php echo number_format($row["leechers"]); ?></td>
           <td><?php echo number_format($row["times_completed"]); ?></td>
           <td><?php echo utc_to_tz($row["added"]); ?></td>
        </tr></tbody>
        
     <?php
     
     }
     
     ?>
     
     </table></div>
     
     <?php
     
     end_frame();
     stdfoot();
    }
	
	
	    public function import(){
dbconn();
global $site_config, $CURUSER, $pdo;
$dir = "import";

//ini_set("upload_max_filesize",$max_torrent_size);

$files = array();
$dh = opendir("$dir/");
while (false !== ($file=readdir($dh))) {
	if (preg_match("/\.torrent$/i", $file))
		$files[] = $file;
}
closedir($dh);


// check access and rights
if ($CURUSER["edit_torrents"] != "yes")
	show_error_msg(T_("ERROR"), T_("ACCESS_DENIED"), 1);

$announce_urls = explode(",", strtolower($site_config["announce_list"]));  //generate announce_urls[] from config.php

if ($_POST["takeupload"] == "yes") {
	set_time_limit(0);
	stdhead(T_("UPLOAD_COMPLETE"));
	begin_frame(T_("UPLOAD_COMPLETE"));
	echo "<center>";

	//check form data
	$catid = (int)$_POST["type"];

	if (!is_valid_id($catid))
		$message = T_("UPLOAD_NO_CAT");
	
	if (empty($message)) {
		$r = $pdo->run("SELECT name, parent_cat FROM categories WHERE id=$catid")->fetch();
		echo "<b>Category:</b> ".htmlspecialchars($r[1])." -> ".htmlspecialchars($r[0])."<br />";
		for ($i=0;$i<count($files);$i++) {
			$fname = $files[$i];

			$descr = T_("UPLOAD_NO_DESC");

			$langid = (int)$_POST["lang"];
	
			preg_match('/^(.+)\.torrent$/si', $fname, $matches);
			$shortfname = $torrent = $matches[1];

			//parse torrent file
			$torrent_dir = $site_config["torrent_dir"];	

			$TorrentInfo = array();
			$TorrentInfo = ParseTorrent("$dir/$fname");


			$announce = strtolower($TorrentInfo[0]);
			$infohash = $TorrentInfo[1];
			$creationdate = $TorrentInfo[2];
			$internalname = $TorrentInfo[3];
			$torrentsize = $TorrentInfo[4];
			$filecount = $TorrentInfo[5];
			$annlist = $TorrentInfo[6];
			$comment = $TorrentInfo[7];
			
			$message = "<br /><br /><hr /><br /><b>$internalname</b><br /><br />fname: ".htmlspecialchars($fname)."<br />message: ";

			//check announce url is local or external
			if (!in_array($announce, $announce_urls, 1))
				$external='yes';
			else
				$external='no';

			if (!$site_config["ALLOWEXTERNAL"] && $external == 'yes') {
				$message .= T_("UPLOAD_NO_TRACKER_ANNOUNCE");
				echo $message;
				continue;
			}

			$name = $internalname;
			$name = str_replace(".torrent","",$name);
			$name = str_replace("_", " ", $name);

			//anonymous upload
			$anonyupload = $_POST["anonycheck"]; 
			if ($anonyupload == "yes")
				$anon = "yes";
			else
				$anon = "no";

			$ret = $pdo->run("INSERT INTO torrents (filename, owner, name, descr, category, added, info_hash, size, numfiles, save_as, announce, external, torrentlang, anon, last_action) VALUES (".sqlesc($fname).", '".$CURUSER['id']."', ".sqlesc($name).", ".sqlesc($descr).", '".$catid."', '" . get_date_time() . "', '".$infohash."', '".$torrentsize."', '".$filecount."', ".sqlesc($fname).", '".$announce."', '".$external."', '".$langid."','$anon', '".get_date_time()."')");

			$id = $ret->lastInsertId();

			if ($ret->errorCode() == 1062) {
				$message .= T_("UPLOAD_ALREADY_UPLOADED");
				echo $message;
				continue;
			}

			if($id == 0){
				$message .= T_("UPLOAD_NO_ID");
				echo $message;
				continue;
			}
    
			copy("$dir/$files[$i]", "uploads/$id.torrent");

			//EXTERNAL SCRAPE
			if ($external=='yes' && $site_config['UPLOADSCRAPE']) {  
				$tracker        = str_replace("/announce","/scrape",$announce);	
				$stats 			= torrent_scrape_url($tracker, $infohash);
				$seeders 		= strip_tags($stats['seeds']);
				$leechers 		= strip_tags($stats['peers']);
				$downloaded 	= strip_tags($stats['downloaded']);

                $pdo->run("UPDATE torrents SET leechers='".$leechers."', seeders='".$seeders."',times_completed='".$downloaded."',last_action= '".get_date_time()."',visible='yes' WHERE id='".$id."'");
			}
			//END SCRAPE

			write_log("Torrent $id ($name) was Uploaded by $CURUSER[username]");

			$message .= "<br /><b>".T_("UPLOAD_OK")."</b><br /><a href='$site_config[SITEURL]/torrents/details?id=".$id."'>".T_("UPLOAD_VIEW_DL")."</a><br /><br />";
			echo $message;
			@unlink("$dir/$fname");
		}
	echo "</center>";
	end_frame();
	stdfoot();
	die;
	}else
		show_error_msg(T_("UPLOAD_FAILED"), $message, 1);

}//takeupload


///////////////////// FORMAT PAGE ////////////////////////

stdhead(T_("UPLOAD"));

begin_frame(T_("UPLOAD"));
?>
<form name="upload" enctype="multipart/form-data" action="torrents/import" method="post">
<input type="hidden" name="takeupload" value="yes" />
<table border="0" cellspacing="0" cellpadding="6" align="center">
<tr><td align="right" valign="top"><b>File List:</b></td><td align="left"><?php
if (!count($files))
	echo T_("NOTHING_TO_SHOW_FILES")." $dir/.";
else{
	foreach ($files as $f)
		echo htmlspecialchars($f)."<br />";
	echo "<br />Total files: ".count($files);
}?></td></tr>
<?php
$category = "<select name=\"type\">\n<option value=\"0\">" .T_("CHOOSE_ONE"). "</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$category .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";

$category .= "</select>\n";
print ("<tr><td align='right'>" .T_("CATEGORY"). ": </td><td align='left'>".$category."</td></tr>");


$language = "<select name=\"lang\">\n<option value=\"0\">Unknown/NA</option>\n";

$langs = langlist();
foreach ($langs as $row)
	$language .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$language .= "</select>\n";
print ("<tr><td align='right'>Language: </td><td align='left'>".$language."</td></tr>");
$anonycheck = '';
if ($site_config['ANONYMOUSUPLOAD']){ ?>
	<tr><td align="right"><?php echo T_("UPLOAD_ANONY");?>: </td><td><?php printf("<input name='anonycheck' value='yes' type='radio' " . ($anonycheck ? " checked='checked'" : "") . " />Yes <input name='anonycheck' value='no' type='radio' " . (!$anonycheck ? " checked='checked'" : "") . " />No"); ?> &nbsp;<?php echo T_("UPLOAD_ANONY_MSG");?>
	</td></tr>

<?php } ?>
<tr><td align="center" colspan="2"><<button type="submit" class="btn btn-primary btn-sm"><?php echo T_("UPLOAD"); ?></button><br />
<i><?php echo T_("CLICK_ONCE_IMAGE");?></i></td></tr></table></form>
<?php
end_frame();
stdfoot();
}

    public function edit(){
dbconn();
global $site_config, $CURUSER, $pdo;
loggedinonly();

$id = (int) $_REQUEST["id"];
if (!is_valid_id($id)) show_error_msg(T_("ERROR"), T_("INVALID_ID"), 1);
$action = $_REQUEST["action"];

$row = $pdo->run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
if($CURUSER["edit_torrents"]=="no" && $CURUSER['id'] != $row['owner'])
    show_error_msg(T_("ERROR"), T_("NO_TORRENT_EDIT_PERMISSION"), 1);




//GET DATA FROM DB
$row = $pdo->run("SELECT * FROM torrents WHERE id =?", [$id])->fetch();
if (!$row){
    show_error_msg(T_("ERROR"), T_("TORRENT_ID_GONE"), 1);
}

$torrent_dir = $site_config["torrent_dir"];    
$nfo_dir = $site_config["nfo_dir"];    

//DELETE TORRENT
if ($action=="deleteit"){
    $torrentid = (int) $_POST["torrentid"];
    $delreason = sqlesc($_POST["delreason"]);
    $torrentname = $_POST["torrentname"];

    if (!is_valid_id($torrentid))
        show_error_msg(T_("FAILED"), T_("INVALID_TORRENT_ID"), 1);

    if (!$delreason){
        show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA"), 1);
    }

    deletetorrent($torrentid);

    write_log($CURUSER['username']." has deleted torrent: ID:$torrentid - ".htmlspecialchars($torrentname)." - Reason: ".htmlspecialchars($delreason));
    if ($CURUSER['id'] != $row['owner']) {
	$delreason = $_POST["delreason"];
	    $pdo->run("INSERT INTO messages (sender, receiver, added, subject, msg, unread, location) VALUES(0, ".$row['owner'].", '".get_date_time()."', 'Your torrent \'$torrentname\' has been deleted by ".$CURUSER['username']."', ".sqlesc("'$torrentname' was deleted by ".$CURUSER['username']."\n\nReason: $delreason").", 'yes', 'in')");
    }

    show_error_msg(T_("COMPLETED"), htmlspecialchars($torrentname)." ".T_("HAS_BEEN_DEL_DB"),1);
    die;
}

//DO THE SAVE TO DB HERE
if ($action=="doedit"){
    $updateset = array();

    $nfoaction = $_POST['nfoaction'];
    if ($nfoaction == "update"){
      $nfofile = $_FILES['nfofile'];
      if (!$nfofile) die("No data " . var_dump($_FILES));
      if ($nfofile['size'] > 65535)
        show_error_msg("NFO is too big!", "Max 65,535 bytes.",1);
      $nfofilename = $nfofile['tmp_name'];
      if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0){
            @move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
            $updateset[] = "nfo = 'yes'";
        }//success
    }

    if (!empty($_POST["name"]))
         $updateset[] = "name = " . sqlesc($_POST["name"]);
    
    $updateset[] = "descr = " . sqlesc($_POST["descr"]);
    $updateset[] = "category = " . (int) $_POST["type"];
    $updateset[] = "torrentlang = " . (int) $_POST["language"];

    if ($CURUSER["edit_torrents"] == "yes") {
        if ($_POST["banned"]) {
            $updateset[] = "banned = 'yes'";
            $_POST["visible"] = 0;
        } else {
            $updateset[] = "banned = 'no'";
        }
    }

    $updateset[] = "visible = '" . ($_POST["visible"] ? "yes" : "no") . "'";

    if ($CURUSER["edit_torrents"] == "yes")
        $updateset[] = "freeleech = '".($_POST["freeleech"] ? "1" : "0")."'";

    $updateset[] = "anon = '" . ($_POST["anon"] ? "yes" : "no") . "'";

    //update images
    $img1action = $_POST['img1action'];
    if ($img1action == "update")
        $updateset[] = "image1 = " .sqlesc(uploadimage(0, $row["image1"], $id));
    if ($img1action == "delete") {
        if ($row['image1']) {
            $del = unlink($site_config["torrent_dir"]."/images/$row[image1]");
            $updateset[] = "image1 = ''";
        }
    }

    $img2action = $_POST['img2action'];
    if ($img2action == "update")
        $updateset[] = "image2 = " .sqlesc(uploadimage(1, $row["image2"], $id));
    if ($img2action == "delete") {
        if ($row['image2']) {
            $del = unlink($site_config["torrent_dir"]."/images/$row[image2]");
            $updateset[] = "image2 = ''";
        }
    }


    $pdo->run("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");

    $returl = "torrents/edit?id=$id&edited=1";
    if (isset($_POST["returnto"])){
        $returl = $_POST["returnto"];
    }

    write_log("Torrent $id (".htmlspecialchars($_POST["name"]).") was edited by $CURUSER[username]");

    header("Location: $returl");
    die();
}//END SAVE TO DB

//UPDATE CATEGORY DROPDOWN
$catdropdown = "<select name=\"type\">\n";
$cats = genrelist();
    foreach ($cats as $catdropdownubrow) {
        $catdropdown .= "<option value=\"" . $catdropdownubrow["id"] . "\"";
        if ($catdropdownubrow["id"] == $row["category"])
            $catdropdown .= " selected=\"selected\"";
        $catdropdown .= ">" . htmlspecialchars($catdropdownubrow["parent_cat"]) . ": " . htmlspecialchars($catdropdownubrow["name"]) . "</option>\n";
    }
$catdropdown .= "</select>\n";
//END CATDROPDOWN

//UPDATE TORRENTLANG DROPDOWN
$langdropdown = "<select name=\"language\"><option value='0'>Unknown</option>\n";
$lang = langlist();
foreach ($lang as $lang) {
    $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
    if ($lang["id"] == $row["torrentlang"])
        $langdropdown .= " selected=\"selected\"";
    $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
}
$langdropdown .= "</select>\n";
//END TORRENTLANG


$char1 = 55;
$shortname = CutName(htmlspecialchars($row["name"]), $char1);

if ($_GET["edited"]){
    show_error_msg("Edited OK", T_("TORRENT_EDITED_OK"), 1);
}

stdhead(T_("EDIT_TORRENT")." \"$shortname\"");

begin_frame(T_("EDIT_TORRENT")." \"$shortname\"");

print("<br /><br /><form method='post' name=\"bbform\" enctype=\"multipart/form-data\" action=\"$site_config[SITEURL]/torrents/edit?action=doedit\">\n");
print("<input type=\"hidden\" name=\"id\" value=\"$id\" />\n");

if (isset($_GET["returnto"]))
    print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($_GET["returnto"]) . "\" />\n");

print("<table class='table_table' cellspacing='0' cellpadding='4' width='586' align='center'>\n");
echo "<tr><td class='table_col1' align='right' width='60'><b>".T_("NAME").": </b></td><td class='table_col2' ><input type=\"text\" name=\"name\" value=\"" . htmlspecialchars($row["name"]) . "\" size=\"60\" /></td></tr>";
echo "<tr><td class='table_col1'  align='right'><b>".T_("IMAGE").": </b></td><td class='table_col2'><b>".T_("IMAGE")." 1:</b>&nbsp;&nbsp;<input type='radio' name='img1action' value='keep' checked='checked' />".T_("KEEP_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img1action' value='delete' />".T_("DELETE_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img1action' value='update' />".T_("UPDATE_IMAGE")."<br /><input type='file' name='image0' size='60' /> <br /><br /> <b>".T_("IMAGE")." 2:</b>&nbsp;&nbsp;<input type='radio' name='img2action' value='keep' checked='checked' />".T_("KEEP_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img2action' value='delete' />".T_("DELETE_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img2action' value='update' />".T_("UPDATE_IMAGE")."<br /><input type='file' name='image1' size='60' /></td></tr>";
echo "<tr><td class='table_col1'  align='right'><b>".T_("NFO").": </b><br /></td><td class='table_col2' ><input type='radio' name='nfoaction' value='keep' checked='checked' />Keep NFO &nbsp; <input type='radio' name='nfoaction' value='update' />Update NFO:";
if ($row["nfo"] == "yes"){
    echo "&nbsp;&nbsp;<a href='$site_config[SITEURL]/nfo/view?id=".$row["id"]."' target='_blank'>[".T_("VIEW_CURRENT_NFO")."]</a>";
} else{
    echo "&nbsp;&nbsp;<font color='#ff0000'>".T_("NO_NFO_UPLOADED")."</font>";
}
echo "<br /><input type='file' name='nfofile' size='60' /></td></tr>";

echo "<tr><td class='table_col1' align='right'><b>".T_("CATEGORIES").": </b></td><td class='table_col2'>".$catdropdown."</td></tr>";

echo "<tr><td class='table_col1' align='right'><b>".T_("LANG").": </b></td><td class='table_col2'>".$langdropdown."</td></tr>";

if ($CURUSER["edit_torrents"] == "yes")
    echo "<tr><td class='table_col1' align='right'><b>".T_("BANNED").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"banned\"" . (($row["banned"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /> ".T_("BANNED")."?<br /></td></tr>";
echo "<tr><td class='table_col1' align='right'><b>".T_("VISIBLE").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"visible\"" . (($row["visible"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /> " .T_("VISIBLEONMAIN"). "<br /></td></tr>";

if ($row["external"] != "yes" && $CURUSER["edit_torrents"] == "yes"){
    echo "<tr><td class='table_col1' align='right'><b>".T_("FREE_LEECH").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"freeleech\"" . (($row["freeleech"] == "1") ? " checked=\"checked\"" : "" ) . " value=\"1\" />".T_("FREE_LEECH_MSG")."<br /></td></tr>";
}

if ($site_config['ANONYMOUSUPLOAD']) {
	echo "<tr><td class='table_col1' align='right'><b>".T_("ANONYMOUS_UPLOAD").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"anon\"" . (($row["anon"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" />(".T_("ANONYMOUS_UPLOAD_MSG").")<br /></td></tr>";
}
print ("<tr><td class='table_head' align='center' colspan='2'><b>" .T_("DESCRIPTION"). ":</b></td></tr></table>");
require_once("helpers/bbcode_helper.php");
print textbbcode("bbform","descr", htmlspecialchars($row["descr"]));

    
print("<br /><center><input type=\"submit\" value='".T_("SUBMIT")."' /> <input type='reset' value='".T_("UNDO")."' /></center>\n");
print("</form>\n");
end_frame();

begin_frame(T_("DELETE_TORRENT"));
        print("<center><form method='post' action='".TTURL."/torrents/edit?action=deleteit&amp;id=$id'>\n");
        print("<input type='hidden' name='torrentid' value='$id' />\n");
        print("<input type='hidden' name='torrentname' value='".htmlspecialchars($row["name"])."' />\n");
        echo "<b>".T_("REASON_FOR_DELETE")."</b><input type='text' size='30' name='delreason' />";
        echo "&nbsp;<input type='submit' value='".T_("DELETE_TORRENT")."' /></form></center>";
end_frame();

stdfoot();
}

    public function details(){
require_once("classes/BEcode.php");
dbconn();
global $site_config, $CURUSER, $pdo;
$torrent_dir = $site_config["torrent_dir"];	
$nfo_dir = $site_config["nfo_dir"];	

//check permissions
if ($site_config["MEMBERSONLY"]){
	loggedinonly();

	if($CURUSER["view_torrents"]=="no")
		show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
}

//************ DO SOME "GET" STUFF BEFORE PAGE LAYOUT ***************

$id = (int) $_GET["id"];
$scrape = (int)$_GET["scrape"];
if (!is_valid_id($id))
	show_error_msg(T_("ERROR"), T_("THATS_NOT_A_VALID_ID"), 1);

//GET ALL MYSQL VALUES FOR THIS TORRENT
$res = $pdo->run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
$row = $res->fetch(PDO::FETCH_ASSOC);

//DECIDE IF TORRENT EXISTS
if (!$row || ($row["banned"] == "yes" && $CURUSER["edit_torrents"] == "no"))
	show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);

//torrent is availiable so do some stuff

if ($_GET["hit"]) {
	$pdo->run("UPDATE torrents SET views = views + 1 WHERE id = $id");
	header("Location: ".TTURL."/torrents/details?id=$id");
	die;
	}

	stdhead(T_("DETAILS_FOR_TORRENT")." \"" . $row["name"] . "\"");

	if ($CURUSER["id"] == $row["owner"] || $CURUSER["edit_torrents"] == "yes")
		$owned = 1;
	else
		$owned = 0;

//take rating
if ($_GET["takerating"] == 'yes'){
	$rating = (int)$_POST['rating'];

	if ($rating <= 0 || $rating > 5)
		show_error_msg(T_("RATING_ERROR"), T_("INVAILD_RATING"), 1);

	$res = $pdo->run("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rating, '".get_date_time()."')");

	if (!$res) {
		if ($res->errorCode() == 1062)
			show_error_msg(T_("RATING_ERROR"), T_("YOU_ALREADY_RATED_TORRENT"), 1);
		else
			show_error_msg(T_("RATING_ERROR"), T_("A_UNKNOWN_ERROR_CONTACT_STAFF"), 1);
	}

	$pdo->run("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
	show_error_msg(T_("RATING_SUCCESS"), T_("RATING_THANK")."<br /><br /><a href='$site_config[SITEURL]/torrents/details?id=$id'>" .T_("BACK_TO_TORRENT"). "</a>");
}

//take comment add
if ($_GET["takecomment"] == 'yes'){
	loggedinonly();
	$body = $_POST['body'];
	
	if (!$body)
		show_error_msg(T_("RATING_ERROR"), T_("YOU_DID_NOT_ENTER_ANYTHING"), 1);

	$pdo->run("UPDATE torrents SET comments = comments + 1 WHERE id = $id");

    $comins = $pdo->run("INSERT INTO comments (user, torrent, added, text) VALUES (".$CURUSER["id"].", ".$id.", '" .get_date_time(). "', " . sqlesc($body).")");

	if ($comins)
			show_error_msg(T_("COMPLETED"), T_("COMMENT_ADDED"), 0);
		else
			show_error_msg(T_("ERROR"), T_("UNABLE_TO_ADD_COMMENT"), 0);
}//end insert comment

//START OF PAGE LAYOUT HERE
$char1 = 50; //cut length
$shortname = CutName(htmlspecialchars($row["name"]), $char1);

begin_frame(T_("TORRENT_DETAILS_FOR"). " \"" . $shortname . "\"");

echo "<div align='right'><a href='$site_config[SITEURL]/report?torrent=$id'><button type='button' class='btn btn-sm btn-danger'><b>" .T_("REPORT_TORRENT"). "</b></button></a>";
if ($owned)
	echo "<a href='$site_config[SITEURL]/torrents/edit?id=$row[id]&amp;returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "'><button type='button' class='btn btn-sm btn-success'><b>".T_("EDIT_TORRENT")."</b></button></a>";

// snatch
echo "<a href=$site_config[SITEURL]/snatched?tid=$row[id]><button type='button' class='btn btn-sm btn-warning'><b>".T_("SNATCHLIST")."</b></button></a>";

echo "</div>";

echo "<center><h1>" . $shortname . "</h1></center>";

// Calculate local torrent speed test
if ($row["leechers"] >= 1 && $row["seeders"] >= 1 && $row["external"]!='yes'){
	$speedQ = $pdo->run("SELECT (SUM(p.downloaded)) / (UNIX_TIMESTAMP('".get_date_time()."') - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$id' GROUP BY t.id ORDER BY added ASC LIMIT 15");
	$a = $speedQ->fetch(PDO::FETCH_ASSOC);
	$totalspeed = mksize($a["totalspeed"]) . "/s";
}else{
	$totalspeed = T_("NO_ACTIVITY"); 
}

//download box
echo "<center><table border='0' width='100%'><tr><td><div id='downloadbox'>";
if ($row["banned"] == "yes"){
	print ("<center><b>" .T_("DOWNLOAD"). ": </b>BANNED!</center>");
}else{
		print ("<table border='0' width='100%'><tr>");
	
    // Like Mod
    if(!$site_config["forcethanks"]) {
    // Magnet
    if ($row["external"] == 'yes'){
    print ("<a href=\"magnet:?xt=urn:btih:".$row["info_hash"]."&dn=".$row["filename"]."&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
    }else{
    print ("<a href=\"magnet:?xt=urn:btih:".$row["info_hash"]."&dn=".$row["filename"]."&tr=".$site_config['SITEURL']."/announce.php?passkey=".$CURUSER["passkey"]."\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
    }
    }
    if($CURUSER["id"] != $row["owner"] && $site_config["forcethanks"]) {
    $data = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$id, 'torrent', $CURUSER['id']]);
    $like = $data->fetch(PDO::FETCH_ASSOC);
    if($like){
    // Magnet
    if ($row["external"] == 'yes'){
    print ("<a href=\"magnet:?xt=urn:btih:".$row["info_hash"]."&dn=".$row["filename"]."&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
    }else{
    print ("<a href=\"magnet:?xt=urn:btih:".$row["info_hash"]."&dn=".$row["filename"]."&tr=".$site_config['SITEURL']."/announce.php?passkey=".$CURUSER["passkey"]."\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
    }
    }else {
       print("<a href='$site_config[SITEURL]/likes/index?id=$id'><button  class='btn btn-sm btn-danger'>Thanks</button></a>&nbsp;");
    }
    }else{
    if ($row["external"] == 'yes'){
    print ("<a href=\"magnet:?xt=urn:btih:".$row["info_hash"]."&dn=".$row["filename"]."&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
    }else{
    print ("<a href=\"magnet:?xt=urn:btih:".$row["info_hash"]."&dn=".$row["filename"]."&tr=".$site_config['SITEURL']."/announce.php?passkey=".$CURUSER["passkey"]."\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
    }
    }
	
	print ("<a href=\"/download?id=$id&amp;name=" . rawurlencode($row["filename"]) . "\"><button type='button' class='btn btn-sm btn-success'>".T_("DOWNLOAD_TORRENT")."</button></a></br>");
	print ("<b>" .T_("HEALTH"). ": </b><img src='".$site_config["SITEURL"]."/images/health/health_".health($row["leechers"], $row["seeders"]).".gif' alt='' /><br />");
	print ("<b>" .T_("SEEDS"). ": </b><font color='green'>" . number_format($row["seeders"]) . "</font><br />");
	print ("<b>".T_("LEECHERS").": </b><font color='#ff0000'>" .  number_format($row["leechers"]) . "</font><br />");

	if ($row["external"]!='yes'){
		print ("<b>".T_("SPEED").": </b>" . $totalspeed . "<br />");
	}

	print ("<b>".T_("COMPLETED").":</b> " . number_format($row["times_completed"]) . "&nbsp;"); 

	if ($row["external"] != "yes" && $row["times_completed"] > 0) {
		echo("[<a href='$site_config[SITEURL]/torrents/completed?id=$id'>" .T_("WHOS_COMPLETED"). "</a>] ");
		if ($row["seeders"] <= 1) {
			echo("[<a href='$site_config[SITEURL]/torrents/reseed?id=$id'>" .T_("REQUEST_A_RE_SEED"). "</a>]");
		}
	}
	echo "<br />";

	if ($row["external"]!='yes' && $row["freeleech"]=='1'){
		print ("<b>".T_("FREE_LEECH").": </b><font color='#ff0000'>".T_("FREE_LEECH_MSG")."</font><br />");
	}

	print ("<b>".T_("LAST_CHECKED").": </b>" . date("d-m-Y H:i:s", utc_to_tz_time($row["last_action"])) . "<br /></td>");

	if ($row["external"]=='yes'){

		if ($scrape =='1'){
			print("<td valign='top' align='right'><b>Tracked: </b>EXTERNAL<br /><br />");
			$seeders1 = $leechers1 = $downloaded1 = null;

			$tres = $pdo->run("SELECT url FROM announce WHERE torrent=$id");
			while ($trow = $tres->fetch(PDO::FETCH_ASSOC)) {
				$ann = $trow["url"];
				$tracker = explode("/", $ann);
				$path = array_pop($tracker);
				$oldpath = $path;
				$path = preg_replace("/^announce/", "scrape", $path);
				$tracker = implode("/", $tracker)."/".$path;

				if ($oldpath == $path) {
					continue; // Scrape not supported, ignored
				}

				// TPB's tracker is dead. Use openbittorrent instead
				if (preg_match("/thepiratebay.org/i", $tracker) || preg_match("/prq.to/", $tracker)) {
					$tracker = "http://tracker.openbittorrent.com/scrape";
				}

				$stats = torrent_scrape_url($tracker, $row["info_hash"]);
				if ($stats['seeds'] != -1) {
					$seeders1 += $stats['seeds'];
					$leechers1 += $stats['peers'];
					$downloaded1 += $stats['downloaded'];
                    $pdo->run("UPDATE `announce` SET `online` = 'yes', `seeders` = $stats[seeds], `leechers` = $stats[peers], `times_completed` = $stats[downloaded] WHERE `url` = ".sqlesc($ann)." AND `torrent` = $id");
				} else {
                    $pdo->run("UPDATE `announce` SET `online` = 'no' WHERE `url` = ".sqlesc($ann)." AND `torrent` = $id");

				}
			}

			if ($seeders1 !== null){ //only update stats if data is received
				print ("<b>".T_("LIVE_STATS").": </b><br />");
				print ("Seeders: ".number_format($seeders1)."<br />");
				print ("Leechers: ".number_format($leechers1)."<br />");
				print (T_("COMPLETED").": ".number_format($downloaded1)."<br />");

                $pdo->run("UPDATE torrents SET leechers='".$leechers1."', seeders='".$seeders1."', times_completed='".$downloaded1."',last_action= '".get_date_time()."',visible='yes' WHERE id='".$row['id']."'");
			}else{
				print ("<b>".T_("LIVE_STATS").": </b><br />");
				print ("<font color='#ff0000'>Tracker Timeout<br />Please retry later</font><br />");
			}

			print ("<form action='/torrents/details?id=$id&amp;scrape=1' method='post'><input type=\"submit\" name=\"submit\" value=\"Update Stats\" /></form></td>");
		}else{
			print ("<td valign='top' align='right'><b>Tracked:</b> EXTERNAL<br /><br /><form action='/torrents/details?id=$id&amp;scrape=1' method='post'><input type=\"submit\" name=\"submit\" value=\"Update Stats\" /></form></td>");
		}
	}

	echo "</tr></table>";
}
echo "</div></td></tr></table></center><br /><br />";
//end download box


echo "<fieldset class='download'><legend><b>Details</b></legend>";
echo "<table cellpadding='3' border='0' width='100%'>";
print("<tr><td align='left'><b>".T_("NAME").":</b></td><td>" . $shortname . "</td></tr>\n");
print("<tr><td align='left' colspan='2'><b>" .T_("DESCRIPTION"). ":</b><br />" .  format_comment($row['descr']) . "</td></tr>\n");
print("<tr><td align='left'><b>" .T_("CATEGORY"). ":</b></td><td>" . $row["cat_parent"] . " > " . $row["cat_name"] . "</td></tr>\n");

if (empty($row["lang_name"])) $row["lang_name"] = "Unknown/NA";
print("<tr><td align='left'><b>" .T_("LANG"). ":</b></td><td>" . $row["lang_name"] . "\n");

if (isset($row["lang_image"]) && $row["lang_image"] != "")
			print("&nbsp;<img border=\"0\" src=\"" . $site_config['SITEURL'] . "/images/languages/" . $row["lang_image"] . "\" alt=\"" . $row["lang_name"] . "\" />");

print("</td></tr>");

print("<tr><td align='left'><b>" .T_("TOTAL_SIZE"). ":</b></td><td>" . mksize($row["size"]) . " </td></tr>\n");
print("<tr><td align='left'><b>" .T_("INFO_HASH"). ":</b></td><td>" . $row["info_hash"] . "</td></tr>\n");
print("");
if ($row["anon"] == "yes" && !$owned)
	print("<tr><td align='left'><b>" .T_("ADDED_BY"). ":</b></td><td>Anonymous</td></tr>");
elseif ($row["username"])
	print("<tr><td align='left'><b>" .T_("ADDED_BY"). ":</b></td><td><a href='$site_config[SITEURL]/accountdetails?id=" . $row["owner"] . "'>" . class_user($row["username"]) . "</a></td></tr>");
else
	print("<tr><td align='left'><b>" .T_("ADDED_BY"). ":</b></td><td>Unknown</td></tr>");

print("<tr><td align='left'><b>" .T_("DATE_ADDED"). ":</b></td><td>" . date("d-m-Y H:i:s", utc_to_tz_time($row["added"])) . "</td></tr>\n");
print("<tr><td align='left'><b>" .T_("VIEWS"). ":</b></td><td>" . number_format($row["views"]) . "</td></tr>\n");
print("<tr><td align='left'><b>".T_("HITS").":</b></td><td>" . number_format($row["hits"]) . "</td></tr>\n");
    // LIKE MOD
    if($site_config["allowlikes"]) {
    $data = DB::run("SELECT user FROM likes WHERE liked=? AND type=? AND user=? AND reaction=?", [$id, 'torrent', $CURUSER['id'], 'like']);
    $likes = $data->fetch(PDO::FETCH_ASSOC);
    if($likes){
        print("<tr><td align='left'><b>Reaction:</b></td><td><a href='$site_config[SITEURL]/likes/unliketorrent?id=$id'><img src='/images/unlike.png' width='80' height='40' border='0'></a></td></tr>\n");
    }else{
        print("<tr><td align='left'><b>Reaction:</b></td><td><a href='$site_config[SITEURL]/likes/liketorrent?id=$id'><img src='/images/like.png' width='80' height='40' border='0'></a></td></tr>\n");
    }
    }
echo "</table></fieldset><br /><br />";

// $srating IS RATING VARIABLE
		$srating = "";
		$srating .= "<table class='f-border' cellspacing=\"1\" cellpadding=\"4\" width='100%'><tr><td class='f-title' width='60'><b>".T_("RATINGS").":</b></td><td class='f-title' valign='middle'>";
		if (!isset($row["rating"])) {
				$srating .= "Not Yet Rated";
		}else{
			$rpic = ratingpic($row["rating"]);
			if (!isset($rpic))
				$srating .= "invalid?";
			else
				$srating .= "$rpic (" . $row["rating"] . " ".T_("OUT_OF")." 5) " . $row["numratings"] . " ".T_("USERS_HAVE_RATED");
		}
		$srating .= "\n";
		if (!isset($CURUSER))
			$srating .= "(<a href='$site_config[SITEURL]/account/login'>Log in</a> to rate it)";
		else {
			$ratings = array(
					5 => T_("COOL"),
					4 => T_("PRETTY_GOOD"),
					3 => T_("DECENT"),
					2 => T_("PRETTY_BAD"),
					1 => T_("SUCKS")
			);
			//if (!$owned || $moderator) {
				$xres = $pdo->run("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
				$xrow = $xres->fetch(PDO::FETCH_ASSOC);
				if ($xrow)
					$srating .= "<br /><i>(".T_("YOU_RATED")." \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")</i>";
				else {
					$srating .= "<form style=\"display:inline;\" method=\"post\" action=\"torrents/details?id=$id&amp;takerating=yes\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
					$srating .= "<select name=\"rating\">\n";
					$srating .= "<option value=\"0\">(".T_("ADD_RATING").")</option>\n";
					foreach ($ratings as $k => $v) {
						$srating .= "<option value=\"$k\">$k - $v</option>\n";
					}
					$srating .= "</select>\n";
					$srating .= "<input type=\"submit\" value=\"".T_("VOTE")."\" />";
					$srating .= "</form>\n";
				}
			//}
		}
		$srating .= "</td></tr></table>";

print("<center>". $srating . "</center>");// rating

//END DEFINE RATING VARIABLE

echo "<br />";
                                                  
if ($row["image1"] != "" OR $row["image2"] != "") {
  if ($row["image1"] != "")
    $img1 = "<img src='".$site_config["SITEURL"]."/uploads/images/$row[image1]' width='150' border='0' alt='' />";
  if ($row["image2"] != "")
    $img2 = "<img src='".$site_config["SITEURL"]."/uploads/images/$row[image2]' width='150' border='0' alt='' />";
  print("<center>". $img1 . "&nbsp;&nbsp;" . $img2."</center><br />");
}

if ($row["external"]=='yes'){
	print ("<br /><b>Tracker:</b><br /> ".htmlspecialchars($row['announce'])."<br />");
}

$tres = $pdo->run("SELECT * FROM `announce` WHERE `torrent` = $id");
if ($tres->rowCount() > 1){
	echo "<br /><b>".T_("THIS_TORRENT_HAS_BACKUP_TRACKERS")."</b><br />";
	echo '<table cellpadding="1" cellspacing="2" class="table_table"><tr>';
	echo '<th class="table_head">URL</th><th class="table_head">'.T_("SEEDERS").'</th><th class="table_head">'.T_("LEECHERS").'</th><th class="table_head">'.T_("COMPLETED").'</th></tr>';
	$x = 1;
	while ($trow = $tres->fetch(PDO::FETCH_ASSOC)) {
		$colour = $trow["online"] == "yes" ? "green" : "red";
		echo "<tr class=\"table_col$x\"><td><font color=\"$colour\"><b>".htmlspecialchars($trow['url'])."</b></font></td><td align=\"center\">".number_format($trow["seeders"])."</td><td align=\"center\">".number_format($trow["leechers"])."</td><td align=\"center\">".number_format($trow["times_completed"])."</td></tr>";
		$x = $x == 1 ? 2 : 1;
	}
	echo '</table>';
}

echo "<br /><br /><b>".T_("FILE_LIST").":</b>&nbsp;<img src='/images/plus.gif' id='pic1' onclick='klappe_torrent(1)' alt='' /><div id='k1' style='display: none;'><table align='center' cellpadding='0' cellspacing='0' class='table_table' border='1' width='100%'><tr><th class='table_head' align='left'>&nbsp;".T_("FILE")."</th><th width='50' class='table_head'>&nbsp;".T_("SIZE")."</th></tr>";
$fres = $pdo->run("SELECT * FROM `files` WHERE `torrent` = $id ORDER BY `path` ASC");
if ($fres->rowCount()) {
    while ($frow = $fres->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td class='table_col1'>".htmlspecialchars($frow['path'])."</td><td class='table_col2'>".mksize($frow['filesize'])."</td></tr>";
    }
}else{
    echo "<tr><td class='table_col1'>".htmlspecialchars($row["name"])."</td><td class='table_col2'>".mksize($row["size"])."</td></tr>";
}
echo "</table></div>";

if ($row["external"]!='yes'){
	echo "<br /><br /><b>".T_("PEERS_LIST").":</b><br />";
	$query = $pdo->run("SELECT * FROM peers WHERE torrent = $id ORDER BY seeder DESC");

	$result = $query->rowCount();
		if($result == 0) {
			echo T_("NO_ACTIVE_PEERS")."\n";
		}else{
			?>

            <table border="0" cellpadding="3" cellspacing="0" width="100%" class="table_table">
			<tr>
                <th class="table_head"><?php echo T_("PORT"); ?></th>
			    <th class="table_head"><?php echo T_("UPLOADED"); ?></th>
			    <th class="table_head"><?php echo T_("DOWNLOADED"); ?></th>
			    <th class="table_head"><?php echo T_("RATIO"); ?></th>
			    <th class="table_head"><?php echo T_("_LEFT_"); ?></th>
			    <th class="table_head"><?php echo T_("FINISHED_SHORT"). "%"; ?></th>
			    <th class="table_head"><?php echo T_("SEED"); ?></th>
			    <th class="table_head"><?php echo T_("CONNECTED_SHORT"); ?></th>
			    <th class="table_head"><?php echo T_("CLIENT"); ?></th>
			    <th class="table_head"><?php echo T_("USER_SHORT"); ?></th>
			</tr>

			<?php
			while($row1 = $query->fetch(PDO::FETCH_ASSOC))	{
				
				if ($row1["downloaded"] > 0){
					$ratio = $row1["uploaded"] / $row1["downloaded"];
					$ratio = number_format($ratio, 3);
				}else{
					$ratio = "---";
				}

				$percentcomp = sprintf("%.2f", 100 * (1 - ($row1["to_go"] / $row["size"])));    

				if ($site_config["MEMBERSONLY"]) {
					$res = $pdo->run("SELECT id, username, privacy FROM users WHERE id=".$row1["userid"]."");
					$arr = $res->fetch(PDO::FETCH_LAZY);
                    
                    $arr["username"] = "<a href='$site_config[SITEURL]/accountdetails?id=$arr[id]'>".class_user($arr['username'])."</a>";
				}
                
                # With $site_config["MEMBERSONLY"] off this will be shown.
                if ( !$arr["username"] ) $arr["username"] = "Unknown User";
        
				if ($arr["privacy"] != "strong" || ($CURUSER["control_panel"] == "yes")) {
					print("<tr><td class='table_col2'>".$row1["port"]."</td><td class='table_col1'>".mksize($row1["uploaded"])."</td><td class='table_col2'>".mksize($row1["downloaded"])."</td><td class='table_col1'>".$ratio."</td><td class='table_col2'>".mksize($row1["to_go"])."</td><td class='table_col1'>".$percentcomp."%</td><td class='table_col2'>$row1[seeder]</td><td class='table_col1'>$row1[connectable]</td><td class='table_col2'>".htmlspecialchars($row1["client"])."</td><td class='table_col1'>$arr[username]</td></tr>");
				}else{
					print("<tr><td class='table_col2'>".$row1["port"]."</td><td class='table_col1'>".mksize($row1["uploaded"])."</td><td class='table_col2'>".mksize($row1["downloaded"])."</td><td class='table_col1'>".$ratio."</td><td class='table_col2'>".mksize($row1["to_go"])."</td><td class='table_col1'>".$percentcomp."%</td><td class='table_col2'>$row1[seeder]</td><td class='table_col1'>$row1[connectable]</td><td class='table_col2'>".htmlspecialchars($row1["client"])."</td><td class='table_col1'>Private</td></tr>");
				}

			}
			echo "</table>";
	}
}

echo "<br /><br />";


//-----------------------------------------------

//DISPLAY NFO BLOCK
if($row["nfo"]== "yes"){
	$nfofilelocation = "$nfo_dir/$row[id].nfo";
	$filegetcontents = file_get_contents($nfofilelocation);
// needs filtering better todo
//	$nfo = htmlspecialchars($filegetcontents);
	$nfo = $filegetcontents;
		if ($nfo) {	
			$nfo = my_nfo_translate($nfo);
			echo "<br /><br /><b>NFO:</b><br />";
			print("<textarea class='nfo' style='width:98%;height:100%;' rows='20' cols='20' readonly='readonly'>".stripslashes($nfo)."</textarea>");
        }else{
            print(T_("ERROR")." reading .nfo file!");
        }
}
end_frame();

begin_frame(T_("COMMENTS"));
	//echo "<p align=center><a class=index href=$site_config[SITEURL]/torrents-comment.php?id=$id>" .T_("ADDCOMMENT"). "</a></p>\n";

  //  $subrow = $pdo->run("SELECT COUNT(*) FROM comments WHERE torrent = $id")->fetch();
	$commcount = $pdo->run("SELECT COUNT(*) FROM comments WHERE torrent = $id")->fetchColumn(); //$subrow[0];

	if ($commcount) {
		list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "/torrents/details?id=$id&amp;");
		$commquery = "SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = $id ORDER BY comments.id $limit";
		$commres = $pdo->run($commquery);
	}else{
		unset($commres);
	}

	if ($commcount) {
		print($pagertop);
		commenttable($commres, 'torrent');
		print($pagerbottom);
	}else {
		print("<br /><b>" .T_("NOCOMMENTS"). "</b><br />\n");
	}

	require_once("helpers/bbcode_helper.php");

	if ($CURUSER) {
		echo "<center>";
		echo "<form name=\"comment\" method=\"post\" action=\"/torrents/details?id=$row[id]&amp;takecomment=yes\">";
		echo textbbcode("comment","body")."<br />";
		echo "<input type=\"submit\"  value=\"".T_("ADDCOMMENT")."\" />";
		echo "</form></center>";
	}

	end_frame();

stdfoot();
}

    public function completed(){
  dbconn();
   global $site_config, $CURUSER, $pdo;
$db = new Database;    
  if ($site_config["MEMBERSONLY"]) {
      loggedinonly();
      
      if ($CURUSER["view_torrents"] == "no")
          show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
  }
                  
  $id = (int) $_GET["id"];
  
  $res = $pdo->run("SELECT name, external, banned FROM torrents WHERE id =?", [$id]);
  $row = $res->fetch(PDO::FETCH_ASSOC);
  
  if ((!$row) || ($row["banned"] == "yes" && $CURUSER["edit_torrents"] == "no"))
       show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);
  if ($row["external"] == "yes")
       show_error_msg(T_("ERROR"), T_("THIS_TORRENT_IS_EXTERNALLY_TRACKED"), 1);

  $res = $pdo->run("SELECT users.id, users.username, users.uploaded, users.downloaded, users.privacy, completed.date FROM users LEFT JOIN completed ON users.id = completed.userid WHERE users.enabled = 'yes' AND completed.torrentid = '$id'");
  if ($res->rowCount() == 0)
      show_error_msg(T_("ERROR"), T_("NO_DOWNLOADS_YET"), 1);
  
  $title = sprintf(T_("COMPLETED_DOWNLOADS"), CutName($row["name"], 40));   
  
  stdhead($title);
  begin_frame($title);
  ?>
  
  <table cellpadding="3" cellspacing="0" align="center" class="table_table">
  <tr>
     <th class="table_head"><?php echo T_("USERNAME"); ?></th>
     <th class="table_head"><?php echo T_("CURRENTLY_SEEDING"); ?></th>
     <th class="table_head"><?php echo T_("DATE_COMPLETED"); ?></th>
     <th class="table_head"><?php echo T_("RATIO"); ?></th>
  </tr>
  <?php 
       while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
           
           if (($row["privacy"] == "strong") && ($CURUSER["edit_users"] == "no"))
                continue;
           
           $ratio = ($row["downloaded"] > 0) ? $row["uploaded"] / $row["downloaded"] : 0;
           $peers = (get_row_count("peers", "WHERE torrent = '$id' AND userid = '$row[id]' AND seeder = 'yes'")) ? "<font color='green'>" . T_("YES") . "</font>" : "<font color='#ff0000'>" . T_("NO") . "</font>";
  ?>
       <tr>
           <td class="table_col1"><a href="<?php echo TTURL; ?>/accountdetails?id=<?php echo $row["id"]; ?>"><?php echo class_user($row['username']); ?></a></td>
           <td class="table_col2"><?php echo $peers; ?></td>
           <td class="table_col1"><?php echo utc_to_tz($row["date"]); ?></td>
           <td class="table_col2"><?php echo number_format($ratio, 2); ?></td>
       </tr>
  <?php } ?>
  </table>
  
  <center><a href="<?php echo TTURL; ?>/torrents/details?id=<?php echo $id; ?>"><?php echo T_("BACK_TO_DETAILS"); ?></a></center>
  
  <?php
  end_frame();
  stdfoot();
       }
	   
	       public function today(){

dbconn();
global $site_config, $CURUSER, $pdo;
//check permissions
if ($site_config["MEMBERSONLY"]){
	loggedinonly();

	if($CURUSER["view_torrents"]=="no")
		show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
}

stdhead(T_("TODAYS_TORRENTS"));

begin_frame(T_("TODAYS_TORRENTS"));

$date_time=get_date_time(gmtime()-(3600*24)); // the 24 is the hours you want listed

	$catresult = $this->torrentModel->getCatSort();

		while($cat = $catresult->fetch(PDO::FETCH_ASSOC))
		{
			$orderby = "ORDER BY torrents.id DESC"; //Order
			$where = "WHERE banned = 'no' AND category='$cat[id]' AND visible='yes'";
			$limit = "LIMIT 10"; //Limit

			$res = $this->torrentModel->getCatSortAll($where, $date_time, $orderby, $limit);
			$numtor = $res->rowCount();

			if ($numtor != 0) {
					echo "<b><a href='$site_config[SITEURL]/torrents/browse?cat=".$cat["id"]."'>$cat[name]</a></b>";
					# Got to think of a nice way to display this.
                    #list($pagertop, $pagerbottom, $limit) = pager(1000, $count, "torrents/browse"); //adjust pager to match LIMIT
					torrenttable($res);
					echo "<br />";
			}
		

		}
end_frame();
stdfoot();
	}
	
	
    public function reseed(){
  dbconn();
global $site_config, $CURUSER, $pdo;
  loggedinonly();
  
  if ($CURUSER["view_torrents"] == "no")
      show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1); 

  $id = (int) $_GET["id"];
  
  if (isset($_COOKIE["reseed$id"]))
      show_error_msg(T_("ERROR"), T_("RESEED_ALREADY_ASK"), 1);
      
  $res = $pdo->run("SELECT `owner`, `banned`, `external` FROM `torrents` WHERE `id` = $id");
  $row = $res->fetch(PDO::FETCH_ASSOC);
  
  if (!$row || $row["banned"] == "yes" || $row["external"] == "yes")
       show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);  
  
  $res2 = $pdo->run("SELECT users.id FROM completed LEFT JOIN users ON completed.userid = users.id WHERE users.enabled = 'yes' AND users.status = 'confirmed' AND completed.torrentid = $id");

  $message = sprintf(T_('RESEED_MESSAGE'), $CURUSER['username'], $site_config['SITEURL'], $id);
  
  while ( $row2 = $res2->fetch(PDO::FETCH_ASSOC) )
  {
      $pdo->run("INSERT INTO `messages` (`subject`, `sender`, `receiver`, `added`, `msg`) VALUES ('".T_("RESEED_MES_SUBJECT")."', '".$CURUSER['id']."', '".$row2['id']."', '".get_date_time()."', ".sqlesc($message).")");
  }
  
  if ($row["owner"] && $row["owner"] != $CURUSER["id"])
      $pdo->run("INSERT INTO `messages` (`subject`, `sender`, `receiver`, `added`, `msg`) VALUES ('Torrent Reseed Request', '".$CURUSER['id']."', '".$row['owner']."', '".get_date_time()."', ".sqlesc($message).")");
      
  setcookie("reseed$id", $id, time() + 86400, '/');
  
  show_error_msg("Complete", T_("RESEED_SENT"), 1);
}
	

  }