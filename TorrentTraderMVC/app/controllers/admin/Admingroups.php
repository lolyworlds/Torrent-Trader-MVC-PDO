<?php
class Admingroups extends Controller
{

    public function __construct()
    {
        Auth::user(); // should check admin here
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public static function adminnavmenu1()
    {
        //Get Last Cleanup
        $row = DB::run("SELECT last_time FROM tasks WHERE task =?", ['cleanup'])->fetchColumn();
        if (!$row) {
            $lastclean = "never done...";
        } else {
            $lastclean = TimeDate::get_elapsed_time($row);
        }?><br>
        <div class="card w-100 ">
        <div class="border border-primary">
        <?php
echo "<center>Last cleanup performed: " . $lastclean . " ago [<a href='" . URLROOT . "/admincleanup'><b>" . Lang::T("FORCE_CLEAN") . "</b></a>]</center>";
        /*
        if (VERSION != "PDO") {
        $file = @file_get_contents('https://www.torrenttradertest.uk/ttversion.php');
        if (VERSION >= $file) {
        echo "<br /><center><b>" . Lang::T("YOU_HAVE_LATEST_VER_INSTALLED") . " VERSION</b></center>";
        } else {
        echo "<br /><center><b><font class='error'>" . Lang::T("NEW_VERSION_OF_TT_NOW_AVAIL") . ": v" . $file . " you have " . VERSION . "<br /> Please visit <a href=http://www.torrenttrader.xyz/>TorrentTrader.xyz</a> to upgrade.</font></b></center>";
        }
        }
         */
        $row = DB::run("SELECT VERSION() AS version")->fetch();
        $mysqlver = $row['version'];
        function apache_version()
        {
            $ver = explode(" ", $_SERVER["SERVER_SOFTWARE"], 3);
            return ($ver[0] . " " . $ver[1]);
        }
        $pending = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
        echo "<center><b>" . Lang::T("USERS_AWAITING_VALIDATION") . ":</b> <a href='" . URLROOT . "/adminconfirmusers'><b>($pending)</b></a></center>";
        echo "<center>" . Lang::T("VERSION_MYSQL") . ": <b>" . $mysqlver . "</b>&nbsp;-&nbsp;" . Lang::T("VERSION_PHP") . ": <b>" . phpversion() . "</b>&nbsp;-&nbsp;" . Lang::T("Apache Version") . ": <b>" . apache_version() . "</b></center>";
        echo "<center><a href=" . URLROOT . "/admincache><b>Purge Cache</b></a><br></center>";
        echo '</div></div><br>';
    }

    public function adminnavmenu()
    {
        //Get Last Cleanup
        $row = DB::run("SELECT last_time FROM tasks WHERE task =?", ['cleanup'])->fetchColumn();
        if (!$row) {
            $lastclean = "never done...";
        } else {
            $lastclean = TimeDate::get_elapsed_time($row);
        }?><br>
        <div class="card w-100 ">
        <div class="border border-primary">
        <?php
echo "<center>Last cleanup performed: " . $lastclean . " ago [<a href='" . URLROOT . "/admincleanup'><b>" . Lang::T("FORCE_CLEAN") . "</b></a>]</center>";
        /*
        if (VERSION != "PDO") {
        $file = @file_get_contents('https://www.torrenttradertest.uk/ttversion.php');
        if (VERSION >= $file) {
        echo "<br /><center><b>" . Lang::T("YOU_HAVE_LATEST_VER_INSTALLED") . " VERSION</b></center>";
        } else {
        echo "<br /><center><b><font class='error'>" . Lang::T("NEW_VERSION_OF_TT_NOW_AVAIL") . ": v" . $file . " you have " . VERSION . "<br /> Please visit <a href=http://www.torrenttrader.xyz/>TorrentTrader.xyz</a> to upgrade.</font></b></center>";
        }
        }
         */
        $row = DB::run("SELECT VERSION() AS version")->fetch();
        $mysqlver = $row['version'];
        function apache_version()
        {
            $ver = explode(" ", $_SERVER["SERVER_SOFTWARE"], 3);
            return ($ver[0] . " " . $ver[1]);
        }
        $pending = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
        echo "<center><b>" . Lang::T("USERS_AWAITING_VALIDATION") . ":</b> <a href='" . URLROOT . "/adminconfirmusers'><b>($pending)</b></a></center>";
        echo "<center>" . Lang::T("VERSION_MYSQL") . ": <b>" . $mysqlver . "</b>&nbsp;-&nbsp;" . Lang::T("VERSION_PHP") . ": <b>" . phpversion() . "</b>&nbsp;-&nbsp;" . Lang::T("Apache Version") . ": <b>" . apache_version() . "</b></center>";
        echo "<center><a href=" . URLROOT . "/admincache><b>Purge Cache</b></a><br></center>";
        echo '</div></div><br>';
    }

    public function index()
    {
        if (!$_SESSION['class'] > 5 || $_SESSION["control_panel"] != "yes") {
            show_error_msg(Lang::T("ERROR"), Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
        }
        $title = 'admin';
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        echo '<div class="border border-primary">';
        echo '<center>';
        echo '<b>Welcome To The Staff Panel</b>';
        echo '</center>';
        echo '</div>';
        require APPROOT . '/views/admin/footer.php';
    }


    public function groupsview()
    {
        $getlevel = DB::run("SELECT * from groups ORDER BY group_id");
        $title = Lang::T("GROUPS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'getlevel' => $getlevel,
        ];
        $this->view('groups/view', $data);
        require APPROOT . '/views/admin/footer.php';
    }

    public function groupsedit()
    {
        var_dump($_GET);
        $group_id = intval($_GET["group_id"]);
        $rlevel = DB::run("SELECT * FROM groups WHERE group_id=?", [$group_id]);
        if (!$rlevel) {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_NO_GROUP_ID_FOUND"), 1);
        }

        $title = Lang::T("GROUPS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'rlevel' => $rlevel,
        ];
        $this->view('groups/edit', $data);
        require APPROOT . '/views/admin/footer.php';
    }

    public function groupsupdate()
    {
        $title = Lang::T("GROUPS_MANAGEMENT");
        $update = array();
        $update[] = "level = " . sqlesc($_POST["gname"]);
        $update[] = "Color= " . sqlesc($_POST["gcolor"]);
        $update[] = "view_torrents = " . sqlesc($_POST["vtorrent"]);
        $update[] = "edit_torrents = " . sqlesc($_POST["etorrent"]);
        $update[] = "delete_torrents = " . sqlesc($_POST["dtorrent"]);
        $update[] = "view_users = " . sqlesc($_POST["vuser"]);
        $update[] = "edit_users = " . sqlesc($_POST["euser"]);
        $update[] = "delete_users = " . sqlesc($_POST["duser"]);
        $update[] = "view_news = " . sqlesc($_POST["vnews"]);
        $update[] = "edit_news = " . sqlesc($_POST["enews"]);
        $update[] = "delete_news = " . sqlesc($_POST["dnews"]);
        $update[] = "view_forum = " . sqlesc($_POST["vforum"]);
        $update[] = "edit_forum = " . sqlesc($_POST["eforum"]);
        $update[] = "delete_forum = " . sqlesc($_POST["dforum"]);
        $update[] = "can_upload = " . sqlesc($_POST["upload"]);
        $update[] = "can_download = " . sqlesc($_POST["down"]);
        $update[] = "maxslots= ' " . $_POST["downslots"] . " ' "; // TODO
        $update[] = "control_panel = " . sqlesc($_POST["admincp"]);
        $update[] = "staff_page = " . sqlesc($_POST["staffpage"]);
        $update[] = "staff_public = " . sqlesc($_POST["staffpublic"]);
        $update[] = "staff_sort = " . intval($_POST['sort']);
        $strupdate = implode(",", $update);
        $group_id = intval($_GET["group_id"]);
        DB::run("UPDATE groups SET $strupdate WHERE group_id=?", [$group_id]);
        Redirect::autolink(URLROOT . "/admingroups/groupsview", Lang::T("SUCCESS"), "Groups Updated!");
        require APPROOT . '/views/admin/footer.php';
    }

    public function groupsdelete()
    {
        //Needs to be secured!!!!
        $group_id = intval($_GET["group_id"]);
        if (($group_id == "1") || ($group_id == "7")) {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_YOU_CANT_DEL_THIS_GRP"), 1);
        }
        DB::run("DELETE FROM groups WHERE group_id=?", [$group_id]);
        Redirect::autolink(URLROOT . "/admingroups/groupsview", Lang::T("CP_DEL_OK"));
    }

    public function groupsadd()
    {
        $rlevel = DB::run("SELECT DISTINCT group_id, level FROM groups ORDER BY group_id");

        $title = Lang::T("GROUPS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'rlevel' => $rlevel,
        ];
        $this->view('groups/add', $data);
        require APPROOT . '/views/admin/footer.php';
    }

    public function groupsaddnew()
    {
        $gname = $_POST["gname"];
        $gcolor = $_POST["gcolor"];
        $group_id = $_POST["getlevel"];
        $rlevel = DB::run("SELECT * FROM groups WHERE group_id=?", [$group_id]);
        $level = $rlevel->fetch(PDO::FETCH_ASSOC);
        if (!$level) {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_INVALID_ID"), 1);
        }
        $test = DB::run("INSERT INTO groups
  (level, color, view_torrents, edit_torrents, delete_torrents, view_users, edit_users, delete_users,
	view_news, edit_news, delete_news, view_forum, edit_forum, delete_forum, can_upload, can_download,
	control_panel, staff_page, staff_public, staff_sort, maxslots)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$gname, $gcolor, $level['view_torrents'], $level["edit_torrents"], $level["delete_torrents"], $level["view_users"],
                $level["edit_users"], $level["delete_users"], $level["view_news"], $level["edit_news"], $level["delete_news"],
                $level["edit_forum"], $level["edit_forum"], $level["delete_forum"], $level["can_upload"], $level["can_download"], $level["control_panel"],
                $level["staff_page"], $level["staff_public"], $level["staff_sort"], $level["maxslots"]]);
        Redirect::autolink(URLROOT . "/admingroups/groupsview", Lang::T("SUCCESS"), "Groups Updated!");
        require APPROOT . '/views/admin/footer.php';
    }
}