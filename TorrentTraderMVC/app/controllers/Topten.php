<?php
class Topten extends Controller
{
    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
    }

    public function index()
    {
        $db = new Database();
        
        $limit = isset($_GET["lim"]) ? (int) $_GET["lim"] : false;
        $subtype = isset($_GET["subtype"]) ? (int) $_GET["subtype"] : false;
        $pu = $_SESSION["class"] >= 3;

        Style::header("Top 10");
        Style::begin("..:: Top Ten ::..");
        print("<div style='font: bold 12px Verdana; margin-top:10px; margin-bottom:15px' align=center>
        <a href=".URLROOT."/topten>Users</a> | 
        <a href=".URLROOT."/topten/torrents>Torrents</a> | 
        <a href=".URLROOT."/topten/countries>Countries</a>
        </div>\n");
        $mainquery = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed FROM users WHERE enabled = 'yes'";
        if (!$limit || $limit > 250) {
            $limit = 10;
        }
        if ($limit == 10 || $subtype == "ul") {
            $order = "uploaded DESC";
            $res = $db->run($mainquery . " ORDER BY $order " . " LIMIT $limit");
            $title = "Top $limit Uploaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=".URLROOT."/topten?lim=100&amp;subtype=ul>Top 100</a>] - [<a href=topten?lim=250&amp;subtype=ul>Top 250</a>]</font>" : "");
            include APPROOT."/views/topten/user.php";
        }
        if ($limit == 10 || $subtype == "dl") {
            $order = "downloaded DESC";
            $res = $db->run($mainquery . " ORDER BY $order " . " LIMIT $limit");
            $title = "Top $limit Downloaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=".URLROOT."/topten?lim=100&amp;subtype=dl>Top 100</a>] - [<a href=topten?lim=250&amp;subtype=dl>Top 250</a>]</font>" : "");
            include APPROOT."/views/topten/user.php";
        }
        if ($limit == 10 || $subtype == "dls") {
            $order = "downspeed DESC";
            $res = $db->run($mainquery . "  ORDER BY $order " . " LIMIT $limit");
            $title = "Top $limit Fastest Downloaders <font class=small>(average, includes inactive time)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten?lim=100&amp;subtype=dls>Top 100</a>] - [<a href=topten?lim=250&amp;subtype=dls>Top 250</a>]</font>" : "");
            include APPROOT."/views/topten/user.php";
        }
        if ($limit == 10 || $subtype == "bsh") {
            $order = "uploaded / downloaded DESC";
            $extrawhere = " AND downloaded > 1073741824";
            $res = $db->run($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit");
            $title = "Top $limit Best Sharers <font class=small>(with minimum 1 GB downloaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten?lim=100&amp;subtype=bsh>Top 100</a>] - [<a href=topten?lim=250&amp;subtype=bsh>Top 250</a>]</font>" : "");
            include APPROOT."/views/topten/user.php";
        }
        if ($limit == 10 || $subtype == "wsh") {
            $order = "uploaded / downloaded ASC, downloaded DESC";
            $extrawhere = " AND downloaded > 1073741824";
            $res = $db->run($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit");
            $title = "Top $limit Worst Sharers <font class=small>(with minimum 1 GB downloaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten?lim=100&amp;subtype=wsh>Top 100</a>] - [<a href=topten?lim=250&amp;subtype=wsh>Top 250</a>]</font>" : "");
            include APPROOT."/views/topten/user.php";
        }
        Style::end();
        Style::footer();
    }


    public function torrents()
    {
        $db = new Database();
        
        $limit = isset($_GET["lim"]) ? (int) $_GET["lim"] : false;
        $subtype = isset($_GET["subtype"]) ? (int) $_GET["subtype"] : false;
        $pu = $_SESSION["class"] >= 3;

        Style::header("Top 10");
        Style::begin("..:: Top Ten ::..");
        print("<div style='font: bold 12px Verdana; margin-top:10px; margin-bottom:15px' align=center>
        <a href=".URLROOT."/topten>Users</a> | 
        <a href=".URLROOT."/topten/torrents>Torrents</a> | 
        <a href=".URLROOT."/topten/countries>Countries</a>
        </div>\n");
        if (!$limit || $limit > 50) {
            $limit = 10;
        }
        if ($limit == 10 || $subtype == "act") {
            $res = $db->run("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT $limit");
            $title = "Top $limit Most Active Torrents" . ($limit == 10 && $pu ? " <font class=small> - [<a href=".URLROOT."/topten/torrents?lim=25&amp;subtype=act>Top 25</a>] - [<a href=".URLROOT."/topten/torrents?lim=50&amp;subtype=act>Top 50</a>]</font>" : "");
            include APPROOT."/views/topten/torrent.php";
        }
        if ($limit == 10 || $subtype == "sna") {
            $res = $db->run("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY times_completed DESC LIMIT $limit");
            $title = "Top $limit Most Snatched Torrents" . ($limit == 10 && $pu ? " <font class=small> - [<a href=".URLROOT."/topten/torrents?lim=25&amp;subtype=sna>Top 25</a>] - [<a href=".URLROOT."/topten/torrents?lim=50&amp;subtype=sna>Top 50</a>]</font>" : "");
            include APPROOT."/views/topten/torrent.php";
        }
        Style::end();
        Style::footer();
    }



    public function countries()
    {
        $db = new Database();
        
        $limit = isset($_GET["lim"]) ? (int) $_GET["lim"] : false;
        $subtype = isset($_GET["subtype"]) ? (int) $_GET["subtype"] : false;
        $pu = $_SESSION["class"] >= 3;

        Style::header("Top 10");
        Style::begin("..:: Top Ten ::..");
        print("<div style='font: bold 12px Verdana; margin-top:10px; margin-bottom:15px' align=center>
        <a href=".URLROOT."/topten>Users</a> | 
        <a href=".URLROOT."/topten/torrents>Torrents</a> | 
        <a href=".URLROOT."/topten/countries>Countries</a>
        </div>\n");
        if (!$limit || $limit > 25) {
            $limit = 10;
        }
        if ($limit == 10 || $subtype == "us") {
            $res = $db->run("SELECT name, flagpic, COUNT(users.country) as num FROM countries LEFT JOIN users ON users.country = countries.id GROUP BY name ORDER BY num DESC LIMIT $limit");
            $title = "Top $limit Countries<font class=small> (users)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=".URLROOT."/topten/countries?lim=25&amp;subtype=us>Top 25</a>]</font>" : "");
            include APPROOT."/views/topten/country.php";
        }
        if ($limit == 10 || $subtype == "ul") {
            $res = $db->run("SELECT c.name, c.flagpic, sum(u.uploaded) AS ul FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name ORDER BY ul DESC LIMIT $limit");
            $title = "Top $limit Countries<font class=small> (total uploaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=".URLROOT."/topten/countries?lim=25&amp;subtype=ul>Top 25</a>]</font>" : "");
            include APPROOT."/views/topten/country.php";
        }
        Style::end();
        Style::footer();
    }

}