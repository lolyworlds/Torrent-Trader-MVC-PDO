<?php
  class Rule {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

public function getRules(){
$stmt = $this->db->run("SELECT * FROM `rules` ORDER BY `id`");
return $stmt;

}

  }