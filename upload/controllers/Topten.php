<?php
class Topten extends Controller
{
    public function __construct()
    {
        // $this->userModel = $this->model('User');
    }

    public function index()
    {
        dbconn();
        global $config, $pdo;
        loggedinonly();

        if (get_user_class() < 1) {
            show_error_msg("Acces denied", "You must be at least Power User!", 1);
        }

        stdhead("Top 10");

        begin_frame("..:: Top Ten ::..");

        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;

        if (!in_array($type, array(1, 2, 3))) {
            $type = 1;
        }

        $limit = isset($_GET["lim"]) ? (int) $_GET["lim"] : false;
        $subtype = isset($_GET["subtype"]) ? (int) $_GET["subtype"] : false;

        print("<div style='font: bold 12px Verdana; margin-top:10px; margin-bottom:15px' align=center>" .

            ($type == 1 && !$limit ? "Users" : "<a href=$config[SITEURL]/topten?type=1>Users</a>") . " | " .
            ($type == 2 && !$limit ? "Torrents" : "<a href=$config[SITEURL]/topten?type=2>Torrents</a>") . " | " .
            ($type == 3 && !$limit ? "Countries" : "<a href=$config[SITEURL]/topten?type=3>Countries</a>") . "
        </div>\n");

        $pu = get_user_class() >= 3;

        if (!$pu) {
            $limit = 10;
        }

        if ($type == 1) {
            $mainquery = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed FROM users WHERE enabled = 'yes'";

            if (!$limit || $limit > 250) {
                $limit = 10;
            }

            if ($limit == 10 || $subtype == "ul") {
                $order = "uploaded DESC";
                $r = $pdo->run($mainquery . " ORDER BY $order " . " LIMIT $limit");
                usertable($r, "Top $limit Uploaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=$config[SITEURL]/topten?type=1&amp;lim=100&amp;subtype=ul>Top 100</a>] - [<a href=topten?type=1&amp;lim=250&amp;subtype=ul>Top 250</a>]</font>" : ""));
            }

            if ($limit == 10 || $subtype == "dl") {
                $order = "downloaded DESC";
                $r = $pdo->run($mainquery . " ORDER BY $order " . " LIMIT $limit");
                usertable($r, "Top $limit Downloaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=$config[SITEURL]/topten?type=1&amp;lim=100&amp;subtype=dl>Top 100</a>] - [<a href=topten?type=1&amp;lim=250&amp;subtype=dl>Top 250</a>]</font>" : ""));
            }

            if ($limit == 10 || $subtype == "dls") {
                $order = "downspeed DESC";
                $r = $pdo->run($mainquery . "  ORDER BY $order " . " LIMIT $limit");
                usertable($r, "Top $limit Fastest Downloaders <font class=small>(average, includes inactive time)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten?type=1&amp;lim=100&amp;subtype=dls>Top 100</a>] - [<a href=topten?type=1&amp;lim=250&amp;subtype=dls>Top 250</a>]</font>" : ""));
            }

            if ($limit == 10 || $subtype == "bsh") {
                $order = "uploaded / downloaded DESC";
                $extrawhere = " AND downloaded > 1073741824";
                $r = $pdo->run($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit");
                usertable($r, "Top $limit Best Sharers <font class=small>(with minimum 1 GB downloaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten?type=1&amp;lim=100&amp;subtype=bsh>Top 100</a>] - [<a href=topten?type=1&amp;lim=250&amp;subtype=bsh>Top 250</a>]</font>" : ""));
            }

            if ($limit == 10 || $subtype == "wsh") {
                $order = "uploaded / downloaded ASC, downloaded DESC";
                $extrawhere = " AND downloaded > 1073741824";
                $r = $pdo->run($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit");

                usertable($r, "Top $limit Worst Sharers <font class=small>(with minimum 1 GB downloaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten?type=1&amp;lim=100&amp;subtype=wsh>Top 100</a>] - [<a href=topten?type=1&amp;lim=250&amp;subtype=wsh>Top 250</a>]</font>" : ""));
            }
        } elseif ($type == 2) {

            if (!$limit || $limit > 50) {
                $limit = 10;
            }

            if ($limit == 10 || $subtype == "act") {
                $r = $pdo->run("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT $limit");

                _torrenttable($r, "Top $limit Most Active Torrents" . ($limit == 10 && $pu ? " <font class=small> - [<a href=$config[SITEURL]/topten?type=2&amp;lim=25&amp;subtype=act>Top 25</a>] - [<a href=topten?type=2&amp;lim=50&amp;subtype=act>Top 50</a>]</font>" : ""));
            }

            if ($limit == 10 || $subtype == "sna") {
                $r = $pdo->run("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY times_completed DESC LIMIT $limit");
                _torrenttable($r, "Top $limit Most Snatched Torrents" . ($limit == 10 && $pu ? " <font class=small> - [<a href=$config[SITEURL]/topten?type=2&amp;lim=25&amp;subtype=sna>Top 25</a>] - [<a href=topten?type=2&amp;lim=50&amp;subtype=sna>Top 50</a>]</font>" : ""));
            }

        } elseif ($type == 3) {

            if (!$limit || $limit > 25) {
                $limit = 10;
            }

            if ($limit == 10 || $subtype == "us") {
                $r = $pdo->run("SELECT name, flagpic, COUNT(users.country) as num FROM countries LEFT JOIN users ON users.country = countries.id GROUP BY name ORDER BY num DESC LIMIT $limit");
                countriestable($r, "Top $limit Countries<font class=small> (users)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten?type=3&amp;lim=25&amp;subtype=us>Top 25</a>]</font>" : ""), "Users");
            }

            if ($limit == 10 || $subtype == "ul") {
                $r = $pdo->run("SELECT c.name, c.flagpic, sum(u.uploaded) AS ul FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name ORDER BY ul DESC LIMIT $limit");
                countriestable($r, "Top $limit Countries<font class=small> (total uploaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten?type=3&amp;lim=25&amp;subtype=ul>Top 25</a>]</font>" : ""), "Uploaded");
            }

        }

        end_frame();
        stdfoot();
    }
}
