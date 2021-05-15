<?php
class Session
{
    public function __construct()
    {}

    private function __clone()
    {}

    public static function set($key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    public static function destroy($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroySession()
    {
        setcookie("PHPSESSID", null, time() - 7000000, "/");
        $_SESSION = array();
        unset($_SESSION);
        @session_destroy();
    }

    public static function flash($type, $msg, $url, $wrapper = "1")
    {
        if ($wrapper) {
            ob_start();
            ob_clean();
        }

        if (self::get($type)) {
            self::get($type);
            self::destroy($type);
        } else {
            self::set($type, $msg);
        };
        Style::header($type);
        Style::begin(LANG::T('ERROR'));
        echo '<div class="alert alert-info">'.$msg.'</div>';
        echo "\n<meta http-equiv=\"refresh\" content=\"3; url='$url'\">\n";
        Style::end();
        Style::footer();
        if ($wrapper) {
            die();
        }
    }

}