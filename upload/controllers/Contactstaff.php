<?php
  class Contactstaff extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }

    public function index(){
dbconn();
global $site_config, $CURUSER;
echo 'im here, your page is not !';
	}

    public function contactstaff(){
dbconn();
global $site_config, $CURUSER;
if ($site_config["MEMBERSONLY"])
    loggedinonly();
stdhead("Contact us");
begin_frame("Contact us");
if ((!(isset($_POST["msg"]))) & (!(isset($_POST["sub"])))) {
?>
    <p>
        Send message to Staff
        <form method=post name=message action=<?php echo TTURL; ?>/contactstaff/contactstaff>
            <table>
                <tr>
                    <td>Subject</td>
                    <td style="text-align:left;"><input type=text size=83 name=sub style='margin-left: 5px'></td>
                </tr>
                <tr>
                    <td>Message</td>
                    <td style="text-align:left;"><textarea name=msg cols=61 rows=10></textarea></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type=submit value="Submit" class=btn></td>
                </tr>
            </table>
        </form>
    </p>
<?php
} else {
    $msg = trim($_POST["msg"]);
    $sub = trim($_POST["sub"]);
    $error_msg = "";
    if (!$msg)
        $error_msg = $error_msg . "You did not put message.</br>";
    if (!$sub)
        $error_msg = $error_msg . "You did not put subject.</br>";
    if ($error_msg != "") {
        echo "<center><h3 style=\"color:red;\">Your message can not be sent:</br></br>";
        echo $error_msg . "</h3></br></br><a href=\"$site_config[SITEURL]/contactstaff/contactstaff\">Back</a></center>";
    } else {
        $added = get_date_time();
        $userid = $CURUSER['id'];
        $REQ = DB::run("INSERT INTO staffmessages (sender, added, msg, subject) VALUES(?,?,?,?)", [$userid, $added, $msg, $sub]);
        if ($REQ) {
            echo "<center><h3 style=\"color:green;\">Your message has been sent. We will reply as soon as possible.</h3></br></br><a href=\"$site_config[SITEURL]/index\">Back</a></center>";
        } else {
            echo "<center><h3 style=\"color:red;\">You send the wrong message. try again later</h3></br></br><a href=\"$site_config[SITEURL]/index\">Back</a></center>";
        }
    }
}
end_frame();
stdfoot();

	}


    public function staffbox(){
$mod_class = 5; // Change this to what ever class you want accessing this page.
$del_class = 6; // Change this to what ever class you want to be able to delete the staff messages.
$spam = 0;
dbconn();
loggedinonly();
global $site_config, $CURUSER;
if ($CURUSER["class"] < $site_config['Moderator']) {
    show_error_msg("Error", "Permission denied.", 1);
}

$action = $_GET["action"];
// SHOW PM'S
if (!$action) {
    if ($CURUSER["class"] < $site_config['Moderator']) {
        show_error_msg("Error", "Permission denied.", 1);
    }
    stdhead("Staff PM's");
    $count = get_row_count("staffmessages");
    $url = " .$_SERVER[PHP_SELF]?";
    $perpage = 20;
    list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $url);
    if ($count == 0) {
        begin_frame('Staff PMs');
        print("<h2>No messages yet!</h2>");
        end_frame();
        if ($spam == 1)
            print("<center>---<a href=#><font color=red><b>Marked as spam !!!</a> ---</b></font color></center>");
    } else {
        //echo $pagertop;
        begin_frame('Staff PMs');
        print("<table class='table table-striped table-bordered table-hover'><thead>\n");
        print("
<tr>
<td class=colhead align=left>Subject</td>
<td class=colhead align=left>Sender</td>
<td class=colhead align=left>Added</td>
<td class=colhead align=left>Answered</td>
<td class=colhead align=center>Set Answered</td>
<td class=colhead align=left>Del</td>
</tr></thead><tbody>
");
        print("<form method=post action=?action=takecontactanswered>");
        $res = DB::run("SELECT staffmessages.id, staffmessages.added, staffmessages.subject, staffmessages.answered, staffmessages.answeredby, staffmessages.sender, staffmessages.answer, users.username FROM staffmessages INNER JOIN users on staffmessages.sender = users.id ORDER BY id desc $limit");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            if ($arr[answered]) {
                $res3 = DB::run("SELECT username FROM users WHERE id=$arr[answeredby]");
                $arr3 = $res3->fetch(PDO::FETCH_ASSOC);
                $answered = "<font color=green><b>Yes - <a href=users/read?id=$arr[answeredby]><b>".class_user_colour($arr3[username])."</b></a> (<a href=staffbox?action=viewanswer&pmid=$arr[id]>View Answer</a>)</b></font>";
            } else
                $answered = "<font color=red><b>No</b></font>";
            $pmid = $arr["id"];
            print("<tr>
<td><a href=" . $site_config["SITEURL"] . "/contactstaff/staffbox?action=viewpm&pmid=$pmid><b>$arr[subject]</b></td>
<td><a href=" . $site_config["SITEURL"] . "/users/read?id=$arr[sender]><b>".class_user_colour($arr[username])."</b></a></td>
<td>$arr[added]</td><td align=left>$answered</td>");

 print("<td><input type=\"checkbox\" name=\"setanswered[]\" value=\"" . $arr[id] . "\" /></td>
<td><a href=" . $site_config["SITEURL"] . "/contactstaff/staffbox?action=deletestaffmessage&id=$arr[id]>Del</a></td>");
 print("</tr></tbody>\n");
        }
        print("</table>\n");
        print("<p align=right><input type=submit value=Confirm></p>");
        if ($spam == 1)
            print("<center><a href=#><b>--- </a> ---</b></font color></center>");
        print("</form>");
        echo $pagerbottom;
        end_frame();
    }
    stdfoot();
}

// VIEW PM'S
if ($action == "viewpm") {
    if ($CURUSER["class"] < $site_config['Moderator'])
        show_error_msg("Error", "Permission denied.", 1);
    $pmid = (int) $_GET["pmid"];
    $ress4 = DB::run("SELECT id, subject, sender, added, msg, answeredby, answered FROM staffmessages WHERE id=$pmid");
    $arr4 = $ress4->fetch(PDO::FETCH_ASSOC);
    $answeredby = $arr4["answeredby"];
    $rast = DB::run("SELECT username FROM users WHERE id=$answeredby");
    $arr5 = $rast->fetch(PDO::FETCH_ASSOC);
    $senderr = "" . $arr4["sender"] . "";
    if (is_valid_id($arr4["sender"])) {
        $res2 = DB::run("SELECT username FROM users WHERE id=" . $arr4["sender"]);
        $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
        $sender = "<a href='users/read?id=$senderr'>" . ($arr2["username"] ? $arr2["username"] : "[Deleted]") . "</a>";
    } else
        $sender = "System";
    $subject = $arr4["subject"];
    if ($arr4["answered"] == '0') {
        $answered = "<font color=red><b>No</b></font>";
    } else {
        $answered = "<font color=blue><b>Yes</b></font> by <a href=users/read?id=$answeredby>".class_user_colour($arr5[username])."</a> (<a href=" . $site_config["SITEURL"] . "/contactstaff/staffbox?action=viewanswer&pmid=$pmid>Show Answer</a>)";
    }
    if ($arr4["answered"] == '0') {
        $setanswered = "[<a href=" . $site_config["SITEURL"] . "/contactstaff/staffbox?action=setanswered&id=$arr4[id]>Mark Answered</a>]";
    } else {
        $setanswered = "";
    }
    $iidee = $arr4["id"];
    stdhead("Staff PM's");
    begin_frame('Messages to staff');
    print("<table class=bottom width=730 border=0 cellspacing=0 cellpadding=10><tr><td class=embedded width=700>\n");
    $elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr4["added"]));
    print("<table width=750 border=1 cellspacing=0 cellpadding=10 style='margin-bottom: 10px'><tr><td class=text>\n");
    print("From <b>$sender</b> at\n" . $arr4["added"] . " ($elapsed ago) GMT\n");
    print("<br><br style='margin-bottom: -10px'><div align=left><b>Subject: <font color=darkred>$subject</b></font>
&nbsp;&nbsp;<br><b>Answered:</b> $answered&nbsp;&nbsp;$setanswered</div>
<br><table class=main width=730 border=1 cellspacing=0 cellpadding=10><tr><td class=staffpms>\n");
    print(format_comment($arr4["msg"]));
    print("</td></tr></table>\n");
    print("<table width=730 border=0><tr><td class=embedded>\n");
    print(($arr4["sender"] ? "<a href=" . $site_config["SITEURL"] . "/contactstaff/staffbox?action=answermessage&receiver=" . $arr4["sender"] . "&answeringto=$iidee><b>Reply</b></a>" : "<font class=gray><b>Reply</b></font>") . " | <a href=" . $site_config["SITEURL"] . "/contactstaff/staffbox?action=deletestaffmessage&id=" . $arr4["id"] . "><b>Delete</b></a></td>");
    if ($spam == 1)
        print("<center><a href='#'><font color=red><b>--- </a> ---</b></font color></center>");
    print("</table></table>\n");
    print("</table>\n");
    end_frame();
    stdfoot();
}

// VIEW ANSWERS
if ($action == "viewanswer") {
    if ($CURUSER["class"] < $site_config['Moderator'])
        show_error_msg("Error", "Permission denied.", 1);
    $pmid = (int) $_GET["pmid"];
    $ress4 = DB::run("SELECT id, subject, sender, added, msg, answeredby, answered, answer FROM staffmessages WHERE id=$pmid");
    $arr4 = $ress4->fetch(PDO::FETCH_ASSOC);
    $answeredby = $arr4["answeredby"];
    $rast = DB::run("SELECT username FROM users WHERE id=$answeredby");
    $arr5 = $rast->fetch(PDO::FETCH_ASSOC);
    if (is_valid_id($arr4["sender"])) {
        $res2 = DB::run("SELECT username FROM users WHERE id=" . $arr4["sender"]);
        $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
        $sender = "<a href=" . $site_config["SITEURL"] . "/users/profile?id=" . $arr4["sender"] . ">" . ($arr2["username"] ? $arr2["username"] : "[Deleted]") . "</a>";
    } else
        $sender = "System";
    if ($arr4['subject'] == "") {
        $subject = "No subject";
    } else {
        $subject = "<a style='color: darkred' href=staffbox.php?action=viewpm&pmid=$pmid>$arr4[subject]</a>";
    }
    $iidee = $arr4["id"];
    if ($arr4[answer] == "") {
        $answer = "This message has not been answered yet!";
    } else {
        $answer = $arr4["answer"];
    }
    stdhead("Staff PM's");
    begin_frame("Viewing Answer");
    print("<table class=bottom width=730 border=0 cellspacing=0 cellpadding=10><tr><td class=embedded width=700>\n");
    $elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr4["added"]));
    print("<table width=750 border=1 cellspacing=0 cellpadding=10 style='margin-bottom: 10px'><tr><td class=text>\n");
    print("<b><a href=$site_configSITEURL].php?id=$answeredby>".class_user_colour($arr5[username])."</a></b> answered this message sent by $sender");
    print("<br><br style='margin-bottom: -10px'><div align=left><b>Subject: $subject</b>
&nbsp;&nbsp;<br><b>Answer:</b></div>
<br><table class=main width=730 border=1 cellspacing=0 cellpadding=10><tr><td class=staffpms>\n");
    print(format_comment($answer));
    print("</td></tr></table>\n");
    print("</table>\n");
    print("</table>\n");
    end_frame();
    stdfoot();
}

