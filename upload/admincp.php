<?php
require_once("backend/functions.php");
require_once("backend/bbcode.php");
dbconn();
loggedinonly();

if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
     show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}

 $action = $_REQUEST["action"];
 $do = $_REQUEST["do"];
 
function navmenu(){
global $site_config;

//Get Last Cleanup
$row = DB::run("SELECT last_time FROM tasks WHERE task =?", ['cleanup'])->fetchColumn();
if (!$row){
		$lastclean="never done...";
}else{
/*	$row[0]=gmtime()-$row[0]; $days=intval($row[0] / 86400);$row[0]-=$days*86400;
	$hours=intval($row[0] / 3600); $row[0]-=$hours*3600; $mins=intval($row[0] / 60);
	$secs=$row[0]-($mins*60);
	$lastclean = "$days days, $hours hrs, $mins minutes, $secs seconds ago.";*/
	$lastclean = get_elapsed_time($row);
}

	begin_frame(T_("MENU"));
	echo "<center>Last cleanup performed: ".$lastclean." [<a href='admincp.php?action=forceclean'>".T_("FORCE_CLEAN")."</a>]</center><br /><br />";                                      
//	if ($site_config["ttversion"] != "2-svn") {  
//        $file = @file_get_contents('https://www.torrenttrader.org/tt2version.php');
//        if ($site_config['ttversion'] >= $file){
//			echo "<br /><center><b>".T_("YOU_HAVE_LATEST_VER_INSTALLED")." v$site_config[ttversion]</b></center>";
//		}else{
//			echo "<br /><center><b><font class='error'>".T_("NEW_VERSION_OF_TT_NOW_AVAIL").": v".$file." you have v".$site_config['ttversion']."<br /> Please visit <a href=http://www.torrenttrader.org>TorrentTrader.org</a> to upgrade.</font></b></center>";
//		}
//	}

	$row = DB::run("SELECT VERSION() AS version")->fetch();
    $mysqlver = $row['version'];
	function apache_version()
    {
$ver = explode(" ",$_SERVER["SERVER_SOFTWARE"],3);
return ($ver[0] . " " . $ver[1]);
}
	$pending = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
	echo "<center><b>".T_("USERS_AWAITING_VALIDATION").":</b> <a href='admincp.php?action=confirmreg'>($pending)</a></center><br />";
	echo "<center>".T_("VERSION_MYSQL").": <b>" . $mysqlver . "</b><br />".T_("VERSION_PHP").": <b>" . phpversion() . "</b><br />".T_("VERSION_APACHE").": <b>" . apache_version() . "</b></center><hr/>";
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
    <td align="center"><a href="admincp.php?action=usersearch"><img src="images/admin/user_search.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("ADVANCED_USER_SEARCH"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=avatars"><img src="images/admin/avatar_log.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("AVATAR_LOG"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=backups"><img src="images/admin/db_backup.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BACKUPS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=ipbans"><img src="images/admin/ip_block.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BANNED_IPS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=bannedtorrents"><img src="images/admin/banned_torrents.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BANNED_TORRENTS"); ?></a><br /></td>
