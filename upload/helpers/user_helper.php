<?php
// Login User Function
function userlogin()
{
    $ip = getip();
    // If there's no IP a script is being ran from CLI. Any checks here will fail, skip all.
    if ($ip == '') {
        return;
    }

    global $CURUSER;
    unset($GLOBALS["CURUSER"]);

    //Check IP bans
    if (is_ipv6($ip)) {
        $nip = ip2long6($ip);
    } else {
        $nip = ip2long($ip);
    }

    $res = DB::run('SELECT * FROM bans WHERE true');
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $banned = false;
        if (is_ipv6($row["first"]) && is_ipv6($row["last"]) && is_ipv6($ip)) {
            $row["first"] = ip2long6($row["first"]);
            $row["last"] = ip2long6($row["last"]);
            $banned = bccomp($row["first"], $nip) != -1 && bccomp($row["last"], $nip) != -1;
        } else {
            $row["first"] = ip2long($row["first"]);
            $row["last"] = ip2long($row["last"]);
            $banned = $nip >= $row["first"] && $nip <= $row["last"];
        }
        if ($banned) {
            header("HTTP/1.0 403 Forbidden");
// todo        echo "<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>Forbidden</title></head><body><h1>Forbidden</h1>Unauthorized IP address.<br />";
            //    "Reason for banning: $row[comment]</body></html>";
            //    die;
        }
    }

    //Check The Cookie and get CURUSER details
    if (strlen($_COOKIE["pass"]) != 60 || !is_numeric($_COOKIE["uid"])) {
        logoutcookie();
        return;
    }
    //Get User Details And Permissions
    $res = DB::run("SELECT * FROM users INNER JOIN groups ON users.class=groups.group_id WHERE id=$_COOKIE[uid] AND users.enabled='yes' AND users.status = 'confirmed'");
    $row = $res->fetch(PDO::FETCH_ASSOC);
    $hash = $row["id"] . $row["secret"] . $row["password"] . $ip . $row["secret"];
    if (!$row || !password_verify($hash, $_COOKIE["pass"])) {
        logoutcookie();
        return;
    }

    $where = where($_SERVER["SCRIPT_FILENAME"], $row["id"], 0);
    $id = $row['id'];

    $stmt = DB::run("UPDATE users SET last_access=?,ip=?,page=? WHERE id=?", [get_date_time(), $ip, $where, $id]);
    $GLOBALS["CURUSER"] = $row;
    unset($row);
}

// Connection Verification Function Otherwise Connection Page
function loggedinonly()
{
    global $CURUSER;
    if (!$CURUSER) {
        header("Refresh: 0; url=/accountlogin");
        exit();
    }
}

// Function That Removes All From An Account
function deleteaccount($userid)
{
    DB::run("DELETE FROM users WHERE id = $userid");
    DB::run("DELETE FROM warnings WHERE userid = $userid");
    DB::run("DELETE FROM ratings WHERE user = $userid");
    DB::run("DELETE FROM peers WHERE userid = $userid");
    DB::run("DELETE FROM completed WHERE userid = $userid");
    DB::run("DELETE FROM reports WHERE addedby = $userid");
    DB::run("DELETE FROM reports WHERE votedfor = $userid AND type = 'user'");
    DB::run("DELETE FROM forum_readposts WHERE userid = $userid");
    DB::run("DELETE FROM pollanswers WHERE userid = $userid");
    // snatch
    DB::run("DELETE FROM `snatched` WHERE `uid` = '$userid'");
}

function guestadd()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = gmtime();
    DB::run("INSERT INTO `guests` (`ip`, `time`) VALUES ('$ip', '$time') ON DUPLICATE KEY UPDATE `time` = '$time'");
}

function getguests()
{
    $past = (gmtime() - 2400);
    DB::run("DELETE FROM `guests` WHERE `time` < $past");
    return get_row_count("guests");
}
// Function Who Finds Where The Member Is
function where($scriptname = "index", $userid, $update = 1)
{
    if (!is_valid_id($userid)) {
        die;
    }

    if (preg_match("/torrents-details/i", $scriptname)) {
        $where = "Browsing Torrents Details (ID: $_GET[id])...";
    } elseif (preg_match("/torrentsmain/i", $scriptname)) {
        $where = "Browsing Torrents...";
    } elseif (preg_match("/account-details/i", $scriptname)) {
        $where = "Browsing Account Details (ID: $_GET[id])...";
    } elseif (preg_match("/torrents-upload/i", $scriptname)) {
        $where = "Uploading Torrent..";
    } elseif (preg_match("/account/i", $scriptname)) {
        $where = "Browsing User Control Panel...";
    } elseif (preg_match("/torrents-search/i", $scriptname)) {
        $where = "Searching Torrents...";
    } elseif (preg_match("/forums/i", $scriptname)) {
        $where = "Browsing Forums...";
    } elseif (preg_match("/index/i", $scriptname)) {
        $where = "Browsing Homepage...";
    } else {
        $where = "Unknown Location...";
    }

    if ($update) {
        $stmt = DB::run("UPDATE users SET page=? WHERE id=?", [$where, $userid]);
    }
    return $where;
}
// Function That Returns The Group Name
function get_user_class_name($i)
{
    global $CURUSER;
    if ($i == $CURUSER["class"]) {
        return $CURUSER["level"];
    }

    $res = DB::run("SELECT level FROM groups WHERE group_id=" . $i . "");
    $row = $res->fetch(PDO::FETCH_LAZY);
    return $row[0];
}
// Function That Returns The Class Of A Given Member
function get_user_class()
{
    return $GLOBALS["CURUSER"]["class"];
}

// Function To List Groups Of Members Of The Database
function classlist()
{
    $ret = array();
    $res = DB::run("SELECT * FROM groups ORDER BY group_id ASC");
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $ret[] = $row;
    }

    return $ret;
}

function priv($name, $descr)
{
    global $CURUSER;
    if ($CURUSER["privacy"] == $name) {
        return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
    }

    return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
}

// Start class_user colour function
function class_user($name)
{
    global $site_config;
    $classy = DB::run("SELECT u.class, u.donated, u.warned, u.enabled, g.Color, g.level, u.uploaded, u.downloaded FROM `users` `u` INNER JOIN `groups` `g` ON g.group_id=u.class WHERE username ='" . $name . "'")->fetch();
    $gcolor = $classy['Color'];
    if ($classy['donated'] > 0) {
        $star = "<img src='" . $site_config['SITEURL'] . "/images/donor.png' alt='donated' border='0' width='15' height='15'>";
    } else {
        $star = "";
    }
    if ($classy['warned'] == "yes") {
        $warn = "<img src='" . $site_config['SITEURL'] . "/images/warn.png' alt='Warn' border='0'>";
    } else {
        $warn = "";
    }
    if ($classy['enabled'] == "no") {
        $disabled = "<img src='" . $site_config['SITEURL'] . "/images/disabled.png' title='Disabled' border='0'>";
    } else {
        $disabled = "";
    }

    return unesc("<font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font>");
}
