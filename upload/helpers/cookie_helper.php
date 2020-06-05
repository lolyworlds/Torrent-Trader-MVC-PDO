<?php
// Obtain / Create Cookie Function
function logincookie($id, $password, $secret, $updatedb = 1, $expires = 0x7fffffff)
{
    global $pdo;
    // get sess_id
    $sid = session_id();
    // store only sess_id
    setcookie("PHPSESSID", $sid, time()+ 30*30*60*60, "/"); // one month
    // maybe add session in cleanup
    if ($updatedb) {
        $stmt = $pdo->run("UPDATE users SET last_login=? WHERE id=?", [get_date_time(), $id]);
    }
}

// Cookie Destruction function
function logoutcookie()
{
    // get sess_id
    $sid = session_id();
    // reset cookie to session 
    setcookie("PHPSESSID", $sid, "0", "/");
    session_start();
    session_destroy();
}

function setsess($id, $password, $secret)
{
    session_start();
	// encrypt pass
    $hash = $id . $secret . $password . getip() . $secret;
    // set session handler
    $_SESSION['id'] = $id;
    $_SESSION['password'] = $hash;
}