<?php
  class Mailbox extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
require_once("helpers/mailbox_helper.php");
dbconn();
global $site_config, $CURUSER;
loggedinonly();

$readme = add_get('read').'=';
$unread = false;

if (isset($_REQUEST['compose'])); // This blocks everything until done...

if (isset($_GET['inbox']))
{
    $pagename = T_("INBOX");
    $tablefmt = "&nbsp;,Sender,Subject,Date";
    $where = "`receiver` = $CURUSER[id] AND `location` IN ('in','both')";
    $type = "Mail";
}
elseif (isset($_GET['outbox']))
{
    $pagename = "Outbox";
    $tablefmt = "&nbsp;,Sent_to,Subject,Date";
    $where = "`sender` = $CURUSER[id] AND `location` IN ('out','both')";
    $type = "Mail";
}
elseif (isset($_GET['draft']))
{
    $pagename = "Draft";
    $tablefmt = "&nbsp;,Sent_to,Subject,Date";
    $where = "`sender` = $CURUSER[id] AND `location` = 'draft'";
    $type = "Mail";
}
elseif (isset($_GET['templates']))
{
    $pagename = "Templates";
    $tablefmt = "&nbsp;,Subject,Date";
    $where = "`sender` = $CURUSER[id] AND `location` = 'template'";
    $type = "Mail";
}
else
{
    $pagename = "Mail Overview";
    $type = "Overview";
}

//****** Send a message, or save after editing ******
if (isset($_POST['send']) || isset($_POST['draft']) || isset($_POST['template']))
{
    if (!isset($_POST['template']) && !isset($_POST['change']) && (!isset($_POST['userid']) || !is_valid_id($_POST['userid']))) $error = "Unknown recipient";
    else
    {
        $sendto = (@$_POST['template'] ? $CURUSER['id'] : @$_REQUEST['userid']);
        if (isset($_POST['usetemplate']) && is_valid_id($_POST['usetemplate']))
        {
            $res = DB::run("SELECT * FROM messages WHERE `id` = $_POST[usetemplate] AND `location` = 'template' LIMIT 1");
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            $subject = $arr['subject'].(@$_POST['oldsubject'] ? " (was ".$_POST['oldsubject'].")" : "");
            $msg = $arr['msg'];
        } else {
            $subject = $_POST['subject'];
            $msg = $_POST['msg'];
        }
        if ($msg)
        {
            $subject = $subject;
            if ((isset($_POST['draft']) || isset($_POST['template'])) && isset($_POST['msgid'])) DB::run("UPDATE messages SET `subject` = $subject, `msg` = $msg WHERE `id` = $_POST[msgid] AND `sender` = $CURUSER[id]") or die("arghh");
            else
            {
                $to = (@$_POST['draft'] ? 'draft' : (@$_POST['template'] ? 'template' : (@$_POST['save'] ? 'both' : 'in')));
                $status = (@$_POST['send'] ? 'yes' : 'no');
                DB::run("INSERT INTO `messages` (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`) VALUES (?,?,?,?,?,?,?)", [$CURUSER['id'], $sendto, get_date_time(), $subject, $msg, $status, $to]) or die("Aargh!");

                // email notif
                $res = DB::run("SELECT id, acceptpms, notifs, email FROM users WHERE id='$sendto'");
                $user = $res->fetch(PDO::FETCH_ASSOC);

                if (strpos($user['notifs'], '[pm]') !== false) {
                    $cusername = $CURUSER["username"];

                    $body = "You have received a PM from ".$cusername."\n\nYou can use the URL below to view the message (you may have to login).\n\n    ".$site_config['SITEURL']."/mailbox\n\n".$site_config['SITENAME']."";

                    sendmail($user["email"], "You have received a PM from $cusername", $body, "From: $site_config[SITEEMAIL]", "-f$site_config[SITEEMAIL]");
                }
                //end email notif

                if (isset($_POST['msgid'])) DB::run("DELETE FROM messages WHERE `location` = 'draft' AND `sender` = $CURUSER[id] AND `id` = $_POST[msgid]") or die("arghh");
            }
            if (isset($_POST['send'])) $info = "Message sent successfully".(@$_POST['save'] ? ", a copy has been saved in your Outbox" : "");
            else $info = "Message saved successfully";
        }
        else $error = "Unable to send message";
    }
}

