<?php
  class Faqs {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

public function getFaqByCat(){
$stmt = $this->db->run("SELECT `id`, `question`, `flag` FROM `faq` WHERE `type`=? ORDER BY `order` ASC", ['categ']);
return $stmt;
// $this->db->run("SELECT `id`, `question`, `flag` FROM `faq` WHERE `type`=? ORDER BY `order` ASC", ['categ'])->fetch(PDO::FETCH_BOTH);

}

public function getFaqByType(){
$stmt = $this->db->run("SELECT `id`, `question`, `answer`, `flag`, `categ` FROM `faq` WHERE `type`=? ORDER BY `order` ASC", ['item']);
return $stmt;

}


  }