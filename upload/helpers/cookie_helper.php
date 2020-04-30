<?php
// Obtain / Create Cookie Function
function logincookie($id, $password, $secret, $updatedb = 1, $expires = 0x7fffffff)
{
    global $pdo;
    // Retrieving Cookie Information
    $hash = $id . $secret . $password . getip() . $secret;
    // Cookie Information Encryption
    $hash = password_hash($hash, PASSWORD_BCRYPT);
    // Declaration And Sending Cookie
    setcookie("token", $hash, $expires, "/");

    if ($updatedb) {
        $stmt = $pdo->run("UPDATE users SET last_login=? WHERE id=?", [get_date_time(), $id]);
		$token = $pdo->run("UPDATE users SET token=? WHERE id=?", [$hash, $id]);
    }

}
// Cookie Destruction function
function logoutcookie()
{
    session_start();
    // As Set to NULL ==> Destruction
    setcookie("token", null, time(), "/");
    // Destroy Sessions
    unset($_SESSION['uid']);
    unset($_SESSION['pass']);
    session_destroy();
}

function setsess($id, $password)
{
    session_start();
    $_SESSION['uid'] = $id;
    $_SESSION['pass'] = $password;
}