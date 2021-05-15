<?php
class Adminemailban extends Controller
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
        $remove = (int) $_GET['remove'];
        $valid = new Validation();
        if ($valid->validId($remove)) {
            DB::run("DELETE FROM email_bans WHERE id=$remove");
            Logs::write(sprintf(Lang::T("EMAIL_BANS_REM"), $remove, $_SESSION["username"]));
        }
        if ($_GET["add"] == '1') {
            $mail_domain = trim($_POST["mail_domain"]);
            $comment = trim($_POST["comment"]);
            if (!$mail_domain || !$comment) {
                show_error_msg(Lang::T("ERROR"), Lang::T("MISSING_FORM_DATA") . ".", 0);
                require APPROOT . '/views/admin/footer.php';
                die;
            }
            $mail_domain = $mail_domain;
            $comment = $comment;
            $added = TimeDate::get_date_time();
            $ins = DB::run("INSERT INTO email_bans (added, addedby, mail_domain, comment) VALUES(?,?,?,?)", [$added, $_SESSION['id'], $mail_domain, $comment]);
            Logs::write(sprintf(Lang::T("EMAIL_BANS_ADD"), $mail_domain, $_SESSION["username"]));
            show_error_msg(Lang::T("COMPLETE"), Lang::T("EMAIL_BAN_ADDED"), 0);
            require APPROOT . '/views/admin/footer.php';
            die;
        }

        $count = DB::run("SELECT count(id) FROM email_bans")->fetchColumn();
        $perpage = 40;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, URLROOT . "/admin/emailbans?");
        $title = Lang::T("EMAIL_BANS");
        $res = DB::run("SELECT * FROM email_bans ORDER BY added DESC $data[limit]");
        
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'count' => $count,
            'pagertop' => $pagertop,
            'res' => $res,
            'pagerbottom' => $pagerbottom,
            'limit' => $limit,
        ];
        $this->view('admin/emails/bans', $data);
        require APPROOT . '/views/admin/footer.php';
    }
}