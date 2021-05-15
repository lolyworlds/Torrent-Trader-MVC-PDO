<?php
class Adminwarnedusers extends Controller
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
        if ($_GET['do'] == "delete") {
            if ($_POST["removeall"]) {
                $res = DB::run("SELECT `id` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `warned` = 'yes'");
                while ($row = $res->fetch(PDO::FETCH_LAZY)) {
                    DB::run("DELETE FROM `warnings` WHERE `active` = 'yes' AND `userid` = '$row[id]'");
                    DB::run("UPDATE `users` SET `warned` = 'no' WHERE `id` = '$row[id]'");
                }
            } else {
                if (!@count($_POST['warned'])) {
                    show_error_msg(Lang::T("ERROR"), Lang::T("NOTHING_SELECTED"), 1);
                }
                $ids = array_map("intval", $_POST["warned"]);
                $ids = implode(", ", $ids);
                DB::run("DELETE FROM `warnings` WHERE `active` = 'yes' AND `userid` IN ($ids)");
                DB::run("UPDATE `users` SET `warned` = 'no' WHERE `id` IN ($ids)");
            }
            Redirect::autolink(URLROOT . "/adminwarnedusers", "Entries Confirmed");
        }
        $count = get_row_count("users", "WHERE enabled = 'yes' AND status = 'confirmed' AND warned = 'yes'");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, '/adminwarnedusers?');
        $res = DB::run("SELECT `id`, `username`, `class`, `added`, `last_access` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `warned` = 'yes' ORDER BY `added` DESC $limit");
        $title = "Warned Users";
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'pagerbottom' => $pagerbottom,
            'count' => $count,
            'res' => $res,
        ];
        $this->view('admin/warned', $data);
        require APPROOT . '/views/admin/footer.php';
    }

}