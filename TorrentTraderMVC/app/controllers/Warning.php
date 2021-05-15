<?php
class Warning extends Controller
{
    public function __construct()
    {
        Auth::user();
        $this->userModel = $this->model('User');
        $this->valid = new Validation();
        $this->logs = $this->model('Logs');
    }

    public function index()
    {
        $id = (int) $_GET["id"];
        // VALIDATION STUFF
        if (!$this->valid->validId($id)) {
            Session::flash('info', "Bad ID.", URLROOT . '/members');
        }
        $user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
        if (!$user) {
            Session::flash('info', Lang::T("NO_USER_WITH_ID") . " $id.", URLROOT . '/members');
        }
        if ($_SESSION["view_users"] == "no" && $_SESSION["id"] != $id) {
            Session::flash('info', Lang::T("NO_USER_VIEW"), URLROOT . '/home');
        }
        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $_SESSION["edit_users"] == "no") {
            Session::flash('info', Lang::T("NO_ACCESS_ACCOUNT_DISABLED"), URLROOT . '/members');
        }
        $res = DB::run("SELECT * FROM warnings WHERE userid=? ORDER BY id DESC", [$user['id']]);
        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Helper::userColour($user["username"]));
        $data = [
            'title' => $title,
            'res' => $res,
            'id' => $user['id'],
            'username' => $user['username'],
        ];
        $this->view('warning/index', $data, true);
    }

    public function submit()
    {
        $userid = (int) $_POST["userid"];
        $reason = $_POST["reason"];
        $expiry = (int) $_POST["expiry"];
        $type = $_POST["type"];
        if (!$this->valid->validId($userid)) {
            Session::flash('info', Lang::T("INVALID_USERID"), URLROOT . '/members');
        }
        if (!$reason || !$expiry || !$type) {
            Session::flash('info', Lang::T("MISSING_FORM_DATA"), URLROOT . "/profile?id=$userid");
        }
        $timenow = TimeDate::get_date_time();
        $expiretime = TimeDate::get_date_time(TimeDate::gmtime() + (86400 * $expiry));
        DB::run("INSERT INTO warnings (userid, reason, added, expiry, warnedby, type) VALUES ('$userid','$reason','$timenow','$expiretime','" . $_SESSION['id'] . "','$type')");
        DB::run("UPDATE users SET warned=? WHERE id=?", ['yes', $userid]);
        $msg = "You have been warned by " . $_SESSION["username"] . " - Reason: " . $reason . " - Expiry: " . $expiretime . "";
        $added = TimeDate::get_date_time();
        DB::run("INSERT INTO messages (sender, receiver, msg, added) VALUES(?,?,?,?)", [0, $userid, $msg, $added]);
        Logs::write($_SESSION['username'] . " has added a warning for user: <a href='" . URLROOT . "/profile?id=$userid'>$userid</a>");
        Session::flash('info', "Warning given", URLROOT . "/profile?id=$userid");
        die;
    }

    public function deleteaccount()
    {
        $userid = (int) $_POST["userid"];
        $username = $_POST["username"];
        $delreason = $_POST["delreason"];
        if ($_SESSION["delete_users"] != "yes" ) {
            Session::flash('info', Lang::T("TASK_ADMIN"), URLROOT . "/profile?id=$userid");
        }
        if (!$this->valid->validId($userid)) {
            Session::flash('info', Lang::T("INVALID_USERID"), URLROOT . "/profile?id=$userid");
        }
        if ($_SESSION["id"] == $userid) {
            Session::flash('info', "You cannot delete yourself.", URLROOT . "/profile?id=$userid");
        }
        if (!$delreason) {
            Session::flash('info', Lang::T("MISSING_FORM_DATA"), URLROOT . "/profile?id=$userid");
        }
        $this->userModel->deleteaccount($userid);
        Logs::write($_SESSION['username'] . " has deleted account: $username");
        Session::flash('info', Lang::T("USER_DELETE"), URLROOT . "/profile?id=$userid");
        die;
    }

}