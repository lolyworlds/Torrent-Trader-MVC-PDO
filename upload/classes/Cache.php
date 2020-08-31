<?php
class Cache
{
    // Fonction Constructeur De La Classe Cache
    public function __construct()
    {
        global $config, $pdo;
        $this->cachedir = $config["cache_dir"];
        $this->type = strtolower(trim($config["cache_type"]));
        // Cache Connection
        switch ($this->type) {
            case "memcache":
                $this->obj = new Memcache;
                if (!@$this->obj->Connect($config["cache_memcache_host"], $config["cache_memcache_port"])) {
                    $this->type = "disk";
                }
                break;
            default:
                $this->type = "disk";
        }
    }
    // Caching Function According to the Type of Cache Used
    public function Set($var, $val, $expire = 0)
    {
        global $config;
        if ($expire == 0) {
            return;
        }

        switch ($this->type) {
            case "memcache":
                return $this->obj->set($config['SITENAME'] . "_" . $var, $val, 0, $expire);
                break;
            case "disk":
                $fp = fopen($this->cachedir . "/$var.cache", "w");
                fwrite($fp, serialize($val));
                fclose($fp);
                return;
                break;
        }
    }
    // Function Delete Memcache According To Its Type
    public function Delete($var)
    {
        global $config;

        switch ($this->type) {
            case "memcache":
                return $this->obj->delete($config['SITENAME'] . "_" . $var);
                break;
            case "disk":
                @unlink($this->cachedir . "/$var.cache");
                break;
        }
    }
    // Get Memcache Type Function Used
    public function Get($var, $expire = 0)
    {
        global $config;
        if ($expire == 0) {
            return false;
        }

        switch ($this->type) {
            case "memcache":
                return $this->obj->get($config['SITENAME'] . "_" . $var);
                break;
            case "disk":
                $file = $this->cachedir . "/$var.cache";
                if (file_exists($file) && (time() - filemtime($file)) < $expire) {
                    return unserialize(file_get_contents($file));
                }
                return false;
                break;
        }
    }
}