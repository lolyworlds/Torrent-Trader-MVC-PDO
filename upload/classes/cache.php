<?php
// Global Variables Declaration
$GLOBALS["TTCache"] = new TTCache;
// Cache Class
class TTCache
{
    // Fonction Constructeur De La Classe Cache
    public function __construct()
    {
        global $site_config, $pdo;
        $this->cachedir = $site_config["cache_dir"];
        $this->type = strtolower(trim($site_config["cache_type"]));
// Cache Connection
        switch ($this->type) {
            case "memcache":
                $this->obj = new Memcache;
                if (!@$this->obj->Connect($site_config["cache_memcache_host"], $site_config["cache_memcache_port"])) {
                    $this->type = "disk";
                }

                break;
            case "apc":
                if (function_exists("apc_store")) {
                    break;
                }

            case "xcache":
                if (function_exists("xcache_set")) {
                    break;
                }

            default:
                $this->type = "disk";
        }
    }
// Caching Function According to the Type of Cache Used
    public function Set($var, $val, $expire = 0)
    {
        global $site_config;
        if ($expire == 0) {
            return;
        }

        switch ($this->type) {
            case "memcache":
                return $this->obj->set($site_config['SITENAME'] . "_" . $var, $val, 0, $expire);
                break;
            case "apc":
                return apc_store($var, $val, $expire);
                break;
            case "disk":
                $fp = fopen($this->cachedir . "/$var.cache", "w");
                fwrite($fp, serialize($val));
                fclose($fp);
                return;
                break;
            case "xcache":
                return xcache_set($var, serialize($val), $expire);
                break;
        }
    }
// Function Delete Memcache According To Its Type
    public function Delete($var)
    {
        global $site_config;

        switch ($this->type) {
            case "memcache":
                return $this->obj->delete($site_config['SITENAME'] . "_" . $var);
                break;
            case "apc":
                return apc_delete($var);
                break;
            case "disk":
                @unlink($this->cachedir . "/$var.cache");
                break;
            case "xcache":
                return xcache_unset($var);
                break;
        }
    }

// Get Memcache Type Function Used
    public function Get($var, $expire = 0)
    {
        global $site_config;
        if ($expire == 0) {
            return false;
        }

        switch ($this->type) {
            case "memcache":
                return $this->obj->get($site_config['SITENAME'] . "_" . $var);
                break;
            case "apc":
                return apc_fetch($var);
                break;
            case "disk":
                $file = $this->cachedir . "/$var.cache";
                if (file_exists($file) && (time() - filemtime($file)) < $expire) {
                    return unserialize(file_get_contents($file));
                }

                return false;
                break;
            case "xcache":
                if (xcache_isset($var)) {
                    return unserialize(xcache_get($var));
                }

                return false;
                break;
        }
    }
}

// Cached PDO Functions
function get_row_count_cached($table, $suffix = "")
{
    global $TTCache;

    $query = "SELECT COUNT(*) FROM $table $suffix";
    $cache = "get_row_count/" . sha1($query);
    if (($ret = $TTCache->Get($cache, 300)) === false) {
        $row = $pdo->run($query)->fetch();
        $ret = $row[0];
        $TTCache->Set($cache, $ret, 300);
    }
    return $ret;
}
// todo
function SQL_Query_exec_cached($query, $cache_time = 300, $cache_blank = 1)
{
    global $TTCache;

    $cache = "queries/" . sha1($query);
    if (($rows = $TTCache->Get($cache, $cache_time)) === false) {
        $res = $pdo->run($query);
        $rows = array();
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }

        if (count($rows) || $cache_blank) {
            $TTCache->Set($cache, $rows, $cache_time);
        }

    }
    return count($rows) ? $rows : false;
}