//****** Delete a message ******
if (isset($_POST['remove']) && (isset($_POST['msgs']) || is_array($_POST['remove'])))
{
    if (is_array($_POST['remove'])) $tmp[] = key($_POST['remove']);
    else foreach($_POST['msgs'] as $key => $value) if (is_valid_id($key)) $tmp[] = $key;
    $msgs = implode(', ', $tmp);
    if ($msgs)
    {
        if (isset($_GET['inbox']))
        {
            DB::run("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = $CURUSER[id] AND `id` IN ($msgs)");
            DB::run("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = $CURUSER[id] AND `id` IN ($msgs)");
        } else {
            if (isset($_GET['outbox'])) DB::run("UPDATE messages SET `location` = 'in' WHERE `location` = 'both' AND `sender` = $CURUSER[id] AND `id` IN ($msgs)");
            DB::run("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = $CURUSER[id] AND `id` IN ($msgs)");
        }
        $info = count($tmp)." ".P_("message", count($tmp))." deleted";
    }
    else $error = "No messages to delete";
}

//****** Mark a message as read - only if you're the recipient ******
if (isset($_POST['mark']) && (isset($_POST['msgs']) || is_array($_POST['mark'])))
{
    if (is_array($_POST['mark'])) $tmp[] = key($_POST['mark']);
    else foreach($_POST['msgs'] as $key => $value) if (is_valid_id($key)) $tmp[] = $key;
    $msgs = implode(', ', $tmp);
    if ($msgs)
    {
        DB::run("UPDATE messages SET `unread` = 'no' WHERE `id` IN ($msgs) AND `receiver` = $CURUSER[id]");
        $info = count($tmp)." ".P_("message",  count($tmp))." marked as read";
    }
    else $error = "No messages marked as read";
}


stdhead($pagename, false);