// ANSWER MESSAGE
if ($action == "answermessage") {
    if ($CURUSER["class"] < $site_config['Moderator'])
        show_error_msg("Error", "Permission denied.", 1);
    $answeringto = $_GET["answeringto"];
    $receiver = (int) $_GET["receiver"];
    if (!is_valid_id($receiver))
        die;
    $res = DB::run("SELECT * FROM users WHERE id=$receiver");
    $user = $res->fetch(PDO::FETCH_ASSOC);
    if (!$user)
        stderr("Error", "No user with that ID.");
    $res2 = DB::run("SELECT * FROM staffmessages WHERE id=$answeringto");
    $array = $res2->fetch(PDO::FETCH_ASSOC);
    stdhead("Answer to Staff PM", false);
    begin_frame('Answer to Staff PM');
?>
<table class=main width=450 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<div align=center>
<center></center><b>Answering to <a href=".$site_config["SITEURL"]."/contactstaff/staffbox?action=viewpm&pmid=<?php echo $array['id'];?>><i><?php echo $array["subject"];?></i></a> sent by <i><?php echo $user["username"];?></i></b></center>

<form method=post name=message action=?action=takeanswer>
<?php
    if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"]) {
?>
<?php
    }
?>
<table class=message cellspacing=0 cellpadding=5>
<tr><td colspan=2>
<b><font color=red>>Message:</font></b>
<textarea name=msg cols=50 rows=5><?php echo htmlspecialchars($body);?></textarea>
<?php
    if ($spam == 1)
        print("<center><a href=#><font color=red><b>--- </a> ---</b></font color></center>");
