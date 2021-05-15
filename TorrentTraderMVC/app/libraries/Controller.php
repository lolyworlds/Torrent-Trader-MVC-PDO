<?php

class Controller
{
    public function __construct()
    {
        //$this->loggedIn();
        //$this->ipBanned();
    }

    public function __clone()
    {
        
    }
    
    public function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    public function view($file, $data = [], $inc = false)
    {
        if (file_exists('../app/views/' . $file . '.php')) {
            if ($inc) {
                require_once "../app/views/inc/" . ($_SESSION['stylesheet'] ?: DEFAULTTHEME) . "/header.php";
                require_once "../app/views/" . $file . ".php";
                require_once "../app/views/inc/" . ($_SESSION['stylesheet'] ?: DEFAULTTHEME) . "/footer.php";
            } else {
                require_once "../app/views/" . $file . ".php";
            }
        } else {
            die('View does not exist');
        }
    }
    /*
public function loggedIn()
{
if ((Session::get("id") || Session::get("password")) == null) {
return false;
}
if (LOGINFINGERPRINT == true) {
$loginString = $this->loginString();
$stringNow = Session::get("login_fingerprint");
if ($stringNow != null && $stringNow == $loginString) {
return true;
} else {
$this->logout();
return false;
}
}
//if you got to this point, user is logged in
return true;
}

private function loginString()
{
$ip = Helper::getIP();
$browser = Helper::browser();
return hash("sha512", $ip, $browser);
}

private function logout()
{
Session::destroySession();
Redirect::to("login");
}

public function ipBanned()
{
$ip = Helper::getIP();
if ($ip == '') {
return;
}
Ip::checkipban($ip);
}
 */
}
