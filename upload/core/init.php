<?php
mb_internal_encoding('UTF-8');

define("TTROOT", dirname(dirname(__FILE__)));

include(TTROOT."/helpers/exception_helper.php");
// Register custom exception handler
set_exception_handler("handleUncaughtException");

if (function_exists("date_default_timezone_set"))
	date_default_timezone_set("Europe/London"); // Do NOT change this. All times are converted to user's chosen timezone.

// Get Site Settings and Vars ($site_config)
require_once (TTROOT."/config/config.php");
// Include all helpers & dbconn function
require_once (TTROOT."/helpers/functions_helper.php");

// Classes 
require TTROOT."/classes/dbclass.php"; //Get PDO Connection Info
require TTROOT."/classes/cache.php"; // Caching
require TTROOT."/classes/mail.php"; // Mail functions

$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));

// Autoload Core
spl_autoload_register(function($className) {
	include_once TTROOT . '/core/' . $className . '.php';
});