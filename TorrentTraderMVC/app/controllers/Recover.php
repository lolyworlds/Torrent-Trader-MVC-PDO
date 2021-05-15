<?php
class Recover extends Controller
{

    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->pdo = new Database();
        $this->valid = new Validation();
    }

    public function index()
    {
        $data = [];
        $this->view('account/recover', $data, true);
    }

    public function submit()
    {
        // check if using google captcha
        (new Captcha)->response($_POST['g-recaptcha-response']);
        if (Input::exist()) {
            $email = Input::get("email");
            if (!$this->valid->validEmail($email)) {
                Session::flash('info', Lang::T("EMAIL_ADDRESS_NOT_VAILD"), URLROOT . "/home");
            } else {
                $arr = $this->userModel->getIdEmailByEmail($email);
                if (!$arr) {
                    Session::flash('info', Lang::T("EMAIL_ADDRESS_NOT_FOUND"), URLROOT . "/home");
                }
                if ($arr) {
                    $sec = mksecret();
                    $id = $arr->id;
                    $username = $arr->username; // 06/01
                    $emailmain = SITEEMAIL;
                    $url = URLROOT;
                    $body = Lang::T("SOMEONE_FROM") . " " . $_SERVER["REMOTE_ADDR"] . " " . Lang::T("MAILED_BACK") . " ($email) " . Lang::T("BE_MAILED_BACK") . " \r\n\r\n " . Lang::T("ACCOUNT_INFO") . " \r\n\r\n " . Lang::T("USERNAME") . ": " . $username . " \r\n " . Lang::T("CHANGE_PSW") . "\n\n$url/recover/confirm?id=$id&secret=$sec\n\n\n" . $url . "\r\n";
                    $TTMail = new TTMail();
                    $TTMail->Send($email, Lang::T("ACCOUNT_DETAILS"), $body, "", "-f$emailmain");
                    $res2 = $this->userModel->setSecret($sec, $email);
                    Session::flash('info', sprintf(Lang::T('MAIL_RECOVER'), htmlspecialchars($email)), URLROOT . "/home");
                }
            }
        }
    }

    public function confirm()
    {
        $data = [];
        $this->view('account/confirm', $data, true);
    }

    public function ok()
    {
        $id = Input::get("id");
        $secret = Input::get("secret");
        if ($this->valid->validId(Input::get("id")) && strlen(Input::get("secret")) == 20) {
            $password = Input::get("password");
            $password1 = Input::get("password1");
            if (empty($password) || empty($password1)) {
                Session::flash('info', Lang::T("NO_EMPTY_FIELDS"), URLROOT . "/home");
            } elseif ($password != $password1) {
                Session::flash('info', Lang::T("PASSWORD_NO_MATCH"), URLROOT . "/home");
            } else {
                $count = $this->pdo->run("SELECT COUNT(*) FROM users WHERE id=? AND secret=?", [$id, $secret])->fetchColumn();
                if ($count != 1) {
                    Session::flash('info', Lang::T("NO_SUCH_USER"), URLROOT . "/home");
                }
                $newsec = mksecret();
                $wantpassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $this->userModel->recoverUpdate($wantpassword, $newsec, $id, $secret);
                Session::flash('info', Lang::T("PASSWORD_CHANGED_OK"), URLROOT . "/home");
            }
        } else {
            Session::flash('info', Lang::T("Wrong Imput"), URLROOT . "/home");
        }
    }
}
