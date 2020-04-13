<?php
mb_internal_encoding('UTF-8');

define("TTROOT", dirname(dirname(__FILE__)));

include(TTROOT."/helpers/exceptionhelper.php");
// Register custom exception handler
set_exception_handler("handleUncaughtException");

if (function_exists("date_default_timezone_set"))
	date_default_timezone_set("Europe/London"); // Do NOT change this. All times are converted to user's chosen timezone.

require_once (TTROOT."/config/config.php");  //Get Site Settings and Vars ($site_config)
require_once (TTROOT."/helpers/dbclass.php"); //Get MYSQL Connection Info
require_once (TTROOT."/helpers/cache.php"); // Caching
require_once (TTROOT."/helpers/mail.php"); // Mail functions
require_once (TTROOT."/helpers/tzs.php"); // Get Timezones
require_once (TTROOT."/helpers/languages.php");
require_once (TTROOT."/helpers/functions.php");
require_once (TTROOT."/helpers/bootstraphelper.php");
$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));

// Autoload Core
spl_autoload_register(function($className) {
	include_once TTROOT . '/core/' . $className . '.php';
});