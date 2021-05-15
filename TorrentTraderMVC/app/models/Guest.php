<?php
  class Guest {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    public function guestadd()
    {
        $ip = Helper::getIP();
        $time = TimeDate::gmtime();
        $this->db->run("INSERT INTO `guests` (`ip`, `time`) VALUES ('$ip', '$time') ON DUPLICATE KEY UPDATE `time` = '$time'");
    }
    
    public function getguests()
    {
        $past = (TimeDate::gmtime() - 2400);
        $this->db->run("DELETE FROM `guests` WHERE `time` < $past");
        return $this->db->get_row_count("guests");
    }

  }