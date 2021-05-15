<?php

class Validation
{
    public $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    private function __clone()
    {}

    public function isEmpty($data)
    {
        if (is_array($data)) {
            return empty($data);
        } elseif ($data == "") {
            return true;
        } else {
            return false;
        }
    }

    public function validEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        return true;
    }

    public function userExist($username)
    {
        $count = get_row_count("users", "WHERE username=$username");
        if ($count != 0) {
            $message = sprintf(Lang::T("USERNAME_INUSE_S"), $username);
            return true;
        }
    }

    public function emailExist($email)
    {
        return $this->exist("users", "email", $email);
    }

    public function userAlfNum($username)
    {
        return ctype_alnum($username) ? true : false;
    }

    public function validDate($date, $format = "Y-m-d")
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    private function exist($table, $column, $value)
    {
        $result = $this->db->run("SELECT * FROM `$table` WHERE `$column` = :value", ["value" => $value]);
        return count($result) > 0 ? true : false;
    }

    public function validFilename($name)
    {
        return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
    }

    public function validId($id)
    {
        return is_numeric($id) && ($id > 0) && (floor($id) == $id);
    }

    public function validInt($id)
    {
        return is_numeric($id) && (floor($id) == $id);
    }

    public function cleanstr($s)
    {
        if (function_exists("filter_var")) {
            return filter_var($s, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
        } else {
            return preg_replace('/[\x00-\x1F]/', "", $s);
        }
    }
    
    public function validUsername($username)
    {
        $allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for ($i = 0; $i < strlen($username); ++$i) {
            if (strpos($allowedchars, $username[$i]) === false) {
                return false;
            }
        }

        return true;
    }

}