?>
</td></tr>
<tr><td<?php echo $replyto ? " colspan=2" : "";?> align=center><input type=submit value="Send it!" class=btn></td></tr>

</table>
<input type=hidden name=receiver value=<?php echo $receiver;?>>
<input type=hidden name=answeringto value=<?php echo $answeringto;?>>
</form>
</div></td></tr></table>

<?php
end_frame();
    stdfoot();
}

// TAKE ANSWER
if ($action == "takeanswer") {
    if ($_SERVER["REQUEST_METHOD"] != "POST")
        show_error_msg("Error", "Method", 1);
    if ($CURUSER["class"] < $site_config['Moderator'])
        show_error_msg("Error", "Permission denied.", 1);
    $receiver = (int) $_POST["receiver"];
    $answeringto = $_POST["answeringto"];
    if (!is_valid_id($receiver))
        show_error_msg("Error", "Invalid ID",1);
    $userid = $CURUSER["id"];
    $msg = trim($_POST["msg"]);
    $message = $msg;
    $added = get_date_time();
    if (!$msg)
        show_error_msg("Error", "Please enter something!",1);
    //DB::run("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES($userid, $userid, $receiver, $added, $message)");
    DB::run("UPDATE staffmessages SET answer=? WHERE id=?", [$message, $answeringto]);
    DB::run("UPDATE staffmessages SET answered=?, answeredby=? WHERE id=?", [1, $userid, $answeringto]);
    $smsg = "Staff Message $answeringto has been answered.";
    autolink($site_config["SITEURL"] . '/contactstaff/staffbox', $smsg);
    die;
}

