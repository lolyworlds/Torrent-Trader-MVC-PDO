<?php
class Invite extends Controller
{

    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
        $this->valid = new Validation();
    }

    public function index()
    {
        if (!INVITEONLY && !ENABLEINVITES) {
            show_error_msg(Lang::T("INVITES_DISABLED"), Lang::T("INVITES_DISABLED_MSG"), 1);
        }
        $users = get_row_count("users", "WHERE enabled = 'yes'");
        if ($users >= MAXUSERSINVITE) {
            show_error_msg(Lang::T("ERROR"), "Sorry, The current user account limit (" . number_format(MAXUSERSINVITE) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...", 1);
        }
        if ($_SESSION["invites"] == 0) {
            show_error_msg(Lang::T("YOU_HAVE_NO_INVITES"), Lang::T("YOU_HAVE_NO_INVITES_MSG"), 1);
        }
        $data = [];
        $this->view('invite/index', $data, true);
    }

    public function submit()
    {
        if ($_GET["take"]) {
            $email = $_POST["email"];
            if (!$this->valid->validEmail($email)) {
                show_error_msg(Lang::T("ERROR"), Lang::T("INVALID_EMAIL_ADDRESS"), 1);
            }
            //check email isnt banned
            $maildomain = (substr($email, strpos($email, "@") + 1));
            $a = DB::run("select count(*) from email_bans where mail_domain=?", [$email])->fetch();
            if ($a[0] != 0) {
                $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED"), $email);
            }
            $a = DB::run("select count(*) from email_bans where mail_domain=?", [$maildomain])->fetch();
            if ($a[0] != 0) {
                $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED"), $email);
            }
            // check if email addy is already in use
            if (get_row_count("users", "WHERE email='$email'")) {
                $message = sprintf(Lang::T("EMAIL_ADDRESS_INUSE"), $email);
            }
            if ($message) {
                show_error_msg(Lang::T("ERROR"), $message, 1);
            }

            $secret = mksecret();
            $username = "invite_" . mksecret(20);
            $ret = DB::run("INSERT INTO users (username, secret, email, status, invited_by, added, stylesheet, language) VALUES (?,?,?,?,?,?,?,?)",
            [$username, $secret, $email, 'pending', $_SESSION["id"], TimeDate::get_date_time(), DEFAULTTHEME, DEFAULTLANG]);
            $id = DB::lastInsertId();
            $invitees = "$id $_SESSION[invitees]";
            DB::run("UPDATE users SET invites = invites - 1, invitees='$invitees' WHERE id = $_SESSION[id]");
            $mess = strip_tags($_POST["mess"]);
                    $names = SITENAME;
                    $links = URLROOT;
                    $emailmain = SITEEMAIL;
            $body = <<<EOD
        You have been invited to $names by $_SESSION[username]. They have specified this address ($email) as your email.
        If you do not know this person, please ignore this email. Please do not reply.
        
        Message:
        -------------------------------------------------------------------------------
        $mess
        -------------------------------------------------------------------------------
        
        This is a private site and you must agree to the rules before you can enter:
        
        $links/rules.php
        $links/faq.php
        
        
        To confirm your invitation, you have to follow this link:
        
        $links/signup?invite=$id&secret=$secret
        
        After you do this, you will be able to use your new account. If you fail to
        do this, your account will be deleted within a few days. We urge you to read
        the RULES and FAQ before you start using $names.
        EOD;
        $TTMail = new TTMail();
                    $TTMail->Send($email, "$names user registration confirmation", $body, "", "-f$emailmain");
        
                    Session::flash('info', Lang::T("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT") . " (" . htmlspecialchars($email) . "). " . Lang::T("ACCOUNT_CONFIRM_SENT_TO_ADDY_REST") . " <br/ >", URLROOT."/home");
            die;
        }

    }

    public function invitetree()
    {
        $id = $_GET["id"];
        if (!$this->valid->validId($id)) {
            $id = $_SESSION["id"];
        }
        $res = DB::run("SELECT * FROM users WHERE status = 'confirmed' AND invited_by = $id ORDER BY username");
        $num = $res->rowCount();
        $invitees = number_format(get_row_count("users", "WHERE status = 'confirmed' && invited_by = $id"));
        if ($invitees == 0) {
            Session::flash('info', "This member has no invitees", URLROOT."/profile?id=$id");
        }

        Style::header("Invite Tree for " . $id . "");
        $data = [
            'id' => $id,
            'invitees' => $invitees,
            'res' => $res,
            'num' => $num,
        ];
        $this->view('invite/tree', $data, true);
        Style::footer();
    }
}