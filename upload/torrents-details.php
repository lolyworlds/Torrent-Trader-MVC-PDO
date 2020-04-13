<?php
require_once("backend/functions.php");
require_once("backend/BDecode.php");
dbconn();

$torrent_dir = $site_config["torrent_dir"];	
$nfo_dir = $site_config["nfo_dir"];	

//check permissions
if ($site_config["MEMBERSONLY"]){
	loggedinonly();
}
		if($CURUSER["view_torrents"]=="no")	
		show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);

//************ DO SOME "GET" STUFF BEFORE PAGE LAYOUT ***************

$id = (int) $_GET["id"];
$scrape = (int)$_GET["scrape"];
if (!is_valid_id($id))
	show_error_msg(T_("ERROR"), T_("THATS_NOT_A_VALID_ID"), 1);

//GET ALL MYSQL VALUES FOR THIS TORRENT
$res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
$row = $res->fetch(PDO::FETCH_ASSOC);

//DECIDE IF TORRENT EXISTS
if (!$row || ($row["banned"] == "yes" && $CURUSER["edit_torrents"] == "no"))
	show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);

//torrent is availiable so do some stuff

if ($_GET["hit"]) {
	DB::run("UPDATE torrents SET views = views + 1 WHERE id = $id");
    header("Location: torrents-details.php?id=$id"); 
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

	$res = DB::run("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rating, '".get_date_time()."')");

	if (!$res) {
		if ($res->errorCode() == 1062)
			show_error_msg(T_("RATING_ERROR"), T_("YOU_ALREADY_RATED_TORRENT"), 1);
		else
			show_error_msg(T_("RATING_ERROR"), T_("A_UNKNOWN_ERROR_CONTACT_STAFF"), 1);
	}

	DB::run("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
	show_error_msg(T_("RATING_SUCCESS"), T_("RATING_THANK")."<br /><br /><a href='torrents-details.php/?id=$id'>" .T_("BACK_TO_TORRENT"). "</a>");
}

//take comment add
if ($_GET["takecomment"] == 'yes'){
	loggedinonly();
	$body = $_POST['body'];
	
	if (!$body)
		show_error_msg(T_("RATING_ERROR"), T_("YOU_DID_NOT_ENTER_ANYTHING"), 1);

	DB::run("UPDATE torrents SET comments = comments + 1 WHERE id = $id");

	$comins = ("INSERT INTO comments (user, torrent, added, text) VALUES (".$CURUSER["id"].", ".$id.", '" .get_date_time(). "', " . sqlesc($body).")");

	if ($comins)
			show_error_msg(T_("COMPLETED"), T_("COMMENT_ADDED"), 0);
		else
			show_error_msg(T_("ERROR"), T_("UNABLE_TO_ADD_COMMENT"), 0);
}//end insert comment

//START OF PAGE LAYOUT HERE

?>
<style> 
.hide{
    display:none;
}

.block{
    width: 100px;
    height:50px;
    background-color:#cccccc;
    text-align:center;
    line-height:50px;
}	
</style> 




<?php		$char1 = 55; //cut length
$shortname = CutName(htmlspecialchars($row["name"]), $char1);

begin_frame(T_("TORRENT_DETAILS_FOR"). " \"" . $shortname . "\"");
?>
<script type="text/javascript">
function show1(elementId) { 
 document.getElementById("id1").style.display="none";
  document.getElementById(elementId).style.display="block";
}
function show(elementId) { 
document.getElementById("id1").style.display="none";
 document.getElementById("id2").style.display="none";
 document.getElementById("id3").style.display="none";
 document.getElementById("id4").style.display="none";
 document.getElementById("id5").style.display="none";
 document.getElementById("id6").style.display="none";
 document.getElementById("id7").style.display="none";
 document.getElementById(elementId).style.display="block";
}

</script>


<button type="button" onclick="show('id1');">DETAILS</button>
<button type="button" onclick="show('id2');">RATING</button>
<button type="button" onclick="show('id3');">IMAGES</button>
<button type="button" onclick="show('id4');">FILES</button>
<button type="button" onclick="show('id5');">PEERLIST</button>
<button type="button" onclick="show('id6');">NFO</button>
<button type="button" onclick="show('id7');">COMMENTS</button>
<?php
echo "<table border=\"0\" width=\"100%\" cellspacing=\"5\" cellpadding=\"5\" class=\"account\"><tr><td><h3>" . $shortname . "</h3></td><td><p align=\"right\"><a href='report.php?torrent=$id' class='btn btn-danger' ><i class=\"fas fa-exclamation-triangle\"></i>&nbsp;&nbsp;<b>" .T_("REPORT_TORRENT"). "</b></a> &nbsp;";	
if ($owned)	
	echo " <a href='torrents-edit.php?id=$row[id]&amp;returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "' class='btn btn-success' ><i class=\"far fa-edit\"></i>&nbsp;&nbsp;<b>".T_("EDIT_TORRENT")."</b></a>&nbsp; ";	
print ("<a href=\"download.php?id=$id&amp;name=" . rawurlencode($row["filename"]) . "\"><img src=\"".$site_config["SITEURL"]."/images/download_torrent.png\" border=\"0\" alt='' /></a><br />");
echo "</td>";	
echo "</tr></table>";



echo "<div id=id1  style=display:block>"; // start id1
//download box











echo "<div class=row>"; // start row

echo "<div class=col-sm-2>";
print ("<div><img src='".$site_config["SITEURL"]."/uploads/images/$row[image1]' width='200' height='265' class='poster' alt='' /><br>");	
print ("</div>");
echo "  </div>"; 


echo "<div class=col-sm>"; // first col
print ("<ul class='list'>");		
	print ("<b>" .T_("HEALTH"). ": </b></br><img src='".$site_config["SITEURL"]."/images/health/health_".health($row["leechers"], $row["seeders"]).".gif' alt='' /><br />");
	print ("<b>" .T_("SEEDS"). ": </b></br><font color='green'>" . number_format($row["seeders"]) . "</font><br />");
	print ("<b>".T_("LEECHERS").": </b></br><font color='#ff0000'>" .  number_format($row["leechers"]) . "</font><br />");

	if ($row["external"]!='yes'){
		print ("<b>".T_("SPEED").": </b></br>" . $totalspeed . "<br />");
	}

	print ("<b>".T_("COMPLETED").":</b></br> " . number_format($row["times_completed"]) . "&nbsp;"); 

	if ($row["external"] != "yes" && $row["times_completed"] > 0) {
		echo("[<a href='torrents-completed.php?id=$id'>" .T_("WHOS_COMPLETED"). "</a>] ");
		if ($row["seeders"] <= 1) {
			echo("[<a href='torrents-reseed.php?id=$id'>" .T_("REQUEST_A_RE_SEED"). "</a>]");
		}
	}
	echo "<br />";

	if ($row["external"]!='yes' && $row["freeleech"]=='1'){
		print ("<b>".T_("FREE_LEECH").": </b></br><font color='#ff0000'>".T_("FREE_LEECH_MSG")."</font><br />");
	}

print ("<b>".T_("LAST_CHECKED").": </b></br>" . date("d-m-Y H:i:s", utc_to_tz_time($row["last_action"])) . "<br />");		
echo "</ul>";
echo "  </div>"; 

echo "<div class=col-sm>";
print("<ul class='list'>");	
print("<b>" .T_("CATEGORY"). ":</b></br>" . $row["cat_parent"] . " > " . $row["cat_name"] . "</br>");

if (empty($row["lang_name"])) $row["lang_name"] = "Unknown/NA";
print("<b>" .T_("LANG"). ":</b></br>" . $row["lang_name"] . "</br>");

if (isset($row["lang_image"]) && $row["lang_image"] != "")
			print("&nbsp;<img border=\"0\" src=\"" . $site_config['SITEURL'] . "/images/languages/" . $row["lang_image"] . "\" alt=\"" . $row["lang_name"] . "\"</br>");



print("<b>" .T_("TOTAL_SIZE"). ":</b></br>" . mksize($row["size"]) . "</br> ");
print("<b>" .T_("INFO_HASH"). ":</b></br><font color='green'>" . $row["info_hash"] . "</font></br>");
print("");
if ($row["anon"] == "yes" && !$owned)
	print("<b>" .T_("ADDED_BY"). ":</b></br>Anonymous");
elseif ($row["username"])
	print("<b>" .T_("ADDED_BY"). ":</b></br><a href='account-details.php?id=" . $row["owner"] . "'>" . $row["username"] . "</a></br>");
else
	print("<b>" .T_("ADDED_BY"). ":</b></br>Unknown");

print("<b>" .T_("DATE_ADDED"). ":</b></br>" . date("d-m-Y H:i:s", utc_to_tz_time($row["added"])) . "</br>");
print("<b>" .T_("VIEWS"). ":</b></br>" . number_format($row["views"]) . "</br>");
print("<b>".T_("HITS").":</b></br>" . number_format($row["hits"]) . "");		
echo "</ul>";
echo "  </div>"; 

echo "<div class=col-sm>";
echo " we can add whatever";
echo "  </div>"; 


echo "</div>";// end row




echo "</div>"; // end id1










echo "<div id=id2  style=display:none>";
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
			$srating .= "(<a href=\"account-login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;nowarn=1\">Log in</a> to rate it)";
		else {
			$ratings = array(
					5 => T_("COOL"),
					4 => T_("PRETTY_GOOD"),
					3 => T_("DECENT"),
					2 => T_("PRETTY_BAD"),
					1 => T_("SUCKS")
			);
			//if (!$owned || $moderator) {
				$xres = DB::run("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
				$xrow = $xres->fetch(PDO::FETCH_ASSOC);
				if ($xrow)
					$srating .= "<br /><i>(".T_("YOU_RATED")." \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")</i>";
				else {
					$srating .= "<form style=\"display:inline;\" method=\"post\" action=\"torrents-details.php?id=$id&amp;takerating=yes\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
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
echo "</div>";

echo "<div id=id3  style=display:none>";
if ($row["image1"] != "" OR $row["image2"] != "") {
  if ($row["image1"] != "")
    $img1 = "<img src='".$site_config["SITEURL"]."/uploads/images/$row[image1]' width='150' border='0' alt='' />";
  if ($row["image2"] != "")
    $img2 = "<img src='".$site_config["SITEURL"]."/uploads/images/$row[image2]' width='150' border='0' alt='' />";
  print("<center>". $img1 . "&nbsp;&nbsp;" . $img2."</center><br />");
}
echo "</div>";

echo "<div id=id4  style=display:none>";
if ($row["external"]=='yes'){
	print ("<br /><b>Tracker:</b><br /> ".htmlspecialchars($row['announce'])."<br />");
}

$tres = DB::run("SELECT * FROM `announce` WHERE `torrent` = $id");
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

echo "<br /><br /><b>".T_("FILE_LIST").":</b>&nbsp;<img src='images/plus.gif' id='pic1' onclick='klappe_torrent(1)' alt='' /><div id='k1' style='display: none;'><table align='center' cellpadding='0' cellspacing='0' class='table_table' border='1' width='100%'><tr><th class='table_head' align='left'>&nbsp;".T_("FILE")."</th><th width='50' class='table_head'>&nbsp;".T_("SIZE")."</th></tr>";
$fres = DB::run("SELECT * FROM `files` WHERE `torrent` = $id ORDER BY `path` ASC");
if ($fres->rowCount()) {
    while ($frow = $fres->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td class='table_col1'>".htmlspecialchars($frow['path'])."</td><td class='table_col2'>".mksize($frow['filesize'])."</td></tr>";
    }
}else{
    echo "<tr><td class='table_col1'>".htmlspecialchars($row["name"])."</td><td class='table_col2'>".mksize($row["size"])."</td></tr>";
}
echo "</table></div>";
echo "</div>";

echo "<div id=id5  style=display:none>";
if ($row["external"]!='yes'){
	echo "<br /><br /><b>".T_("PEERS_LIST").":</b><br />";
	$query = DB::run("SELECT * FROM peers WHERE torrent = $id ORDER BY seeder DESC");

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
					$res = DB::run("SELECT id, username, privacy FROM users WHERE id=".$row1["userid"]."");
					$arr = $res->fetch(PDO::FETCH_ASSOC);
                    
                    $arr["username"] = "<a href='account-details.php?id=$arr[id]'>$arr[username]</a>";
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
echo "</div>";	

echo "<div id=id6  style=display:none>";
//DISPLAY NFO BLOCK
function my_nfo_translate($nfo){
        $trans = array(
        "\x80" => "&#199;", "\x81" => "&#252;", "\x82" => "&#233;", "\x83" => "&#226;", "\x84" => "&#228;", "\x85" => "&#224;", "\x86" => "&#229;", "\x87" => "&#231;", "\x88" => "&#234;", "\x89" => "&#235;", "\x8a" => "&#232;", "\x8b" => "&#239;", "\x8c" => "&#238;", "\x8d" => "&#236;", "\x8e" => "&#196;", "\x8f" => "&#197;", "\x90" => "&#201;",
        "\x91" => "&#230;", "\x92" => "&#198;", "\x93" => "&#244;", "\x94" => "&#246;", "\x95" => "&#242;", "\x96" => "&#251;", "\x97" => "&#249;", "\x98" => "&#255;", "\x99" => "&#214;", "\x9a" => "&#220;", "\x9b" => "&#162;", "\x9c" => "&#163;", "\x9d" => "&#165;", "\x9e" => "&#8359;", "\x9f" => "&#402;", "\xa0" => "&#225;", "\xa1" => "&#237;",
        "\xa2" => "&#243;", "\xa3" => "&#250;", "\xa4" => "&#241;", "\xa5" => "&#209;", "\xa6" => "&#170;", "\xa7" => "&#186;", "\xa8" => "&#191;", "\xa9" => "&#8976;", "\xaa" => "&#172;", "\xab" => "&#189;", "\xac" => "&#188;", "\xad" => "&#161;", "\xae" => "&#171;", "\xaf" => "&#187;", "\xb0" => "&#9617;", "\xb1" => "&#9618;", "\xb2" => "&#9619;",
        "\xb3" => "&#9474;", "\xb4" => "&#9508;", "\xb5" => "&#9569;", "\xb6" => "&#9570;", "\xb7" => "&#9558;", "\xb8" => "&#9557;", "\xb9" => "&#9571;", "\xba" => "&#9553;", "\xbb" => "&#9559;", "\xbc" => "&#9565;", "\xbd" => "&#9564;", "\xbe" => "&#9563;", "\xbf" => "&#9488;", "\xc0" => "&#9492;", "\xc1" => "&#9524;", "\xc2" => "&#9516;", "\xc3" => "&#9500;",
        "\xc4" => "&#9472;", "\xc5" => "&#9532;", "\xc6" => "&#9566;", "\xc7" => "&#9567;", "\xc8" => "&#9562;", "\xc9" => "&#9556;", "\xca" => "&#9577;", "\xcb" => "&#9574;", "\xcc" => "&#9568;", "\xcd" => "&#9552;", "\xce" => "&#9580;", "\xcf" => "&#9575;", "\xd0" => "&#9576;", "\xd1" => "&#9572;", "\xd2" => "&#9573;", "\xd3" => "&#9561;", "\xd4" => "&#9560;",
        "\xd5" => "&#9554;", "\xd6" => "&#9555;", "\xd7" => "&#9579;", "\xd8" => "&#9578;", "\xd9" => "&#9496;", "\xda" => "&#9484;", "\xdb" => "&#9608;", "\xdc" => "&#9604;", "\xdd" => "&#9612;", "\xde" => "&#9616;", "\xdf" => "&#9600;", "\xe0" => "&#945;", "\xe1" => "&#223;", "\xe2" => "&#915;", "\xe3" => "&#960;", "\xe4" => "&#931;", "\xe5" => "&#963;",
        "\xe6" => "&#181;", "\xe7" => "&#964;", "\xe8" => "&#934;", "\xe9" => "&#920;", "\xea" => "&#937;", "\xeb" => "&#948;", "\xec" => "&#8734;", "\xed" => "&#966;", "\xee" => "&#949;", "\xef" => "&#8745;", "\xf0" => "&#8801;", "\xf1" => "&#177;", "\xf2" => "&#8805;", "\xf3" => "&#8804;", "\xf4" => "&#8992;", "\xf5" => "&#8993;", "\xf6" => "&#247;",
        "\xf7" => "&#8776;", "\xf8" => "&#176;", "\xf9" => "&#8729;", "\xfa" => "&#183;", "\xfb" => "&#8730;", "\xfc" => "&#8319;", "\xfd" => "&#178;", "\xfe" => "&#9632;", "\xff" => "&#160;",
        );
        $trans2 = array("\xe4" => "&auml;",        "\xF6" => "&ouml;",        "\xFC" => "&uuml;",        "\xC4" => "&Auml;",        "\xD6" => "&Ouml;",        "\xDC" => "&Uuml;",        "\xDF" => "&szlig;");
        $all_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $last_was_ascii = False;
        $tmp = "";
        $nfo = $nfo . "\00";
        for ($i = 0; $i < (strlen($nfo) - 1); $i++)
        {
                $char = $nfo[$i];
                if (isset($trans2[$char]) and ($last_was_ascii or strpos($all_chars, ($nfo[$i + 1]))))
                {
                        $tmp = $tmp . $trans2[$char];
                        $last_was_ascii = True;
                }
                else
                {
                        if (isset($trans[$char]))
                        {
                                $tmp = $tmp . $trans[$char];
                        }
                        else
                        {
                            $tmp = $tmp . $char;
                        }
                        $last_was_ascii = strpos($all_chars, $char);
                }
        }
        return $tmp;
}
//-----------------------------------------------

//DISPLAY NFO BLOCK
if($row["nfo"]== "yes"){
	$nfofilelocation = "$nfo_dir/$row[id].nfo";
	$filegetcontents = file_get_contents($nfofilelocation);
	$nfo = htmlspecialchars($filegetcontents);
		if ($nfo) {	
			$nfo = my_nfo_translate($nfo);
			echo "<br /><br /><b>NFO:</b><br />";
			print("<textarea class='nfo' style='width:98%;height:100%;' rows='20' cols='20' readonly='readonly'>".stripslashes($nfo)."</textarea>");
        }else{
            print(T_("ERROR")." reading .nfo file!");
        }
}
echo "</div>";

echo "<div id=id7  style=display:none>";
    $commcount = DB::run("SELECT COUNT(*) FROM comments WHERE torrent = $id")->fetchColumn();

	if ($commcount) {
		list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "torrents-details.php?id=$id&amp;");
		$commquery = "SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = $id ORDER BY comments.id $limit";
		$commres = DB::run($commquery);
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

	require_once("backend/bbcode.php");

	if ($CURUSER) {
		echo "<center>";
		echo "<form name=\"comment\" method=\"post\" action=\"torrents-details.php?id=$row[id]&amp;takecomment=yes\">";
		echo textbbcode("comment","body")."<br />";
		echo "<input type=\"submit\"  value=\"".T_("ADDCOMMENT")."\" />";
		echo "</form></center>";
	}
echo "</div>";
	

end_frame();
stdfoot();
?>