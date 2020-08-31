<?php

// PDO escaping
function sqlesc($x)
{
    if (!is_numeric($x)) {
        $x = "'" . $x . "'";
    }
    return $x;
}

function unesc($x)
{
    return stripslashes($x);
    return $x;
}

function escape_url($url)
{
    $ret = '';
    for ($i = 0; $i < strlen($url); $i += 2) {
        $ret .= '%' . $url[$i] . $url[$i + 1];
    }

    return $ret;
}

function mksecret($len = 20)
{
    $chars = array_merge(range(0, 9), range("A", "Z"), range("a", "z"));
    shuffle($chars);
    $x = count($chars) - 1;
    for ($i = 1; $i <= $len; $i++) {
        $str .= $chars[mt_rand(0, $x)];
    }

    return $str;
}

function sqlwildcardesc($x)
{
    return str_replace(array("%", "_"), array("\\%", "\\_"), $x);
}