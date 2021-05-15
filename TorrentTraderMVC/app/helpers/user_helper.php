<?php

// Function Who Finds Where The Member Is
function where($where, $userid, $update = 1)
{
    $valid = new Validation();
    if (!$valid->validId($userid)) {
        die;
    }
    if (empty($where)) {
        $where = "Unknown Location...";
    }
    if ($update) {
        DB::run("UPDATE users SET page=? WHERE id=?", [$where, $userid]);
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

function get_others_class($user)
{
    return $user["class"];
}

// Function To List Groups Of Members Of The Database
function classlist()
{
    $pdo = new Database();
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