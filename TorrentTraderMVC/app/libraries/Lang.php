<?php

class Lang
{
    public static function T($s)
    {
        global $LANG;
        if ($ret = (isset($LANG[$s]) ? $LANG[$s] : null)) {
            return $ret;
        }
        if ($ret = (isset($LANG["{$s}[0]"]) ? $LANG["{$s}[0]"] : null)) {
            return $ret;
        }
        return $s;
    }

    public static function N($s, $num)
    {
        global $LANG;
        $num = (int) $num;
        $plural = str_replace("n", $num, $LANG["PLURAL_FORMS"]);
        $i = eval("return intval($plural);");
        if ($ret = (isset($LANG["{$s}[$i]"]) ? $LANG["{$s}[$i]"] : null)) {
            return $ret;
        }
        return $s;
    }
}
