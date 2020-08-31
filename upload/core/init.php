<?php
mb_internal_encoding('UTF-8');

// Error Reporting
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
// error_reporting(E_ALL);

// Register custom exception handler
include "helpers/exception_helper.php";
set_exception_handler("handleUncaughtException");

// Do NOT change this. All times are converted to user's chosen timezone.
if (function_exists("date_default_timezone_set")) {
    date_default_timezone_set("Europe/London");
}

// Get Site Settings and Vars ($config)
require_once "config/config.php";
define("TTURL", $config['SITEURL']);

// Include all helpers & connection
require_once "helpers/a_start_function.php";

// Core
require "core/Database.php"; // Extened PDO connection
require "core/Router.php"; // Set Paths
require "core/Controller.php"; // Load Models/Views

// Autoload Classes (Controllers & Models already loaded)
spl_autoload_register(function ($className) {
    include_once 'classes/' . $className . '.php';
});

session_start();

// Micro Time
$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));