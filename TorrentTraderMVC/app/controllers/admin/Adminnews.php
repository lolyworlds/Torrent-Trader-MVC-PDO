<?php
class Adminnews extends Controller
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
        if (!$_SESSION['class'] > 5 || $_SESSION["control_panel"] != "yes") {
            show_error_msg(Lang::T("ERROR"), Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
        }
        $title = 'admin';
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        echo '<div class="border border-primary">';
        echo '<center>';
        echo '<b>Welcome To The Staff Panel</b>';
        echo '</center>';
        echo '</div>';
        require APPROOT . '/views/admin/footer.php';
    }


    public function newsview()
    {
        $title = Lang::T("NEWS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin(Lang::T("NEWS"));
        echo "<center><a href='" . URLROOT . "/adminnews/newsadd'><b>" . Lang::T("CP_NEWS_ADD_ITEM") . "</b></a></center><br />";
        $res = DB::run("SELECT * FROM news ORDER BY added DESC");
        if ($res->rowCount() > 0) {
            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                $newsid = $arr["id"];
                $body = format_comment($arr["body"]);
                $title = $arr["title"];
                $userid = $arr["userid"];
                $added = $arr["added"] . " GMT (" . (TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";
                $arr2 = DB::run("SELECT username FROM users WHERE id =?", [$userid])->fetch();
                $postername = Users::coloredname($arr2["username"]);
                if ($postername == "") {
                    $by = "Unknown";
                } else {
                    $by = "<a href='" . URLROOT . "/profile?id=$userid'><b>$postername</b></a>";
                }
                print("<table border='0' cellspacing='0' cellpadding='0'><tr><td>");
                print("$added&nbsp;---&nbsp;by&nbsp;$by");
                print(" - [<a href='" . URLROOT . "/adminnews/newsedit?newsid=$newsid'><b>" . Lang::T("EDIT") . "</b></a>]");
                print(" - [<a href='" . URLROOT . "/adminnews/newsdelete?newsid=$newsid'><b>" . Lang::T("DEL") . "</b></a>]");
                print("</td></tr>\n");
                print("<tr valign='top'><td><b>$title</b><br />$body</td></tr></table><br />\n");
            }
        } else {
            echo "No News Posted";
        }
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function newstakeadd()
    {
        $body = $_POST["body"];
        if (!$body) {
            show_error_msg(Lang::T("ERROR"), Lang::T("ERR_NEWS_ITEM_CAN_NOT_BE_EMPTY"), 1);
        }
        $title = $_POST['title'];
        if (!$title) {
            show_error_msg(Lang::T("ERROR"), Lang::T("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"), 1);
        }
        $added = $_POST["added"];
        if (!$added) {
            $added = TimeDate::get_date_time();
        }
        $afr = DB::run("INSERT INTO news (userid, added, body, title) VALUES (?,?,?,?)", [$_SESSION['id'], $added, $body, $title]);
        if ($afr) {
            Redirect::autolink(URLROOT . "/adminnews/newsview", Lang::T("CP_NEWS_ITEM_ADDED_SUCCESS"));
        } else {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_NEWS_UNABLE_TO_ADD"), 1);
        }
    }

    public function newsadd()
    {
        $title = Lang::T("NEWS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin(Lang::T("CP_NEWS_ADD"));
        print("<center><form method='post' action='" . URLROOT . "/adminnews/newstakeadd' name='news'>\n");
        print("<input type='hidden' name='action' value='news' />\n");
        print("<input type='hidden' name='do' value='takeadd' />\n");
        print("<b>" . Lang::T("CP_NEWS_TITLE") . ":</b> <input type='text' name='title' /><br />\n");
        include APPROOT . '/helpers/bbcode_helper.php';
        echo "<br />" . textbbcode("news", "body") . "<br />";
        print("<br /><br /><input type='submit' value='" . Lang::T("SUBMIT") . "' />\n");
        print("</form><br /><br /></center>\n");
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function newsedit()
    {
        $title = Lang::T("NEWS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $newsid = (int) $_GET["newsid"];
        if (!$this->valid->validId($newsid)) {
            show_error_msg(Lang::T("ERROR"), sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID"), $newsid), 1);
        }
        $res = DB::run("SELECT * FROM news WHERE id=?", [$newsid]);
        if ($res->rowCount() != 1) {
            show_error_msg(Lang::T("ERROR"), sprintf(Lang::T("CP_NEWS_NO_ITEM_WITH_ID"), $newsid), 1);
        }
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $body = $_POST['body'];
            if ($body == "") {
                show_error_msg(Lang::T("ERROR"), Lang::T("FORUMS_BODY_CANNOT_BE_EMPTY"), 1);
            }
            $title = $_POST['title'];
            if ($title == "") {
                show_error_msg(Lang::T("ERROR"), Lang::T("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"), 1);
            }
            $body = $body;
            $editedat = TimeDate::get_date_time();
            DB::run("UPDATE news SET body=?, title=? WHERE id=?", [$body, $title, $newsid]);
            $returnto = $_POST['returnto'];
            if ($returnto != "") {
                header("Location: $returnto");
            } else {
                Redirect::autolink(URLROOT . "/adminnews/newsview", Lang::T("CP_NEWS_ITEM_WAS_EDITED_SUCCESS"));
            }
        } else {
            $returnto = htmlspecialchars($_GET['returnto']);
            Style::begin(Lang::T("CP_NEWS_EDIT"));
            print("<form method='post' action='" . URLROOT . "/adminnews/newsedit?newsid=$newsid' name='news'>\n");
            print("<center>");
            print("<input type='hidden' name='returnto' value='$returnto' />\n");
            print("<b>" . Lang::T("CP_NEWS_TITLE") . ": </b><input type='text' name='title' value=\"" . $arr['title'] . "\" /><br /><br />\n");
            include APPROOT . '/helpers/bbcode_helper.php';
            echo "<br />" . textbbcode("news", "body", $arr["body"]) . "<br />";
            print("<br /><input type='submit' value='Okay' />\n");
            print("</center>\n");
            print("</form>\n");
        }
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function newsdelete()
    {
        $newsid = (int) $_GET["newsid"];
        if (!$this->valid->validId($newsid)) {
            show_error_msg(Lang::T("ERROR"), sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID"), $newsid), 1);
        }
        DB::run("DELETE FROM news WHERE id=?", [$newsid]);
        DB::run("DELETE FROM comments WHERE news =?", [$newsid]);
        Redirect::autolink(URLROOT . "/adminnews/newsview", Lang::T("CP_NEWS_ITEM_DEL_SUCCESS"));
    }

}