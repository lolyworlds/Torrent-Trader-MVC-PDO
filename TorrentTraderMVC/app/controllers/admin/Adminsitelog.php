<?php
class Adminsitelog extends Controller
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
        if ($_POST['del']) {
            if ($_POST["delall"]) {
                DB::run("DELETE FROM `log`");
            } else {
                if (!@count($_POST["del"])) {
                    show_error_msg(Lang::T("ERROR"), Lang::T("NOTHING_SELECTED"), 1);
                }
                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("DELETE FROM `log` WHERE `id` IN ($ids)");
            }
            Redirect::autolink(URLROOT . "/adminsitelog", Lang::T("CP_DELETED_ENTRIES"));
            $title = Lang::T("Log");
            require APPROOT . '/views/admin/header.php';
            show_error_msg(Lang::T("SUCCESS"), Lang::T("CP_DELETED_ENTRIES"), 0);
            require APPROOT . '/views/admin/footer.php';
            die;
        }

        $search = trim($_GET['search']);
        if ($search != '') {
            $where = "WHERE txt LIKE " . sqlesc("%$search%") . "";
        }
        $res2 = DB::run("SELECT COUNT(*) FROM log $where");
        $row = $res2->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/adminsitelog?");
        $rqq = "SELECT id, added, txt FROM log $where ORDER BY id DESC $limit";
        $res = DB::run($rqq);

        $title = Lang::T("Site Log");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin("Site Log");
        $data = [
            'pagerbottom' => $pagerbottom,
            'res' => $res,
        ];
        $this->view('admin/sitelog', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }
}