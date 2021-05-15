<?php
//Access Security check
if (preg_match('/config.php/i', $_SERVER['PHP_SELF'])) {
    die;
}
// DB Details
define("DB_HOST", "localhost");
define("DB_USER", "dbusername");
define("DB_PASS", "password");
define("DB_NAME", "dbname");
define('DB_CHAR', 'utf8');
// URL Root
define('URLROOT', 'http://localhost/TorrentTraderMVC');
// Captcha use google api
define('CAPTCHA_ON', false);
define('CAPTCHA_KEY', 'googlecaptchakey');
define('CAPTCHA_SECRET', 'googlecaptchasecret');
// App Root
define('APPROOT', dirname(dirname(__FILE__)));
// Paths
define('CACHE', '../data/cache'); // Cache dir (only used if type is "disk"). Must be CHMOD 777
define('LANG', '../data/languages/');
define('TORRENTDIR', '../data/uploads');
define('NFODIR', '../data/uploads');
define('BLOCKSDIR', '../views/blocks');
define('IMPORT', '../data/import');
define('LOGGER', '../data/logs');
define('BACUP', '../data/backups');
// Site Name
define('SITENAME', 'TorrentTraderMVC');
define('_SITEDESC', 'A PHP support forum for Torrent Trader');
define('SITEEMAIL', 'something@email.com'); //Emails will be sent from this address
define('VERSION', 'PDO');
// Caching settings
define('CACHE_TYPE', 'disk'); // disk = Save cache to disk, memcache = Use memcache, apc = Use APC, xcache = Use XCache
define('MEMCACHE_HOST', 'localhost'); // Host memcache is running on
define('MEMCACHE_PORT', 11211); // Port memcache is running on
// Settings
define('SITENOTICEON', true);
define('SITENOTICE', 'Welcome To TorrentTrader MVC/PDO');
define('DEFAULTLANG', 'english'); //DEFAULT LANGUAGE ID
define('DEFAULTTHEME', 'darktheme'); //DEFAULT THEME ID
define('CHARSET', 'utf-8'); //Site Charset
define('ANNOUNCELIST', URLROOT . '/announce.php'); //seperate via comma
define('MEMBERSONLY', false); //MAKE MEMBERS SIGNUP
define('MEMBERSONLY_WAIT', true); //ENABLE WAIT TIMES FOR BAD RATIO
define('ALLOWEXTERNAL', false); // Work in Progress  Enable Uploading of external tracked torrents
define('UPLOADERSONLY', false); //Limit uploading to uploader group only
define('INVITEONLY', false); //Only allow signups via invite
define('ENABLEINVITES', true); // Enable invites regardless of INVITEONLY setting
define('CONFIRMEMAIL', false); //Enable / Disable Signup confirmation email
define('ACONFIRM', false); //Enable / Disable ADMIN CONFIRM ACCOUNT SIGNUP
define('ANONYMOUSUPLOAD', false); //Enable / Disable anonymous uploads
define('PASSKEYURL', URLROOT . '/announce.php?passkey=%s'); // Announce URL to use for passkey
define('UPLOADSCRAPE', true); // Scrape external torrents on upload? If using mega-scrape.php you should disable this
define('FORUMS', true); // Enable / Disable Forums
define('FORUMS_GUESTREAD', false); // Allow / Disallow Guests To Read Forums
define('OLD_CENSOR', false); // Use the old change to word censor set to true otherwise use the new one.
define('MAXUSERS', 20000); // Max # of enabled accounts
define('MAXUSERSINVITE', 50000); // Max # of enabled accounts when inviting
define('CURRENCYSYMBOL', '£'); // Currency symbol (HTML allowed)
// seed bonus
define('BONUSPERTIME', 0.1); // per seeded torrent
define('ADDBONUS', 3600); // time to add bonus (1 hour)
// likes/thanks
define('FORCETHANKS', false); // force members to thank to download
define('ALLOWLIKES', true); // allow likes/unlikes
//AGENT BANS (MUST BE AGENT ID, USE FULL ID FOR SPECIFIC VERSIONS)
define('BANNED_AGENTS', '-AZ21, -BC, LIME');
// Image upload settings
define('IMAGEMAXFILESIZE', 524288); // Max uploaded image size in bytes (Default: 512 kB)
define('ALLOWEDIMAGETYPES', array(
    "image/gif" => ".gif",
    "image/pjpeg" => ".jpg",
    "image/jpeg" => ".jpg",
    "image/jpg" => ".jpg",
    "image/png" => ".png",
));

