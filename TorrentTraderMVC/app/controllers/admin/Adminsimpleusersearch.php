<?php
class Adminsimpleusersearch extends Controller
{

    public function __construct()
    {
        Auth::user(); // should check admin here
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()

    {
        if ($_SESSION['delete_users'] == 'no' || $_SESSION['delete_torrents'] == 'no') {
            Redirect::autolink(URLROOT . "/admincp", "You do not have permission to be here.");
        }
        if ($_POST['do'] == "del") {
            if (!@count($_POST["users"])) {
                show_error_msg(Lang::T("ERROR"), "Nothing Selected.", 1);
            }
            $ids = array_map("intval", $_POST["users"]);
            $ids = implode(", ", $ids);
            $res = DB::run("SELECT `id`, `username` FROM `users` WHERE `id` IN ($ids)");
            while ($row = $res->fetch(PDO::FETCH_LAZY)) {
                Logs::write("Account '$row[1]' (ID: $row[0]) was deleted by $_SESSION[username]");
                // deleteaccount($row[0]); todo
            }
            if ($_POST['inc']) {
                $res = DB::run("SELECT `id`, `name` FROM `torrents` WHERE `owner` IN ($ids)");
                while ($row = $res->fetch(PDO::FETCH_LAZY)) {
                    Logs::write("Torrent '$row[1]' (ID: $row[0]) was deleted by $_SESSION[username]");
                    deletetorrent($row["id"]);
                }
            }
            Redirect::autolink(URLROOT . "/adminsimpleusersearch", "Entries Deleted");
        }
        $where = null;
        if (!empty($_GET['search'])) {
            $search = sqlesc('%' . $_GET['search'] . '%');
            $where = "AND username LIKE " . $search . " OR email LIKE " . $search . "
                     OR ip LIKE " . $search;
        }
        $count = get_row_count("users", "WHERE enabled = 'yes' AND status = 'confirmed' $where");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, '/admin/simpleusersearch?;');
        $res = DB::run("SELECT id, username, class, email, ip, added, last_access FROM users WHERE enabled = 'yes' AND status = 'confirmed' $where ORDER BY username DESC $limit");

        $title = Lang::T("USERS_SEARCH_SIMPLE");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin(Lang::T("USERS_SEARCH_SIMPLE"));
        $data = [
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'res' => $res,
        ];
        $this->view('admin/simpleusersearch', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }
}