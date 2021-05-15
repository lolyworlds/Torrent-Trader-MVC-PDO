<?php
class Adminadduser extends Controller
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
        if ($_SESSION["class"] < "7") {
            show_error_msg("Error", "Sorry you do not have the rights to view this page!", 1);
        }
        $data = [];
        $this->view('admin/adduser', $data);
    }

    public function addeduserok()
    {
        if (Token::check($_SESSION['ttttt']) == false) {
            show_error_msg("Error", "Please try again.");
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_POST["username"] == "" || $_POST["password"] == "" || $_POST["email"] == "") {
                show_error_msg("Error", "Missing form data.");
            }
            if ($_POST["password"] != $_POST["password2"]) {
                show_error_msg("Error", "Passwords mismatch.");
            }
            $username = $_POST["username"];
            $password = $_POST["password"];
            $email = $_POST["email"];
            $secret = mksecret();
            $passhash = md5($password);
            $secret = $secret;
            /* 
            $count = get_row_count("users", "WHERE username=$username");
            if (!$count !=0) {
            show_error_msg("Error", "Unable to create the account. The user name is possibly already taken.");
            Redirect::to(URLROOT . "/admincp");
            die;
            }
            */
            DB::run("INSERT INTO users (added, last_access, secret, username, password, status, email) VALUES (?,?,?,?,?,?,?)", [TimeDate::get_date_time(), TimeDate::get_date_time(), $secret, $username, $passhash, 'confirmed', $email]);
            Redirect::autolink(URLROOT . "/admincp", Lang::T("COMPLETE"));
        }
    }

}