<?php
class Report extends Controller
{

    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
    }

    public function index()
    {
        Session::flash('info', Lang::T("NO_ID"), URLROOT."/home");
    }

    public function user()
    {
        $takeuser = (int) $_POST["user"];
        $takereason = $_POST["reason"];
        $user = (int) $_GET["user"];

        if (!empty($takeuser)) {
            if (empty($takereason)) {
                Session::flash('info',  Lang::T("YOU_MUST_ENTER_A_REASON"), URLROOT."/report/user?user=$user");
                die;
            }
            $res = DB::run("SELECT id FROM reports WHERE addedby =? AND votedfor =? AND type =?", [$_SESSION['id'], $takeuser, 'user']);
            if ($res->rowCount() == 0) {
                DB::run("INSERT into reports (addedby,votedfor,type,reason) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $takeuser, 'user', $takereason]);
                $msg = "User: $takeuser, Reason: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>";
                Session::flash('info',  $msg, URLROOT."/profile?id=$user");
                die();
            } else {
                $msg = Lang::T("YOU_HAVE_ALREADY_REPORTED") . " user $takeuser";
                Session::flash('info',  $msg, URLROOT."/profile?id=$user");
                die();
            }
        }

        if ($user != "") {
            $res = DB::run("SELECT username, class FROM users WHERE id=?", [$user]);
            if ($res->rowCount() == 0) {
                Session::flash('danger',  Lang::T("INVALID_USERID"), URLROOT."/home");
                die();
            }
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            $data = [
                'username' => $arr['username'],
                'user' => $user
            ];
            $this->view('report/user', $data, true);
            die();
        } else {
            Session::flash('info', Lang::T("MISSING_INFO"), URLROOT."/profile?id=$user");
        }
    }

    public function torrent()
    {$taketorrent = (int) $_POST["torrent"];
        $takereason = $_POST["reason"];
        $torrent = (int) $_GET["torrent"];

        if (($taketorrent != "") && ($takereason != "")) {
            if (!$takereason) {
                Session::flash('info', Lang::T("YOU_MUST_ENTER_A_REASON"), URLROOT."/report/torrent?torrent=$torrent");
                die;
            }
            $res = DB::run("SELECT id FROM reports WHERE addedby =? AND votedfor =? AND type =?", [$_SESSION['id'], $taketorrent, 'torrent']);
            if ($res->rowCount() == 0) {
                DB::run("INSERT into reports (addedby,votedfor,type,reason) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $taketorrent, 'torrent', $takereason]);
                $msg = "Torrent with id: $taketorrent, Reason for report: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>";
                Session::flash('info', $msg, URLROOT."/torrents/read?id=$torrent");
                die();
            } else {
                $msg = Lang::T("YOU_HAVE_ALREADY_REPORTED") . " torrent $taketorrent";
                Session::flash('info', $msg, URLROOT."/torrents/read?id=$torrent");
                die();
            }
        }

        if ($torrent != "") {
            $res = DB::run("SELECT name FROM torrents WHERE id=?", [$torrent]);
            if ($res->rowCount() == 0) {
                Session::flash('info', 'Invalid TorrentID', URLROOT."/torrents/read?id=$torrent");
                die();
            }
            $arr = $res->fetch(PDO::FETCH_LAZY);
            $data = [
                'name' => $arr['name'],
                'torrent' => $torrent
            ];
            $this->view('report/torrent', $data, true);
            die();
        } else {
            Session::flash('info', Lang::T("MISSING_INFO") . ".", URLROOT."/torrents/read?id=$torrent");
        }
    }

    public function comment()
    {
        $takecomment = (int) $_POST["comment"];
        $takereason = $_POST["reason"];
        $comment = (int) $_GET["comment"];

        if (($takecomment != "") && ($takereason != "")) {
            if (!$takereason) {
                Session::flash('info', Lang::T("YOU_MUST_ENTER_A_REASON"), URLROOT."/report/comment?comment=$comment");
                die;
            }
            $res = DB::run("SELECT id FROM reports WHERE addedby =? AND votedfor =? AND type =?", [$_SESSION['id'], $takecomment, 'comment']);
            if ($res->rowCount() == 0) {
                DB::run("INSERT into reports (addedby,votedfor,type,reason) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $takecomment, 'comment', $takereason]);
                $msg = "Comment with id: $takecomment, Reason for report: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>";
                Session::flash('info', $msg, URLROOT."/home");
                die();
            } else {
                $msg = Lang::T("YOU_HAVE_ALREADY_REPORTED") . " torrent $takecomment";
                Session::flash('info', $msg, URLROOT."/home");
                die();
            }
        }

        if ($comment != "") {
            $res = DB::run("SELECT id, text FROM comments WHERE id=?", [$comment]);
            if ($res->rowCount() == 0) {
                Session::flash('info', "Invalid Comment", URLROOT."/home");
                die();
            }
            $arr = $res->fetch(PDO::FETCH_LAZY);
            $data = [
                'text' => $arr['text'],
                'comment' => $comment
            ];
            $this->view('report/torrent', $data, true);
            die();
        } else {
            Session::flash('info', Lang::T("MISSING_INFO") . ".", URLROOT."/home");
        }
    }

    public function forum()
    {
        $takeforumid = (int) $_POST["forumid"];
        $takeforumpost = (int) $_POST["forumpost"];
        $takereason = $_POST["reason"];
        $forumid = (int) $_GET["forumid"];
        $forumpost = (int) $_GET["forumpost"];

        if (($takeforumid != "") && ($takereason != "")) {
            if (!$takereason) {
                Session::flash('danger', Lang::T("YOU_MUST_ENTER_A_REASON"), URLROOT."/home");
                die;
            }
            $res = DB::run("SELECT id FROM reports WHERE addedby =? AND votedfor=? AND votedfor_xtra=? AND type =?", [$_SESSION['id'], $takeforumid, $takeforumpost, 'forum']);
            if ($res->rowCount() == 0) {
                DB::run("INSERT into reports (addedby,votedfor,votedfor_xtra,type,reason) VALUES (?, ?, ?, ?, ?)", [$_SESSION['id'], $takeforumid, $takeforumpost, 'forum', $takereason]);
                $mss = "User: $_SESSION[username], Reason: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>";
                Session::flash('danger', $mss, URLROOT."/home");
                die();
            } else {
                $mss = Lang::T("YOU_HAVE_ALREADY_REPORTED") . " post $takeforumid";
                Session::flash('danger', $mss, URLROOT."/home");
                die();
            }
        }

        if (($forumid != "") && ($forumpost != "")) {
            $res = DB::run("SELECT subject FROM forum_topics WHERE id=?", [$forumid]);
            if ($res->rowCount() == 0) {
                Session::flash('danger', "Invalid Forum ID", URLROOT."/home");
                die();
            }
            $arr = $res->fetch(PDO::FETCH_LAZY);
            $data = [
                'subject' => $arr['subject'],
                'forumpost' => $forumpost,
                'forumid' => $forumid,
            ];
            $this->view('report/forum', $data, true);
            die;
        }
        Session::flash('danger', Lang::T("MISSING_INFO") . ".", URLROOT."/home");
    }

}