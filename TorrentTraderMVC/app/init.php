<?php
session_start();
// Load Config
require_once 'config/config.php';
// Load Langauge
require_once LANG . 'english.php';
// Error Reporting
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
ini_set('error_log', '../data/logs/errors_log.txt');
//error_reporting(E_ALL ^ E_NOTICE);
// Register custom exception handler
include "helpers/exception_helper.php";
set_exception_handler("handleUncaughtException"); //handleException
// Load TimeZones
require_once 'helpers/tzs_helper.php';
// Load Helpers
require "helpers/general_helper.php";
require "helpers/cleanup_helper.php";
require "helpers/security_helper.php";
require "helpers/forum_helper.php";
require "helpers/pagination_helper.php";
require "helpers/format_helper.php";
require "helpers/comment_helper.php";
require "helpers/user_helper.php";
require "helpers/torrent_helper.php";
require "helpers/smileys.php";
// Autoload Classes
spl_autoload_register(function ($className) {
    require_once 'libraries/' . $className . '.php';
});