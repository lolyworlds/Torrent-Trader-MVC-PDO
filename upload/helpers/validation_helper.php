<?php

// File Name Validation Function
function validfilename($name)
{
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

// Email Validation Function
function validemail($email)
{
    if (function_exists("filter_var")) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    return preg_match('/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i', $email);
}

// ID Validation Function
function is_valid_id($id)
{
    return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

// Number Validation Function
function is_valid_int($id)
{
    return is_numeric($id) && (floor($id) == $id);
}

function cleanstr($s)
{
    if (function_exists("filter_var")) {
        return filter_var($s, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    } else {
        return preg_replace('/[\x00-\x1F]/', "", $s);
    }
}
// User Name Validation Function
function validusername($username)
{
    $allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for ($i = 0; $i < strlen($username); ++$i) {
        if (strpos($allowedchars, $username[$i]) === false) {
            return false;
        }
    }

    return true;
}