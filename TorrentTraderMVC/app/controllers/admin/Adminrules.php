<?php
class Adminrules extends Controller
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
        $res = DB::run("SELECT * FROM rules ORDER BY id");
        $title = Lang::T("SITE_RULES_EDITOR");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin(Lang::T("SITE_RULES_EDITOR"));
        $data = [
            'res' => $res,
        ];
        $this->view('admin/rulesview', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function rulesedit()
    {
        if ($_GET["save"] == "1") {
            $id = (int) $_POST["id"];
            $title = $_POST["title"];
            $text = $_POST["text"];
            $public = $_POST["public"];
            $class = $_POST["class"];
            DB::run("update rules set title=?, text=?, public=?, class=? where id=?", [$title, $text, $public, $class, $id]);
            Logs::write("Rules have been changed by ($_SESSION[username])");
            show_error_msg(Lang::T("COMPLETE"), "Rules edited ok<br /><br /><a href=" . URLROOT . "/adminrules>Back To Rules</a>", 1);
            die;
        }
        $id = (int) $_POST["id"];
        $res = DB::run("select * from rules where id='$id'");
        $title = Lang::T("SITE_RULES_EDITOR");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin("Edit Rule Section");
        $data = [
            'id' => $id,
            'res' => $res,
        ];
        $this->view('admin/rulesedit', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function rulesaddsect()
    {
        if ($_GET["save"] == "1") {
            $title = $_POST["title"];
            $text = $_POST["text"];
            $public = $_POST["public"];
            $class = $_POST["class"];
            DB::run("insert into rules (title, text, public, class) values(?,?,?,?)", [$title, $text, $public, $class]);
            show_error_msg(Lang::T("COMPLETE"), "New Section Added<br /><br /><a href=" . URLROOT . "/adminrules>Back To Rules</a>", 1);
            die();
        }
        $title = Lang::T("SITE_RULES_EDITOR");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin(Lang::T("ADD_NEW_RULES_SECTION"));
        $data = [];
        $this->view('admin/rulesaddsect', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

}