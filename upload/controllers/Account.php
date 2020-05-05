<?php
  class Account extends Controller {
    
    public function __construct(){
         $this->userModel = $this->model('User');
    }
    
    public function login(){
		dbconn();
		global $site_config, $pdo;
		// Check for POST
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
			// Sanitize POST data
			$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
			// Init data
			$username = $_POST['username'];
			$password = $_POST['password'];
			$testing = $_POST['rememberme'];
            // called model method/function
            $row =	DB::run("SELECT id, password, secret, status, enabled FROM users WHERE username =? ", [$username])->fetch();
    	    // Verify user password and set $_SESSION
		if ( !$row || !password_verify($password,$row["password"]))
			$message = T_("LOGIN_INCORRECT");
		elseif ($row["status"] == "pending")
			$message = T_("ACCOUNT_PENDING");
		elseif ($row["enabled"] == "no")
			$message = T_("ACCOUNT_DISABLED");
			
			if (!$message){
				// Session Handler
				setsess($row["id"], $row["password"], $row["secret"]);
			    if( isset($_POST['rememberme']) ){			        
			        logincookie($row["id"], $row["password"], $row["secret"]);
			        setsess($row["id"], $row["password"], $row["secret"]);
			    }
                header("Location: ".TTURL."/index.php");
               }else {
                  show_error_msg(T_("ACCESS_DENIED"), $message, 1);
               }

        }    

        stdhead(T_("LOGIN"));
		begin_frame(T_("LOGIN"));
		        // Members Only
				if ($site_config["MEMBERSONLY"]) {
					$message = T_("MEMBERS_ONLY");
					print("<center><b>" . $message . "</b></center>\n");
				}
		// add view
		$data = [];
		$this->view('account/login', $data);
        end_frame();
        stdfoot();
}

	
	public function logout()
    {
        dbconn();
		// Remove cookies & sessions
		session_destroy();
		logoutcookie();
        header("Location: ".TTURL."/index.php");
    }

    public function recover(){
dbconn();
global $site_config, $CURUSER, $pdo;
$kind = '0';

if (is_valid_id($_POST["id"]) && strlen($_POST["secret"]) == 20) {
    $password = $_POST["password"];
    $password1 = $_POST["password1"];
    if (empty($password) || empty($password1)) {
        $kind = T_("ERROR");
        $msg =  T_("NO_EMPTY_FIELDS");
    } elseif ($password != $password1) {
        $kind = T_("ERROR");
        $msg = T_("PASSWORD_NO_MATCH");
    } else {
	$n = get_row_count("users", "WHERE `id`=".intval($_POST["id"])." AND `secret` = ".sqlesc($_POST["secret"]));
	if ($n != 1)
		show_error_msg(T_("ERROR"), T_("NO_SUCH_USER"));
        $newsec = mksecret();
        $wantpassword = password_hash($password, PASSWORD_BCRYPT);
        $pid = $_POST['id']; 
        $psecret = $_POST["secret"];
        $stmt = $this->userModel->recoverUpdate($wantpassword, $newsec, $pid, $psecret);
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
        $arr = $this->userModel->getIdEmailByEmail($email);
        if (!$arr) {
            $msg = T_("EMAIL_ADDRESS_NOT_FOUND");
            $kind = T_("ERROR");
        }

        if ($arr) {
              $sec = mksecret();
            $id = $arr['id'];

            $body = T_("SOMEONE_FROM")." " . $_SERVER["REMOTE_ADDR"] . " ".T_("MAILED_BACK")." ($email) ".T_("BE_MAILED_BACK")." \r\n\r\n ".T_("ACCOUNT_INFO")." \r\n\r\n ".T_("USERNAME").": ".class_user($arr["username"])." \r\n ".T_("CHANGE_PSW")."\n\n$site_config[SITEURL]/account/recover?id=$id&secret=$sec\n\n\n".$site_config["SITENAME"]."\r\n";
            
            @sendmail($arr["email"], T_("ACCOUNT_DETAILS"), $body, "", "-f".$site_config['SITEEMAIL']);
            $res2 = $this->userModel->setSecret($sec, $email);
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

if (is_valid_id($_GET["id"]) && strlen($_GET["secret"]) == 20) {
	$data = [];
	$this->view('account/recover', $data);
  } else { echo T_("USE_FORM_FOR_ACCOUNT_DETAILS");
	$data = [];
	$this->view('account/recoverpass', $data);	
}
end_frame();
stdfoot();
    }

    public function ce(){
dbconn();
global $site_config, $CURUSER, $pdo;
$id = (int) $_GET["id"];
$md5 = $_GET["secret"];
$email = $_GET["email"];

if (!$id || !$md5 || !$email) {
	show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA"), 1);
}

$row = $pdo->run("SELECT `editsecret` FROM `users` WHERE `enabled` =? AND `status` =? AND `editsecret` !=?  AND `id` =?", ['yes', 'confirmed', '', $id])->fetch();

if (!$row) {
	show_error_msg(T_("ERROR"), T_("NOTHING_FOUND"), 1);
}

$sec = $row["editsecret"];

if ($md5 != md5($sec . $email . $sec))
    show_error_msg(T_("ERROR"), T_("NOTHING_FOUND"), 1);

$pdo->run("UPDATE `users` SET `editsecret` =?, `email` =? WHERE `id` =? AND `editsecret` =?", ['', $email, $id, $row["editsecret"]]);

header("Refresh: 0; url=".TTURL."/account");
header("Location: ".TTURL."/account");
	}

    public function confirm(){
dbconn();
global $site_config, $CURUSER, $pdo;
$id = (int) $_GET["id"];
$md5 = $_GET["secret"];

if (!$id || !$md5)
	show_error_msg(T_("ERROR"), T_("INVALID_ID"), 1);

$row = $pdo->run("SELECT `password`, `secret`, `status` FROM `users` WHERE `id` =?", [$id])->fetch();
if (!$row)
	show_error_msg(T_("ERROR"), sprintf(T_("CONFIRM_EXPIRE"), $site_config['signup_timeout']/86400), 1);

if ($row["status"] != "pending") {
	header("Refresh: 0; url=".TTURL."/account/confirmok?type=confirmed");
	die;
}

if ($md5 != $row["secret"])
	show_error_msg(T_("ERROR"), T_("SIGNUP_ACTIVATE_LINK"), 1);

$secret = mksecret();

$upd =$pdo->run("UPDATE `users` SET `secret` =?, `status` =? WHERE `id` =? AND `secret` =? AND `status` =?", [$secret, 'confirmed', $id, $row["secret"], 'pending']);
if (!$upd)
	show_error_msg(T_("ERROR"), T_("SIGNUP_UNABLE"), 1);

header("Refresh: 0; url=".TTURL."/account/confirmok?type=confirm");
	}

    public function confirmok(){
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

  public function signup(){
dbconn();
global $site_config, $CURUSER, $pdo;
$username_length = 15; // Max username length. You shouldn't set this higher without editing the database first
$password_minlength = 6;
$password_maxlength = 60;

// Disable checks if we're signing up with an invite
if (!is_valid_id($_REQUEST["invite"]) || strlen($_REQUEST["secret"]) != 20) {
	//invite only check
	if ($site_config["INVITEONLY"]) {
		show_error_msg(T_("INVITE_ONLY"), "<br /><br /><center>".T_("INVITE_ONLY_MSG")."<br /><br /></center>",1);
	}

	//get max members, and check how many users there is
	$numsitemembers = get_row_count("users");
	if ($numsitemembers >= $site_config["maxusers"])
		show_error_msg(T_("SORRY")."...", T_("SITE_FULL_LIMIT_MSG") . number_format($site_config["maxusers"])." ".T_("SITE_FULL_LIMIT_REACHED_MSG")." ".number_format($numsitemembers)." members",1);
} else {
	    $stmt = $pdo->run("SELECT id FROM users WHERE id = $_REQUEST[invite] AND secret = ".sqlesc($_REQUEST["secret"]));
        $invite_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invite_row) {
            show_error_msg(T_("ERROR"), T_("INVITE_ONLY_NOT_FOUND")." ".($site_config['signup_timeout']/86400)." days.", 1);
        }
}

if ($_GET["takesignup"] == "1") {

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
            $a = $pdo->run("SELECT count(*) FROM email_bans where mail_domain=?",[$email])->fetch();
			if ($a[0] != 0)
				$message = sprintf(T_("EMAIL_ADDRESS_BANNED_S"), $email);

            $a = $pdo->run("SELECT count(*) FROM email_bans where mail_domain LIKE '%$maildomain%'")->fetch();
			if ($a[0] != 0)
				$message = sprintf(T_("EMAIL_ADDRESS_BANNED_S"), $email);

		  // check if email addy is already in use
            $a = $pdo->run("SELECT count(*) FROM users where email=?",[$email])->fetch();
		  if ($a[0] != 0)
			$message = sprintf(T_("EMAIL_ADDRESS_INUSE_S"), $email);
		}

	   //check username isnt in use
        $a = $pdo->run("SELECT count(*) FROM users where username=?",[$wantusername])->fetch();
	  if ($a[0] != 0)
		$message = sprintf(T_("USERNAME_INUSE_S"), $wantusername); 

	  $secret = mksecret(); //generate secret field

	  $wantpassword = password_hash($wantpassword, PASSWORD_BCRYPT); // hash the password
	}

	if ($message != "")
		show_error_msg(T_("SIGNUP_FAILED"), $message, 1);

  if ($message == "") {
		if ($invite_row) {
            $upd = $pdo->run("UPDATE users SET username=".sqlesc($wantusername).", password=".sqlesc($wantpassword).", secret=".sqlesc($secret).", status='confirmed', added='".get_date_time()."' WHERE id=$invite_row[id]");
			//send pm to new user
			if ($site_config["WELCOMEPMON"]){
				$dt = sqlesc(get_date_time());
				$msg = sqlesc($site_config["WELCOMEPMMSG"]);
                $ins =  $pdo->run("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $invite_row[id], $dt, $msg, 0)");
			}
			header("Refresh: 0; url=".TTURL."/account/confirmok?type=confirm");
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
    $ins_user =  DB::run($sql);
    $id = DB::lastInsertId();

    $thishost = $_SERVER["HTTP_HOST"];
    $thisdomain = preg_replace('/^www\./is', "", $thishost);

	//ADMIN CONFIRM
	if ($site_config["ACONFIRM"]) {
		$body = T_("YOUR_ACCOUNT_AT")." ".$site_config['SITENAME']." ".T_("HAS_BEEN_CREATED_YOU_WILL_HAVE_TO_WAIT")."\n\n".$site_config['SITENAME']." ".T_("ADMIN");
	}else{//NO ADMIN CONFIRM, BUT EMAIL CONFIRM
		$body = T_("YOUR_ACCOUNT_AT")." ".$site_config['SITENAME']." ".T_("HAS_BEEN_APPROVED_EMAIL")."\n\n	".$site_config['SITEURL']."/account/confirm?id=$id&secret=$secret\n\n".T_("HAS_BEEN_APPROVED_EMAIL_AFTER")."\n\n	".T_("HAS_BEEN_APPROVED_EMAIL_DELETED")."\n\n".$site_config['SITENAME']." ".T_("ADMIN");
	}

	if ($site_config["CONFIRMEMAIL"]){ //email confirmation is on
		sendmail($email, "Your $site_config[SITENAME] User Account", $body, "", "-f$site_config[SITEEMAIL]");
		header("Refresh: 0; url=".TTURL."/account/confirmok?type=signup&email=" . urlencode($email));
	}else{ //email confirmation is off
		header("Refresh: 0; url=".TTURL."/account/confirmok?type=noconf");
	}
	//send pm to new user
	if ($site_config["WELCOMEPMON"]){
		$dt = sqlesc(get_date_time());
		$msg = sqlesc($site_config["WELCOMEPMMSG"]);
        $qry = $pdo->run("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $id, $dt, $msg, 0)");;
	}

    die;
  }

}//end takesignup



stdhead(T_("SIGNUP"));
begin_frame(T_("SIGNUP"));
?>
<?php echo T_("COOKIES"); ?>

<form method="post" action="<?php echo TTURL; ?>/account/signup?takesignup=1">
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
                        $ct_r = $pdo->run("SELECT id,name,domain from countries ORDER BY name");
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