</tr>
<tr>
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=blocks&amp;do=view"><img src="images/admin/blocks.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BLOCKS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=cheats"><img src="images/admin/cheats.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("DETECT_POSS_CHEATS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=emailbans"><img src="images/admin/mail_bans.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("EMAIL_BANS"); ?></a><br /></td>
    <td align="center"><a href="faq-manage.php"><img src="images/admin/faq.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("FAQ"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=freetorrents"><img src="images/admin/free_leech.png" border="0" width="32" height="32" alt="" /><br />Freeleech Torrents<?php /*echo T_("TORRENTS_FREE_LEECH");*/ ?></a><br /></td>
</tr>
<tr>
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=lastcomm"><img src="images/admin/comments.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("LATEST_COMMENTS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=masspm"><img src="images/admin/mass_pm.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("MASS_PM"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=messagespy"><img src="images/admin/message_spy.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("MESSAGE_SPY"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=news&amp;do=view"><img src="images/admin/news.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("NEWS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=peers"><img src="images/admin/peer_list.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("PEERS_LIST"); ?></a><br /></td>
</tr>
<tr>
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=polls&amp;do=view"><img src="images/admin/polls.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("POLLS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=reports&amp;do=view"><img src="images/admin/report_system.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("REPORTS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=rules&amp;do=view"><img src="images/admin/rules.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("RULES"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=sitelog"><img src="images/admin/site_log.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("SITELOG"); ?></a><br /></td>
    <td align="center"><a href="teams-create.php"><img src="images/admin/teams.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TEAMS"); ?></a><br /></td>
</tr>
<tr> 
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=style"><img src="images/admin/themes.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("THEME_MANAGEMENT"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=categories&amp;do=view"><img src="images/admin/torrent_cats.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TORRENT_CAT_VIEW"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=torrentlangs&amp;do=view"><img src="images/admin/torrent_lang.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TORRENT_LANG"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=torrentmanage"><img src="images/admin/torrents.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TORRENTS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=groups&amp;do=view"><img src="images/admin/user_groups.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("USER_GROUPS_VIEW"); ?></a><br /></td>
</tr>
<tr>
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=warned"><img src="images/admin/warned_user.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("WARNED_USERS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=whoswhere"><img src="images/admin/whos_where.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("WHOS_WHERE"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=censor"><img src="images/admin/word_censor.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("WORD_CENSOR"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=forum"><img src="images/admin/forums.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("FORUM_MANAGEMENT"); ?><br /></a></td>
    <td align="center"><a href="admincp.php?action=users"><img src="images/admin/simple_user_search.png" border="0" width="32" height="32" alt="" /><br />Simple User Search<br /></a></td>  
</tr>
<tr>
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=privacylevel"><img src="images/admin/privacy_level.png" border="0" width="32" height="32" alt="" /><br />Privacy Level<br /></a></td>     
    <td align="center"><a href="admincp.php?action=pendinginvite"><img src="images/admin/pending_invited_user.png" border="0" width="32" height="32" alt="" /><br />Pending Invited Users<br /></a></td>    
    <td align="center"><a href="admincp.php?action=invited"><img src="images/admin/invited_user.png" border="0" width="32" height="32" alt="" /><br />Invited Users<br /></a></td>    
    <td align="center"><a href="exception-view.php"><img src="images/admin/sql_error.png" border="0" width="32" height="32" alt="" /><br />SQL Error<br /></a></td>  
   </a></td>  
</tr>
</table>

<?php
	end_frame();
}


if (!$action){
	stdhead(T_("ADMIN_CP"));
	navmenu();
	stdfoot();
}



if ($action=="forceclean"){
	$now = gmtime();
	DB::run("UPDATE tasks SET last_time=$now WHERE task='cleanup'");
    
	require_once("backend/cleanup.php");
	do_cleanup();
    
    autolink('admincp.php', T_("FORCE_CLEAN_COMPLETED"));
}

#======================================================================#
#    Manual Conf Reg - Updated by djhowarth (29-10-2011)
#======================================================================#
if ($action == "confirmreg")
{
    if ($do == "confirm") 
    {
        if ($_POST["confirmall"])
            DB::run("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0'");
        else
        {
            if (!@count($_POST["users"])) show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1); 
            $ids = array_map("intval", $_POST["users"]);
            $ids = implode(", ", $ids);
            DB::run("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0' AND `id` IN ($ids)");
        }
        
        autolink("admincp.php?action=confirmreg", "Entries Confirmed");
    }
    
    $count = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
    
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'admincp.php?action=confirmreg&amp;'); 
    
    $res = DB::run("SELECT `id`, `username`, `email`, `added`, `ip` FROM `users` WHERE `status` = 'pending' AND `invited_by` = '0' ORDER BY `added` DESC $limit");

    stdhead("Manual Registration Confirm");
    navmenu();
    
    begin_frame("Manual Registration Confirm");
    ?>
    
    <center>
    This page displays all unconfirmed users excluding users which have been invited by current members. <?php echo number_format($count); ?> members are pending;
    </center>

    <?php if ($count > 0): ?>
    <br />
    <form id="confirmreg" method="post" action="admincp.php?action=confirmreg&amp;do=confirm">
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head">E-mail</th>
        <th class="table_head">Registered</th>
        <th class="table_head">IP</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = $res->fetch(PDO::FETCH_LAZY)): ?>
    <tr>
        <td class="table_col1" align="center"><?php echo $row["username"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td colspan="5" align="right">
        <input type="submit" value="Confirm Checked" />
        <input type="submit" name="confirmall" value="Confirm All" />
        </td>
    </tr>
    </table>         
    </form>
    <?php 
    endif;
    
    if ($count > 25) echo $pagerbottom;
    
    end_frame();
    stdfoot(); 
}

include("admin/admin_advancedsearch.php");
include("admin/admin_avatar.php");
include("admin/admin_backup.php");
include("admin/admin_bantorrent.php");
include("admin/admin_blocks.php");
include("admin/admin_categories.php");
include("admin/admin_censor.php");
include("admin/admin_cheats.php");
include("admin/admin_comments.php");
include("admin/admin_config.php");
include("admin/admin_emailban.php");
include("admin/admin_exceptionlog.php");
include("admin/admin_forum.php");
include("admin/admin_freetorrent.php");
include("admin/admin_groups.php");
include("admin/admin_inviteusers.php");
include("admin/admin_ipban.php");
include("admin/admin_masspm.php");
include("admin/admin_messagespy.php");
include("admin/admin_news.php");
include("admin/admin_peers.php");
include("admin/admin_pendinginvite.php");
include("admin/admin_polls.php");
include("admin/admin_privacy.php");
include("admin/admin_reports.php");
include("admin/admin_rules.php");
include("admin/admin_simpleusersearch.php");
include("admin/admin_sitelog.php");
include("admin/admin_theme.php");
include("admin/admin_torrentlang.php");
include("admin/admin_torrents.php");
include("admin/admin_warnedusers.php");
include("admin/admin_whoswhere.php");

?>