<?php

//Access Security check
if (preg_match('/config.php/i',$_SERVER['PHP_SELF'])) {
	die;
}

$config = array();

//Change the settings below to match your MYSQL server connection settings
$config['mysql_host'] = "localhost";  //leave this as localhost if you are unsure
$config['mysql_user'] = "user";  //Username to connect
$config['mysql_pass'] = "pass"; //Password to connect
$config['mysql_db'] = "dbname";  //Database name

$config['ttversion'] = 'MVC/PDO';							//DONT CHANGE THIS!

// Main Site Settings
$config['SITENAME'] = 'TorrentTrader';					//Site Name
$config['SITEEMAIL'] = 'change@myemailsux.com';		//Emails will be sent from this address
$config['SITEURL'] = 'http://changemydomain.com';	//Main Site URL
$config['default_language'] = "english";						//DEFAULT LANGUAGE ID
$config['default_theme'] = "default";						//DEFAULT THEME ID
$config['CHARSET'] = "utf-8";						//Site Charset
$config['announce_list'] = "$config[SITEURL]/announce.php"; //seperate via comma
$config['MEMBERSONLY'] = true;							//MAKE MEMBERS SIGNUP
$config['MEMBERSONLY_WAIT'] = true;					//ENABLE WAIT TIMES FOR BAD RATIO
$config['ALLOWEXTERNAL'] = true;		//Enable Uploading of external tracked torrents
$config['UPLOADERSONLY'] = false;		//Limit uploading to uploader group only
$config['INVITEONLY'] = false;			//Only allow signups via invite
$config['ENABLEINVITES'] = true;		// Enable invites regardless of INVITEONLY setting
$config['CONFIRMEMAIL'] = false;		//Enable / Disable Signup confirmation email
$config['ACONFIRM'] = false;			//Enable / Disable ADMIN CONFIRM ACCOUNT SIGNUP
$config['ANONYMOUSUPLOAD'] = false;		//Enable / Disable anonymous uploads
$config['PASSKEYURL'] =  "$config[SITEURL]/announce.php?passkey=%s"; // Announce URL to use for passkey
$config['UPLOADSCRAPE'] = true; // Scrape external torrents on upload? If using mega-scrape.php you should disable this
$config['FORUMS'] = true; // Enable / Disable Forums
$config['FORUMS_GUESTREAD'] = false; // Allow / Disallow Guests To Read Forums
$config["OLD_CENSOR"] = false; // Use the old change to word censor set to true otherwise use the new one.   

$config['maxusers'] = 20000; // Max # of enabled accounts
$config['maxusers_invites'] = $config['maxusers'] + 5000; // Max # of enabled accounts when inviting

$config['currency_symbol'] = '$'; // Currency symbol (HTML allowed)

// sedd bonus
$config["bonuspertime"] = 0.1; // per seeded torrent
$config['add_bonus'] = 3600; // time to add bonus (1 hour)

// cleanup sessions
$config["session_time"] = 1800; // default above 30 mins

// likes
$config["forcethanks"] = true;     // force members to thank to download
$config['allowlikes'] = true;          // allow likes/unlikes

//AGENT BANS (MUST BE AGENT ID, USE FULL ID FOR SPECIFIC VERSIONS)
$config['BANNED_AGENTS'] = "-AZ21, -BC, LIME";

//PATHS, ENSURE THESE ARE CORRECT AND CHMOD TO 777 (ALSO ENSURE TORRENT_DIR/images is CHMOD 777)
$config['torrent_dir'] = getcwd().'/uploads';
$config['nfo_dir'] = getcwd().'/uploads';
$config['blocks_dir'] = getcwd().'/blocks';

// Image upload settings
$config['image_max_filesize'] = 524288; // Max uploaded image size in bytes (Default: 512 kB)
$config['allowed_image_types'] = array(
					// "mimetype" => ".ext",
					"image/gif" => ".gif",
					"image/pjpeg" => ".jpg",
					"image/jpeg" => ".jpg",
					"image/jpg" => ".jpg",
					"image/png" => ".png"
				);

$config['SITE_ONLINE'] = true;									//Turn Site on/off
$config['OFFLINEMSG'] = 'Site is down for a little while';	

$config['WELCOMEPMON'] = true;			//Auto PM New members
$config['WELCOMEPMMSG'] = 'Thank you for registering at our tracker! Please remember to keep your ratio at 1.00 or greater :)';

$config['SITENOTICEON'] = true;
$config['SITENOTICE'] = 'Welcome To TorrentTrader MVC/PDO';