if (isset($_REQUEST['compose']))
{
    begin_frame("Compose");
    usermenu ();
    $userid = @$_REQUEST['id'];
    $subject = ''; $msg = ''; $to = ''; $hidden = ''; $output = ''; $reply = false;
	$sreplay = T_("REPLY");//bugfix
    if (is_array($_REQUEST['compose'])) // In reply or followup to another msg	
    {
        $msgid = key($_REQUEST['compose']);
        if (is_valid_id($msgid))
        {
            $res = DB::run("SELECT * FROM `messages` WHERE `id` = $msgid AND '$CURUSER[id]' IN (`sender`,`receiver`) LIMIT 1");
            if ($arr = $res->fetch(PDO::FETCH_ASSOC))
            {
                $subject = htmlspecialchars($arr['subject']);
                $msg .= htmlspecialchars($arr['msg']);
                if (current($_REQUEST['compose']) == $sreplay) //bugfix 
                {
                    if ($arr['unread'] == 'yes' && $arr['receiver'] == $CURUSER['id']) DB::run("UPDATE messages SET `unread` = 'no' WHERE `id` = $arr[id]");
                    $reply = true;
                    $userid = $arr['sender'];
                    if (substr($arr['subject'],0,4) != 'Re: ') $subject = "Re: $subject";
                }
                else $userid = $arr['receiver'];
                $hidden .= "<input type=\"hidden\" name=\"msgid\" value=\"$msgid\" />";
            }
        }
    }
    if (isset($_GET['templates'])) $to = 'who cares';
    elseif (is_valid_id($userid))
    {
        $where = null;
        if ($CURUSER["view_users"] == "no" && $userid != $CURUSER["id"])
            $where = "AND acceptpms = 'yes'";

        # Allow users to PM themself's, Privacy is determined on acceptpms - (From All or Staff Only).
        $row = DB::run("SELECT username FROM users WHERE id = $userid AND status = 'confirmed' AND enabled = 'yes' $where")->fetch();

        if ( !$row )
        {
            print("You either do not have permission to pm this user, or they don't exist.");
            end_frame();
            stdfoot();
            die;
        }

        $to = $row["username"];
        $hidden .= "<input type=\"hidden\" name=\"userid\" value=\"$userid\" />";
        if ($to == $CURUSER["username"])
            $to = "Yourself";
        $to = "<b>$to</b>";
    }
    else
    {
        $where = null;
        if ($CURUSER["view_users"] == "no")
            $where = "AND acceptpms = 'yes'";

        # Don't display yourself, Privacy is determined on acceptpms - (From All or Staff Only).
        $res = DB::run("SELECT id, username FROM users WHERE id != $CURUSER[id] AND enabled = 'yes' AND status = 'confirmed' $where ORDER BY username");

        if ($res->rowCount() > 0)
        {
            $to = "<select name=\"userid\">\n";
            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) $to .= "<option value=\"$arr[id]\">$arr[username]</option>\n";
            $to .= "</select>\n";
        }

    }
    if (isset($_GET['id']) && !$to) print T_("INVALID_USER_ID");
    elseif (!isset($_GET['id']) && !$to) print T_("NO_FRIENDS");
    else
    {
        /******** compose frame ********/

        begin_form(rem_get('compose'),'name="compose"');
?>
<style>
.table thead th a {
    text-shadow: 0px 1px 1px #000;
    color: #FFF;
    text-decoration: none;
}
</style>
	<br />
<div class='table-responsive'><table class='table table-striped'>
<thead><tr><th><center>
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox"><b><?php echo T_("OVERVIEW"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox?inbox"><b><?php echo T_("INBOX"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox?outbox"><b><?php echo T_("OUTBOX"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox?draft"><b><?php echo T_("DRAFT"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox?templates"><b><?php echo T_("TEMPLATES"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox?compose"><b><?php echo T_("COMPOSE"); ?></b></a>
</center></th></tr></thead>
</table></div>
	<?php
	

	echo "<br /><br />";
        if ($subject) $hidden .= "<input type=\"hidden\" name=\"oldsubject\" value=\"$subject\" />";
        if ($hidden) print($hidden);
        echo "<center><div id='tablebox'><br /><table class='table_mb' width='591px' border='1' align='center' cellpadding='0' cellspacing='0'></center>";
        if (!isset($_GET['templates'])){
            echo "<tr><td align='right'><b>" . T_("TO") . "&nbsp;:&nbsp;</b></td><td> $to</td></tr>";


            $res = DB::run("SELECT * FROM `messages` WHERE `sender` = $CURUSER[id] AND `location` = 'template' ORDER BY `subject`");
            if ($res->rowCount() > 0)
            {
                $tmp = "<select name=\"usetemplate\" onchange=\"toggleTemplate(this);\">\n<option name=\"0\">---</option>\n";
                while ($arr = $res->fetch(PDO::FETCH_ASSOC)) $tmp .= "<option value=\"$arr[id]\">$arr[subject]</option>\n";
                $tmp .= "</select><br />\n";
                echo "<tr><td align='right'><b>".T_("TEMPLATES")."&nbsp;:&nbsp;</b></td><td>$tmp</td></tr>";
            }
        }
        echo "<tr><td align='right'><b>".T_("SUBJECT")."&nbsp;:&nbsp;</b></td><td><input name=\"subject\" type=\"text\" size=\"40\" value=\"$subject\"></td></tr>";
//
//   tr2("Message","<textarea name=\"msg\" cols=\"50\" rows=\"15\">$msg</textarea>", 1);
        require_once("helpers/bbcode_helper.php");
        echo "</table>";
        print textbbcode("compose","msg","$msg");
        echo "<table width='600px' border='0' align='center' cellpadding='4' cellspacing='0'>";

        if (!isset($_GET['templates'])) $output .= "<input type=\"submit\" name=\"send\" value=\"Send\" />&nbsp;<label><input type=\"checkbox\" name=\"save\" checked='checked' />Save Copy In Outbox</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"draft\" value=\"Save Draft\" />&nbsp;";
        echo "<tr><td align='left'>$output<input type=\"submit\" name=\"template\" value=\"".T_("SAVE_TEMPLATE")."\" /></td></tr>";
        echo "</table><br /></div></center><br /><br />";
        end_form();
        end_frame();
        stdfoot();
        die;
    }
    end_frame();
}

begin_frame($pagename);
usermenu ();
echo "<center>";
?>
<style>
.table thead th a {
    text-shadow: 0px 1px 1px #000;
    color: #FFF;
    text-decoration: none;
}
</style>
	<br />
<div class='table-responsive'><table class='table table-striped'>
<thead><tr><th><center>
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox"><b><?php echo T_("OVERVIEW"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox?inbox"><b><?php echo T_("INBOX"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox?outbox"><b><?php echo T_("OUTBOX"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox?draft"><b><?php echo T_("DRAFT"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox?templates"><b><?php echo T_("TEMPLATES"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo $site_config['SITEURL']; ?>/mailbox?compose"><b><?php echo T_("COMPOSE"); ?></b></a>
</center></th></tr></thead>
</table></div>	
<?php
echo "<br />";

if ($type == "Overview")
{

    $res = DB::run("SELECT COUNT(*), COUNT(`unread` = 'yes') FROM messages WHERE `receiver` = $CURUSER[id] AND `location` IN ('in','both')");
    $res = DB::run("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND `location` IN ('in','both')");
    $inbox = $res->fetchColumn();
    $res = DB::run("SELECT COUNT(*) FROM messages WHERE `receiver` = " . $CURUSER["id"] . " AND `location` IN ('in','both') AND `unread` = 'yes'");
    $unread = $res->fetchColumn();
    $res = DB::run("SELECT COUNT(*) FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` IN ('out','both')");
    $outbox = $res->fetchColumn();
    $res = DB::run("SELECT COUNT(*) FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` = 'draft'");
    $draft = $res->fetchColumn();
    $res = DB::run("SELECT COUNT(*) AS count FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` = 'template'");
    $template = $res->fetchColumn(); //Mysqli Result Need to change It
	echo"<br />";
	echo("<center><div id='tablebox'><table class='table_mb' align='center' border='1' width='40%' cellspacing='5' cellpadding='5'><br /></center>");
	echo('<tr><td class="table_head" align="center" colspan="2"><b><i>'.T_("OVERVIEW_INFO").'</i></b></td></tr>');
	echo('<tr><td align="right" width="25%"><!--<a href="<?php echo $site_config[SITEURL]; ?>/mailbox?inbox">-->'.T_("INBOX").' :</a></td><td align="center" "width="25%" >'. " [<font color=green> $inbox </font>] ".P_("", $inbox)." (<font color=red>$unread ".T_("UNREAD")."</font>)</td></tr>");
	echo('<tr><td align="right" width="25%"><!--<a href="<?php echo $site_config[SITEURL]; ?>/mailbox?outbox">-->'.T_("OUTBOX").' :</a></td><td align="center" width="25%">'. " [ $outbox ] ".P_("", $outbox)."</td></tr>");
	echo('<tr><td align="right" width="25%"><!--<a href="<?php echo $site_config[SITEURL]; ?>/mailbox?draft">-->'.T_("DRAFT").' :</a></td><td align="center" width="25%">'. " [ $draft ] ".P_("", $draft)."</td></tr>");
	echo('<tr><td align="right" width="25%"><!--<a href="<?php echo $site_config[SITEURL]; ?>/mailbox?templates">-->'.T_("TEMPLATES").' :</a></td><td align="center" width="25%">'. " [ $template ] ".P_("", $template)."</td></tr>");
	echo('</table><br /></div>');
    echo"<br /><br />";
}
elseif ($type == "Mail")//////////////////////////////////////////////////////////////////////////////////
{
begin_form();
	echo("<br /><center><div id='tablebox'>");
    $order = order("added,sender,sendto,subject", "added", true);
    $res = DB::run("SELECT COUNT(*) AS count FROM messages WHERE $where");
    $count = $res->fetchColumn();
    list($pagertop, $pagerbottom, $limit) = pager2(20, $count);

    print($pagertop);
	echo("<table class='table_mb' align='center' border='1' width='97%' cellspacing='5' cellpadding='5'><br /></center>\n");
    $table['&nbsp;']  = th("<input type=\"checkbox\" onclick=\"toggleChecked(this.checked);this.form.remove.disabled=true;\" />", 1);
    $table['Unread']  = th_center("".T_("READ")."",'unread');
	$table['Sender']  = th_left("".T_("SENDER")."",'sender');
    $table['Sent_to'] = th_left("".T_("SENT_TO")."",'receiver');
    $table['Subject'] = th_left("".T_("SUBJECT")."",'subject');
    $table['Date']    = th_left("".T_("DATE")."",'added');
    table($table, $tablefmt);

    $res = DB::run("SELECT * FROM messages WHERE $where $order $limit");
    while ($arr = $res->fetch(PDO::FETCH_ASSOC))
    {
        unset($table);
        $userid = 0;
        $format = '';
        $reading = false;

        if ($arr["sender"] == $CURUSER['id']) $sender = "Yourself";
        elseif (is_valid_id($arr["sender"]))
        {
            $res2 = DB::run("SELECT username FROM users WHERE `id` = $arr[sender]");
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $sender = "<a href=\"/users?id=$arr[sender]\">".($arr2["username"] ? $arr2["username"] : "[Deleted]")."</a>";
        }
        else $sender = T_("SYSTEM");
//    $sender = $arr['sendername'];

        if ($arr["receiver"] == $CURUSER['id']) $sentto = "Yourself";
        elseif (is_valid_id($arr["receiver"]))
        {
            $res2 = DB::run("SELECT username FROM users WHERE `id` = $arr[receiver]");
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $sentto = "<a href=\"/users?id=$arr[receiver]\">".($arr2["username"] ? $arr2["username"] : "[Deleted]")."</a>";
        }
        else $sentto = T_("SYSTEM");

        $subject = ($arr['subject'] ? htmlspecialchars($arr['subject']) : "no subject");

        if (@$_GET['read'] == $arr['id'])
        {
            $reading = true;
            if (isset($_GET['inbox']) && $arr["unread"] == "yes") DB::run("UPDATE messages SET `unread` = 'no' WHERE `id` = $arr[id] AND `receiver` = $CURUSER[id]");
        }
        if ($arr["unread"] == "yes")
        {
            $format = "font-weight:bold;";
            $unread = true;
			$unread = "<font color=red>".T_("NO")."</font>";
        }
    else
	$unread = "<font color=green>".T_("YES")."</font>";

        $table['&nbsp;']  = th_left("<input type=\"checkbox\" name=\"msgs[$arr[id]]\" ".($reading ? "checked='checked'" : "")." onclick=\"this.form.remove.disabled=true;\" />", 1);
        $table['Unread']  = th_center("$unread");
		$table['Sender']  = th_left("$sender", 1, $format);
        $table['Sent_to'] = th_left("$sentto", 1, $format);
        $table['Subject'] = th_left("<a href=\"javascript:read($arr[id]);\"><img src=\"".$site_config["SITEURL"]."/images/plus.gif\" id=\"img_$arr[id]\" class=\"read\" border=\"0\" alt='' /></a>&nbsp;<a href=\"javascript:read($arr[id]);\">$subject</a>", 1, $format);
        $table['Date']    = th_left(utc_to_tz($arr['added']), 1, $format);

        table($table, $tablefmt);

        $display = "<div>".format_comment($arr['msg'])."<br /><br />";
		$display .= "</div><br />";
        if (isset($_GET['inbox']) && is_valid_id($arr["sender"]))   $display .= "<input type=\"submit\" name=\"compose[$arr[id]]\" value=\"Reply\" />&nbsp;\n";
        elseif (isset($_GET['draft']) || isset($_GET['templates'])) $display .= "<input type=\"submit\" name=\"compose[$arr[id]]\" value=\"Edit\" />&nbsp;";
        if (isset($_GET['inbox']) && $arr['unread'] == 'yes') $display .= "<input type=\"submit\" name=\"mark[$arr[id]]\" value=\"Mark as Read\" />&nbsp;\n";
        $display .= "<input type=\"submit\" name=\"remove[$arr[id]]\" value=\"Delete\" />&nbsp;\n";
        table(td_left($display, 1, "padding:0 6px 6px 6px"), $tablefmt, "id=\"msg_$arr[id]\" style=\"display:none;\"");
    }
	print("</table><br />");
	print($pagerbottom);
	print("</div><br /><br />");

	print("<center><div id='tablebox'><table align='center' border='0' width='98%' cellspacing='1' cellpadding='1'></center>\n");

// if ($count)
//{
    $buttons = "<input type=\"button\" value=\"".T_("SELECTED_DELETE")."\" onclick=\"this.form.remove.disabled=!this.form.remove.disabled;\" />";
    $buttons .= "<input type=\"submit\" name=\"remove\" value=\"...confirm\" disabled=\"disabled\" />";
    if (isset($_GET['inbox']) && $unread) $buttons .= "&nbsp;<input type=\"button\" value=\"Mark Selected as Read\" onclick=\"this.form.mark.disabled=!this.form.mark.disabled;\" /><input type=\"submit\" name=\"mark\" value=\"...confirm\" disabled=\"disabled\" />";
    if (isset($_GET['templates'])) $buttons .= "&nbsp;<input type=\"submit\" name=\"compose\" value=\"Create New Template\" />";
    table(td_left($buttons, 1, "border:0"), $tablefmt);
//}
	print("</table></div>");
	print("<br />");   
	print("<br />");
}
end_frame();

stdfoot();
}
  }