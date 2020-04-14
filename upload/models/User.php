<?php
  class User {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }
    // Get User by ID
    public function getUserByUsername($username){
      $row = $this->db->run("SELECT id, password, secret, status, enabled FROM users WHERE username =? ", [$username])->fetch();
      return $row;
    }

  }