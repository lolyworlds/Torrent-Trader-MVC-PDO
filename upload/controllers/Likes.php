<?php
  class Likes extends Controller {
  
    public function __construct(){
    }
  
    // Thanks
    public function index(){
    dbconn();
    global $site_config, $CURUSER;
    $id = (int)$_GET['id'];
    if (!is_valid_id($id));
    DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)",[$CURUSER['id'], $id, get_date_time(), 'torrent']);
    header("Refresh: 3;url=$site_config[SITEURL]/torrents/details?id=$id");
    show_error_msg("Error", "Thanks you for you appreciation.",1);
    }

    public function liketorrent(){
    dbconn();
    global $site_config, $CURUSER;
    $id = (int)$_GET['id'];
    if (!is_valid_id($id));
    DB::run("INSERT INTO likes (user, liked, added, type, reaction) VALUES (?, ?, ?, ?, ?)",[$CURUSER['id'], $id, get_date_time(), 'torrent', 'like']);
    header("Refresh: 3;url=$site_config[SITEURL]/torrents/details?id=$id");
    show_error_msg("Error", "Thanks you for you appreciation.",1);
    }

    public function unliketorrent(){
    dbconn();
    global $site_config, $CURUSER;
    $id = (int)$_GET['id'];
    if (!is_valid_id($id));
    DB::run("DELETE FROM likes WHERE user=? AND liked=? AND type=?", [$CURUSER['id'], $id, 'torrent']);
    header("Refresh: 3;url=$site_config[SITEURL]/torrents/details?id=$id");
    show_error_msg("Error", "Sorry you dont like.",1);
    }

}