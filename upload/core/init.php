<?php
mb_internal_encoding('UTF-8');

// Error Reporting
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
// error_reporting(E_ALL);

// MOVE TO xampp.php
define("TTROOT", dirname(dirname(__FILE__)));

// Register custom exception handler
include( "helpers/exception_helper.php");
set_exception_handler("handleUncaughtException");

// Do NOT change this. All times are converted to user's chosen timezone.
if (function_exists("date_default_timezone_set"))
	date_default_timezone_set("Europe/London"); 

// Get Site Settings and Vars ($site_config)
require_once ("config/config.php");
define("TTURL", $site_config['SITEURL']);

// Include all helpers & connection
require_once ("helpers/functions_connect.php");

// Global Standard connection
global $dbh;
$dbh = new PDO('mysql:host='.$site_config['mysql_host'].';dbname='.$site_config['mysql_db'], $site_config['mysql_user'], $site_config['mysql_pass']);

// Classes 
require "classes/dbclass.php"; // DB::run Static prepared atatements
require "classes/cache.php"; // Caching
require "classes/mail.php"; // Mail functions

// Session Handler
require "classes/Session.php";

$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));

// Autoload Core
spl_autoload_register(function($className) {
	include_once 'core/' . $className . '.php';
});