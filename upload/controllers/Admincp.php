<?php
class Admincp extends Controller
{

    public function __construct()
    {
        // $this->userModel = $this->model('User');
    }

    public function index()
    {
        // Lets try walkthrough this
        dbconn();
        global $site_config, $CURUSER, $THEME;
        // Checks
        loggedinonly();
        if (!$CURUSER || $CURUSER["control_panel"] != "yes") {
            show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
        }
        // Calls
        $action = $_REQUEST["action"];
        $do = $_REQUEST["do"];
        // navbar to pass round
        function adminnavmenu()
        {
            global $site_config;
            //Get Last Cleanup
            $row = DB::run("SELECT last_time FROM tasks WHERE task =?", ['cleanup'])->fetchColumn();
            if (!$row) {
                $lastclean = "never done...";
            } else {
                $lastclean = get_elapsed_time($row);
            }
            ?><br>
            <div class="card w-100 ">
            <div class="border border-primary">
            <?php
            echo "<center>Last cleanup performed: " . $lastclean . " ago [<a href='$site_config[SITEURL]/admincp?action=forceclean'><b>" . T_("FORCE_CLEAN") . "</b></a>]</center>";
            if ($site_config["ttversion"] != "PDO") {
                $file = @file_get_contents('https://www.torrenttradertest.uk/ttversion.php');
                if ($site_config['ttversion'] >= $file) {
                    echo "<br /><center><b>" . T_("YOU_HAVE_LATEST_VER_INSTALLED") . " $site_config[ttversion]</b></center>";
                } else {
                    echo "<br /><center><b><font class='error'>" . T_("NEW_VERSION_OF_TT_NOW_AVAIL") . ": v" . $file . " you have " . $site_config['ttversion'] . "<br /> Please visit <a href=http://www.torrenttrader.xyz/>TorrentTrader.xyz</a> to upgrade.</font></b></center>";
                }
            }

            $row = DB::run("SELECT VERSION() AS version")->fetch();
            $mysqlver = $row['version'];
            function apache_version()
            {
                $ver = explode(" ", $_SERVER["SERVER_SOFTWARE"], 3);
                return ($ver[0] . " " . $ver[1]);
            }
            $pending = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
            echo "<center><b>" . T_("USERS_AWAITING_VALIDATION") . ":</b> <a href='/admincp?action=confirmreg'><b>($pending)</b></a></center>";
            echo "<center>" . T_("VERSION_MYSQL") . ": <b>" . $mysqlver . "</b>&nbsp;-&nbsp;" . T_("VERSION_PHP") . ": <b>" . phpversion() . "</b>&nbsp;-&nbsp;" . T_("Apache Version") . ": <b>" . apache_version() . "</b></center>";
            echo "<center><a href=$site_config[SITEURL]/admincp?action=prune><b>Prune Cache</b></a><br></center>";

            echo '</div></div><br>';
        }

        if (!$action) {
            $title = 'admin';
            require 'views/admin/header.php';
            adminnavmenu();
            echo '<div class="border border-primary">';
            echo '<center>';
            echo '<b>Welcome To The Staff Panel</b>';
            echo '</center>';
            echo '</div>';
            require 'views/admin/footer.php';
        }

        if ($action == "forceclean") {
            $now = gmtime();
            DB::run("UPDATE tasks SET last_time=$now WHERE task='cleanup'");
            require_once "helpers/cleanup_helper.php";
            do_cleanup();
            autolink(TTURL . '/admincp', T_("FORCE_CLEAN_COMPLETED"));
        }

        include "admin/admin_cache.php";
        include "admin/admin_confirmusers.php";
        include "admin/admin_seedbonus.php";
        include "admin/admin_advancedsearch.php";
        include "admin/admin_avatar.php";
        include "admin/admin_backup.php";
        include "admin/admin_bantorrent.php";
        include "admin/admin_blocks.php";
        include "admin/admin_categories.php";
        include "admin/admin_censor.php";
        include "admin/admin_cheats.php";
        include "admin/admin_comments.php";
        include "admin/admin_config.php";
        include "admin/admin_duplicateip.php";
        include "admin/admin_emailban.php";
        include "admin/admin_exceptionlog.php";
        include "admin/admin_forum.php";
        include "admin/admin_freetorrent.php";
        include "admin/admin_groups.php";
        include "admin/admin_inviteusers.php";
        include "admin/admin_ipban.php";
        include "admin/admin_masspm.php";
        include "admin/admin_messagespy.php";
        include "admin/admin_news.php";
        include "admin/admin_peers.php";
        include "admin/admin_pendinginvite.php";
        include "admin/admin_polls.php";
        include "admin/admin_privacy.php";
        include "admin/admin_reports.php";
        include "admin/admin_rules.php";
        include "admin/admin_simpleusersearch.php";
        include "admin/admin_sitelog.php";
        include "admin/admin_theme.php";
        include "admin/admin_torrentlang.php";
        include "admin/admin_torrents.php";
        include "admin/admin_warnedusers.php";
        include "admin/admin_whoswhere.php";
    }
}