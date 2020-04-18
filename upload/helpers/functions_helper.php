<?php
// Functions
require "tzs_helper.php"; // Get Timezones
require "language_helper.php";
require "cleanup_helper.php";
require "security_helper.php"; // todo
require "nfo_helper.php"; // todo
require "forum_helper.php";
require "blocks_helper.php";
require "pagination_helper.php";
require "ip_helper.php";
require "cookie_helper.php";
require "general_helper.php";
require "format_helper.php";
require "comment_helper.php";
require "timedate_helper.php";
require "layout_helper.php";
require "user_helper.php";
require "validation_helper.php";
require "torrent_helper.php";
require "mod_helper.php";

// Set user globals
function dbconn($autoclean = false)
{
    global $THEME, $LANGUAGE, $LANG, $site_config, $pdo;
    $THEME = $LANGUAGE = null;
    $pdo = new Database;
	
	if (!ob_get_level()) {
        if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }

    }

    header("Content-Type: text/html;charset=$site_config[CHARSET]");

    userlogin(); //Get user info

    //Get language and theme
    $CURUSER = $GLOBALS["CURUSER"];

    $stmt = DB::run("select uri from stylesheets where id='" . ($CURUSER ? $CURUSER['stylesheet'] : $site_config['default_theme']) . "'");
    $ss_a = $stmt->fetch(PDO::FETCH_ASSOC);
    $THEME = $ss_a["uri"];

    $stmt = DB::run("select uri from languages where id='" . ($CURUSER ? $CURUSER['language'] : $site_config['default_language']) . "'");
    $lng_a = $stmt->fetch(PDO::FETCH_ASSOC);
    $LANGUAGE = $lng_a["uri"];

    require_once "languages/$LANGUAGE";

    if ($autoclean) {
        autoclean();
    }

}