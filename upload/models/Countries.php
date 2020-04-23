<?php
  class Countries {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }
public function getCountry ($row) 
{
 $stmt =   $this->db->run("
   SELECT name,flagpic FROM countries WHERE id=?", [$row['country']]);

      return $stmt;
}
  }