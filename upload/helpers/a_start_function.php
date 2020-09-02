<?php
// Functions
require "tzs_helper.php"; // Get Timezones
require "language_helper.php";
require "cleanup_helper.php";
require "security_helper.php";
require "nfo_helper.php";
require "forum_helper.php";
require "blocks_helper.php";
require "pagination_helper.php";
require "ip_helper.php";
require "backup_helper.php";
require "general_helper.php";
require "format_helper.php";
require "comment_helper.php";
require "timedate_helper.php";
require "layout_helper.php";
require "user_helper.php";
require "validation_helper.php";
require "torrent_helper.php";
require "smileys.php";
require "topten_helper.php";

// Set user up globals
function dbconn($autoclean = false)
{
    global $config, $THEME, $LANGUAGE, $LANG, $config, $pdo;
    $THEME = $LANGUAGE = null;
    $pdo = new Database();
    global $pdo;

    if (!ob_get_level()) {
        if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }
    }
    header("Content-Type: text/html;charset=$config[CHARSET]");

    $ip = getip();
    // If there's no IP a script is being ran from CLI. Any checks here will fail, skip all.
    if ($ip == '') {
        return;
    }
    checkipban($ip);

    // Check The Cookies and Sessions details
    if ($_SESSION["loggedin"] = false && !is_numeric($_SESSION["id"]) && strlen($_SESSION["password"]) != 60) {
        Cookie::destroy();
    } else {
        // Get User Details And Permissions
        $res = $pdo->run("SELECT * FROM users INNER JOIN groups ON users.class=groups.group_id WHERE id=? AND users.enabled=? AND users.status =? ", [$_SESSION['id'], 'yes', 'confirmed']);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        // Row there so update
        if ($row) {
            $where = where($_SERVER['REQUEST_URI'], $row["id"], 0);
            $id = $row['id'];
            $stmt = $pdo->run("UPDATE users SET last_access=?,ip=?,page=? WHERE id=?", [get_date_time(), $ip, $where, $id]);
            // Super Session Array
            $_SESSION = $row;
            $_SESSION["loggedin"] = true;
            unset($row);
        }
    }
    // Set Lang
    require_once "languages/" . ($_SESSION['language'] ?: $config['default_language']) . ".php";

    if ($autoclean) {
        autoclean();
    }
}
