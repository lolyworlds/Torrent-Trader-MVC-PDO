<?php
class Comments extends Controller
{
    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
    }

    public function index()
    {
        require_once APPROOT . "/helpers/bbcode_helper.php";
        $id = (int) ($_GET["id"] ?? 0);
        $type = $_GET["type"];
        if (!isset($id) || !$id || ($type != "torrent" && $type != "news")) {
            Session::flash('warning', Lang::T("ERROR"), URLROOT."/home");
        }
        //NEWS
        if ($type == "news") {
            $res = DB::run("SELECT * FROM news WHERE id =?", [$id]);
            $row = $res->fetch(PDO::FETCH_LAZY);
            if (!$row) {
                Session::flash('warning', "News id invalid", URLROOT."/comments?type=news&id=$id");
            }
            Style::header(Lang::T("COMMENTS"));
            Style::begin(Lang::T("NEWS"));
            echo htmlspecialchars($row['title']) . "<br /><br />" . format_comment($row['body']) . "<br />";
            Style::end();
        }
        //TORRENT
        $title = Lang::T("COMMENTS");
        if ($type == "torrent") {
            $res = DB::run("SELECT id, name FROM torrents WHERE id =?", [$id]);
            $row = $res->fetch(PDO::FETCH_LAZY);
            if (!$row) {
                Session::flash('warning', "News id invalid", URLROOT."/home");
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='torrents/read?id=" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a>";
        }
        Style::header(Lang::T("COMMENTS"));
        Style::begin($title);
        $commcount = DB::run("SELECT COUNT(*) FROM comments WHERE $type =?", [$id])->fetchColumn();
        if ($commcount) {
            list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "comments?id=$id&amp;type=$type");
            $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE $type = $id ORDER BY comments.id $limit");
        } else {
            unset($commres);
        }
        if ($commcount) {
            print($pagertop);
            commenttable($commres, $type);
            print($pagerbottom);
        } else {
            print("<br><b>" . Lang::T("NOCOMMENTS") . "</b><br>\n");
        }
        echo "<center><div class='form-group'>";
        echo "<form name='comment' method='post' action=\"comments/take?type=$type&amp;id=$id\">";
        echo textbbcode("comment", "body") . "<br>";
        echo "<input type=\"submit\"  value=\"" . Lang::T("ADDCOMMENT") . "\" />";
        echo "</form></div></center>";
        Style::end();
        Style::footer();
    }

    public function edit()
    {
        require_once APPROOT . "/helpers/bbcode_helper.php";
        $id = (int) ($_GET["id"] ?? 0);
        $type = $_GET["type"];
        $edit = (int) ($_GET["edit"] ?? 0);
        if (!isset($id) || !$id || ($type != "torrent" && $type != "news")) {
            Session::flash('warning', Lang::T("ERROR"), URLROOT."/home");
        }
        $row = DB::run("SELECT user FROM comments WHERE id=?", [$id])->fetch();
        if (($type == "torrent" && $_SESSION["edit_torrents"] == "no" || $type == "news" && $_SESSION["edit_news"] == "no") && $_SESSION['id'] != $row['user']) {
            Session::flash('warning', Lang::T("ERR_YOU_CANT_DO_THIS"), URLROOT."/home");
        }
        $save = (int) $_GET["save"];
        if ($save) {
            $text = $_POST['text'];
            $result = DB::run("UPDATE comments SET text=? WHERE id=?", [$text, $id]);
            Logs::write(Users::coloredname($_SESSION['username']) . " has edited comment: ID:$id");
            Session::flash('warning', "Comment Edited OK", URLROOT."/home");
        }
        $arr = DB::run("SELECT * FROM comments WHERE id=?", [$id])->fetch();

        Style::header("Edit Comment");
        Style::begin(Lang::T("EDITCOMMENT"));
        print("<center><b> " . Lang::T("EDITCOMMENT") . " </b><p>\n");
        print("<form method=\"post\" name=\"comment\" action=\"" . URLROOT . "/comments/edit?type=$type&save=1&amp;id=$id\">\n");
        print textbbcode("comment", "text", htmlspecialchars($arr["text"]));
        print("<p><input type=\"submit\"  value=\"Submit Changes\" /></p></form></center>\n");
        Style::end();
        Style::footer();
        die();
    }

    public function delete()
    {
        $id = (int) ($_GET["id"] ?? 0);
        $type = $_GET["type"];
        if ($_SESSION["delete_news"] == "no" && $type == "news" || $_SESSION["delete_torrents"] == "no" && $type == "torrent") {
            Session::flash('warning', Lang::T("ERR_YOU_CANT_DO_THIS"), URLROOT."/home");
        }
        if ($type == "torrent") {
            $res = DB::run("SELECT torrent FROM comments WHERE id=?", [$id]);
            $row = $res->fetch(PDO::FETCH_ASSOC);
            if ($row["torrent"] > 0) {
                DB::run("UPDATE torrents SET comments = comments - 1 WHERE id = $row[torrent]");
            }
        }
        DB::run("DELETE FROM comments WHERE id =?", [$id]);
        Logs::write(Users::coloredname($_SESSION['username']) . " has deleted comment: ID: $id");
        Session::flash('warning', "Comment deleted OK", URLROOT."/home");
    }

    public function take()
    {
        $id = (int) ($_GET["id"] ?? 0);
        $type = $_GET["type"];
        $body = $_POST['body'];
        if (!$body) {
            Session::set('message', Lang::T("YOU_DID_NOT_ENTER_ANYTHING"));
            Redirect::to(URLROOT . "/comments?type=$type&id=$id");
        }
        if ($type == "torrent") {
            DB::run("UPDATE torrents SET comments = comments + 1 WHERE id = $id");
        }
        $ins = DB::run("INSERT INTO comments (user, " . $type . ", added, text) VALUES (?, ?, ?, ?)", [$_SESSION["id"], $id, TimeDate::get_date_time(), $body]);
        if ($ins) {
            Session::set('message', "Your Comment was added successfully.",URLROOT."/home" );
            Redirect::to(URLROOT . "/comments?type=$type&id=$id");
        } else {
            Session::set('message', Lang::T("UNABLE_TO_ADD_COMMENT"));
            Redirect::to(URLROOT . "/comments?type=$type&id=$id");
        }
    }

    public function user()
    {

        require_once APPROOT . "/helpers/bbcode_helper.php";
        $id = (int) ($_GET["id"] ?? 0);
        if (!isset($id) || !$id) {
            Session::flash('warning', Lang::T("ERROR"), URLROOT."/home");
        }

        //TORRENT
        $title = Lang::T("COMMENTS");
            
        $res = DB::run("SELECT 
            comments.id, text, user, comments.added, avatar, 
            signature, username, title, class, uploaded, downloaded, privacy, donated 
            FROM comments
            LEFT JOIN users 
            ON comments.user = users.id 
            WHERE user = $id ORDER BY comments.id "); //$limit
        //$res = DB::run("SELECT * FROM comments WHERE user =?", [$id]);
            $row = $res->fetch(PDO::FETCH_LAZY);
            if (!$row) {
                Session::flash('warning', "User id invalid", URLROOT."/home");
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='profile?id=" . $row['user'] . "'>&nbsp;$row[username]</a>";


        Style::header(Lang::T("COMMENTS"));
        Style::begin($title);

        $commcount = DB::run("SELECT COUNT(*) FROM comments WHERE user =? AND torrent = ?", [$id, 0])->fetchColumn();
        if ($commcount) {
            list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "comments?id=$id");
            $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE user = $id ORDER BY comments.id"); // $limit
        } else {
            unset($commres);
        }
        if ($commcount) {
            print($pagertop);
            commenttable($commres, 'torrent');
            print($pagerbottom);
        } else {
            print("<br><b>" . Lang::T("NOCOMMENTS") . "</b><br>\n");
        }

        Style::end();
        Style::footer();

    }

}