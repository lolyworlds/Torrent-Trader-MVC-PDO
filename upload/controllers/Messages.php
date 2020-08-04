<?php
class Messages extends Controller
{

    public function __construct()
    {
        $this->messageModel = $this->model('Message');
    }
    /**
     * View Landing Page.
     */
    public function index()
    {
        dbconn();
        global $site_config, $CURUSER;
        loggedinonly();
        stdhead("Index");
        begin_frame("Index");
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
        include 'views/message/usernavbar.php';
        include 'views/message/messagenavbar.php';
        echo("<center><div id='tablebox'><table class='table_mb' align='center' border='1' width='60%' cellspacing='5' cellpadding='5'></center>");
        echo('<tr><td class="table_head" align="center" colspan="2"><b><i>'.T_("OVERVIEW_INFO").'</i></b></td></tr>');
        echo('<tr><td align="right" width="25%"><!--<a href="<?php echo $site_config[SITEURL]; ?>/messages/inbox">-->'.T_("INBOX").' :</a></td><td align="center" "width="25%" >'. " [<font color=green> $inbox </font>] ".P_("", $inbox)." (<font color=red>$unread ".T_("UNREAD")."</font>)</td></tr>");
        echo('<tr><td align="right" width="25%"><!--<a href="<?php echo $site_config[SITEURL]; ?>/messages/outbox">-->'.T_("OUTBOX").' :</a></td><td align="center" width="25%">'. " [ $outbox ] ".P_("", $outbox)."</td></tr>");
        echo('<tr><td align="right" width="25%"><!--<a href="<?php echo $site_config[SITEURL]; ?>/messages/draft">-->'.T_("DRAFT").' :</a></td><td align="center" width="25%">'. " [ $draft ] ".P_("", $draft)."</td></tr>");
        echo('<tr><td align="right" width="25%"><!--<a href="<?php echo $site_config[SITEURL]; ?>/messages/templates">-->'.T_("TEMPLATES").' :</a></td><td align="center" width="25%">'. " [ $template ] ".P_("", $template)."</td></tr>");
        echo('</table><br /></div>');
        echo"<br /><br />";
        end_frame();
        stdfoot();
    }
    /**
     * View Read.
     */
    public function read()
    {
        dbconn();
        global $site_config, $CURUSER;
        // Get Message Id from url
        $id = (int) $_GET['id'];

        // Get Page from url
        $inbox = isset($_GET['inbox']) ? $_GET['inbox'] : null;
        $outbox = isset($_GET['outbox']) ? $_GET['outbox'] : null;
        $draft = isset($_GET['draft']) ? $_GET['draft'] : null;
        $templates = isset($_GET['templates']) ? $_GET['templates'] : null;

        // Set button condition
        if (isset($templates)) {
            $button = "
        <a href='$site_config[SITEURL]/messages/delete?templates&amp;id=$id'><button  class='btn btn-sm btn-success'>Delete</button></a>
        <a href='$site_config[SITEURL]/messages/update?templates&amp;id=$id'><button  class='btn btn-sm btn-success'>Edit</button></a>
        ";
        } elseif (isset($draft)) {
            $button = "
        <a href='$site_config[SITEURL]/messages/delete?draft&amp;id=$id'><button  class='btn btn-sm btn-success'>Delete</button></a>
        <a href='$site_config[SITEURL]/messages/update?draft&amp;id=$id'><button  class='btn btn-sm btn-success'>Edit</button></a>
        ";
        } elseif (isset($outbox)) {
            $button = "
            <a href='$site_config[SITEURL]/messages/reply?outbox&amp;id=$id'><button  class='btn btn-sm btn-success'>Reply</button></a>
            <a href='$site_config[SITEURL]/messages/delete?outbox&amp;id=$id'><button  class='btn btn-sm btn-success'>Delete</button></a>
            <a href='$site_config[SITEURL]/messages/update?outbox&amp;id=$id'><button  class='btn btn-sm btn-success'>Edit</button></a>
            ";
        } else {
            $button = "
            <a href='$site_config[SITEURL]/messages/reply?inbox&amp;id=$id'><button  class='btn btn-sm btn-success'>Reply</button></a>
            <a href='$site_config[SITEURL]/messages/delete?inbox&amp;id=$id'><button  class='btn btn-sm btn-success'>Delete</button></a>
            <a href='$site_config[SITEURL]/messages/update?inbox&amp;id=$id'><button  class='btn btn-sm btn-success'>Edit</button></a>
            ";
        }
        // get row
        $res = DB::run("SELECT * FROM messages WHERE id=$id");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        // mark read
        if ($arr["unread"] == "yes" && $arr["receiver"] == $CURUSER['id']) {
            DB::run("UPDATE messages SET `unread` = 'no' WHERE `id` = $arr[id] AND `receiver` = $CURUSER[id]");
        }
        // get history
        $arr4 = DB::run("SELECT * FROM messages WHERE subject=? AND added <=?  ORDER BY id DESC ", [$arr["subject"], $arr['added']])->fetchAll();

        // $lastposter get sender of message
        $arr5 = DB::run("SELECT username FROM users WHERE id=?", [$arr["sender"]])->fetch();
        $lastposter = "<a href='" . TTURL . "/users/profile?id=" . $arr["sender"] . "'><b>" . class_user_colour($arr5["username"]) . "</b></a>";
        if ($arr["sender"] == 0) {
            $lastposter = "<font class='error'><b>System</b></font>";
        }

        stdhead("Message");
        begin_frame($arr["subject"]);
        if (!isset($draft) && !isset($templates)) {
            include 'views/message/readone.php';
        } else {
            include 'views/message/readtwo.php';
        }

        end_frame();
        stdfoot();
    }
    /**
     * View Update.
     */
    public function update()
    {
        dbconn();
        global $site_config, $CURUSER;

        // Get Page from url
        $inbox = isset($_GET['inbox']) ? $_GET['inbox'] : null;
        $outbox = isset($_GET['outbox']) ? $_GET['outbox'] : null;
        $draft = isset($_GET['draft']) ? $_GET['draft'] : null;
        $templates = isset($_GET['templates']) ? $_GET['templates'] : null;

        if (isset($_GET['id'])) {
            if (!empty($_POST)) {
                $id = isset($_GET['id']) ? $_GET['id'] : null;
                $receiver = isset($_POST['receiver']) ? $_POST['receiver'] : null;
                $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
                $msg = isset($_POST['msg']) ? $_POST['msg'] : '';
                // Update the record
                if (isset($draft)) {
                $stmt = DB::run('UPDATE messages SET receiver = ?, subject = ?, msg = ? WHERE id = ?', [$receiver, $subject, $msg, $id]);
                
                } elseif (isset($templates)) {
                    $stmt = DB::run('UPDATE messages SET subject = ?, msg = ? WHERE id = ?', [$subject, $msg, $id]);
                
                } elseif (isset($outbox)) {
                    $stmt = DB::run('UPDATE messages SET msg = ? WHERE id = ?', [$msg, $id]);
                    autolink(TTURL . '/messages/outbox', "Edited Outbox!");
                } elseif (isset($inbox)) {
                    $stmt = DB::run('UPDATE messages SET msg = ? WHERE id = ?', [$msg, $id]);
                }

                autolink(TTURL . '/messages/inbox', "Edited Successfully!");
            }

            $stmt = DB::run('SELECT * FROM messages WHERE id = ?', [$_GET['id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $msg = $row['msg'];
            if (!$row) {
                autolink(TTURL . '/messages/inbox', "Message does not exist with that ID!");
            }
            // get the username
            $stmt7 = DB::run('SELECT * FROM messages WHERE id = ?', [$_GET['id']]);
            $row7 = $stmt7->fetch(PDO::FETCH_ASSOC);
            $arr27 = DB::run("SELECT username FROM users WHERE id=?", [$row7['receiver']])->fetch(PDO::FETCH_LAZY);
            $username = $arr27["username"];


            $ress1 = DB::run("SELECT * FROM `messages` WHERE `sender` = $CURUSER[id] AND `location` = 'template' ORDER BY `subject`");
        }

        stdhead("edit");
        begin_frame('edit');

        // Set some conditions
        if (isset($draft)) {
            include 'views/message/editdraft.php';
        } elseif (isset($templates)) {
            include 'views/message/edittemplate.php';
        } elseif (isset($outbox)) {
            include 'views/message/edit.php';
        } elseif (isset($inbox)) {
            include 'views/message/editinbox.php';
        }
        end_frame();
        stdfoot();
    }
    /**
     * View Delete.
     */
    public function delete()
    {
        dbconn();
        loggedinonly();
        global $CURUSER;
        // Get Page from url
        $inbox = isset($_GET['inbox']) ? $_GET['inbox'] : null;
        $outbox = isset($_GET['outbox']) ? $_GET['outbox'] : null;
        $draft = isset($_GET['draft']) ? $_GET['draft'] : null;
        $templates = isset($_GET['templates']) ? $_GET['templates'] : null;
        $messageid = $_GET["id"];
        if (!is_valid_id($messageid)) {
            showerror(T_("ERROR"), T_("FORUMS_DENIED"));
        }
        // Update the record
        
            DB::run("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = $CURUSER[id] AND `id` IN ($messageid)");
            DB::run("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = $CURUSER[id] AND `id` IN ($messageid)");
        
           if (isset($outbox)) {
            DB::run("UPDATE messages SET `location` = 'in' WHERE `location` = 'both' AND `sender` = $CURUSER[id] AND `id` IN ($messageid)");
        }

        DB::run("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = $CURUSER[id] AND `id` IN ($messageid)");
        
        header("Location: " . TTURL . "/messages/inbox");
        die;
    }
    /**
     * View Create.
     */
    public function create()
    {
        dbconn();
        global $site_config, $CURUSER;
        // Get Stuff from URL
        $url_id = isset($_GET['id']) ? $_GET['id'] : null;
        $urlreply = isset($_GET['reply']) ? $_GET['reply'] : null;
        $urltemplate = isset($_GET['templates']) ? $_GET['templates'] : null;

        // Set Post Vars
        $create = $_POST['create'];
        $save = $_POST['save'];
        $draft = $_POST['draft'];
        $template = $_POST['template'];
        $receiver = $_POST['receiver'];
        $subject = $_POST['subject'];
        $body = $_POST['body'];

        // POST Var Button Switch
        switch ($_REQUEST['Update']) {
            case 'create':
                if (isset($_POST['save'])) {
                    DB::run("INSERT INTO `messages`
                (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                VALUES (?,?,?,?,?,?,?)",
                        [$CURUSER['id'], $receiver, get_date_time(), $subject, $body, 'yes', 'both']);
                } else {
                    DB::run("INSERT INTO `messages`
                (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                VALUES (?,?,?,?,?,?,?)",
                        [$CURUSER['id'], $receiver, get_date_time(), $subject, $body, 'yes', 'in']);
                }
                autolink(TTURL . '/messages/outbox', "yeah i posted a new post!");
                break;

            case 'draft':
                $status = 'no';
                $to = 'draft';
                DB::run("
                INSERT INTO `messages`
                (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                VALUES (?,?,?,?,?,?,?)",
                    [$CURUSER['id'], $receiver, get_date_time(), $subject, $body, $status, $to]);
                autolink(TTURL . '/messages/draft', "yeah i posted a draft!");
                break;

            case 'template':
                $status = 'no';
                $to = 'template';
                DB::run("
                INSERT INTO `messages`
                (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                VALUES (?,?,?,?,?,?,?)",
                    [$CURUSER['id'], $receiver, get_date_time(), $subject, $body, $status, $to]);
                autolink(TTURL . '/messages/templates', "yeah i posted a template!");
                break;

        }

        // User & Template Dropdown List
        $ress = DB::run("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
        $ress1 = DB::run("SELECT * FROM `messages` WHERE `sender` = $CURUSER[id] AND `location` = 'template' ORDER BY `subject`");

        stdhead("compose");
        begin_frame("compose");
        if (isset($urltemplate)) {
            include 'views/message/template.php';
        } else {
            include 'views/message/create.php';
        }
        end_frame();
        stdfoot();
    }
    /**
     * View Reply.
     */
    public function reply()
    {
        dbconn();
        global $site_config, $CURUSER;
        // Get Stuff from URL
        $url_id = isset($_GET['id']) ? $_GET['id'] : null;
        $urlreply = isset($_GET['reply']) ? $_GET['reply'] : null;
        $urltemplate = isset($_GET['templates']) ? $_GET['templates'] : null;
        $inbox = isset($_GET['inbox']) ? $_GET['inbox'] : null;

        // Set Post Vars
        $create = $_POST['create'];
        $save = $_POST['save'];
        $draft = $_POST['draft'];
        $template = $_POST['template'];
        $receiver = $_POST['receiver'];
        $subject = $_POST['subject'];
        $body = $_POST['body'];

        // POST Var Button Switch
        switch ($_REQUEST['Update']) {
            case 'create':
                if (isset($_POST['save'])) {
                    DB::run("INSERT INTO `messages`
                (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                VALUES (?,?,?,?,?,?,?)",
                        [$CURUSER['id'], $receiver, get_date_time(), $subject, $body, 'yes', 'both']);
                } else {
                    DB::run("INSERT INTO `messages`
                (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                VALUES (?,?,?,?,?,?,?)",
                        [$CURUSER['id'], $receiver, get_date_time(), $subject, $body, 'yes', 'in']);
                }
                autolink(TTURL . '/messages/outbox', "yeah i posted a new post!");
                break;

            case 'draft':
                $status = 'no';
                $to = 'draft';
                DB::run("
                INSERT INTO `messages`
                (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                VALUES (?,?,?,?,?,?,?)",
                    [$CURUSER['id'], $receiver, get_date_time(), $subject, $body, $status, $to]);
                autolink(TTURL . '/messages/draft', "yeah i posted a draft!");
                break;

            case 'template':
                $status = 'no';
                $to = 'template';
                DB::run("
                INSERT INTO `messages`
                (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                VALUES (?,?,?,?,?,?,?)",
                    [$CURUSER['id'], $receiver, get_date_time(), $subject, $body, $status, $to]);
                autolink(TTURL . '/messages/templates', "yeah i posted a template!");
                break;

        }

        // Reply URL
       // if (isset($inbox)) {
            $stmt = DB::run('SELECT * FROM messages WHERE id = ?', [$url_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $arr2 = DB::run("SELECT username FROM users WHERE id=?", [$row['receiver']])->fetch(PDO::FETCH_LAZY);
            if ($arr2 == $CURUSER["username"]) {
                $username = $arr2["username"];
            } else {
                $username = "Yourself";
            }
            $msg = $row['msg'];
       // }

        // User & Template Dropdown List
        $ress = DB::run("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
        $ress1 = DB::run("SELECT * FROM `messages` WHERE `sender` = $CURUSER[id] AND `location` = 'template' ORDER BY `subject`");

        stdhead("compose");
        begin_frame("compose");
        include 'views/message/reply.php';
        end_frame();
        stdfoot();
    }
    /**
     * View Outbox.
     */
    public function outbox()
    {
        dbconn();
        global $site_config, $CURUSER;
        loggedinonly();

        // Mark or Delete
        $do = $_REQUEST["do"];
        if ($do == "del") {
            if (!empty($_POST)) {
                if (!@count($_POST["del"])) {
                    show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
                }

                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("UPDATE messages SET `location` = 'in' WHERE `location` = 'both' AND `sender` = $CURUSER[id] AND `id` IN ($ids)");
                DB::run("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = $CURUSER[id] AND `id` IN ($ids)");
            }
            autolink(TTURL . "/messages/outbox", "Action Completed");
            stdhead();
            show_error_msg(T_("SUCCESS"), "Action Completed", 0);
            stdfoot();
            die;
        }

        $pagename = 'Outbox';
        $where = "`sender` = $CURUSER[id] AND `location` IN ('out','both')";

        // Pagination
        $row = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "messages/outbox&amp;");

        // Set database query for views
        $res = DB::run("SELECT * FROM messages WHERE $where $limit");

        stdhead($pagename);
        begin_frame($pagename);
        include 'views/message/outboxtable.php';
        print($pagerbottom);
        end_frame();
        stdfoot();
    }
    /**
     * View Inbox.
     */
    public function inbox()
    {
        dbconn();
        global $site_config, $CURUSER;
        loggedinonly();

        // Mark or Delete
        $do = $_REQUEST["do"];
        if ($do == "del") {
            if ($_POST["read"]) {
                if (!@count($_POST["del"])) {
                    show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
                }
                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("UPDATE messages SET `unread` = 'no' WHERE `id` IN ($ids)");
            } else {
                if (!@count($_POST["del"])) {
                    show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
                }

                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = $CURUSER[id] AND `id` IN ($ids)");
                DB::run("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = $CURUSER[id] AND `id` IN ($ids)");
            }
            autolink(TTURL . "/messages", "Action Completed");
            stdhead();
            show_error_msg(T_("SUCCESS"), "Action Completed", 0);
            stdfoot();
            die;
        }

        // Get Page from url
        $inbox = isset($_GET['inbox']) ? $_GET['inbox'] : null;

        $pagename = 'Inbox';
        $where = "`receiver` = $CURUSER[id] AND `location` IN ('in','both') ORDER BY added DESC";

        // Pagination
        $row = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "messages/inbox&amp;");

        // Set database query for views
        $res = DB::run("SELECT * FROM messages WHERE $where $limit");

        stdhead($pagename);
        begin_frame($pagename);
        include 'views/message/indextable.php';
        print($pagerbottom);
        end_frame();
        stdfoot();
    }
    /**
     * View Template.
     */
    public function templates()
    {
        dbconn();
        global $site_config, $CURUSER;
        loggedinonly();

        // Mark or Delete
        $do = $_REQUEST["do"];
        if ($do == "del") {
            if ($_POST) {
                if (!@count($_POST["del"])) {
                    show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
                }

                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = $CURUSER[id] AND `id` IN ($ids)");
                DB::run("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = $CURUSER[id] AND `id` IN ($ids)");
            }
            autolink(TTURL . "/messages", "Action Completed");
            stdhead();
            show_error_msg(T_("SUCCESS"), "Action Completed", 0);
            stdfoot();
            die;
        }

        $pagename = 'Templates';
        $where = "`sender` = $CURUSER[id] AND `location` = 'template'";

        // Pagination
        $row = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "messages/templates&amp;");

        // Set database query for views
        $res = DB::run("SELECT * FROM messages WHERE $where $limit");

        stdhead($pagename);
        begin_frame($pagename);include 'views/message/templatetable.php';
        print($pagerbottom);
        end_frame();
        stdfoot();
    }
    /**
     * View Draft.
     */
    public function draft()
    {
        dbconn();
        global $site_config, $CURUSER;
        loggedinonly();

        // Mark or Delete
        $do = $_REQUEST["do"];
        if ($do == "del") {
            if ($_POST) {
                if (!@count($_POST["del"])) {
                    show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
                }

                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = $CURUSER[id] AND `id` IN ($ids)");
                DB::run("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = $CURUSER[id] AND `id` IN ($ids)");
            }
            autolink(TTURL . "/messages", "Action Completed");
            stdhead();
            show_error_msg(T_("SUCCESS"), "Action Completed", 0);
            stdfoot();
            die;
        }

        $pagename = 'Draft';
        $where = "`sender` = $CURUSER[id] AND `location` = 'draft'";

        // Pagination
        $row = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "messages/draft&amp;");

        // Set database query for views
        $res = DB::run("SELECT * FROM messages WHERE $where $limit");

        stdhead($pagename);
        begin_frame($pagename);
        include 'views/message/drafttable.php';
        print($pagerbottom);
        end_frame();
        stdfoot();
    }
}
