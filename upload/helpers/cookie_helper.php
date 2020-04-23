<?php
// Obtain / Create Cookie Function
function logincookie($id, $password, $secret, $updatedb = 1, $expires = 0x7fffffff)
{
    global $pdo;
    // Retrieving Cookie Information
    $hash = $id . $secret . $password . getip() . $secret;
    // Cookie Information Encryption
    $hash = password_hash($hash, PASSWORD_BCRYPT);
    // Use the encryption below if you are in PHP 7.2 or higher,
    // Comment The Line Above, Uncomment The Line Below
    // $hash = password_hash ($hash, PASSWORD_ARGON2I);
    // Declaration And Sending Cookie
    setcookie("pass", $hash, $expires, "/");
    setcookie("uid", $id, $expires, "/");

    if ($updatedb) {
        $stmt = $pdo->run("UPDATE users SET last_login=? WHERE id=?", [get_date_time(), $id]);
    }

}
// Cookie Destruction function
function logoutcookie()
{
    // Declaration And Sending Cookie
    // As Set to NULL ==> Destruction
    setcookie("pass", null, time(), "/");
    setcookie("uid", null, time(), "/");
}
