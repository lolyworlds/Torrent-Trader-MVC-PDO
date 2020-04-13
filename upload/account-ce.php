<?php
require_once("backend/functions.php");
dbconn();

$id = (int) $_GET["id"];
$md5 = $_GET["secret"];
$email = $_GET["email"];

if (!$id || !$md5 || !$email) {
	show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA"), 1);
}

$row = DB::run("SELECT `editsecret` FROM `users` WHERE `enabled` =? AND `status` =? AND `editsecret` !=?  AND `id` =?", ['yes', 'confirmed', '', $id])->fetch();

if (!$row) {
	show_error_msg(T_("ERROR"), T_("NOTHING_FOUND"), 1);
}

$sec = $row["editsecret"];

if ($md5 != md5($sec . $email . $sec))
    show_error_msg(T_("ERROR"), T_("NOTHING_FOUND"), 1);

DB::run("UPDATE `users` SET `editsecret` =?, `email` =? WHERE `id` =? AND `editsecret` =?", ['', $email, $id, $row["editsecret"]]);

header("Refresh: 0; url=account.php");
header("Location: account.php");