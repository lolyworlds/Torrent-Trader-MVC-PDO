<?php
  class Likes extends Controller {
  
    public function __construct(){
    }
  
    // Thanks on index
    public function index(){
    dbconn();
    global $config;
    $id = (int)$_GET['id'];
    if (!is_valid_id($id));
    DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)",[$_SESSION['id'], $id, get_date_time(), 'torrent']);
    header("Refresh: 3;url=$config[SITEURL]/index.php");
    show_error_msg("Error", "Thanks you for you appreciation.",1);
    }
    // Thanks on details
    public function details(){
    dbconn();
    global $config;
    $id = (int)$_GET['id'];
    if (!is_valid_id($id));
    DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)",[$_SESSION['id'], $id, get_date_time(), 'torrent']);
    header("Refresh: 3;url=$config[SITEURL]/torrents/read?id=$id");
    show_error_msg("Error", "Thanks you for you appreciation.",1);
    }
    
    public function liketorrent(){
    dbconn();
    global $config;
    $id = (int)$_GET['id'];
    if (!is_valid_id($id));
    DB::run("INSERT INTO likes (user, liked, added, type, reaction) VALUES (?, ?, ?, ?, ?)",[$_SESSION['id'], $id, get_date_time(), 'torrent', 'like']);
    header("Refresh: 3;url=$config[SITEURL]/torrents/read?id=$id");
    show_error_msg("Error", "Thanks you for you appreciation.",1);
    }

    public function unliketorrent(){
    dbconn();
    global $config;
    $id = (int)$_GET['id'];
    if (!is_valid_id($id));
    DB::run("DELETE FROM likes WHERE user=? AND liked=? AND type=?", [$_SESSION['id'], $id, 'torrent']);
    header("Refresh: 3;url=$config[SITEURL]/torrents/read?id=$id");
    show_error_msg("Error", "Sorry you dont like.",1);
    }

    public function likeforum(){
      dbconn();
      global $config;
      $id = (int)$_GET['id'];
      if (!is_valid_id($id));
      DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)",[$_SESSION['id'], $id, get_date_time(), 'forum']);
      header("Refresh: 3;url=$config[SITEURL]/forums/viewtopic&topicid=$id");
      show_error_msg("Error", "Thanks you for you appreciation.",1);
      } 
}