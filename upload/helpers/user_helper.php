<?php
// Login User Function
function userlogin()
{
    session_start();
    $ip = getip();
    // If there's no IP a script is being ran from CLI. Any checks here will fail, skip all.
    if ($ip == '') {
        return;
    }
    checkipban($ip);

    global $CURUSER, $pdo;

    unset($GLOBALS["CURUSER"]);
    // Check The Cookies and Sessions details
    if (isset($_COOKIE['token'])) {
		$res1 = $pdo->run("SELECT id FROM users WHERE token=?",[$_COOKIE['token']]);
		$row = $res1->fetch(PDO::FETCH_ASSOC);
		$id = $row['id'];
		$_SESSION["uid"] = $id;
    }

    if (!isset($_SESSION['uid'])) {
        logoutcookie();
        return;
    }
    
    //Get User Details And Permissions
    $res = $pdo->run("SELECT * FROM users INNER JOIN groups ON users.class=groups.group_id WHERE id=$_SESSION[uid] AND users.enabled='yes' AND users.status = 'confirmed'");
    $row = $res->fetch(PDO::FETCH_ASSOC);
    $hash = $row["id"] . $row["secret"] . $row["password"] . $ip . $row["secret"];
    if (!$row || !password_verify($hash, $_COOKIE["token"])) {
        logoutcookie();
        return;
    }

    $where = where($_SERVER["SCRIPT_FILENAME"], $row["id"], 0);
    $id = $row['id'];

    $stmt = $pdo->run("UPDATE users SET last_access=?,ip=?,page=? WHERE id=?", [get_date_time(), $ip, $where, $id]);
    $GLOBALS["CURUSER"] = $row;
    unset($row);
}

// Connection Verification Function Otherwise Connection Page
function loggedinonly()
{
    global $CURUSER;
    if (!$CURUSER) {
        header("Refresh: 0; url=".TTURL."/account/login");
        exit();
    }
}

// Function That Removes All From An Account
function deleteaccount($userid)
{
    global $pdo;
    $pdo->run("DELETE FROM users WHERE id = $userid");
    $pdo->run("DELETE FROM warnings WHERE userid = $userid");
    $pdo->run("DELETE FROM ratings WHERE user = $userid");
    $pdo->run("DELETE FROM peers WHERE userid = $userid");
    $pdo->run("DELETE FROM completed WHERE userid = $userid");
    $pdo->run("DELETE FROM reports WHERE addedby = $userid");
    $pdo->run("DELETE FROM reports WHERE votedfor = $userid AND type = 'user'");
    $pdo->run("DELETE FROM forum_readposts WHERE userid = $userid");
    $pdo->run("DELETE FROM pollanswers WHERE userid = $userid");
    // snatch
    $pdo->run("DELETE FROM `snatched` WHERE `uid` = '$userid'");
}

function guestadd()
{
    global $pdo;
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = gmtime();
    $pdo->run("INSERT INTO `guests` (`ip`, `time`) VALUES ('$ip', '$time') ON DUPLICATE KEY UPDATE `time` = '$time'");
}

function getguests()
{
    global $pdo;
    $past = (gmtime() - 2400);
    $pdo->run("DELETE FROM `guests` WHERE `time` < $past");
    return get_row_count("guests");
}
// Function Who Finds Where The Member Is
function where($scriptname = "index", $userid, $update = 1)
{
    global $pdo;
    if (!is_valid_id($userid)) {
        die;
    }

    if (preg_match("/torrents/details/i", $scriptname)) {
        $where = "Browsing Torrents Details (ID: $_GET[id])...";
    } elseif (preg_match("/torrents/browse/i", $scriptname)) {
        $where = "Browsing Torrents...";
    } elseif (preg_match("/accountdetails/i", $scriptname)) {
        $where = "Browsing Account Details (ID: $_GET[id])...";
    } elseif (preg_match("/torrentsupload/i", $scriptname)) {
        $where = "Uploading Torrent..";
    } elseif (preg_match("/usercp/i", $scriptname)) {
        $where = "Browsing User Control Panel...";
    } elseif (preg_match("/torrents/search/i", $scriptname)) {
        $where = "Searching Torrents...";
    } elseif (preg_match("/forums/i", $scriptname)) {
        $where = "Browsing Forums...";
    } elseif (preg_match("/index/i", $scriptname)) {
        $where = "Browsing Homepage...";
    } else {
        $where = "Unknown Location...";
    }

    if ($update) {
        $stmt = $pdo->run("UPDATE users SET page=? WHERE id=?", [$where, $userid]);
    }
    return $where;
}
// Function That Returns The Group Name
function get_user_class_name($i)
{
    global $CURUSER, $pdo;
    if ($i == $CURUSER["class"]) {
        return $CURUSER["level"];
    }

    $res = $pdo->run("SELECT level FROM groups WHERE group_id=" . $i . "");
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
    global $pdo;
    $ret = array();
    $res = $pdo->run("SELECT * FROM groups ORDER BY group_id ASC");
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
    global $site_config, $pdo;
    $classy = $pdo->run("SELECT u.class, u.donated, u.warned, u.enabled, g.Color, g.level, u.uploaded, u.downloaded FROM `users` `u` INNER JOIN `groups` `g` ON g.group_id=u.class WHERE username ='" . $name . "'")->fetch();
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

    return stripslashes("<font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font>");
}
