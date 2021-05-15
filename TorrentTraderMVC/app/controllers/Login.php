<?php
class Login extends Controller
{

    public function __construct()
    {
        Auth::ipBanned();
        Auth::isClosed();
		$this->userModel = $this->model('User');
    }

    public function index()
    {
        $data = [
            'title' => Lang::T("LOGIN")
        ];
        $this->view('account/login', $data, true);
    }

    public function submit() {
        if (Token::check($_SESSION['ttttt']) == false) {
            show_error_msg("Error", "Issue with token. Please try again.");
        }
        // check if using google captcha
        (new Captcha)->response($_POST['g-recaptcha-response']);
        
        if (Input::exist()) {
            $username = Input::get("username");
            $password = Input::get("password");
            $sql = $this->userModel->getUserByUsername($username);
            if (!$sql || !password_verify($password, $sql->password)) {
                $message = Lang::T("LOGIN_INCORRECT");
            } elseif ($sql->status == "pending") {
                $message = Lang::T("ACCOUNT_PENDING");
            } elseif ($sql->enabled == "no") {
                $message = Lang::T("ACCOUNT_DISABLED");
            }

            if (!$message) {
                Cookie::set1('id', $sql->id, 58585858);
                Cookie::set1('password', $sql->password, 58585858);
                Cookie::set1("login_fingerprint", $this->loginString(), 58585858);
                $token = $sql->password;
                //$this->userModel->updatelogin($sql->id);
                DB::run("UPDATE users SET last_login=?,token=? WHERE id=?", [Helper::get_date_time(), $token, $sql->id]);
                Redirect::to(URLROOT."/home");
            } else {
                Session::flash('info', $message, URLROOT."/login");
            }
        } else {
            Redirect::to(URLROOT."/login");
        }
    }

    private function loginString()
    {
        $ip = Helper::getIP();
        $browser = Helper::browser();
        return hash("sha512", $ip, $browser);
    }
}