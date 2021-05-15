<?php
class Messages extends Controller
{

    public function __construct()
    {
        Auth::user();
        $this->messageModel = $this->model('Message');
        $this->valid = new Validation();
    }
    
    public function index()
    {
        $arr = $this->messageModel->countmsg();
        $data = [
            'inbox' => $arr['inbox'],
            'unread' => $arr['unread'],
            'outbox' => $arr['outbox'],
            'draft' => $arr['draft'],
            'template' => $arr['template']
        ];
        $this->view('message/overview', $data, true);
    }

    public function inbox()
    {
        // Mark or Delete
        $do = $_REQUEST["do"];
        if ($do == "del") {
            if ($_POST["read"]) {
                if (!@count($_POST["del"])) {
                    Session::flash('info', Lang::T("NOTHING_SELECTED"), URLROOT."/messages/inbox");
                }
                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("UPDATE messages SET `unread` = 'no' WHERE `id` IN ($ids)");
            } else {
                if (!@count($_POST["del"])) {
                    Session::flash('info', Lang::T("NOTHING_SELECTED"), URLROOT."/messages/inbox");
                }

                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = $_SESSION[id] AND `id` IN ($ids)");
                DB::run("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = $_SESSION[id] AND `id` IN ($ids)");
            }
            Session::flash('info', "Action Completed", URLROOT."/messages/inbox");
            die;
        }

        // Get Page from url
        $inbox = isset($_GET['inbox']) ? $_GET['inbox'] : null;
        $pagename = 'Inbox';
        $where = "`receiver` = $_SESSION[id] AND `location` IN ('in','both') ORDER BY added ASC";
        // Pagination
        $row = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "messages/inbox&amp;");

