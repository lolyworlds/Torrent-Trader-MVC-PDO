<?php
  class Message {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    public function countmsg(){
      $res = DB::run("SELECT COUNT(*), COUNT(`unread` = 'yes') FROM messages WHERE `receiver` = $_SESSION[id] AND `location` IN ('in','both')");
      $res = DB::run("SELECT COUNT(*) FROM messages WHERE receiver=" . $_SESSION["id"] . " AND `location` IN ('in','both')");
      $inbox = $res->fetchColumn();
      $res = DB::run("SELECT COUNT(*) FROM messages WHERE `receiver` = " . $_SESSION["id"] . " AND `location` IN ('in','both') AND `unread` = 'yes'");
      $unread = $res->fetchColumn();
      $res = DB::run("SELECT COUNT(*) FROM messages WHERE `sender` = " . $_SESSION["id"] . " AND `location` IN ('out','both')");
      $outbox = $res->fetchColumn();
      $res = DB::run("SELECT COUNT(*) FROM messages WHERE `sender` = " . $_SESSION["id"] . " AND `location` = 'draft'");
      $draft = $res->fetchColumn();
      $res = DB::run("SELECT COUNT(*) AS count FROM messages WHERE `sender` = " . $_SESSION["id"] . " AND `location` = 'template'");
      $template = $res->fetchColumn();

      $arr = [
        'inbox' => $inbox,
        'unread' => $unread,
        'outbox' => $outbox,
        'draft' => $draft,
        'template' => $template
      ];
      return $arr;
    }

  }