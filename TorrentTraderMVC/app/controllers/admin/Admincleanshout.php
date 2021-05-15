<?php
class Admincleanshout extends Controller
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
        $do = $_GET['do'];
        if ($do == "delete") {
            DB::run("TRUNCATE TABLE `shoutbox`");
            Logs::write("Shoutbox cleared by $_SESSION[username]");
            $msg_shout = "[color=#ff0000]" . Lang::T("SHOUTBOX_CLEARED_MESSAGE") . "[/color]";
            DB::run("INSERT INTO shoutbox (userid, date, user, message) VALUES(?,?,?,?)", [0, TimeDate::get_date_time(), 'System', $msg_shout]);
            Redirect::autolink(URLROOT . "/admincp", "<b><font color='#ff0000'>Shoutbox Cleared....</font></b>");
        }

        $title = Lang::T("CLEAR_SHOUTBOX");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [];
        $this->view('admin/clearshoutbox', $data);
        require APPROOT . '/views/admin/footer.php';
    }
}