<?php
class Adminconfirmusers extends Controller
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
        $do = $_GET['do']; // todo
        if ($do == "confirm") {
            if ($_POST["confirmall"]) {
                DB::run("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0'");
            } else {
                if (!@count($_POST["users"])) {
                    show_error_msg(Lang::T("ERROR"), Lang::T("NOTHING_SELECTED"), 1);
                }
                $ids = array_map("intval", $_POST["users"]);
                $ids = implode(", ", $ids);
                DB::run("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0' AND `id` IN ($ids)");
            }
            Redirect::autolink(URLROOT . "/adminconfirmusers", "Entries Confirmed");
        }
        $count = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, '' . URLROOT . '/adminconfirmusers?');

        $res = DB::run("SELECT `id`, `username`, `email`, `added`, `ip` FROM `users` WHERE `status` = 'pending' AND `invited_by` = '0' ORDER BY `added` DESC $limit");

        $title = "Manual Registration Confirm";
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();

        Style::begin("Manual Registration Confirm");
        $data = [
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'res' => $res,
        ];
        $this->view('admin/confirmreg', $data);
        require APPROOT . '/views/admin/footer.php';
    }
}