<?php
class Invite extends Controller
{

    public function __construct()
    {
        // $this->userModel = $this->model('User');
    }

    public function index()
    {
        dbconn();
        global $config;
        loggedinonly();

        if (!$config["INVITEONLY"] && !$config["ENABLEINVITES"]) {
            show_error_msg(T_("INVITES_DISABLED"), T_("INVITES_DISABLED_MSG"), 1);
        }

        $users = get_row_count("users", "WHERE enabled = 'yes'");

        if ($users >= $config["maxusers_invites"]) {
            show_error_msg(T_("ERROR"), "Sorry, The current user account limit (" . number_format($config["maxusers_invites"]) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...", 1);
        }

        if ($_SESSION["invites"] == 0) {
            show_error_msg(T_("YOU_HAVE_NO_INVITES"), T_("YOU_HAVE_NO_INVITES_MSG"), 1);
        }

        if ($_GET["take"]) {
            $email = $_POST["email"];
            if (!validemail($email)) {
                show_error_msg(T_("ERROR"), T_("INVALID_EMAIL_ADDRESS"), 1);
            }

            //check email isnt banned
            $maildomain = (substr($email, strpos($email, "@") + 1));
            $a = DB::run("select count(*) from email_bans where mail_domain=?", [$email])->fetch();
            if ($a[0] != 0) {
                $message = sprintf(T_("EMAIL_ADDRESS_BANNED"), $email);
            }

            $a = DB::run("select count(*) from email_bans where mail_domain=?", [$maildomain])->fetch();
            if ($a[0] != 0) {
                $message = sprintf(T_("EMAIL_ADDRESS_BANNED"), $email);
            }

            // check if email addy is already in use
            if (get_row_count("users", "WHERE email='$email'")) {
                $message = sprintf(T_("EMAIL_ADDRESS_INUSE"), $email);
            }

            if ($message) {
                show_error_msg(T_("ERROR"), $message, 1);
            }

            $secret = mksecret();
            $username = "invite_" . mksecret(20);
            $ret = DB::run("INSERT INTO users (username, secret, email, status, invited_by, added, stylesheet, language) VALUES (" .
                implode(",", array_map("sqlesc", array($username, $secret, $email, 'pending', $_SESSION["id"]))) . ",'" . get_date_time() . "', $config[default_theme], $config[default_language])");

            if (!$ret) {
                // If username is somehow taken, keep trying
                while ($ret->errorCode() == 1062) {
                    $username = "invite_" . mksecret(20);
                    $ret = DB::run("INSERT INTO users (username, secret, email, status, invited_by, added, stylesheet, language) VALUES (" .
                        implode(",", array_map("sqlesc", array($username, $secret, $email, 'pending', $_SESSION["id"]))) . ",'" . get_date_time() . "', $config[default_theme], $config[default_language])");
                }
                show_error_msg(T_("ERROR"), T_("DATABASE_ERROR"), 1);
            }

            $id = DB::lastInsertId();
            $invitees = "$id $_SESSION[invitees]";
            DB::run("UPDATE users SET invites = invites - 1, invitees='$invitees' WHERE id = $_SESSION[id]");

            $mess = strip_tags($_POST["mess"]);

            $body = <<<EOD
You have been invited to $config[SITENAME] by $_SESSION[username]. They have specified this address ($email) as your email.
If you do not know this person, please ignore this email. Please do not reply.

Message:
-------------------------------------------------------------------------------
$mess
-------------------------------------------------------------------------------

This is a private site and you must agree to the rules before you can enter:

$config[SITEURL]/rules
$config[SITEURL]/faq


To confirm your invitation, you have to follow this link:

$config[SITEURL]/account/signup?invite=$id&secret=$secret

After you do this, you will be able to use your new account. If you fail to
do this, your account will be deleted within a few days. We urge you to read
the RULES and FAQ before you start using $config[SITENAME].
EOD;
            $TTMail = new TTMail();
            $TTMail->Send($email, "$config[SITENAME] user registration confirmation", $body, "", "-f$config[SITEEMAIL]");

            header("Refresh: 0; url=" . TTURL . "/account/confirmok?type=invite&email=" . urlencode($email));
            die;
        }

        stdhead(T_("INVITE"));
        begin_frame(T_("INVITE"));
        ?>
        <form method="post" action="<?php echo TTURL ?>/invite?take=1">
        <table border="0" cellspacing="0" cellpadding="3">
        <tr valign="top"><td align="right"><b><?php echo T_("EMAIL_ADDRESS"); ?>:</b></td><td align="left"><input type="text" size="40" name="email" />
        <table width="250" border="0" cellspacing="0" cellpadding="0"><tr><td><font class="small"><?php echo T_("EMAIL_ADDRESS_VALID_MSG"); ?></font></td></tr></table></td></tr>
        <tr><td align="right"><b><?php echo T_("MESSAGE"); ?>:</b></td><td align="left"><textarea name="mess" rows="10" cols="80"></textarea>
        </td></tr>
        <tr><td colspan="2" align="center"><input type="submit" value="<?php echo T_("SEND_AN_INVITE"); ?>" /></td></tr>
        </table>
        </form>
        <?php
        end_frame();
        stdfoot();
    }

    public function invitetree()
    {
        dbconn();
        global $config;
        loggedinonly();
        $id = $_GET["id"];
        if (!is_valid_id($id)) {
            $id = $_SESSION["id"];
        }

        $res = DB::run("SELECT * FROM users WHERE status = 'confirmed' AND invited_by = $id ORDER BY username");
        $num = $res->rowCount();
        stdhead("Invite Tree for " . $id . "");
        $invitees = number_format(get_row_count("users", "WHERE status = 'confirmed' && invited_by = $id"));
        if ($invitees == 0) {
            show_error_msg("Nothing to see here!", "<div style='margin-top:10px; margin-bottom:10px' align='center'><font size=2>This member has no invitees</font></div>
	       <div style='margin-bottom:10px' align='center'>[<a href=$config[SITEURL]/users/profile?id=$id>Go Back to User Profile</a>]</div>");
        }

        if ($id != $_SESSION["id"]) {
            begin_frame("Invite Tree for [<a href=$config[SITEURL]/users/profile?id=$id>" . $id . "</a>]");
        } else {
            begin_frame("You have $invitees invitees " . class_user_colour($_SESSION["username"]) . "");
        }
        print("<br />"); //one small space here!
        print("<table class=table_table border=1 cellspacing=0 cellpadding=5 align=center>\n");
        print("<tr>
	    <td class=table_head><b>Invited&nbsp;Members</b></td>
    	<td class=table_head><b>Class</b></td>
    	<td class=table_head align=center><b>Registered</b></td>
	    <td class=table_head align=center><b>Last&nbsp;access</b></td>
    	<td class=table_head align=center><b>Downloaded</b></td>
	    <td class=table_head align=center><b>Uploaded<b></td>
	    <td class=table_head align=center><b>Ratio</b></td>
	    <td class=table_head align=center><b>Warned</b></td>
	    </tr>\n");
        for ($i = 1; $i <= $num; $i++) {
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            if ($arr["invited_by"] != $_SESSION['id'] && $_SESSION["class"] < 5) {
                print("<tr><td class=table_col1 align=center colspan=8><font color=red><b>Access Denied</b>.</font>&nbsp; You don't have permission to view the invitees of other users!</td></tr>\n");
                print("</table>\n");
                print("<br />"); //one small space here!
                end_frame();
                stdfoot();
            }
            if ($arr['added'] == '0000-00-00 00:00:00') {
                $arr['added'] = '---';
            }

            if ($arr['last_access'] == '0000-00-00 00:00:00') {
                $arr['last_access'] = '---';
            }

            if ($arr["downloaded"] != 0) {
                $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
            } else {
                $ratio = "---";
            }
            $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
            if ($arr["warned"] !== "yes") {
                $warned = "<font color=limegreen><b>No</b></font>";
            } else {
                $warned = "<font color=red><b>Yes</b></font>";
            }
            $class = get_user_class_name($arr["class"]);
            $added = substr($arr['added'], 0, 10);
            $last_access = substr($arr['last_access'], 0, 10);
            $downloaded = mksize($arr["downloaded"]);
            $uploaded = mksize($arr["uploaded"]);
            print("<tr><td class=table_col1 align=left><a href=$config[SITEURL]/users/profile?id=$arr[id]><b>" . class_user_colour($arr['username']) . "</b></a></td>
	        <td class=table_col2 align=left>$class</td>
	        <td class=table_col1 align=center>$added</td>
	        <td class=table_col2 class=table_col1 align=center>$last_access</td>
	        <td class=table_col1 align=center><font color=orangered>$downloaded</font></td>
	        <td class=table_col2 align=center><font color=limegreen>$uploaded</font></td>
	        <td class=table_col1 align=center>$ratio</td>
	        <td class=table_col2 align=center>$warned</td>
	        </tr>\n");
        }
        print("</table>\n");
        if ($arr["invited_by"] != $_SESSION['id']) {
            print("<div style='margin-top:10px' align='center'>[<a href=$config[SITEURL]/users/profile?id=$id><b>Go Back to User Profile</b></a>]</div>");
        }
        print("<br />"); //one small space here!
        end_frame();
        stdfoot();
    }
}