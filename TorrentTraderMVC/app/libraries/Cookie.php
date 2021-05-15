<?php
class Cookie
{

    // a classical static method to make it universally available
    public static function destroyAll()
    {
        setcookie("id", null, time() - 7000000, "/");
        setcookie("password", null, time() - 7000000, "/");
        setcookie("login_fingerprint", null, time() - 7000000, "/");
        setcookie("PHPSESSID", null, time() - 7000000, "/");
        $_SESSION = array();
        unset($_SESSION);
        @session_destroy();
    }

    public static function set()
    {
        $sid = session_id();
        setcookie("PHPSESSID", $sid, time() + 30 * 30 * 60 * 60, "/"); // one month
    }

    public static function exists($name)
    {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    public static function get($name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        } else {
            return $name;
        }
    }

    public static function set1($name, $value, $expiry)
    {
        if (setcookie($name, $value, time() + $expiry, "/")) {
            return true;
        }
        return false;
    }

    public static function delete($name)
    {
        self::set($name, "", time() - 7777777, "/");
    }

}
