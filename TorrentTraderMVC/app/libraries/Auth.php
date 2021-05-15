<?php

class Auth
{

    public static function user($autoclean = false)
    {
        $db = new Database();
        self::ipBanned();
		self::isClosed();
        if (strlen($_COOKIE["password"]) != 60 || !is_numeric($_COOKIE["id"]) || $_COOKIE["login_fingerprint"] != self::loginString()) {
            Redirect::to(URLROOT . "/logout");
        } else {
            // Put User Details in Session Array
            $message = null;
            if (Session::get('message')) {
                $message = $_SESSION['message'];
            }
            $token = null;
            if (Session::get('ttttt')) {
                $token = $_SESSION['ttttt'];
            }
            $res = $db->run("SELECT * FROM users INNER JOIN groups ON users.class=groups.group_id WHERE id=? AND users.enabled=? AND users.status =? ", [$_COOKIE["id"], 'yes', 'confirmed']);
            $row = $res->fetch(PDO::FETCH_ASSOC);

            if ($row['token'] != $_COOKIE['password']) {
                Redirect::to(URLROOT . "/logout");
            }

            if ($row) {
                $where = Helper::where($_SERVER['REQUEST_URI'], $row["id"], 0);
                $db->run("UPDATE users SET last_access=?,ip=?,page=? WHERE id=?", [Helper::get_date_time(), Helper::getIP(), $where, $row["id"]]);
                $_SESSION = $row;
                $_SESSION["ttttt"] = $token;
                $_SESSION["loggedin"] = true;
                $_SESSION['login_fingerprint'] = self::loginString();
                $_SESSION['message'] = $message;
                unset($row);
            } else {
                Redirect::to(URLROOT . "/logout");
            }
        }

        if ($autoclean) {
            autoclean();
        }
    }

    private static function loginString()
    {
        $ip = Helper::getIP();
        $browser = Helper::browser();
        return hash("sha512", $ip, $browser);
    }

    public static function ipBanned()
    {
        $ip = Helper::getIP();
        if ($ip == '') {
            return;
        }
        Ip::checkipban($ip);
    }
	    
    public static function isClosed($wrapper = 1) {
    if (!SITE_ONLINE) {
        if ($_SESSION["control_panel"] != "yes") {
            if ($wrapper) {
                ob_start();
                ob_clean();
            }
            require_once "../app/views/inc/default/header.php";
            echo '<div class="alert alert-info"><center>' . stripslashes(OFFLINEMSG) . '</center></div>';
            require_once "../app/views/inc/default/footer.php";
            if ($wrapper) {
                die();
            }
        }
    }
    }
}