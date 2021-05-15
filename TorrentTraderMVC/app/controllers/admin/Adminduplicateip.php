<?php
class Adminduplicateip extends Controller
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
        $res = DB::run("SELECT ip FROM users GROUP BY ip HAVING count(*) > 1");
        $num = $res->rowCount();
        list($pagertop, $pagerbottom, $limit) = pager(25, $num, 'adminduplicateip?');
        $res = DB::run("SELECT id, username, class, email, ip, added, last_access, COUNT(*) as count FROM users GROUP BY ip HAVING count(*) > 1 ORDER BY id ASC $limit");

        $title = Lang::T("DUPLICATEIP");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'num' => $num,
            'res' => $res,

        ];
        $this->view('admin/duplicuteip', $data);
        require APPROOT . '/views/admin/footer.php';
    }
}