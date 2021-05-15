<?php
  class User {
    private $db;

    public function __construct(){
      $this->db = new Database();
    }

    // Get User by Username
    public function getUserByUsername($username){
      $row = $this->db->run("SELECT id, password, secret, status, enabled FROM users WHERE username =? ", [$username])->fetch();
      return $row;
    }

    // Update User pass & secret
    public function recoverUpdate($wantpassword, $newsec, $pid, $psecret){
      $row = $this->db->run("UPDATE `users` SET `password` =?, `secret` =? WHERE `id`=? AND `secret` =?", [$wantpassword, $newsec, $pid, $psecret]);
    }

    // Set User secret
    public function setSecret($sec, $email){
      $row = $this->db->run("UPDATE `users` SET `secret` =? WHERE `email`=? LIMIT 1", [$sec, $email]);
    }

    // Get Email&Id by Email
    public function getIdEmailByEmail($email){
      $row = $this->db->run("SELECT id, username, email FROM users WHERE email=? LIMIT 1", [$email])->fetch();
      return $row;
    }
  
    public function updatelogin($id){
      $this->db->run("UPDATE users SET last_login = ? WHERE id = ? ", [Helper::get_date_time(), $id]);
    }

        // Get Email&Id by Email
        public static function getUserById($id)
        {
            $user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
            return $user;
        }
        // Get All User Array
        public function getAll($id){
            $row = DB::run("SELECT * FROM users WHERE id =? ", [$id]);
            $user1 = $row->fetchAll();
            return $user1;
         }
           
    // Get Email&Id by Email
    public function checkinvite(){
      $stmt = $this->pdo->run("SELECT id FROM users WHERE id = $_REQUEST[invite] AND secret = " . sqlesc($_REQUEST["secret"]));
      $invite_row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Function That Removes All From An Account
public function deleteaccount($userid)
{
  $this->db->run("DELETE FROM users WHERE id = $userid");
  $this->db->run("DELETE FROM warnings WHERE userid = $userid");
  $this->db->run("DELETE FROM ratings WHERE user = $userid");
  $this->db->run("DELETE FROM peers WHERE userid = $userid");
    $this->db->run("DELETE FROM completed WHERE userid = $userid");
    $this->db->run("DELETE FROM reports WHERE addedby = $userid");
    $this->db->run("DELETE FROM reports WHERE votedfor = $userid AND type = 'user'");
    $this->db->run("DELETE FROM forum_readposts WHERE userid = $userid");
    $this->db->run("DELETE FROM pollanswers WHERE userid = $userid");
    // snatch
    $this->db->run("DELETE FROM `snatched` WHERE `uid` = '$userid'");
}

  }