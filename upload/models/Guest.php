<?php
  class Guest {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

  }