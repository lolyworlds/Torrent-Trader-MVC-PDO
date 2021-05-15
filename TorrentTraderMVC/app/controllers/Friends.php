<?php
class Friends extends Controller
{

    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
        $this->valid = new Validation();
    }

    public function index()
    {
        $userid = (int) $_GET['id'];
        if (!$this->valid->validId($userid)) {
            Session::flash('info', "Invalid ID $userid.", URLROOT . "/home");
        }
        $res = DB::run("SELECT * FROM users WHERE id=$userid");
        $user = $res->fetch(PDO::FETCH_ASSOC);
        $enemy = DB::run("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$userid AND friend='enemy' ORDER BY name");
        $friend = DB::run("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$userid AND friend='friend' ORDER BY name");
        $data = [
            'sql' => $user,
            'username' => $user['username'],
            'userid' => $userid,
            'friend' => $friend,
            'enemy' => $enemy,
        ];
        $this->view('friends/index', $data, true);
    }

    public function add()
    {
        $targetid = (int) $_GET['targetid'];
        $type = $_GET['type'];
        if (!$this->valid->validId($targetid)) {
            Session::flash('warning', "Invalid ID $$targetid.", URLROOT . "/home");
        }
        if ($type == 'friend') {
            $r = DB::run("SELECT id FROM friends WHERE userid=$_SESSION[id] AND userid=$targetid");
            if ($r->rowCount() == 1) {
                Session::flash('warning', "User ID $targetid is already in your friends list.", URLROOT . "/friends?id=$_SESSION[id]");
            }
            DB::run("INSERT INTO friends (id, userid, friendid, friend) VALUES (0,$_SESSION[id], $targetid, 'friend')");
            Redirect::to(URLROOT . "/friends?id=$_SESSION[id]");
            die();
        } elseif ($type == 'block') {
            $r = DB::run("SELECT id FROM friends WHERE userid=$_SESSION[id] AND userid=$targetid");
            if ($r->rowCount() == 1) {
                Session::flash('warning', "User ID $targetid is already in your friends list.", URLROOT . "/friends?id=$_SESSION[id]");
            }
            DB::run("INSERT INTO friends (id, userid, friendid, friend) VALUES (0,$_SESSION[id], $targetid, 'enemy')");
            Session::flash('warning', "Success", URLROOT . "/friends?id=$_SESSION[id]");
            die();
        } else {
            Session::flash('warning', "Unknown type $type", URLROOT . "/friends?id=$_SESSION[id]");
        }
    }

    public function delete()
    {
        $targetid = (int) $_GET['targetid'];
        $sure = htmlentities($_GET['sure']);
        $type = htmlentities($_GET['type']);
        if ($type != "block") {$typ = "friend from list";} else { $typ = "blocked user from list";}
        if (!$this->valid->validId($targetid)) {
            Session::flash('warning', "Invalid ID $_SESSION[id].", URLROOT . "/friends?id=$_SESSION[id]");
        }
        if (!$sure) {
            $msg = "<div style='margin-top:10px; margin-bottom:10px' align='center'>Do you really want to delete this $typ? &nbsp; \n" . "<a href=?id=$_SESSION[id]/delete&type=$type&targetid=$targetid&sure=1>Yes</a> | <a href=friends.php>No</a></div>";
            Session::flash('warning', $msg, URLROOT."/profile?id=$targetid");
        }
        if ($type == 'friend') {
            $stmt = DB::run("DELETE FROM friends WHERE userid=$_SESSION[id] AND friendid=$targetid AND friend=friend");
            if ($stmt->rowCount() == 0) {
                Session::flash('warning', "No friend found with ID $targetid", URLROOT."/profile?id=$targetid");
            }
            $frag = "friends";
        } elseif ($type == 'block') {
            $stmt = DB::run("DELETE FROM friends WHERE userid=$_SESSION[id] AND friendid=$targetid AND friend=?", ['enemy']);
            if ($stmt->rowCount() == 0) {
                Session::flash('warning', "No block found with ID $targetid", URLROOT."/profile?id=$targetid");
            }
            $frag = "blocked";
        } else {
            Session::flash('warning', "Unknown type $type", URLROOT."/profile?id=$targetid");
        }
        Session::flash('warning', "Success", URLROOT . "/friends?id=$_SESSION[id]#$frag");
        die;
    }
}