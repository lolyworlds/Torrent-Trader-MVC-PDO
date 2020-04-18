<?php

// Plural forms: http://www.gnu.org/software/hello/manual/gettext/Plural-forms.html
// $LANG["PLURAL_FORMS"] is in the plural= format

function T_($s)
{
    global $LANG;

    if ($ret = (isset($LANG[$s]) ? $LANG[$s] : null)) { // index
        //return "TRANSLATED";
        return $ret;
    }

    if ($ret = (isset($LANG["{$s}[0]"]) ? $LANG["{$s}[0]"] : null)) { // index
        //return "TRANSLATED";
        return $ret;
    }

    return $s;
}

function P_($s, $num)
{
    global $LANG;

    $num = (int) $num;

    $plural = str_replace("n", $num, $LANG["PLURAL_FORMS"]);
    $i = eval("return intval($plural);");

    if ($ret = (isset($LANG["{$s}[$i]"]) ? $LANG["{$s}[$i]"] : null))
    //return "TRANSLATED";
    {
        return $ret;
    }

    return $s;
}
