<?php
class Adminwhoswhere extends Controller
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
        $res = DB::run("SELECT `id`, `username`, `page`, `last_access` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `page` != '' ORDER BY `last_access` DESC LIMIT 100");

        $title = "Where are members";
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin("Last 100 Page Views");
        $data = [
            'res' => $res,
        ];
        $this->view('admin/whoswhere', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }
}