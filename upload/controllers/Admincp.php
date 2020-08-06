<?php
  class Admincp extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER;
loggedinonly();

if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
     show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}

 $action = $_REQUEST["action"];
 $do = $_REQUEST["do"];
 
function adminnavmenu(){
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
	echo "<center>Last cleanup performed: ".$lastclean." ago [<a href='$site_config[SITEURL]/admincp?action=forceclean'>".T_("FORCE_CLEAN")."</a>]</center>";                                      
	if ($site_config["ttversion"] != "PDO") {  
        $file = @file_get_contents('https://www.torrenttradertest.uk/ttversion.php');
        if ($site_config['ttversion'] >= $file){
			echo "<br /><center><b>".T_("YOU_HAVE_LATEST_VER_INSTALLED")." $site_config[ttversion]</b></center>";
		}else{
			echo "<br /><center><b><font class='error'>".T_("NEW_VERSION_OF_TT_NOW_AVAIL").": v".$file." you have ".$site_config['ttversion']."<br /> Please visit <a href=http://www.torrenttrader.xyz/>TorrentTrader.xyz</a> to upgrade.</font></b></center>";
		}
	}

	$row = DB::run("SELECT VERSION() AS version")->fetch();
    $mysqlver = $row['version'];
	function apache_version()
    {
$ver = explode(" ",$_SERVER["SERVER_SOFTWARE"],3);
return ($ver[0] . " " . $ver[1]);
}
	$pending = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
	echo "<center><b>".T_("USERS_AWAITING_VALIDATION").":</b> <a href='/admincp?action=confirmreg'>($pending)</a></center><br />";
	echo "<center>".T_("VERSION_MYSQL").": <b>" . $mysqlver . "</b>&nbsp;-&nbsp;".T_("VERSION_PHP").": <b>" . phpversion() . "</b>&nbsp;-&nbsp;".T_("Apache Version").": <b>" . apache_version() . "</b></center>";
    echo "<center><a href=$site_config[SITEURL]/admincp?action=prune>Prune Cache</a><br></center>";
?>
</br><div class="row">
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=seedbonus"><img src="images/admin/seedbonus.png" border=0 width=32 height=32><br>Management of Seed Bonus</a><br /><td></div>
	<div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=usersearch"><img src="images/admin/user_search.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("ADVANCED_USER_SEARCH"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=avatars"><img src="images/admin/avatar_log.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("AVATAR_LOG"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=backups"><img src="images/admin/db_backup.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BACKUPS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=ipbans"><img src="images/admin/ip_block.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BANNED_IPS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=bannedtorrents"><img src="images/admin/banned_torrents.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BANNED_TORRENTS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=blocks&amp;do=view"><img src="images/admin/blocks.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BLOCKS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=cheats"><img src="images/admin/cheats.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("DETECT_POSS_CHEATS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=emailbans"><img src="images/admin/mail_bans.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("EMAIL_BANS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/faq/manage"><img src="images/admin/faq.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("FAQ"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=freetorrents"><img src="images/admin/free_leech.png" border="0" width="32" height="32" alt="" /><br />Freeleech Torrents<?php /*echo T_("TORRENTS_FREE_LEECH");*/ ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=lastcomm"><img src="images/admin/comments.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("LATEST_COMMENTS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=masspm"><img src="images/admin/mass_pm.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("MASS_PM"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=messagespy"><img src="images/admin/message_spy.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("MESSAGE_SPY"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=news&amp;do=view"><img src="images/admin/news.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("NEWS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=peers"><img src="images/admin/peer_list.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("PEERS_LIST"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=polls&amp;do=view"><img src="images/admin/polls.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("POLLS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=reports&amp;do=view"><img src="images/admin/report_system.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("REPORTS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=rules&amp;do=view"><img src="images/admin/rules.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("RULES"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=sitelog"><img src="images/admin/site_log.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("SITELOG"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/teams/create"><img src="images/admin/teams.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TEAMS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=style"><img src="images/admin/themes.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("THEME_MANAGEMENT"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=categories&amp;do=view"><img src="images/admin/torrent_cats.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TORRENT_CAT_VIEW"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=torrentlangs&amp;do=view"><img src="images/admin/torrent_lang.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TORRENT_LANG"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=torrentmanage"><img src="images/admin/torrents.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TORRENTS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=groups&amp;do=view"><img src="images/admin/user_groups.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("USER_GROUPS_VIEW"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=warned"><img src="images/admin/warned_user.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("WARNED_USERS"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=whoswhere"><img src="images/admin/whos_where.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("WHOS_WHERE"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=censor"><img src="images/admin/word_censor.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("WORD_CENSOR"); ?></a><br /><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=forum"><img src="images/admin/forums.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("FORUM_MANAGEMENT"); ?><br /></a><td></div>
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=users"><img src="images/admin/simple_user_search.png" border="0" width="32" height="32" alt="" /><br />Simple User Search<br /></a><td></div>  
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=privacylevel"><img src="images/admin/privacy_level.png" border="0" width="32" height="32" alt="" /><br />Privacy Level<br /></a><td></div>     
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=pendinginvite"><img src="images/admin/pending_invited_user.png" border="0" width="32" height="32" alt="" /><br />Pending Invited Users<br /></a><td></div>    
    <div class="col"><td><a href="<?php echo TTURL; ?>/admincp?action=invited"><img src="images/admin/invited_user.png" border="0" width="32" height="32" alt="" /><br />Invited Users<br /></a><td></div>    
    <div class="col"><td><a href="<?php echo TTURL; ?>/exceptions/admin"><img src="images/admin/sql_error.png" border="0" width="32" height="32" alt="" /><br />SQL Error<br /></a><td></div>  
</div>
<?php
	end_frame();
}

if (!$action){
	stdhead(T_("ADMIN_CP"));
	adminnavmenu();
	stdfoot();
}

if ($action=="forceclean"){
	$now = gmtime();
	DB::run("UPDATE tasks SET last_time=$now WHERE task='cleanup'");
    
	require_once("helpers/cleanup_helper.php");
	do_cleanup();
    
    autolink(TTURL.'/admincp', T_("FORCE_CLEAN_COMPLETED"));
}



include("admin/admin_cache.php");
include("admin/admin_confirmusers.php");
include("admin/admin_seedbonus.php");
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
}
  }