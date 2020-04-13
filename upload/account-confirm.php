<?php
require_once("backend/functions.php");
dbconn();

$id = (int) $_GET["id"];
$md5 = $_GET["secret"];

if (!$id || !$md5)
	show_error_msg(T_("ERROR"), T_("INVALID_ID"), 1);

$row = DB::run("SELECT `password`, `secret`, `status` FROM `users` WHERE `id` =?", [$id])->fetch();
if (!$row)
	show_error_msg(T_("ERROR"), sprintf(T_("CONFIRM_EXPIRE"), $site_config['signup_timeout']/86400), 1);

if ($row["status"] != "pending") {
	header("Refresh: 0; url=account-confirm-ok.php?type=confirmed");
	die;
}

if ($md5 != md5($row["secret"]))
	show_error_msg(T_("ERROR"), T_("SIGNUP_ACTIVATE_LINK"), 1);

$secret = mksecret();

$upd = DB::run("UPDATE `users` SET `secret` =?, `status` =? WHERE `id` =? AND `secret` =? AND `status` =?", [$secret, 'confirmed', $id, $row["secret"], 'pending']);
if (!$upd)
	show_error_msg(T_("ERROR"), T_("SIGNUP_UNABLE"), 1);

header("Refresh: 0; url=account-confirm-ok.php?type=confirm");