$config['UPLOADRULES'] = 'You should also include a .nfo file wherever possible<br />Try to make sure your torrents are well-seeded for at least 24 hours<br />Do not re-release material that is still active';

//Setup Site Blocks
$config['LEFTNAV'] = true; //Left Column Enable/Disable
$config['RIGHTNAV'] = true; // Right Column Enable/Disable
$config['MIDDLENAV'] = true; // Middle Column Enable/Disable
$config['SHOUTBOX'] = true; //enable/disable shoutbox
$config['NEWSON'] = true;
$config['DONATEON'] = true;
$config['DISCLAIMERON'] = true;

//WAIT TIME VARS
$config['WAIT_CLASS'] = '1,2';		//Classes wait time applies to, comma seperated
$config['GIGSA'] = '1';			//Minimum gigs
$config['RATIOA'] = '0.50';		//Minimum ratio
$config['WAITA'] = '24';			//If neither are met, wait time in hours

$config['GIGSB'] = '3';			//Minimum gigs
$config['RATIOB'] = '0.65';		//Minimum ratio
$config['WAITB'] = '12';			//If neither are met, wait time in hours

$config['GIGSC'] = '5';			//Minimum gigs
$config['RATIOC'] = '0.80';		//Minimum ratio
$config['WAITC'] = '6';			//If neither are met, wait time in hours

$config['GIGSD'] = '7';			//Minimum gigs
$config['RATIOD'] = '0.95';		//Minimum ratio
$config['WAITD'] = '2';			//If neither are met, wait time in hours

//CLEANUP AND ANNOUNCE SETTINGS
$config['PEERLIMIT'] = '10000';			//LIMIT NUMBER OF PEERS GIVEN IN EACH ANNOUNCE
$config['autoclean_interval'] = '600';		//Time between each auto cleanup (Seconds)
$config['LOGCLEAN'] = 28 * 86400;			// How often to delete old entries. (Default: 28 days)
$config['announce_interval'] = '900';		//Announce Interval (Seconds)
$config['signup_timeout'] = '259200';		//Time a user stays as pending before being deleted(Seconds)
$config['maxsiteusers'] = '10000';			//Maximum site members
$config['max_dead_torrent_time'] = '21600';//Time until torrents that are dead are set invisible (Seconds)

//AUTO RATIO WARNING
$config["ratiowarn_enable"] = true; //Enable/Disable auto ratio warning
$config["ratiowarn_minratio"] = 0.4; //Min Ratio
$config["ratiowarn_mingigs"] = 4;  //Min GB Downloaded
$config["ratiowarn_daystowarn"] = 14; //Days to ban

// category = Category Image/Name, name = Torrent Name, dl = Download Link, uploader, comments = # of comments, completed = times completed, size, seeders, leechers, health = seeder/leecher ratio, external, wait = Wait Time (if enabled), rating = Torrent Rating, added = Date Added, nfo = link to nfo (if exists)
$config["torrenttable_columns"] = "category,name,dl,magnet,tube,uploader,comments,size,seeders,leechers,health,external";
// size, speed, added = Date Added, tracker, completed = times completed
$config["torrenttable_expand"] = "";

// Caching settings
$config["cache_type"] = "disk"; // disk = Save cache to disk, memcache = Use memcache, apc = Use APC, xcache = Use XCache
$config["cache_memcache_host"] = "localhost"; // Host memcache is running on
$config["cache_memcache_port"] = 11211; // Port memcache is running on
$config['cache_dir'] = getcwd().'/cache'; // Cache dir (only used if type is "disk"). Must be CHMOD 777


// Mail settings
// php to use PHP's built-in mail function. or pear to use http://pear.php.net/Mail
// MUST use pear for SMTP
$config["mail_type"] = "php";
$config["mail_smtp_host"] = "localhost"; // SMTP server hostname
$config["mail_smtp_port"] = "25"; // SMTP server port
$config["mail_smtp_ssl"] = false; // true to use SSL
$config["mail_smtp_auth"] = false; // true to use auth for SMTP
$config["mail_smtp_user"] = ""; // SMTP username
$config["mail_smtp_pass"] = ""; // SMTP password

// Set User Group
$config['User'] = "1";
$config['PowerUser'] = "2";
$config['VIP'] = "3";
$config['Uploader'] = "4";
$config['Moderator'] = "5";
$config['SuperModerator'] = "6";
$config['Administrator'] = "7";

// FORUM POST ON INDEX & Hidden Replys
$config['FORUMONINDEX'] = true;
$config['hideforum'] = 2; // Hide replys until after member replys

// Ip Check
$config["ipcheck"]  = true;
$config["accountmax"] = "1";
?>