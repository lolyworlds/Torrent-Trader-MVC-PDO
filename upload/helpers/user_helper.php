<?php

// Connection Verification Function Otherwise Connection Page
function loggedinonly()
{
    if (!$_SESSION['loggedin'] == true) {
        header("Refresh: 0; url=" . TTURL . "/account/login");
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
function where($where, $userid, $update = 1)
{
    if (!is_valid_id($userid)) {
        die;
    }

    if (empty($where)) {
        $where = "Unknown Location...";
    }

    if ($update) {
        $stmt = DB::run("UPDATE users SET page=? WHERE id=?", [$where, $userid]);
    }

    if (!$update) {
        return $where;
    } else {
        return;
    }
}
// Function That Returns The Group Name
function get_user_class_name($i)
{
    $pdo = new Database();
    if ($i == $_SESSION["class"]) {
        return $_SESSION["level"];
    }

    $res = $pdo->run("SELECT level FROM groups WHERE group_id=" . $i . "");
    $row = $res->fetch(PDO::FETCH_LAZY);
    return $row[0];
}
// Function That Returns The Class Of A Given Member
function get_user_class()
{
    return $_SESSION["class"];
}
function get_others_class($user)
{
    return $user["class"];
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
    if ($_SESSION["privacy"] == $name) {
        return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
    }

    return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
}

// Start class_user_colour colour function
function class_user_colour($name)
{
    global $config, $pdo;
    $classy = $pdo->run("SELECT u.class, u.donated, u.warned, u.enabled, g.Color, g.level, u.uploaded, u.downloaded FROM `users` `u` INNER JOIN `groups` `g` ON g.group_id=u.class WHERE username ='" . $name . "'")->fetch();
    $gcolor = $classy->Color;
    if ($classy->donated > 0) {
        $star = "<img src='" . $config['SITEURL'] . "/images/donor.png' alt='donated' border='0' width='15' height='15'>";
    } else {
        $star = "";
    }
    if ($classy->warned == "yes") {
        $warn = "<img src='" . $config['SITEURL'] . "/images/warn.png' alt='Warn' border='0'>";
    } else {
        $warn = "";
    }
    if ($classy->enabled == "no") {
        $disabled = "<img src='" . $config['SITEURL'] . "/images/disabled.png' title='Disabled' border='0'>";
    } else {
        $disabled = "";
    }

    return stripslashes("<font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font>");
}