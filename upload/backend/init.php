<?php
mb_internal_encoding('UTF-8');

define("TTROOT", dirname(__DIR__));
define("TTCORE", str_replace(__DIR__."backend/", "", dirname(__FILE__)));

include(TTCORE.'/exceptionhelper.php');
// Register custom exception handler
set_exception_handler("handleUncaughtException");

if (function_exists("date_default_timezone_set"))
	date_default_timezone_set("Europe/London"); // Do NOT change this. All times are converted to user's chosen timezone.

require_once (TTCORE."/config.php");  //Get Site Settings and Vars ($site_config)
require_once (TTCORE."/dbclass.php"); //Get MYSQL Connection Info
require_once (TTCORE."/cache.php"); // Caching
require_once (TTCORE."/mail.php"); // Mail functions
require_once (TTCORE."/tzs.php"); // Get Timezones
require_once (TTCORE."/languages.php");
require_once (TTCORE."/functions.php");

$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));
