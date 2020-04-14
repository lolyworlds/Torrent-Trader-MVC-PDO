<?php
class Accountlogin extends Controller
{
    // autoload model with constructor
    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function index()
    {
		dbconn();
		// add globals
        global $site_config, $CURUSER;

        $username = $_POST['username'] ?? false;
        $user_password = $_POST['password'] ?? false;
        $message = '';

        if ($username && $user_password) {

            $password = $user_password;
            // called model method/function
			$row = $this->userModel->getUserByUsername($username);

            if (!$row || !password_verify($password, $row["password"])) {
                $message = T_("LOGIN_INCORRECT");
            } elseif ($row["status"] == "pending") {
                $message = T_("ACCOUNT_PENDING");
            } elseif ($row["enabled"] == "no") {
                $message = T_("ACCOUNT_DISABLED");
            }

            if (!$message) {

                logincookie($row["id"], $row["password"], $row["secret"]);
                if (!empty($_POST)) {
                    header("Refresh: 0; url=index.php");
                    die();
                }
            } else {
                show_error_msg(T_("ACCESS_DENIED"), $message, 1);
            }
        }

        logoutcookie();

        stdhead(T_("LOGIN"));

        begin_frame(T_("LOGIN"));

        if ($site_config["MEMBERSONLY"]) {
            $message = T_("MEMBERS_ONLY");
            print("<center><b>" . $message . "</b></center>\n");
        }

		// add view
		$data = [
		  //  we can add data to view 'posts' => $posts
		  ];
		  // load view
		  $this->view('account/login', $data);

        end_frame();
        stdfoot();
    }
	
	public function logout()
    {
        dbconn();
        logoutcookie();
        header("Location: /index.php");
    }
}
