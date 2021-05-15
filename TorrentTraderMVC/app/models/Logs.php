<?php
  class Logs
  {
      private $db;

      public function __construct()
      {
          $this->db = new Database;
      }

      public static function write($text)
      {
        $db = new Database;
        $text = $text;
        $added = TimeDate::get_date_time();
        $db->run("INSERT INTO log (added, txt) VALUES (?,?)", [$added, $text]);
      }
  }