// DELETE STAFF MESSAGE
if ($action == "deletestaffmessage") {
if ($CURUSER["class"] < $site_config['Moderator'])
        show_error_msg("Error", "Permission denied.", 1);

    $id = (int) $_GET["id"];
    if (!is_numeric($id) || $id < 1 || floor($id) != $id)
        die;
    DB::run("DELETE FROM staffmessages WHERE id=" . sqlesc($id));
    $smsg = "Staff Message $id has been deleted.";
    autolink($site_config["SITEURL"] . "/contactstaff/staffbox", $smsg);
    die;
}

// MARK AS ANSWERED
if ($action == "setanswered") {
    if ($CURUSER["class"] < $site_config['Moderator'])
        show_error_msg("Error", "Permission denied.", 1);
    $id = (int) $_GET["id"];
    DB::run("UPDATE staffmessages SET answered=1, answeredby = $CURUSER[id] WHERE id = $id");
    $smsg = "Staff Message $id has been set as answered.";
    autolink($site_config["SITEURL"] ."/contactstaff/staffbox?action=viewpm&pmid=$id", $smsg);
    die;
}

// MARK AS ANSWERED #2
if ($action == "takecontactanswered") {
    if ($CURUSER["class"] < $site_config['Moderator']) {
        show_error_msg("Error", "Permission denied.", 1);
    }
    $res = DB::run("SELECT id FROM staffmessages WHERE answered=0 AND id IN (" . implode(", ", $_POST[setanswered]) . ")");
    while ($arr = $res->fetch(PDO::FETCH_ASSOC))
        DB::run("UPDATE staffmessages SET answered=?, answeredby =?  WHERE id =?", [1, 1, $arr['id']]);
    $smsg = "Staff Messages have been marked as answered.";
    autolink("staffbox", $smsg);
    die;
}

	}

    public function takecontact(){
dbconn();
global $site_config, $CURUSER;
if ($_SERVER['REQUEST_METHOD'] != 'POST')
    show_error_msg('Error', 'Invalid Request Method.', 1);

$err = false;

if (empty($_POST['msg'])) {
    $err = 'Your message cannot be empty...';
}

if (empty($_POST['subject'])) {
    $err = 'You must define a subject...';
}

if ($err) {
    autolink("takestaff/takestaff", $err);
}

DB::run("INSERT INTO `staffmessages` (`sender`, `added`, `msg`, `subject`) VALUES (?,?,?,?)", [($CURUSER ? $CURUSER['id'] : 0), get_date_time(), $_POST['msg'], $_POST['subject']]);

if (!empty($_POST['returnto'])) {
    autolink($_POST['returnto'], 'Redirecting...');
}

show_error_msg('Success', 'Message Sent...', 1);

	}
}
