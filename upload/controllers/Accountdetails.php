<?php
  class Accountdetails extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
dbconn();
global $site_config, $CURUSER;
loggedinonly();

stdhead("User CP");

$id = (int)$_GET["id"];

if (!is_valid_id($id))
  show_error_msg(T_("NO_SHOW_DETAILS"), "Bad ID.",1);

$user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
if(!$user)
    show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID")." $id.",1);

//add invites check here
if ($CURUSER["view_users"] == "no" && $CURUSER["id"] != $id)
     show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);
     
if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $CURUSER["edit_users"] == "no")
	show_error_msg(T_("ERROR"), T_("NO_ACCESS_ACCOUNT_DISABLED"), 1);

//get all vars first

//$country
$res = DB::run("SELECT name FROM countries WHERE id=? LIMIT 1", [$user['country']]);
if ($res->rowCount() == 1){
	$arr =$res->fetch();
	$country = "$arr[name]";
}
if (!$country) $country = "<b>Unknown</b>";

//$ratio
if ($user["downloaded"] > 0) {
    $ratio = $user["uploaded"] / $user["downloaded"];
}else{
	$ratio = "---";
}

$numtorrents = get_row_count("torrents", "WHERE owner = $id");
$numcomments = get_row_count("comments", "WHERE user = $id");
$numforumposts = get_row_count("forum_posts", "WHERE userid = $id");

$avatar = htmlspecialchars($user["avatar"]);
	if (!$avatar) {
		$avatar = $site_config["SITEURL"]."/images/default_avatar.png";
	}


//Layout		
begin_frame(sprintf(T_("USER_DETAILS_FOR"), class_user($user["username"])));
?>
<script type="text/javascript">

function show(elementId) { 
document.getElementById("id1").style.display="none";
 document.getElementById("id2").style.display="none";
 document.getElementById("id3").style.display="none";
 document.getElementById("id4").style.display="none";
 document.getElementById("id5").style.display="none";
 document.getElementById(elementId).style.display="block";
}

</script>


