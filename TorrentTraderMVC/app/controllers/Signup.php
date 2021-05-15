<?php
class Signup extends Controller
{
    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->pdo = new Database();
        $this->valid = new Validation();
        $this->countriesModel = $this->model('Countries');
    }

    public function index()
    {
/*//check if IP is already a peer
if (IPCHECK) {
$ip = $_SERVER['REMOTE_ADDR'];
$ipq = get_row_count("users", "WHERE ip = '$ip'");
if ($ipq >= ACCOUNTMAX) {
Session::flash('info', "This IP is already in use !", URLROOT."/login");
}
}*/
        // Disable checks if we're signing up with an invite
        if (!$this->valid->validId($_REQUEST["invite"]) || strlen($_REQUEST["secret"]) != 20) {
            if (INVITEONLY) {
                Session::flash('info', "<center>".Lang::T("INVITE_ONLY_MSG")."<br></center>", URLROOT . "/home");
            }
        } else {
            $stmt = $this->pdo->run("SELECT id FROM users WHERE id = ? AND secret = ?", [$_REQUEST['invite'], $_REQUEST["secret"]]);
            $invite_row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$invite_row) {
                Session::flash('info', Lang::T("INVITE_ONLY_NOT_FOUND")."".(SIGNUPTIMEOUT / 86400)."days.", URLROOT . "/home");
            }
        }
        $data = [
            'invite' => $invite_row,
        ];
        $this->view('account/signup', $data, true);
    }

    public function submit()
    {
        if (Input::exist()) {
            $wantusername = Input::get("wantusername");
            $email = Input::get("email");
            $wantpassword = Input::get("wantpassword");
            $passagain = Input::get("passagain");
            $country = Input::get("country");
            $gender = Input::get("gender");
            $client = Input::get("client");
            $age = Input::get("age");

            $secret = Input::get("secret");
            $invite = Input::get("invite");
            if (strlen($secret) == 20 || !is_numeric($invite)) {
                $stmt = $this->pdo->run("SELECT id FROM users WHERE id = ? AND secret = ?", [$invite, $secret]);
                $invite_row = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            $message = $this->validSign($wantusername, $email, $wantpassword, $passagain);
            if ($message == "") {
                // Certain checks must be skipped for invites
                if (!$invite_row) {
                    // get max members, and check how many users there is
                    $numsitemembers = get_row_count("users");
                    if ($numsitemembers >= MAXUSERS) {
                        $mess = Lang::T("SITE_FULL_LIMIT_MSG") . number_format(MAXUSERS) . " " . Lang::T("SITE_FULL_LIMIT_REACHED_MSG") . " " . number_format($numsitemembers) . " members";
                        Session::flash('info', $mess, URLROOT . "/home");
                    }
                    // check email isnt banned
                    $maildomain = (substr($email, strpos($email, "@") + 1));
                    $a = $this->pdo->run("SELECT count(*) FROM email_bans where mail_domain=?", [$email])->fetchColumn();
                    if ($a != 0) {
                        $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED_S"), $email);
                    }
                    $a = $this->pdo->run("SELECT count(*) FROM email_bans where mail_domain LIKE '%$maildomain%'")->fetchColumn();
                    if ($a != 0) {
                        $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED_S"), $email);
                    }
                    // check if email addy is already in use
                    $a = $this->pdo->run("SELECT count(*) FROM users where email=?", [$email])->fetchColumn();
                    if ($a != 0) {
                        $message = sprintf(Lang::T("EMAIL_ADDRESS_INUSE_S"), $email);
                    }
                }
                $secret = mksecret(); //generate secret field
                $wantpassword = password_hash($wantpassword, PASSWORD_BCRYPT); // hash the password
            }
            if ($message != "") {
                Session::flash('info', $message, URLROOT . "/login");
            }

            if ($message == "") {
                if ($invite_row) {
                    $this->pdo->run("
			            UPDATE users
			            SET username=?, password=?, secret=?, status=?, added=?
                        WHERE id=?",
                        [$wantusername, $wantpassword, $secret, 'confirmed', TimeDate::get_date_time(), $invite_row['id']]);
                    //send pm to new user
                    if (WELCOMEPM_ON) {
                        $dt = TimeDate::get_date_time();
                        $msg = WELCOMEPM_MSG;
                        $this->pdo->run("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $invite_row[id], $dt, $msg, 0)");
                    }
                    Session::flash('info', Lang::T("ACCOUNT_ACTIVATED"), URLROOT . "/login");
                    die;
                }
                if (CONFIRMEMAIL) {
                    $status = "pending";
                } else {
                    $status = "confirmed";
                }
                //make first member admin
                if ($numsitemembers == '0') {
                    $signupclass = '7';
                } else {
                    $signupclass = '1';
                    // Shout new user
                    $msg_shout = "New User: " . $wantusername . " has joined.";
                    $this->pdo->run("INSERT INTO shoutbox (userid, date, user, message) VALUES(?,?,?,?)", [0, TimeDate::get_date_time(), 'System', $msg_shout]);
                }

                DB::run("
                    INSERT INTO users
                    (username, password, secret, email, status, added, last_login,
                    last_access, age, country, gender, client, stylesheet, language, class, ip)
                    VALUES
                    (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [$wantusername, $wantpassword, $secret, $email, $status, TimeDate::get_date_time(),
                    TimeDate::get_date_time(), TimeDate::get_date_time(), $age, $country, $gender,
                    $client, DEFAULTTHEME, DEFAULTLANG, $signupclass, Helper::getIP()]);
                $id = DB::lastInsertId();

                if (ACONFIRM) {
                    $body = Lang::T("YOUR_ACCOUNT_AT") . " " . SITENAME . " " . Lang::T("HAS_BEEN_CREATED_YOU_WILL_HAVE_TO_WAIT") . "\n\n" . SITENAME . " " . Lang::T("ADMIN");
                } else { //NO ADMIN CONFIRM, BUT EMAIL CONFIRM
                    $body = Lang::T("YOUR_ACCOUNT_AT") . " " . SITENAME . " " . Lang::T("HAS_BEEN_APPROVED_EMAIL") . "\n\n	" . URLROOT . "/confirm?id=$id&secret=$secret\n\n" . Lang::T("HAS_BEEN_APPROVED_EMAIL_AFTER") . "\n\n	" . Lang::T("HAS_BEEN_APPROVED_EMAIL_DELETED") . "\n\n" . URLROOT . " " . Lang::T("ADMIN");
                }
                if (CONFIRMEMAIL) {
                    $TTMail = new TTMail();
                    $TTMail->Send($email, "Your " . SITENAME . " User Account", $body, "", "-f" . SITEEMAIL . "");
                    Session::flash('info', Lang::T("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT") . " (" . htmlspecialchars($email) . "). " . Lang::T("ACCOUNT_CONFIRM_SENT_TO_ADDY_REST") . " <br/ >", URLROOT . "/login");
                } else {
                    Session::flash('info', Lang::T("ACCOUNT_ACTIVATED"), URLROOT . "/login");
                }
                //send pm to new user
                if (WELCOMEPM_ON) {
                    $dt = TimeDate::get_date_time();
                    $msg = WELCOMEPM_MSG;
                    $this->pdo->run("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(?,?,?,?,?,?)", [0, $id, $dt, $msg, 0, 'Welcome']);
                }
                die;
            }
        } else {
            Redirect::to(URLROOT . "/signup");
        }
    }

    public function validSign($wantusername, $email, $wantpassword, $passagain)
    {
        if ($this->valid->isEmpty($wantusername)) {
            $message = "Please enter the account.";
        }
        if ($this->valid->isEmpty($email)) {
            //$message = "Please enter an email.";
        }
        if ($this->valid->isEmpty($wantpassword)) {
            $message = "Please enter a password.";
        }
        if ($this->valid->isEmpty($passagain)) {
            $message = "Please enter the second password.";
        }
        if ($wantpassword != $passagain) {
            $message = "The passwords do not match.";
        }
        if (strlen($wantpassword) < 6 || strlen($passagain) > 16) {
            $message = "Password must be between 6 and 16 characters long.";
        }
        if (strlen($wantusername) < 3 && strlen($wantusername) > 25) {
            $message = "User can have between 3 and 25 characters.";
        }
        if (!ctype_alnum($wantusername)) {
            $message = "The username can only contain letters and numbers with no space.";
        }
        return $message;
    }

}