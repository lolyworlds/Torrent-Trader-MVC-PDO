<?php
include 'config/config.php';
define('DB_HOSTT', $config['mysql_host']);
define('DB_NAMET', $config['mysql_db']);
define('DB_USERT', $config['mysql_user']);
define('DB_PASST', $config['mysql_pass']);
define('DB_CHART', 'utf8');

class Database
{
    protected static $instance;
    protected $pdo;

    public function __construct()
    {
        $opt = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        );
        $dsn = 'mysql:host=' . DB_HOSTT . ';dbname=' . DB_NAMET . ';charset=' . DB_CHART;
        $this->pdo = new PDO($dsn, DB_USERT, DB_PASST, $opt);

    }

    // a classical static method to make it universally available
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // a proxy to native PDO methods
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->pdo, $method), $args);
    }

    // a helper function to run prepared statements smoothly
    public function run($sql, $args = [])
    {
        if (!$args) {
            return $this->query($sql);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }

    // Function To Count A Data Established In A Data Table
    public function get_row_count($table, $suffix = "")
    {
        $suffix = !empty($suffix) ? ' ' . $suffix : '';
        $row = $this->run("SELECT COUNT(*) FROM $table $suffix")->fetchColumn();
        return $row;
    }

}
