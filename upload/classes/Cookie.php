<?php

class Cookie
{

    public function __construct()
    {

    }

    // a classical static method to make it universally available
    public static function destroy()
    {
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

}