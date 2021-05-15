<?php
  class Bonusmodel {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    // Get Bonus by Post Id
    public function getBonusByPost($id){
       $res = $this->db->run("SELECT `type`, `value`, `cost` FROM `bonus` WHERE `id` = '$_POST[id]'");
       $row = $res->fetch(PDO::FETCH_LAZY);
       return $row;
    }

    // Get Bonus Order By Type
       public function getAll(){
       $res = $this->db->run("SELECT * FROM `bonus` ORDER BY `type`");
       $row1 = $res->fetchAll();
       return $row1;
    }

    // Set User Bonus
    public function setBonus($cost, $id){
      $row = $this->db->run("UPDATE `users` SET `seedbonus` = `seedbonus` - '$cost' WHERE `id` = '$id'");
    }

  }