<button type="button" class="btn btn-sm btn-primary" onclick="show('id1');">DETAILS</button>
<button type="button" class="btn btn-sm btn-primary" onclick="show('id2');">SHARING</button>
<button type="button" class="btn btn-sm btn-primary" onclick="show('id3');">UPLOADED</button>
<?php if($CURUSER["edit_users"]=="yes"){ ?>
<button type="button" class="btn btn-sm btn-primary" onclick="show('id4');">EDIT</button>
<button type="button" class="btn btn-sm btn-primary" onclick="show('id5');">WARNINGS</button>
<?php } ?>
<?php

	echo "<div id=id1  style=display:block>"; // start id1
    if ($user["privacy"] != "strong" || ($CURUSER["control_panel"] == "yes") || ($CURUSER["id"] == $user["id"])) {
	?>
	<table align="center" border="0" cellpadding="6" cellspacing="1" width="100%">
	<tr>
		<td width="50%"><b><?php echo T_("PROFILE"); ?></b></td>
		<td width="50%"><b><?php echo T_("ADDITIONAL_INFO"); ?></b></td>
	</tr>

	<tr valign="top">
		<td align="left">
		<?php echo T_("USERNAME"); ?>: <?php echo class_user($user["username"])?><br />
		<?php echo T_("USERCLASS"); ?>: <?php echo get_user_class_name($user["class"])?><br />
		<?php echo T_("TITLE"); ?>: <i><?php echo format_comment($user["title"])?></i><br />
		<?php echo T_("JOINED"); ?>: <?php echo htmlspecialchars(utc_to_tz($user["added"]))?><br />
		<?php echo T_("LAST_VISIT"); ?>: <?php echo htmlspecialchars(utc_to_tz($user["last_access"]))?><br />
		<?php echo T_("LAST_SEEN"); ?>: <?php echo htmlspecialchars($user["page"]);?><br />
		</td>

		<td align="left">
		<?php echo T_("AGE"); ?>: <?php echo htmlspecialchars($user["age"])?><br />
		<?php echo T_("CLIENT"); ?>: <?php echo htmlspecialchars($user["client"])?><br />
		<?php echo T_("COUNTRY"); ?>: <?php echo $country?><br />
		<?php echo T_("DONATED"); ?>  <?php echo $site_config['currency_symbol']; ?><?php echo number_format($user["donated"], 2); ?><br /> 
		<?php echo T_("WARNINGS"); ?>: <?php echo htmlspecialchars($user["warned"])?><br />
		<?php if ($CURUSER["edit_users"] == "yes"){ echo T_("ACCOUNT_PRIVACY_LVL").": <b>".T_($user["privacy"])."</b><br />"; }?>
		</td>
	</tr>

	<tr>
		<td width="50%"><b><?php echo T_("STATISTICS"); ?></b></td>
		<td width="50%"><b><?php echo T_("OTHER"); ?></b></td>
	</tr>

	<tr valign="top">
		<td align="left">
		<?php echo T_("UPLOADED"); ?>: <?php echo mksize($user["uploaded"]); ?><br />
		<?php echo T_("DOWNLOADED"); ?>: <?php echo mksize($user["downloaded"]); ?><br />
		<?php echo T_("RATIO"); ?>: <?php echo $ratio; ?><br />
		<?php echo T_("AVG_DAILY_DL"); ?>: <?php echo mksize($user["downloaded"] / (DateDiff($user["added"], time()) / 86400)); ?><br />
		<?php echo T_("AVG_DAILY_UL"); ?>: <?php echo mksize($user["uploaded"] / (DateDiff($user["added"], time()) / 86400)); ?><br />
		<?php echo T_("TORRENTS_POSTED"); ?>: <?php echo number_format($numtorrents); ?><br />
		<?php echo T_("COMMENTS_POSTED"); ?>: <?php echo number_format($numcomments); ?><br />
        Forum Posts: <?php echo number_format($numforumposts); ?><br />   
		</td>

		<td align="left">
		<img src="<?php echo $avatar; ?>" alt="" title="<?php echo class_user($user["username"]); ?>" height="80" width="80" /><br />
		<a href="<?php echo $site_config['SITEURL'] ?>/mailbox?compose&amp;id=<?php echo $user["id"]?>"><button type='button' class='btn btn-sm btn-success'><?php echo T_("SEND_PM") ?></button></a><br />
		<!-- <a href=#>View Forum Posts</a><br />
		<a href=#>View Comments</a><br /> -->
		<a href="<?php echo $site_config['SITEURL'] ?>/report?user=<?php echo $user["id"]?>"><button type='button' class='btn btn-sm btn-danger'><?php echo T_("REPORT_MEMBER") ?></button></a><br />
	<?php if ($CURUSER["edit_users"]=="yes") { ?>
  <div style="margin-bottom:3px"><a href="<?php echo $site_config['SITEURL']; ?>/snatched?uid=<?php echo $user["id"]?>"><button type='button' class='btn btn-sm btn-warning'><?php echo T_("SNATCHLIST") ?></button></a></div>
<?php } ?>
		</td>
	</tr>
	<?php if ($CURUSER["edit_users"] == "yes") { ?>
	<tr>
		<td width="50%"><b><?php echo T_("STAFF_ONLY_INFO"); ?></b></td>
	</tr>

	<tr valign="top">
		<td align="left">
			<?php
				if ($user["invited_by"]) {
					$invited = $user['invited_by'];
                    $row = DB::run("SELECT username FROM users WHERE id=?", [$invited])->fetch();
					echo "<b>".T_("INVITED_BY").":</b> <a href=\"$site_config[SITEURL]/accountdetails?id=$user[invited_by]\">".class_user($row['username'])."</a><br />";
				}
				echo "<b>".T_("INVITES").":</b> ".number_format($user["invites"])."<br />";
				$invitees = array_reverse(explode(" ", $user["invitees"]));
				$rows = array();
				foreach ($invitees as $invitee) {
					$res = DB::run("SELECT id, username FROM users WHERE id=? and status=?", [$invitee, 'confirmed']);
					if ($row = $res->fetch()) {
						$rows[] = "<a href=\"$site_config[SITEURL]/accountdetails?id=$row[id]\">".class_user($row['username'])."</a>";
					}
				}
				if ($rows)
					echo "<b>".T_("INVITEES").":</b> ".implode(", ", $rows)."<br />";
			?>
		</td>
	</tr>
	<?php
	}
	//team
	$res = DB::run("SELECT name,image FROM teams WHERE id=? LIMIT 1", [$user['team']]);
	if ($res->rowCount() == 1) {
		$arr = $res->fetch();
		echo "<tr><td colspan='2' align='left'><b>Team Member Of:</b><br />";
		echo"<img src='".htmlspecialchars($arr["image"])."' alt='' /><br />".sqlesc($arr["name"])."<br /><br /><a href='$site_config[SITEURL]/teams'>[View ".T_("TEAMS")."]</a></td></tr>"; 
	}
	?>

	</table>

	<?php
}else{
	echo sprintf(T_("REPORT_MEMBER_MSG"), $user["id"]);
}
	echo "</div>"; // start id1
	
	echo "<div id=id2  style=display:none>"; // start id1 // start id1
    if ($user["privacy"] != "strong" || ($CURUSER["control_panel"] == "yes") || ($CURUSER["id"] == $user["id"])) {

	$res = DB::run("SELECT torrent, uploaded, downloaded FROM peers WHERE userid =? AND seeder =?", [$id, 'yes']);
	if ($res->rowCount() > 0)
	  $seeding = peerstable($res);

	$res = DB::run("SELECT torrent, uploaded, downloaded FROM peers WHERE userid =? AND seeder =?", [$id, 'no']);
	if ($res->rowCount() > 0)

	  $leeching = peerstable($res);

	if ($seeding)
		print("<b>" .T_("CURRENTLY_SEEDING"). ":</b><br />$seeding<br /><br />");

	if ($leeching)
		print("<b>" .T_("CURRENTLY_LEECHING"). ":</b><br />$leeching<br /><br />");

	if (!$leeching && !$seeding)
		print("<b>".T_("NO_ACTIVE_TRANSFERS")."</b><br />");

}
	echo "</div>"; // start id1
    
	echo "<div id=id3  style=display:none>"; // start id1
	//page numbers
	$page = (int) $_GET["page"];
	$perpage = 25;
	$where = "";
	if ($CURUSER['control_panel'] != "yes")
		$where = "AND anon='no'";
	$count = DB::run("SELECT COUNT(*) FROM torrents WHERE owner='$id' $where")->fetchColumn();

	unset($where);
	$orderby = "ORDER BY id DESC";
	//get sql info
	if ($count) {
		list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/accountdetails?id=$id&amp;");
		$res = DB::run("SELECT torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, torrents.anon, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.announce FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE owner = $id $orderby $limit");
	}else{
		unset($res);
	}

	if ($count) {
		print($pagertop);
		torrenttable($res);
		print($pagerbottom);
	}else {
		print("<b>".T_("UPLOADED_TORRENTS_ERROR")."</b><br />");
	}

	echo "</div>"; // start id1

	echo "<div id=id4  style=display:none>"; // start id1
    if($CURUSER["edit_users"]=="yes"){

	$avatar = htmlspecialchars($user["avatar"]);
	$signature = htmlspecialchars($user["signature"]);
	$uploaded = $user["uploaded"];
	$downloaded = $user["downloaded"];
	$enabled = $user["enabled"] == 'yes';
	$warned = $user["warned"] == 'yes';
	$forumbanned = $user["forumbanned"] == 'yes';
	$modcomment = htmlspecialchars($user["modcomment"]);

	print("<form method='post' action='adminmodtasks'>\n");
	print("<input type='hidden' name='action' value='edituser' />\n");
	print("<input type='hidden' name='userid' value='$id' />\n");
	print("<table border='0' cellspacing='0' cellpadding='3'>\n");
	print("<tr><td>".T_("TITLE").": </td><td align='left'><input type='text' size='67' name='title' value=\"$user[title]\" /></td></tr>\n");
	print("<tr><td>".T_("EMAIL")."</td><td align='left'><input type='text' size='67' name='email' value=\"$user[email]\" /></td></tr>\n");
	print("<tr><td>".T_("SIGNATURE").": </td><td align='left'><textarea cols='50' rows='10' name='signature'>".htmlspecialchars($user["signature"])."</textarea></td></tr>\n");
	print("<tr><td>".T_("UPLOADED").": </td><td align='left'><input type='text' size='30' name='uploaded' value=\"".mksize($user["uploaded"], 9)."\" /></td></tr>\n");
	print("<tr><td>".T_("DOWNLOADED").": </td><td align='left'><input type='text' size='30' name='downloaded' value=\"".mksize($user["downloaded"], 9)."\" /></td></tr>\n");
	print("<tr><td>".T_("AVATAR_URL")."</td><td align='left'><input type='text' size='67' name='avatar' value=\"$avatar\" /></td></tr>\n");
	print("<tr><td>".T_("IP_ADDRESS").": </td><td align='left'><input type='text' size='20' name='ip' value=\"$user[ip]\" /></td></tr>\n");
	print("<tr><td>".T_("INVITES").": </td><td align='left'><input type='text' size='4' name='invites' value='".$user["invites"]."' /></td></tr>\n");

	if ($CURUSER["class"] > $user["class"]){
		print("<tr><td>".T_("CLASS").": </td><td align='left'><select name='class'>\n");
		$maxclass = $CURUSER["class"];
		for ($i = 1; $i < $maxclass; ++$i)
		print("<option value='$i' " . ($user["class"] == $i ? " selected='selected'" : "") . ">$prefix" . get_user_class_name($i) . "\n");
		print("</select></td></tr>\n");
	}


	print("<tr><td>".T_("DONATED_US").": </td><td align='left'><input type='text' size='4' name='donated' value='$user[donated]' /></td></tr>\n");
	print("<tr><td>".T_("PASSWORD").": </td><td align='left'><input type='password' size='67' name='password' value=\"$user[password]\" /></td></tr>\n");
	print("<tr><td>".T_("CHANGE_PASS").": </td><td align='left'><input type='checkbox' name='chgpasswd' value='yes'/></td></tr>");
	print("<tr><td>".T_("MOD_COMMENT").": </td><td align='left'><textarea cols='50' rows='10' name='modcomment'>$modcomment</textarea></td></tr>\n");
	print("<tr><td>".T_("ACCOUNT_STATUS").": </td><td align='left'><input name='enabled' value='yes' type='radio' " . ($enabled ? " checked='checked'" : "") . " />Enabled <input name='enabled' value='no' type='radio' " . (!$enabled ? " checked='checked' " : "") . " />Disabled</td></tr>\n");
	print("<tr><td>".T_("WARNED").": </td><td align='left'><input name='warned' value='yes' type='radio' " . ($warned ? " checked='checked'" : "") . " />Yes <input name='warned' value='no' type='radio' " . (!$warned ? " checked='checked'" : "") . " />No</td></tr>\n");
	print("<tr><td>".T_("FORUM_BANNED").": </td><td align='left'><input name='forumbanned' value='yes' type='radio' " . ($forumbanned ? " checked='checked'" : "") . " />Yes <input name='forumbanned' value='no' type='radio' " . (!$forumbanned ? " checked='checked'" : "") . " />No</td></tr>\n");
	print("<tr><td>".T_("PASSKEY").": </td><td align='left'>$user[passkey]<br /><input name='resetpasskey' value='yes' type='checkbox' />".T_("RESET_PASSKEY")." (".T_("RESET_PASSKEY_MSG").")</td></tr>\n");
	print("<tr><td colspan='2' align='center'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");
	print("</table>\n");
	print("</form>\n");

}
	echo "</div>"; // start id1
    
	echo "<div id=id5  style=display:none>"; // start id1
    if($CURUSER["edit_users"]=="yes"){


    print '<a name="warnings"></a>';
    
	$res = DB::run("SELECT * FROM warnings WHERE userid=? ORDER BY id DESC", [$id]);

	if ($res->rowCount() > 0){
		?>
		<b>Warnings:</b><br />
		<table border="1" cellpadding="3" cellspacing="0" width="80%" align="center" class="table_table">
		<tr>
            <th class="table_head">Added</th>
		    <th class="table_head"><?php echo T_("EXPIRE"); ?></th>
		    <th class="table_head"><?php echo T_("REASON"); ?></th>
		    <th class="table_head"><?php echo T_("WARNED_BY"); ?></th>
		    <th class="table_head"><?php echo T_("TYPE"); ?></th>      
		</tr>
		<?php

		while ($arr = $res->fetch(PDO::FETCH_ASSOC)){
			if ($arr["warnedby"] == 0) {
				$wusername = T_("SYSTEM");
			} else {
				$res2 = DB::run("SELECT id,username FROM users WHERE id =?", [$arr['warnedby']]);
				$arr2 = $res2->fetch();
				$wusername = class_user($arr2["username"]);
			}
			$arr['added'] = utc_to_tz($arr['added']);
			$arr['expiry'] = utc_to_tz($arr['expiry']);

			$addeddate = substr($arr['added'], 0, strpos($arr['added'], " "));
			$expirydate = substr($arr['expiry'], 0, strpos($arr['expiry'], " "));
			print("<tr><td class='table_col1' align='center'>$addeddate</td><td class='table_col2' align='center'>$expirydate</td><td class='table_col1'>".format_comment($arr['reason'])."</td><td class='table_col2' align='center'><a href='$site_config[SITEURL]/accountdetails?id=".$arr2['id']."'>".$wusername."</a></td><td class='table_col1' align='center'>".$arr['type']."</td></tr>\n");
		 }

		echo "</table>\n";
	}else{
		echo T_("NO_WARNINGS");
	}


	print("<form method='post' action='$site_config[SITEURL]/adminmodtasks'>\n");
	print("<input type='hidden' name='action' value='addwarning' />\n");
	print("<input type='hidden' name='userid' value='$id' />\n");
	echo "<br /><br /><center><table border='0'><tr><td align='right'><b>".T_("REASON").":</b> </td><td align='left'><textarea cols='40' rows='5' name='reason'></textarea></td></tr>";
	echo "<tr><td align='right'><b>".T_("EXPIRE").":</b> </td><td align='left'><input type='text' size='4' name='expiry' />(days)</td></tr>";
	echo "<tr><td align='right'><b>".T_("TYPE").":</b> </td><td align='left'><input type='text' size='10' name='type' /></td></tr>";
	echo "<tr><td colspan='2' align='center'><button type='submit' class='btn btn-sm btn-success'><b>" .T_("ADD_WARNING"). "</b></button></td></tr></table></center></form>";

	if($CURUSER["delete_users"] == "yes"){
		print("<hr /><center><form method='post' action='$site_config[SITEURL]/adminmodtasks'>\n");
		print("<input type='hidden' name='action' value='deleteaccount' />\n");
		print("<input type='hidden' name='userid' value='$id' />\n");
		print("<input type='hidden' name='username' value='".$user["username"]."' />\n");
		echo "<b>".T_("REASON").":</b><input type='text' size='30' name='delreason' />";
		echo "<button type='submit' class='btn btn-sm btn-danger'><b>" .T_("DELETE_ACCOUNT"). "</b></button></form></center>";
	}

}
	echo "</div>"; // start id1

end_frame();
stdfoot();
	}
}