        // Set database query for views
        $res = DB::run("SELECT * FROM messages WHERE $where $limit");
        $data = [
            'pagename' => $pagename,
            'pagerbottom' => $pagerbottom,
            'mainsql' => $res
        ];
        $this->view('message/inbox', $data, true);
    }

    public function outbox()
    {
        // Mark or Delete
        $do = $_REQUEST["do"];
        if ($do == "del") {
            if (!empty($_POST)) {
                if (!@count($_POST["del"])) {
                    Session::flash('info', Lang::T("NOTHING_SELECTED"), URLROOT."/messages/inbox");
                }
                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("UPDATE messages SET `location` = 'in' WHERE `location` = 'both' AND `sender` = $_SESSION[id] AND `id` IN ($ids)");
                DB::run("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = $_SESSION[id] AND `id` IN ($ids)");
            }
            Session::flash('info', "Action Completed", URLROOT."/messages/outbox");
            die;
        }

        $pagename = 'Outbox';
        $where = "`sender` = $_SESSION[id] AND `location` IN ('out','both') ORDER BY added ASC";
        // Pagination
        $row = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "messages/outbox&amp;");
        // Set database query for views
        $res = DB::run("SELECT * FROM messages WHERE $where $limit");
        $data = [
            'pagename' => $pagename,
            'pagerbottom' => $pagerbottom,
            'mainsql' => $res
        ];
        $this->view('message/outbox', $data, true);
    }

    public function create()
    {
        $id = $_GET['id'];
        $data = [
            'id' => $id,
        ];
        $this->view('message/create', $data, true);
    }

    public function submit()
    {        
        $receiver = $_POST['receiver'];
        $subject = $_POST['subject'];
        $body = $_POST['body'];
        if ($body == "") {
            Session::flash('info', "Body cannot be empty!", URLROOT . "/forums");
        }
        if ($receiver == "") {
            Session::flash('info', "Receiver cannot be empty!", URLROOT . "/forums");
        }
        if ($subject == "") {
            Session::flash('info', "Subject cannot be empty!", URLROOT . "/forums");
        }
        // POST Var Button Switch
        $this->checktype($_REQUEST['Update'], $receiver, $subject, $body);
    }

    public function read()
    {
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
        <a href='".URLROOT."/messages/update?type=templates&amp;id=$id'><button  class='btn btn-sm btn-success'>Edit</button></a>
        ";
        } elseif (isset($draft)) {
            $button = "
        <a href='".URLROOT."/messages/update?type=draft&amp;id=$id'><button  class='btn btn-sm btn-success'>Edit</button></a>
        ";
        } elseif (isset($outbox)) {
            $button = "
            <a href='".URLROOT."/messages/reply?type=outbox&amp;id=$id'><button  class='btn btn-sm btn-success'>Reply</button></a>
            <a href='".URLROOT."/messages/update?type=outbox&amp;id=$id'><button  class='btn btn-sm btn-success'>Edit</button></a>
            ";
        } else {
            $button = "
            <a href='".URLROOT."/messages/reply?type=inbox&amp;id=$id'><button  class='btn btn-sm btn-success'>Reply</button></a>
            <a href='".URLROOT."/messages/update?type=inbox&amp;id=$id'><button  class='btn btn-sm btn-success'>Edit</button></a>
            ";
        }
        // get row
        $res = DB::run("SELECT * FROM messages WHERE id=$id");
        $arr = $res->fetch(PDO::FETCH_ASSOC);

        if ($arr["sender"] != $_SESSION['id'] && $arr["receiver"] != $_SESSION['id']) {
            Session::flash('info', "Not your Message!", URLROOT);
        }

        // mark read
        if ($arr["unread"] == "yes" && $arr["receiver"] == $_SESSION['id']) {
            DB::run("UPDATE messages SET `unread` = 'no' WHERE `id` = $arr[id] AND `receiver` = $_SESSION[id]");
        }
        // get history
        $arr4 = DB::run("SELECT * FROM messages WHERE subject=? AND added <=?  ORDER BY id DESC ", [$arr["subject"], $arr['added']]);
        // $lastposter get sender of message
        $arr5 = DB::run("SELECT username FROM users WHERE id=?", [$arr["sender"]])->fetch();
        $lastposter = "<a href='" . URLROOT . "/profile?id=" . $arr["sender"] . "'><b>" . Users::coloredname($arr5["username"]) . "</b></a>";
        if ($arr["sender"] == 0) {
            $lastposter = "<font class='error'><b>System</b></font>";
        }

        $data = [
            'id' => $id,
            'button' => $button,
            'arr' => $arr,
            'lastposter' => $lastposter,
            'arr4' => $arr4,
            'subject' => $arr['subject'],
            'added' => $arr['added'],
            'msg' => $arr['msg'],
        ];
        $this->view('message/read', $data, true);
    }

    public function update()
    {
        // Get Page from url
        if (isset($_GET['id'])) {
            if (!empty($_POST)) {
                $id = isset($_GET['id']) ? $_GET['id'] : null;
                $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
                $msg = isset($_POST['msg']) ? $_POST['msg'] : '';
                // Update the record
                $stmt = DB::run('UPDATE messages SET subject = ?, msg = ? WHERE id = ?', [$subject, $msg, $id]);
                Session::flash('info', "Edited Successfully!", URLROOT."/messages/inbox");
            }

            $stmt = DB::run('SELECT * FROM messages WHERE id = ?', [$_GET['id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $msg = $row['msg'];
            if (!$row) {
                Session::flash('info', "Message does not exist with that ID!", URLROOT."/messages/inbox");
            }
            // get the username
            $stmt7 = DB::run('SELECT * FROM messages WHERE id = ?', [$_GET['id']]);
            $row7 = $stmt7->fetch(PDO::FETCH_ASSOC);
            $arr27 = DB::run("SELECT username FROM users WHERE id=?", [$row7['receiver']])->fetch(PDO::FETCH_LAZY);
            $username = $arr27["username"];
            $ress1 = DB::run("SELECT * FROM `messages` WHERE `sender` = $_SESSION[id] AND `location` = 'template' ORDER BY `subject`");
        }

            $data = [
                'username' => $username,
                'msg' => $msg,
                'subject' => $row['subject'],
                'id' => $row['id'],
            ];
            $this->view('message/edit', $data, true);
        }

    public function reply()
    {
        // Get Stuff from URL
        $url_id = isset($_GET['id']) ? $_GET['id'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : null;

        $stmt = DB::run('SELECT * FROM messages WHERE id = ?', [$url_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($type == 'inbox') {
            $arr2 = DB::run("SELECT username,id FROM users WHERE id=?", [$row['sender']])->fetch(PDO::FETCH_LAZY);
        } else {
            $arr2 = DB::run("SELECT username,id FROM users WHERE id=?", [$row['receiver']])->fetch(PDO::FETCH_LAZY);
        }
        $username = $arr2["username"];
        $msg = $row['msg'];

        $data = [
            'username' => $username,
            'userid' => $arr2['id'],
            'msg' => $msg,
            'subject' => $row['subject'],
            'id' => $row['id'],
        ];
        $this->view('message/reply', $data, true);
    }

    public function templates()
    {
        // Mark or Delete
        $do = $_REQUEST["do"];
        if ($do == "del") {
            if ($_POST) {
                if (!@count($_POST["del"])) {
                    Session::flash('info', Lang::T("NOTHING_SELECTED"), URLROOT."/messages/templates");
                }
                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("DELETE FROM messages WHERE `sender` = $_SESSION[id] AND `location` = 'template' AND `id` IN ($ids)");
            }
            Session::flash('info', "Action Completed", URLROOT."/messages/templates");
            die;
        }

        $pagename = 'Templates';
        $where = "`sender` = $_SESSION[id] AND `location` = 'template'";
        // Pagination
        $row = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "messages/templates&amp;");
        // Set database query for views
        $res = DB::run("SELECT * FROM messages WHERE $where $limit");
        $data = [
            'res' => $res,
            'pagename' => $pagename,
            'pagerbottom' => $pagerbottom,
        ];
        $this->view('message/template', $data, true);
    }


    public function draft()
    {
        // Mark or Delete
        $do = $_REQUEST["do"];
        if ($do == "del") {
            if ($_POST) {
                if (!@count($_POST["del"])) {
                    Session::flash('info', Lang::T("NOTHING_SELECTED"), URLROOT."/messages/draft");
                }
                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("DELETE FROM messages WHERE `sender` = $_SESSION[id] AND `location` = 'draft' AND `id` IN ($ids)");
            }
            Session::flash('info', "Action Completed", URLROOT."/messages/draft");
            die;
        }

        $pagename = 'Draft';
        $where = "`sender` = $_SESSION[id] AND `location` = 'draft'";
        // Pagination
        $row = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "messages/draft&amp;");
        // Set database query for views
        $res = DB::run("SELECT * FROM messages WHERE $where $limit");
        $data = [
            'res' => $res,
            'pagename' => $pagename,
            'pagerbottom' => $pagerbottom,
        ];
        $this->view('message/draft', $data, true);
    }

    public function checktype($type, $receiver, $subject, $body)
    {
        switch ($type) {
        case 'create':
            if (isset($_POST['save'])) {
                DB::run( "INSERT INTO `messages`
                       (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                       VALUES (?,?,?,?,?,?,?)",
                       [$_SESSION['id'], $receiver, TimeDate::get_date_time(), $subject, $body, 'yes', 'both']
                );
            } else {
                DB::run("INSERT INTO `messages`
                       (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                       VALUES (?,?,?,?,?,?,?)",
                      [$_SESSION['id'], $receiver, TimeDate::get_date_time(), $subject, $body, 'yes', 'in']
                );
            }
            Session::flash('info', "yeah i posted a new post!", URLROOT."/messages/outbox");
            break;

        case 'draft':
            $status = 'no';
            $to = 'draft';
            DB::run("INSERT INTO `messages`
                   (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                    VALUES (?,?,?,?,?,?,?)",
                   [$_SESSION['id'], $receiver, TimeDate::get_date_time(), $subject, $body, $status, $to]
            );
            Session::flash('info', "yeah i posted a draft!", URLROOT."/messages/draft");
            break;

        case 'template':
            $status = 'no';
            $to = 'template';
            DB::run("INSERT INTO `messages`
                   (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`)
                    VALUES (?,?,?,?,?,?,?)",
                   [$_SESSION['id'], $receiver, TimeDate::get_date_time(), $subject, $body, $status, $to]
            );
            Session::flash('info', "yeah i posted a template!", URLROOT."/messages/templates");
            break;
    }
    }

}