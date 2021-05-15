<?php
class Admintorrents extends Controller
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
        if ($_POST["do"] == "delete") {
            if (!@count($_POST["torrentids"])) {
                show_error_msg(Lang::T("ERROR"), "Nothing selected click <a href='admintorrents'>here</a> to go back.", 1);
            }
            foreach ($_POST["torrentids"] as $id) {
                deletetorrent(intval($id));
                Logs::write("Torrent ID $id was deleted by $_SESSION[username]");
            }
            show_error_msg("Torrents Deleted", "Go <a href='admintorrents'>back</a>?", 1);
        }
        $search = (!empty($_GET["search"])) ? htmlspecialchars(trim($_GET["search"])) : "";
        $where = ($search == "") ? "" : "WHERE name LIKE " . sqlesc("%$search%") . "";
        $count = get_row_count("torrents", $where);
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, "admintorrents&amp;");
        $res = DB::run("SELECT id, name, seeders, leechers, visible, banned, external FROM torrents $where ORDER BY name $limit");

        $title = Lang::T("Torrent Management");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin("Torrent Management");
        $data = [
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'res' => $res,
            'search' => $search,
        ];
        $this->view('admin/torrentmanage', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';

    }
}