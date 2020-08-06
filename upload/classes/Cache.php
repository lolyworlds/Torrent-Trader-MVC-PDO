<?php
class Cache
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