<?php
class Likes extends Controller
{

    public function __construct()
    {
        Auth::user();
        $this->valid = new Validation();
    }

    // Thanks on index
    public function index()
    {
        $id = (int) $_GET['id'];
        if (!$this->valid->validId($id));
        DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'torrent']);
        Session::flash('info', "Thanks you for you appreciation.", URLROOT."/home");
    }
    // Thanks on details
    public function details()
    {
        $id = (int) $_GET['id'];
        if (!$this->valid->validId($id));
        DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'torrent']);
        Session::flash('info', "Thanks you for you appreciation.", URLROOT."/torrents/read?id=$id");
    }

    public function liketorrent()
    {
        $id = (int) $_GET['id'];
        if (!$this->valid->validId($id));
        DB::run("INSERT INTO likes (user, liked, added, type, reaction) VALUES (?, ?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'torrent', 'like']);
        Session::flash('info', "Thanks you for you appreciation.", URLROOT."/torrents/read?id=$id");
    }

    public function unliketorrent()
    {
        $id = (int) $_GET['id'];
        if (!$this->valid->validId($id));
        DB::run("DELETE FROM likes WHERE user=? AND liked=? AND type=?", [$_SESSION['id'], $id, 'torrent']);
        Session::flash('info', "Unliked.", URLROOT."/torrents/read?id=$id");
    }

    public function likeforum()
    {
        $id = (int) $_GET['id'];
        if (!$this->valid->validId($id));
        DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'forum']);
        Session::flash('info', "Thanks you for you appreciation.", URLROOT."/forums/viewtopic&topicid=$id");
    }
}