define('SITE_ONLINE', true); //Turn Site on/off
define('OFFLINEMSG', 'Site is down for a little while');
define('WELCOMEPM_ON', true); //Auto PM New members
define('WELCOMEPM_MSG', 'Thank you for registering at our tracker! Please remember to keep your ratio at 1.00 or greater');
define('UPLOADRULES', 'You should also include a .nfo file wherever possible<br />Try to make sure your torrents are well-seeded for at least 24 hours<br />Do not re-release material that is still active');
//Setup Site Blocks
define('LEFTNAV', true); //Left Column Enable/Disable
define('RIGHTNAV', true); // Right Column Enable/Disable
define('MIDDLENAV', true); // Middle Column Enable/Disable
define('SHOUTBOX', true); //enable/disable shoutbox
define('NEWSON', true);
define('DONATEON', true);
define('DISCLAIMERON', true);
//WAIT TIME VARS
define('CLASS_WAIT', 1); //Classes wait time applies to, comma seperated
define('GIGSA', 1); //Minimum gigs
define('RATIOA', 0.50); //Minimum ratio
define('A_WAIT', 24); //If neither are met, wait time in hours
define('GIGSB', 3); //Minimum gigs
define('RATIOB', 0.65); //Minimum ratio
define('B_WAIT', 12); //If neither are met, wait time in hours
define('GIGSC', 5); //Minimum gigs
define('RATIOC', 0.80); //Minimum ratio
define('C_WAIT', 6); //If neither are met, wait time in hours
define('GIGSD', 7); //Minimum gigs
define('RATIOD', 0.95); //Minimum ratio
define('D_WAIT', 2); //If neither are met, wait time in hours
//CLEANUP AND ANNOUNCE SETTINGS
define('PEERLIMIT', 10000); //LIMIT NUMBER OF PEERS GIVEN IN EACH ANNOUNCE
define('AUTOCLEANINTERVAL', 600); //Time between each auto cleanup (Seconds)
define('LOGCLEAN', 28 * 86400); // How often to delete old entries. (Default: 28 days)
define('ANNOUNCEINTERVAL', 900); //Announce Interval (Seconds)
define('SIGNUPTIMEOUT', 259200); //Time a user stays as pending before being deleted(Seconds)
define('MAXDEADTORRENTTIMEOUT', 21600); //Time until torrents that are dead are set invisible (Seconds)
//AUTO RATIO WARNING
define('RATIOWARNENABLE', true); //Enable/Disable auto ratio warning
define('RATIOWARNMINRATIO', 0.4); //Min Ratio
define('RATIOWARN_MINGIGS', 4); //Min GB Downloaded
define('RATIOWARN_DAYSTOWARN', 14); //Days to ban
// category = Category Image/Name, name = Torrent Name, dl = Download Link, uploader, comments = # of comments, completed = times completed, size, seeders, leechers, health = seeder/leecher ratio, external, wait = Wait Time (if enabled), rating = Torrent Rating, added = Date Added, nfo = link to nfo (if exists)
define('TORRENTTABLE_COLUMNS', 'category,name,dl,magnet,tube,imdb,uploader,comments,size,seeders,leechers,health,external,added');
// size, speed, added = Date Added, tracker, completed = times completed
define('TORRENTTABLE_EXPAND', '');

// Mail settings
// php to use PHP's built-in mail function. or pear to use http://pear.php.net/Mail
// MUST use pear for SMTP
define('mail_type', 'php');
define('mail_smtp_host', 'localhost'); // SMTP server hostname
define('mail_smtp_port', 25); // SMTP server port
define('mail_smtp_ssl', false); // true to use SSL
define('mail_smtp_auth', false); // true to use auth for SMTP
define('mail_smtp_user', ''); // SMTP username
define('mail_smtp_pass', ''); // SMTP password
// Set User Group
define('_USER', 1);
define('_POWERUSER', 2);
define('_VIP', 3);
define('_UPLOADER', 4);
define('_MODERATOR', 5);
define('_SUPERMODERATOR', 6);
define('_ADMINISTRATOR', 7);
// FORUM POST ON INDEX & Hidden Replys
define('FORUMONINDEX', true);
// Ip Check
define('IPCHECK', true);
define('ACCOUNTMAX', 1);
// IMDB (also remove from config[torrenttable])
define('IMDB1', true); // Set key from  in classes/TTIMDB
// Youtube (hidden either way just remove from config[torrenttable])
define('YOU_TUBE', true);
// Freeleech above xgb
define('FREELEECHGBON', true);
define('FREELEECHGB', 8589934592); // 8gb
// Request
define('REQUESTSON', true);
// Hide links in forum
define('HIDEBBCODE', true); // hide links
// Hit & Run mod
define('HNR_ON', false); // Not Finished only for testing
define('HNR_DEADLINE', 7 * 86400); // 7 days to hit the seed target
define('HNR_SEEDTIME', 172800); // target is to seed for 48 hours
define('HNR_WARN', 5); // 5 hit & runs then warned
define('HNR_STOP_DL', 5); // After 5 H & R stop downloading
define('HNR_BAN', 50); // After 50 H&R they are banned
define('HNR_DISABLED', TRUE);