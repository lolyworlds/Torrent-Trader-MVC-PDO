<?php
   /*
   * PDO Database Class
   * Connect to database
   * Create & chain prepared statements
   * Return rows and results
   */
include (TTROOT.'/config/config.php');
define('DB_HOSTT', $site_config['mysql_host']);
define('DB_NAMET', $site_config['mysql_db']);
define('DB_USERT', $site_config['mysql_user']);
define('DB_PASST', $site_config['mysql_pass']);
define('DB_CHART', 'utf8');

  class Database {
    
    private $host = DB_HOSTT;
    private $user = DB_USERT;
    private $pass = DB_PASST;
    private $dbname = DB_NAMET;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct(){
      // Set DSN
      $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
      $options = array(
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      );

      // Create PDO instance
      try{
        $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
      } catch(PDOException $e){
        $this->error = $e->getMessage();
        echo $this->error;
      }
    }

    // Chained Prepared Statements
    public function run($sql, $bind = NULL){
        if (!$bind)
        {
            return $this->dbh->query($sql);
        }
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }
  }