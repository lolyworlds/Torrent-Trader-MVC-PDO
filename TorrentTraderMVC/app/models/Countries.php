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


public static function echoCountry () 
{
$countries = "<option value=\"0\">---- " . Lang::T("NONE_SELECTED") . " ----</option>\n";
$ct_r = DB::run("SELECT id,name,domain from countries ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
foreach ($ct_r as $ct_a) {
    $countries .= "<option value=\"$ct_a[id]\">$ct_a[name]</option>\n";
}
echo $countries;
}

  }