<?php
  class Invite extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
dbconn();
global $site_config, $CURUSER;
loggedinonly();

if (!$site_config["INVITEONLY"] && !$site_config["ENABLEINVITES"]) {
	show_error_msg(T_("INVITES_DISABLED"), T_("INVITES_DISABLED_MSG"), 1);
}

$users = get_row_count("users", "WHERE enabled = 'yes'");

if ($users >= $site_config["maxusers_invites"]) {
	show_error_msg(T_("ERROR"), "Sorry, The current user account limit (" . number_format($site_config["maxusers_invites"]) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...", 1);
}

if ($CURUSER["invites"] == 0) {
	show_error_msg(T_("YOU_HAVE_NO_INVITES"), T_("YOU_HAVE_NO_INVITES_MSG"), 1);
}

if ($_GET["take"]) {
	$email = $_POST["email"];
	if (!validemail($email))
		show_error_msg(T_("ERROR"), T_("INVALID_EMAIL_ADDRESS"), 1);

	//check email isnt banned
	$maildomain = (substr($email, strpos($email, "@") + 1));
	$a = DB::run("select count(*) from email_bans where mail_domain=?", [$email])->fetch();
	if ($a[0] != 0)
		$message = sprintf(T_("EMAIL_ADDRESS_BANNED"), $email);

	$a = DB::run("select count(*) from email_bans where mail_domain=?", [$maildomain])->fetch();
	if ($a[0] != 0)
		$message = sprintf(T_("EMAIL_ADDRESS_BANNED"), $email);

	// check if email addy is already in use
	if (get_row_count("users", "WHERE email='$email'"))
		$message = sprintf(T_("EMAIL_ADDRESS_INUSE"), $email);

	if ($message)
		show_error_msg(T_("ERROR"), $message, 1);

	$secret = mksecret();
	$username = "invite_".mksecret(20);
	$ret = DB::run("INSERT INTO users (username, secret, email, status, invited_by, added, stylesheet, language) VALUES (".
	implode(",", array_map("sqlesc", array($username, $secret, $email, 'pending', $CURUSER["id"]))) . ",'" . get_date_time() . "', $site_config[default_theme], $site_config[default_language])");

	if (!$ret) {
		// If username is somehow taken, keep trying
		while ($ret->errorCode() == 1062) {
			$username = "invite_".mksecret(20);
			$ret = DB::run("INSERT INTO users (username, secret, email, status, invited_by, added, stylesheet, language) VALUES (".
			implode(",", array_map("sqlesc", array($username, $secret, $email, 'pending', $CURUSER["id"]))) . ",'" . get_date_time() . "', $site_config[default_theme], $site_config[default_language])");
		}
		show_error_msg(T_("ERROR"), T_("DATABASE_ERROR"), 1);
	}

	$id = DB::lastInsertId();
	$invitees = "$id $CURUSER[invitees]";
    DB::run("UPDATE users SET invites = invites - 1, invitees='$invitees' WHERE id = $CURUSER[id]");

	$mess = strip_tags($_POST["mess"]);

	$body = <<<EOD
You have been invited to $site_config[SITENAME] by $CURUSER[username]. They have specified this address ($email) as your email.
If you do not know this person, please ignore this email. Please do not reply.

Message:
-------------------------------------------------------------------------------
$mess
-------------------------------------------------------------------------------

This is a private site and you must agree to the rules before you can enter:

$site_config[SITEURL]/rules
$site_config[SITEURL]/faq


To confirm your invitation, you have to follow this link:

$site_config[SITEURL]/account/signup?invite=$id&secret=$secret

After you do this, you will be able to use your new account. If you fail to
do this, your account will be deleted within a few days. We urge you to read
the RULES and FAQ before you start using $site_config[SITENAME].
EOD;
$TTMail = new TTMail();
$TTMail->Send($email, "$site_config[SITENAME] user registration confirmation", $body, "", "-f$site_config[SITEEMAIL]");

	header("Refresh: 0; url=".TTURL."/account/confirmok?type=invite&email=" . urlencode($email));
	die;
}

stdhead(T_("INVITE"));
begin_frame(T_("INVITE"));
?>

<form method="post" action="<?php echo TTURL ?>/invite?take=1">
<table border="0" cellspacing="0" cellpadding="3">
<tr valign="top"><td align="right"><b><?php echo T_("EMAIL_ADDRESS");?>:</b></td><td align="left"><input type="text" size="40" name="email" /> 
<table width="250" border="0" cellspacing="0" cellpadding="0"><tr><td><font class="small"><?php echo T_("EMAIL_ADDRESS_VALID_MSG");?></font></td></tr></table></td></tr>   
<tr><td align="right"><b><?php echo T_("MESSAGE");?>:</b></td><td align="left"><textarea name="mess" rows="10" cols="80"></textarea>
</td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="<?php echo T_("SEND_AN_INVITE");?>" /></td></tr>
</table>
</form>
<?php
end_frame();
stdfoot();
	}
}