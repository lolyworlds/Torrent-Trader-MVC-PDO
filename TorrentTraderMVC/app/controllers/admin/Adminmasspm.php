<?php
class Adminmasspm extends Controller
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
        //var_dump($_POST); die();
        //send pm
        if ($_GET["send"] == '1') {
            $sender_id = ($_POST['sender'] == 'system' ? 0 : $_SESSION['id']);
            $dt = TimeDate::get_date_time();
            $msg = $_POST['msg'];
            $subject = $_POST["subject"];
            if (!$msg) {
                show_error_msg(Lang::T("ERROR"), "Please Enter Something!", 1);
            }
            $updateset = array_map("intval", $_POST['clases']);
            $query = DB::run("SELECT id FROM users WHERE class IN (" . implode(",", $updateset) . ") AND enabled = 'yes' AND status = 'confirmed'");
            while ($dat = $query->fetch(PDO::FETCH_ASSOC)) {
                DB::run("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES (?,?,?,?,?)", [$sender_id, $dat['id'], TimeDate::get_date_time(), $msg, $subject]);
            }
            Logs::write("A Mass PM was sent by ($_SESSION[username])");
            Redirect::autolink(URLROOT . "/adminmasspm", Lang::T("SUCCESS"), "Mass PM Sent!");
            die;
        }
        $res = DB::run("SELECT group_id, level FROM groups");
        $title = Lang::T("Mass Private Message");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin("Mass Private Message");
        $data = [
            'res' => $res,
        ];
        $this->view('admin/masspm', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }
}