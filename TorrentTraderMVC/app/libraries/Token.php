<?php

class Token
{
    public function __construct() { }

    private function __clone() { }

    public static function generate()
    {
        return Session::set("ttttt", sha1(time()));
    }

    public static function check($token)
    {
        $tokenNome = "ttttt";
        if (Session::get($tokenNome) && $token === Session::get($tokenNome)) {
            Session::destroy($tokenNome);
            return true;
        }
        return false;
    }

}