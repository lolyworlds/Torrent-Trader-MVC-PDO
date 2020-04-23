<?php
mb_internal_encoding('UTF-8');

// MOVE TO xampp.php
define("TTROOT", dirname(dirname(__FILE__)));

// Register custom exception handler
include(TTROOT."/helpers/exception_helper.php");
set_exception_handler("handleUncaughtException");

// Do NOT change this. All times are converted to user's chosen timezone.
if (function_exists("date_default_timezone_set"))
	date_default_timezone_set("Europe/London"); 

// Get Site Settings and Vars ($site_config)
require_once (TTROOT."/config/config.php");
define("TTURL", $site_config['SITEURL']);

// Include all helpers & connection
require_once (TTROOT."/helpers/functions_connect.php");

// Classes 
require TTROOT."/classes/dbclass.php"; //Get PDO Connection Info
require TTROOT."/classes/cache.php"; // Caching
require TTROOT."/classes/mail.php"; // Mail functions

$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));

// Autoload Core
spl_autoload_register(function($className) {
	include_once TTROOT.'core/' . $className . '.php';
});