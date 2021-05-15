<?php
class Cache
{
    public function __construct()
    {
        $this->cachedir = CACHE;
        $this->type = strtolower(trim(CACHE_TYPE));
        // Cache Connection
        switch ($this->type) {
            case "memcache":
                $this->obj = new Memcache;
                if (!@$this->obj->Connect(MEMCACHE_HOST, MEMCACHE_PORT)) {
                    $this->type = "disk";
                }
                break;
            default:
                $this->type = "disk";
        }
    }

    public function Set($var, $val, $expire = 0)
    {
        if ($expire == 0) {
            return;
        }
        switch ($this->type) {
            case "memcache":
                return $this->obj->set(SITENAME . "_" . $var, $val, 0, $expire);
                break;
            case "disk":
                $fp = fopen($this->cachedir . "/$var.cache", "w");
                fwrite($fp, serialize($val));
                fclose($fp);
                return;
                break;
        }
    }

    public function Delete($var)
    {
        switch ($this->type) {
            case "memcache":
                return $this->obj->delete(SITENAME . "_" . $var);
                break;
            case "disk":
                @unlink($this->cachedir . "/$var.cache");
                break;
        }
    }

    public function Get($var, $expire = 0)
    {
        if ($expire == 0) {
            return false;
        }
        switch ($this->type) {
            case "memcache":
                return $this->obj->get(SITENAME . "_" . $var);
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
