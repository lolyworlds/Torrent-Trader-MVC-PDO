<?php
class Contactstaff extends Controller
{
    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
        $this->valid = new Validation();
    }

    public function index()
    {
        if ((isset($_POST["msg"])) & (isset($_POST["sub"]))) {
            $msg = trim($_POST["msg"]);
            $sub = trim($_POST["sub"]);
            $error_msg = "";
            if (!$msg) {
                $error_msg = $error_msg . "You did not put message.</br>";
            }
            if (!$sub) {
                $error_msg = $error_msg . "You did not put subject.</br>";
            }
            if ($error_msg != "") {
                Session::flash('info', "Your message can not be sent:$error_msg</br>", 1);
            } else {
                $added = TimeDate::get_date_time();
                $userid = $_SESSION['id'];
                $req = DB::run("INSERT INTO staffmessages (sender, added, msg, subject) VALUES(?,?,?,?)", [$userid, $added, $msg, $sub]);
                if ($req) {
                    Session::flash('info', 'Your message has been sent. We will reply as soon as possible.', 1);
                } else {
                    Session::flash('info', 'We are busy. try again later', 1);
                }
            }
        } else {
            $data = [
            ];
            $this->view('contact/index', $data, true);
        }
    }

    public function staffbox()
    {
        if ($_SESSION["class"] < _MODERATOR) {
            show_error_msg("Error", "Permission denied.", 1);
        }
        $res = DB::run("SELECT staffmessages.id, staffmessages.added, staffmessages.subject, staffmessages.answered, staffmessages.answeredby, staffmessages.sender, staffmessages.answer, users.username FROM staffmessages INNER JOIN users on staffmessages.sender = users.id ORDER BY id desc");
        $data = [
            'res' => $res,
        ];
        $this->view('contact/staff', $data, true);
    }

    public function viewpm()
    {
        if ($_SESSION["class"] < _MODERATOR) {
            show_error_msg("Error", "Permission denied.", 1);
        }
        $pmid = (int) $_GET["pmid"];
        $ress4 = DB::run("SELECT id, subject, sender, added, msg, answeredby, answered FROM staffmessages WHERE id=$pmid");
        $arr4 = $ress4->fetch(PDO::FETCH_ASSOC);
        $answeredby = $arr4["answeredby"];
        $rast = DB::run("SELECT username FROM users WHERE id=$answeredby");
        $arr5 = $rast->fetch(PDO::FETCH_ASSOC);
        $senderr = "" . $arr4["sender"] . "";
        if ($this->valid->validId($arr4["sender"])) {
            $res2 = DB::run("SELECT username FROM users WHERE id=" . $arr4["sender"]);
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $sender = "<a href='" . URLROOT . "/profile/read?id=$senderr'>" . ($arr2["username"] ? $arr2["username"] : "[Deleted]") . "</a>";
        } else {
            $sender = "System";
        }
        $subject = $arr4["subject"];
        if ($arr4["answered"] == '0') {
            $answered = "<font color=red><b>No</b></font>";
        } else {
            $answered = "<font color=blue><b>Yes</b></font> by <a href='" . URLROOT . "/profile/read?id=$answeredby>" . Users::coloredname($arr5['username']) . "</a> (<a href=" . URLROOT . "/contactstaff/viewanswer?pmid=$pmid>Show Answer</a>)";
        }
        if ($arr4["answered"] == '0') {
            $setanswered = "[<a href=" . URLROOT . "/contactstaff/setanswered?id=$arr4[id]>Mark Answered</a>]";
        } else {
            $setanswered = "";
        }
        $iidee = $arr4["id"];
        $elapsed = TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr4["added"]));
        $data = [
            'elapsed' => $elapsed,
            'sender' => $sender,
            'added' => $arr4["added"],
            'subject' => $subject,
            'answeredby' => $answeredby,
            'answered' => $answered,
            'setanswered' => $setanswered,
            'msg' => $arr4["msg"],
            'sender1' => $arr4["sender"],
            'iidee' => $iidee,
            'id' => $arr4["id"],
        ];
        $this->view('contact/viewpm', $data, true);
    }

    public function viewanswer()
    {
        if ($_SESSION["class"] < _MODERATOR) {
            show_error_msg("Error", "Permission denied.", 1);
        }
        $pmid = (int) $_GET["pmid"];
        $ress4 = DB::run("SELECT id, subject, sender, added, msg, answeredby, answered, answer FROM staffmessages WHERE id=$pmid");
        $arr4 = $ress4->fetch(PDO::FETCH_ASSOC);
        $answeredby = $arr4["answeredby"];
        $rast = DB::run("SELECT username FROM users WHERE id=$answeredby");
        $arr5 = $rast->fetch(PDO::FETCH_ASSOC);
        if ($this->valid->validId($arr4["sender"])) {
            $res2 = DB::run("SELECT username FROM users WHERE id=" . $arr4["sender"]);
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $sender = "<a href=" . URLROOT . "/profile?id=" . $arr4["sender"] . ">" . ($arr2["username"] ? $arr2["username"] : "[Deleted]") . "</a>";
        } else {
            $sender = "System";
        }
        if ($arr4['subject'] == "") {
            $subject = "No subject";
        } else {
            $subject = "<a style='color: darkred' href=staffbox.php?action=viewpm&pmid=$pmid>$arr4[subject]</a>";
        }
        $iidee = $arr4["id"];
        if ($arr4['answer'] == "") {
            $answer = "This message has not been answered yet!";
        } else {
            $answer = $arr4["answer"];
        }
        $data = [
            'answer' => $answer,
            'subject' => $subject,
            'iidee' => $iidee,
            'sender' => $sender,
            'answeredby' => $answeredby,
        ];
        $this->view('contact/viewanswer', $data, true);
    }

    public function answermessage()
    {
        if ($_SESSION["class"] < _MODERATOR) {
            show_error_msg("Error", "Permission denied.", 1);
        }
        $answeringto = $_GET["answeringto"];
        $receiver = (int) $_GET["receiver"];
        if (!$this->valid->validId($receiver)) {
            die;
        }
        $res = DB::run("SELECT * FROM users WHERE id=$receiver");
        $res2 = DB::run("SELECT * FROM staffmessages WHERE id=$answeringto");
        $data = [
            'res' => $res,
            'res2' => $res2,
            'answeringto' => $answeringto,
            'receiver' => $receiver,
        ];
        $this->view('contact/answermessage', $data, true);
    }

    public function takeanswer()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            show_error_msg("Error", "Method", 1);
        }
        if ($_SESSION["class"] < _MODERATOR) {
            show_error_msg("Error", "Permission denied.", 1);
        }
        $receiver = (int) $_POST["receiver"];
        $answeringto = $_POST["answeringto"];
        if (!$this->valid->validId($receiver)) {
            show_error_msg("Error", "Invalid ID", 1);
        }
        $userid = $_SESSION["id"];
        $msg = trim($_POST["msg"]);
        $message = $msg;
        $added = TimeDate::get_date_time();
        if (!$msg) {
            show_error_msg("Error", "Please enter something!", 1);
        }
        DB::run("UPDATE staffmessages SET answer=? WHERE id=?", [$message, $answeringto]);
        DB::run("UPDATE staffmessages SET answered=?, answeredby=? WHERE id=?", [1, $userid, $answeringto]);
        $smsg = "Staff Message $answeringto has been answered.";
        Redirect::autolink(URLROOT . '/contactstaff/staffbox', $smsg);
        die;
    }

    public function deletestaffmessage()
    {
        if ($_SESSION["class"] < _MODERATOR) {
            show_error_msg("Error", "Permission denied.", 1);
        }
        $id = (int) $_GET["id"];
        if (!is_numeric($id) || $id < 1 || floor($id) != $id) {
            die;
        }
        DB::run("DELETE FROM staffmessages WHERE id=?", [$id]);
        $smsg = "Staff Message $id has been deleted.";
        Redirect::autolink(URLROOT . "/contactstaff/staffbox", $smsg);
        die;
    }

    public function setanswered()
    {
        if ($_SESSION["class"] < _MODERATOR) {
            show_error_msg("Error", "Permission denied.", 1);
        }
        $id = (int) $_GET["id"];
        DB::run("UPDATE staffmessages SET answered=1, answeredby = $_SESSION[id] WHERE id = $id");
        $smsg = "Staff Message $id has been set as answered.";
        Redirect::autolink(URLROOT . "/contactstaff/viewpm?pmid=$id", $smsg);
        die;
    }

    public function takecontactanswered()
    {
        if ($_SESSION["class"] < _MODERATOR) {
            show_error_msg("Error", "Permission denied.", 1);
        }
        $res = DB::run("SELECT id FROM staffmessages WHERE answered=0 AND id IN (" . implode(", ", $_POST['setanswered']) . ")");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            DB::run("UPDATE staffmessages SET answered=?, answeredby =?  WHERE id =?", [1, $_SESSION['id'], $arr['id']]);
        }
        $smsg = "Staff Messages have been marked as answered.";
        Redirect::autolink("staffbox", $smsg);
        die;
    }

}
