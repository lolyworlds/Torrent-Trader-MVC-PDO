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
	
	
   public function index(){
		// Set Current User
		// $curuser = $this->userModel->setCurrentUser();
		// Set Current User
		// $db = new Database;
dbconn();
global $site_config, $CURUSER;
$kind = '0';

if (is_valid_id($_POST["id"]) && strlen($_POST["secret"]) == 32) {
    $password = $_POST["password"];
    $password1 = $_POST["password1"];
    if (empty($password) || empty($password1)) {
        $kind = T_("ERROR");
        $msg =  T_("NO_EMPTY_FIELDS");
    } elseif ($password != $password1) {
        $kind = T_("ERROR");
        $msg = T_("PASSWORD_NO_MATCH");
    } else {
	$n = get_row_count("users", "WHERE `id`=".intval($_POST["id"])." AND MD5(`secret`) = ".sqlesc($_POST["secret"]));
	if ($n != 1)
		show_error_msg(T_("ERROR"), T_("NO_SUCH_USER"));
        $newsec = mksecret();
        $wantpassword = password_hash($password, PASSWORD_BCRYPT);
        DB::run("UPDATE `users` SET `password` =?, `secret` =? WHERE `id`=? AND secret =?", [$wantpassword, $newsec, $_POST['id'], $_POST["secret"]]);
        $kind = T_("SUCCESS");
        $msg =  T_("PASSWORD_CHANGED_OK");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_GET["take"] == 1) {
    $email = trim($_POST["email"]);

    if (!validemail($email)) {
        $msg = T_("EMAIL_ADDRESS_NOT_VAILD");
        $kind = T_("ERROR");
    }else{
        $arr = DB::run("SELECT id, username, email FROM users WHERE email=? LIMIT 1", [$email])->fetch();
        if (!$arr) {
            $msg = T_("EMAIL_ADDRESS_NOT_FOUND");
            $kind = T_("ERROR");
        }

        if ($arr) {
              $sec = mksecret();
            $secmd5 = md5($sec);
            $id = $arr['id'];

            $body = T_("SOMEONE_FROM")." " . $_SERVER["REMOTE_ADDR"] . " ".T_("MAILED_BACK")." ($email) ".T_("BE_MAILED_BACK")." \r\n\r\n ".T_("ACCOUNT_INFO")." \r\n\r\n ".T_("USERNAME").": ".class_user($arr["username"])." \r\n ".T_("CHANGE_PSW")."\n\n$site_config[SITEURL]/accountrecover?id=$id&secret=$secmd5\n\n\n".$site_config["SITENAME"]."\r\n";
            
            @sendmail($arr["email"], T_("ACCOUNT_DETAILS"), $body, "", "-f".$site_config['SITEEMAIL']);
            $res2 =DB::run("UPDATE `users` SET `secret` =? WHERE `email`=? LIMIT 1", [$sec, $email]);
            $msg = sprintf(T_('MAIL_RECOVER'), htmlspecialchars($email));
            $kind = T_("SUCCESS");
        }
    }
}

stdhead();

begin_frame(T_("RECOVER_ACCOUNT"));
if ($kind != "0") {
    show_error_msg("Notice", "$kind: $msg", 0);
}

if (is_valid_id($_GET["id"]) && strlen($_GET["secret"]) == 32) {?>
<form method="post" action="/accountrecover">
<table border="0" cellspacing="0" cellpadding="5">
    <tr>
        <td>
            <b><?php echo T_("NEW_PASSWORD"); ?></b>:
        </td>
        <td>
            <input type="hidden" name="secret" value="<?php echo $_GET['secret']; ?>" />
            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
            <input type="password" size="40" name="password" />
        </td>
    </tr>
    <tr>
        <td>
            <b><?php echo T_("REPEAT"); ?></b>:
        </td>
        <td>
            <input type="password" size="40" name="password1" />
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><input type="submit" value="<?php echo T_("SUBMIT"); ?>" /></td>
    </tr>
</table>
</form>
<?php } else { echo T_("USE_FORM_FOR_ACCOUNT_DETAILS"); ?>

<form method="post" action="/accountrecover?take=1">
    <table border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td><b><?php echo T_("EMAIL_ADDRESS"); ?>:</b></td>
            <td><input type="text" size="40" name="email" />&nbsp;<input type="submit" value="<?php echo T_("SUBMIT");?>" /></td>
        </tr>
    </table>
</form>

<?php
}
end_frame();
stdfoot();
    }

    public function index(){
		// Set Current User
		// $curuser = $this->userModel->setCurrentUser();
		// Set Current User
		// $db = new Database;
dbconn();
global $site_config, $CURUSER;
$id = (int) $_GET["id"];
$md5 = $_GET["secret"];
$email = $_GET["email"];

if (!$id || !$md5 || !$email) {
	show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA"), 1);
}

$row = DB::run("SELECT `editsecret` FROM `users` WHERE `enabled` =? AND `status` =? AND `editsecret` !=?  AND `id` =?", ['yes', 'confirmed', '', $id])->fetch();

if (!$row) {
	show_error_msg(T_("ERROR"), T_("NOTHING_FOUND"), 1);
}

$sec = $row["editsecret"];

if ($md5 != md5($sec . $email . $sec))
    show_error_msg(T_("ERROR"), T_("NOTHING_FOUND"), 1);

DB::run("UPDATE `users` SET `editsecret` =?, `email` =? WHERE `id` =? AND `editsecret` =?", ['', $email, $id, $row["editsecret"]]);

header("Refresh: 0; url=/account");
header("Location: /account");
	}
	
	
    public function index(){
		// Set Current User
		// $curuser = $this->userModel->setCurrentUser();
		// Set Current User
		// $db = new Database;
dbconn();
global $site_config, $CURUSER;
$id = (int) $_GET["id"];
$md5 = $_GET["secret"];

if (!$id || !$md5)
	show_error_msg(T_("ERROR"), T_("INVALID_ID"), 1);

$row = DB::run("SELECT `password`, `secret`, `status` FROM `users` WHERE `id` =?", [$id])->fetch();
if (!$row)
	show_error_msg(T_("ERROR"), sprintf(T_("CONFIRM_EXPIRE"), $site_config['signup_timeout']/86400), 1);

if ($row["status"] != "pending") {
	header("Refresh: 0; url=/accountconfirmok?type=confirmed");
	die;
}

if ($md5 != md5($row["secret"]))
	show_error_msg(T_("ERROR"), T_("SIGNUP_ACTIVATE_LINK"), 1);

$secret = mksecret();

$upd = DB::run("UPDATE `users` SET `secret` =?, `status` =? WHERE `id` =? AND `secret` =? AND `status` =?", [$secret, 'confirmed', $id, $row["secret"], 'pending']);
if (!$upd)
	show_error_msg(T_("ERROR"), T_("SIGNUP_UNABLE"), 1);

header("Refresh: 0; url=/accountconfirmok?type=confirm");
	}

    public function index(){
		// Set Current User
		// $curuser = $this->userModel->setCurrentUser();
		// Set Current User
		// $db = new Database;
dbconn();
global $site_config, $CURUSER;
$type = $_GET["type"];
$email = $_GET["email"];

if (!$type)
	die;

if ($type =="noconf"){ //email conf is disabled?
	stdhead(T_("ACCOUNT_ALREADY_CONFIRMED"));
	begin_frame(T_("PLEASE_NOW_LOGIN"));
	print(T_("PLEASE_NOW_LOGIN_REST"));
	end_frame();
	stdfoot();
	die();
}

if ($type == "signup" && validemail($email)) {
	stdhead(T_("ACCOUNT_USER_SIGNUP"));
 begin_frame(T_("ACCOUNT_SIGNUP_SUCCESS"));
        if (!$site_config["ACONFIRM"]) {
            print(T_("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT"). " (" . htmlspecialchars($email) . "). " .T_("ACCOUNT_CONFIRM_SENT_TO_ADDY_REST"). " <br/ >");
        } else {
            print(T_("EMAIL_CHANGE_SEND"). " (" . htmlspecialchars($email) . "). " .T_("ACCOUNT_CONFIRM_SENT_TO_ADDY_ADMIN"). " <br/ >");
        }
    end_frame();
}
elseif ($type == "confirmed") {
	stdhead(T_("ACCOUNT_ALREADY_CONFIRMED"));
        begin_frame(T_("ACCOUNT_ALREADY_CONFIRMED"));
	print(T_("ACCOUNT_ALREADY_CONFIRMED"). "\n");
	end_frame();
}

//invite code
elseif ($type == "invite" && $_GET["email"]) {
stdhead(T_("INVITE_USER"));
     begin_frame();
		Print("<center>".T_("INVITE_SUCCESSFUL")."!</center><br /><br />".T_("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT")." (" . htmlspecialchars($email) . "). ".T_("THEY_NEED_TO_READ_AND_RESPOND_TO_THIS_EMAIL")."");
	end_frame();
stdfoot();
die;
}//end invite code

elseif ($type == "confirm") {
	if (isset($CURUSER)) {
		stdhead(T_("ACCOUNT_SIGNUP_CONFIRMATION"));
		begin_frame(T_("ACCOUNT_SUCCESS_CONFIRMED"));
		print(T_("ACCOUNT_ACTIVATED"). " <a href='". $site_config["SITEURL"] ."/index.php'>" .T_("ACCOUNT_ACTIVATED_REST"). "\n");
		print(T_("ACCOUNT_BEFOR_USING"). " " . $site_config["SITENAME"] . " " .T_("ACCOUNT_BEFOR_USING_REST")."\n");
		end_frame();
	}
	else {
		stdhead(T_("ACCOUNT_SIGNUP_CONFIRMATION"));
		begin_frame(T_("ACCOUNT_SUCCESS_CONFIRMED"));
		print(T_("ACCOUNT_ACTIVATED"));
		end_frame();
	}
}
else
	die();

stdfoot();
	}


  public function index(){
	  // Set Current User
	  // $curuser = $this->userModel->setCurrentUser();
	  // Set Current User
	  // $db = new Database;
dbconn();
global $site_config, $CURUSER;
$username_length = 15; // Max username length. You shouldn't set this higher without editing the database first
$password_minlength = 6;
$password_maxlength = 60;

// Disable checks if we're signing up with an invite
if (!is_valid_id($_REQUEST["invite"]) || strlen($_REQUEST["secret"]) != 32) {
	//invite only check
	if ($site_config["INVITEONLY"]) {
		show_error_msg(T_("INVITE_ONLY"), "<br /><br /><center>".T_("INVITE_ONLY_MSG")."<br /><br /></center>",1);
	}

	//get max members, and check how many users there is
	$numsitemembers = get_row_count("users");
	if ($numsitemembers >= $site_config["maxusers"])
		show_error_msg(T_("SORRY")."...", T_("SITE_FULL_LIMIT_MSG") . number_format($site_config["maxusers"])." ".T_("SITE_FULL_LIMIT_REACHED_MSG")." ".number_format($numsitemembers)." members",1);
} else {
	    $stmt = DB::run("SELECT id FROM users WHERE id = $_REQUEST[invite] AND secret = ".sqlesc($_REQUEST["secret"]));
        $invite_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invite_row) {
            show_error_msg(T_("ERROR"), T_("INVITE_ONLY_NOT_FOUND")." ".($site_config['signup_timeout']/86400)." days.", 1);
        }
}

if ($_GET["takesignup"] == "1") {

$message == "";



	$wantusername = $_POST["wantusername"];
	$email = $_POST["email"];
	$wantpassword = $_POST["wantpassword"];
	$passagain = $_POST["passagain"];
	$country = $_POST["country"];
	$gender = $_POST["gender"];
	$client = $_POST["client"];
	$age = (int) $_POST["age"];

  if (empty($wantpassword) || (empty($email) && !$invite_row) || empty($wantusername))
	$message = T_("DONT_LEAVE_ANY_FIELD_BLANK");
  elseif (strlen($wantusername) > $username_length)
	$message = sprintf(T_("USERNAME_TOO_LONG"), $username_length);
  elseif ($wantpassword != $passagain)
	$message = T_("PASSWORDS_NOT_MATCH");
  elseif (strlen($wantpassword) < $password_minlength)
	$message = sprintf(T_("PASS_TOO_SHORT_2"), $password_minlength);
  elseif (strlen($wantpassword) > $password_maxlength)
	$message = sprintf(T_("PASS_TOO_LONG_2"), $password_maxlength);
  elseif ($wantpassword == $wantusername)
 	$message = T_("PASS_CANT_MATCH_USERNAME");
  elseif (!validusername($wantusername))
	$message = "Invalid username.";
  elseif (!$invite_row && !validemail($email))
		$message = "That doesn't look like a valid email address.";

	if ($message == "") {
		// Certain checks must be skipped for invites
		if (!$invite_row) {
			//check email isnt banned
			$maildomain = (substr($email, strpos($email, "@") + 1));
            $a = DB::run("SELECT count(*) FROM email_bans where mail_domain=?",[$email])->fetch();
			if ($a[0] != 0)
				$message = sprintf(T_("EMAIL_ADDRESS_BANNED_S"), $email);

            $a = DB::run("SELECT count(*) FROM email_bans where mail_domain LIKE '%$maildomain%'")->fetch();
			if ($a[0] != 0)
				$message = sprintf(T_("EMAIL_ADDRESS_BANNED_S"), $email);

		  // check if email addy is already in use
            $a = DB::run("SELECT count(*) FROM users where email=?",[$email])->fetch();
		  if ($a[0] != 0)
			$message = sprintf(T_("EMAIL_ADDRESS_INUSE_S"), $email);
		}

	   //check username isnt in use
        $a = DB::run("SELECT count(*) FROM users where username=?",[$wantusername])->fetch();
	  if ($a[0] != 0)
		$message = sprintf(T_("USERNAME_INUSE_S"), $wantusername); 

	  $secret = mksecret(); //generate secret field

	  $wantpassword = password_hash($wantpassword, PASSWORD_BCRYPT); // hash the password
	}

	if ($message != "")
		show_error_msg(T_("SIGNUP_FAILED"), $message, 1);

  if ($message == "") {
		if ($invite_row) {
            $upd = DB::run("UPDATE users SET username=".sqlesc($wantusername).", password=".sqlesc($wantpassword).", secret=".sqlesc($secret).", status='confirmed', added='".get_date_time()."' WHERE id=$invite_row[id]");
			//send pm to new user
			if ($site_config["WELCOMEPMON"]){
				$dt = sqlesc(get_date_time());
				$msg = sqlesc($site_config["WELCOMEPMMSG"]);
                $ins =  DB::prepare("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $invite_row[id], $dt, $msg, 0)");
                $ins->execute();
			}
			header("Refresh: 0; url=/accountconfirmok?type=confirm");
			die;
		}

	if ($site_config["CONFIRMEMAIL"]) { //req confirm email true/false
		$status = "pending";
	}else{
		$status = "confirmed";
	}

	//make first member admin
	if ($numsitemembers == '0')
		$signupclass = '7';
	else
		$signupclass = '1';


	$sql = "INSERT INTO users (username, password, secret, email, status, added, last_access, age, country, gender, client, stylesheet, language, class, ip) VALUES (" . implode(",", array_map("sqlesc", array($wantusername, $wantpassword, $secret, $email, $status, get_date_time(), get_date_time(), $age, $country, $gender, $client, $site_config["default_theme"], $site_config["default_language"], $signupclass, getip()))).")";
    $ins_user =  DB::prepare($sql);
    $ins_user->execute();
    $id = DB::lastInsertId();

    $psecret = md5($secret);
    $thishost = $_SERVER["HTTP_HOST"];
    $thisdomain = preg_replace('/^www\./is', "", $thishost);

	//ADMIN CONFIRM
	if ($site_config["ACONFIRM"]) {
		$body = T_("YOUR_ACCOUNT_AT")." ".$site_config['SITENAME']." ".T_("HAS_BEEN_CREATED_YOU_WILL_HAVE_TO_WAIT")."\n\n".$site_config['SITENAME']." ".T_("ADMIN");
	}else{//NO ADMIN CONFIRM, BUT EMAIL CONFIRM
		$body = T_("YOUR_ACCOUNT_AT")." ".$site_config['SITENAME']." ".T_("HAS_BEEN_APPROVED_EMAIL")."\n\n	".$site_config['SITEURL']."/accountconfirm?id=$id&secret=$psecret\n\n".T_("HAS_BEEN_APPROVED_EMAIL_AFTER")."\n\n	".T_("HAS_BEEN_APPROVED_EMAIL_DELETED")."\n\n".$site_config['SITENAME']." ".T_("ADMIN");
	}

	if ($site_config["CONFIRMEMAIL"]){ //email confirmation is on
		sendmail($email, "Your $site_config[SITENAME] User Account", $body, "", "-f$site_config[SITEEMAIL]");
		header("Refresh: 0; url=/accountconfirmok?type=signup&email=" . urlencode($email));
	}else{ //email confirmation is off
		header("Refresh: 0; url=/accountconfirmok?type=noconf");
	}
	//send pm to new user
	if ($site_config["WELCOMEPMON"]){
		$dt = sqlesc(get_date_time());
		$msg = sqlesc($site_config["WELCOMEPMMSG"]);
        $qry = DB::prepare("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $id, $dt, $msg, 0)");
        $qry->execute();
	}

    die;
  }

}//end takesignup



stdhead(T_("SIGNUP"));
begin_frame(T_("SIGNUP"));
?>
<?php echo T_("COOKIES"); ?>

<form method="post" action="/accountsignup?takesignup=1">
	<?php if ($invite_row) { ?>
	<input type="hidden" name="invite" value="<?php echo $_GET["invite"]; ?>" />
	<input type="hidden" name="secret" value="<?php echo htmlspecialchars($_GET["secret"]); ?>" />
	<?php } ?>
	<table cellspacing="0" cellpadding="2" border="0">
			<tr>
				<td><?php echo T_("USERNAME"); ?>: <font class="required">*</font></td>
				<td><input type="text" size="40" name="wantusername" /></td>
			</tr>
			<tr>
				<td><?php echo T_("PASSWORD"); ?>: <font class="required">*</font></td>
				<td><input type="password" size="40" name="wantpassword" /></td>
			</tr>
			<tr>
				<td><?php echo T_("CONFIRM"); ?>: <font class="required">*</font></td>
				<td><input type="password" size="40" name="passagain" /></td>
			</tr>
			<?php if (!$invite_row) {?>
			<tr>
				<td><?php echo T_("EMAIL"); ?>: <font class="required">*</font></td>
				<td><input type="text" size="40" name="email" /></td>
			</tr>
			<?php } ?>
			<tr>
				<td><?php echo T_("AGE"); ?>:</td>
				<td><input type="text" size="40" name="age" maxlength="3" /></td>
			</tr>
			<tr>
				<td><?php echo T_("COUNTRY"); ?>:</td>
				<td>
					<select name="country" size="1">
						<?php
						$countries = "<option value=\"0\">---- ".T_("NONE_SELECTED")." ----</option>\n";
                        $ct_r = DB::run("SELECT id,name,domain from countries ORDER BY name");
                        while ($ct_a = $ct_r->fetch(PDO::FETCH_LAZY)){
							$countries .= "<option value=\"$ct_a[id]\">$ct_a[name]</option>\n";
						}
						?>
						<?php echo $countries; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo T_("GENDER"); ?>:</td>
				<td>
					<input type="radio" name="gender" value="Male" /><?php echo T_("MALE"); ?>
					&nbsp;&nbsp;
					<input type="radio" name="gender" value="Female" /><?php echo T_("FEMALE"); ?>
				</td>
			</tr>
			<tr>
				<td><?php echo T_("PREF_BITTORRENT_CLIENT"); ?>:</td>
				<td><input type="text" size="40" name="client"  maxlength="20" /></td>
			</tr>
			<tr>
				<td align="center" colspan="2">
                <input type="submit" value="<?php echo T_("SIGNUP"); ?>" />
              </td>
			</tr>
	</table>
</form>
<?php
end_frame();
stdfoot();
					}	
	
	
}
