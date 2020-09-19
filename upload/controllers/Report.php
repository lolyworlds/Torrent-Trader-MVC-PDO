<?php
class Report extends Controller
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
        stdhead("Report");
        begin_frame("Report");
        echo 'Strange No id in Link, if this keeps happening please post in shoutbox';
        end_frame();
        stdfoot();
    }

    public function user()
    {
        dbconn();
        global $config;
        loggedinonly();
        stdhead("Report");
        begin_frame("Report User");

        $takeuser = (int) $_POST["user"];
        $takereason = $_POST["reason"];

        $user = (int) $_GET["user"];

        //take report user
        if (!empty($takeuser)) {
            if (empty($takereason)) {
                show_error_msg(T_("ERROR"), T_("YOU_MUST_ENTER_A_REASON"), 0);
                end_frame();
                stdfoot();
                die;
            }

            $res = DB::run("SELECT id FROM reports WHERE addedby =? AND votedfor =? AND type =?", [$_SESSION['id'], $takeuser, 'user']);

            if ($res->rowCount() == 0) {
                DB::run("INSERT into reports (addedby,votedfor,type,reason) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $takeuser, 'user', $takereason]);
                print("User: $takeuser, Reason: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>");
                end_frame();
                stdfoot();
                die();
            } else {
                print(T_("YOU_HAVE_ALREADY_REPORTED") . " user $takeuser");
                end_frame();
                stdfoot();
                die();
            }
        }

        //report user form
        if ($user != "") {
            $res = DB::run("SELECT username, class FROM users WHERE id=?", [$user]);
            if ($res->rowCount() == 0) {
                print(T_("INVALID_USERID"));
                end_frame();
                stdfoot();
                die();
            }

            $arr = $res->fetch(PDO::FETCH_ASSOC);

            print("<b>Are you sure you would like to report user:</b><br /><a href='$config[SITEURL]/users/profile?id=$user'><b>" . class_user_colour($arr['username']) . "</b></a>?<br />");
            print("<p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them</p>");
            print("<b>Reason</b> (required): <form method='post' action='$config[SITEURL]/report/user'><input type='hidden' name='user' value='$user' /><input type='text' size='100' name='reason' /><input type='submit' value='Confirm' /></form>");
            end_frame();
            stdfoot();
            die();
        }

        //error
        if ($user != "") {
            print("<h1>" . T_("MISSING_INFO") . "</h1>");
            end_frame();
            stdfoot();
            die();
        }

        show_error_msg(T_("ERROR"), T_("MISSING_INFO") . ".", 0);
        end_frame();
        stdfoot();
    }

    public function torrent()
    {
        dbconn();
        global $config;
        loggedinonly();
        stdhead("Report");
        begin_frame("Report");
        $taketorrent = (int) $_POST["torrent"];
        $takereason = $_POST["reason"];

        $torrent = (int) $_GET["torrent"];

        //take report torrent
        if (($taketorrent != "") && ($takereason != "")) {
            if (!$takereason) {
                show_error_msg(T_("ERROR"), T_("YOU_MUST_ENTER_A_REASON"), 0);
                stdfoot();
                end_frame();
                die;
            }

            $res = DB::run("SELECT id FROM reports WHERE addedby =? AND votedfor =? AND type =?", [$_SESSION['id'], $taketorrent, 'torrent']);
            if ($res->rowCount() == 0) {
                DB::run("INSERT into reports (addedby,votedfor,type,reason) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $taketorrent, 'torrent', $takereason]);
                print("Torrent with id: $taketorrent, Reason for report: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>");
                end_frame();
                stdfoot();
                die();
            } else {
                print(T_("YOU_HAVE_ALREADY_REPORTED") . " torrent $taketorrent");
                end_frame();
                stdfoot();
                die();
            }
        }

        //report torrent form
        if ($torrent != "") {
            $res = DB::run("SELECT name FROM torrents WHERE id=?", [$torrent]);

            if ($res->rowCount() == 0) {
                print("Invalid TorrentID");
                end_frame();
                stdfoot();
                die();
            }

            $arr = $res->fetch(PDO::FETCH_LAZY);
            print("<b>Are you sure you would like to report torrent:</b><br /><a href='$config[SITEURL]/torrents/read?id=$torrent'><b>$arr[name]</b></a>?<br />");
            print("<b>Reason</b> (required): <form method='post' action='$config[SITEURL]/report/torrent'><input type='hidden' name='torrent' value='$torrent' /><input type='text' size='100' name='reason' /><input type='submit' value='Confirm' /></form>");
            end_frame();
            stdfoot();
            die();
        }

        //error
        if ($torrent != "") {
            print("<h1>" . T_("MISSING_INFO") . "</h1>");
            end_frame();
            stdfoot();
            die();
        }

        show_error_msg(T_("ERROR"), T_("MISSING_INFO") . ".", 0);
        end_frame();
        stdfoot();
    }

    public function comment()
    {
        dbconn();
        global $config;
        loggedinonly();
        stdhead("Report");
        begin_frame("Report");

        $takecomment = (int) $_POST["comment"];
        $takereason = $_POST["reason"];

        $comment = (int) $_GET["comment"];

        //take report comment
        if (($takecomment != "") && ($takereason != "")) {
            if (!$takereason) {
                show_error_msg(T_("ERROR"), T_("YOU_MUST_ENTER_A_REASON"), 0);
                stdfoot();
                end_frame();
                die;
            }

            $res = DB::run("SELECT id FROM reports WHERE addedby =? AND votedfor =? AND type =?", [$_SESSION['id'], $takecomment, 'comment']);
            if ($res->rowCount() == 0) {
                DB::run("INSERT into reports (addedby,votedfor,type,reason) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $takecomment, 'comment', $takereason]);
                print("Comment with id: $takecomment, Reason for report: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>");
                end_frame();
                stdfoot();
                die();
            } else {
                print(T_("YOU_HAVE_ALREADY_REPORTED") . " torrent $takecomment");
                end_frame();
                stdfoot();
                die();
            }
        }

        //report comment form
        if ($comment != "") {
            $res = DB::run("SELECT id, text FROM comments WHERE id=?", [$comment]);
            if ($res->rowCount() == 0) {
                print("Invalid Comment");
                end_frame();
                stdfoot();
                die();
            }

            $arr = $res->fetch(PDO::FETCH_LAZY);

            print("<b>Are you sure you would like to report Comment:</b><br /><br /><b>" . format_comment($arr["text"]) . "</b>?<br />");
            print("<p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them</p>");
            print("<b>Reason</b> (required): <form method='post' action='$config[SITEURL]/report/comment'><input type='hidden' name='comment' value='$comment' /><input type='text' size='100' name='reason' /><input type='submit'  value='Confirm' /></form>");
            end_frame();
            stdfoot();
            die();
        }

        show_error_msg(T_("ERROR"), T_("MISSING_INFO") . ".", 0);
        end_frame();
        stdfoot();
    }

    public function forum()
    {
        dbconn();
        global $config;
        loggedinonly();
        stdhead("Report");
        begin_frame("Report");

        $takeforumid = (int) $_POST["forumid"];
        $takeforumpost = (int) $_POST["forumpost"];
        $takereason = $_POST["reason"];

        $forumid = (int) $_GET["forumid"];
        $forumpost = (int) $_GET["forumpost"];

        //take forum post report
        if (($takeforumid != "") && ($takereason != "")) {
            if (!$takereason) {
                show_error_msg(T_("ERROR"), T_("YOU_MUST_ENTER_A_REASON"), 0);
                stdfoot();
                end_frame();
                die;
            }

            $res = DB::run("SELECT id FROM reports WHERE addedby =? AND votedfor=? AND votedfor_xtra=? AND type =?", [$_SESSION['id'], $takeforumid, $takeforumpost, 'forum']);

            if ($res->rowCount() == 0) {
                DB::run("INSERT into reports (addedby,votedfor,votedfor_xtra,type,reason) VALUES (?, ?, ?, ?, ?)", [$_SESSION['id'], $takeforumid, $takeforumpost, 'forum', $takereason]);
                print("User: $_SESSION[username], Reason: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>");
                end_frame();
                stdfoot();
                die();
            } else {
                print(T_("YOU_HAVE_ALREADY_REPORTED") . " post $takeforumid");
                end_frame();
                stdfoot();
                die();
            }

        }

        //report forum post form
        if (($forumid != "") && ($forumpost != "")) {
            $res = DB::run("SELECT subject FROM forum_topics WHERE id=?", [$forumid]);

            if ($res->rowCount() == 0) {
                print("Invalid Forum ID");
                end_frame();
                stdfoot();
                die();
            }

            $arr = $res->fetch(PDO::FETCH_LAZY);
            print("<b>Are you sure you would like to report the following forum post:</b><br /><a href='$config[SITEURL]/forums/viewtopic&amp;topicid=$forumid&amp;page=p#post$forumpost'><b>$arr[subject]</b></a>?<br />");
            print("<b>Reason</b> (required): <form method='post' action='$config[SITEURL]/report/forum'><input type='hidden' name='forumid' value='$forumid' /><input type='hidden' name='forumpost' value='$forumpost'><input type='text' size='100' name='reason' /><input type='submit'  value='Confirm' /></form>");
            end_frame();
            stdfoot();
            die;
        }

        show_error_msg(T_("ERROR"), T_("MISSING_INFO") . ".", 0);
        end_frame();
        stdfoot();
    }

}
