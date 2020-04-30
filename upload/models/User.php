<?php
  class User {
    private $db;

    public function __construct(){
      $this->db = new Database;
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

  }