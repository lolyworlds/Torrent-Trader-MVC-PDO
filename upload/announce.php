<?php
// Please Note: Languages should not be implemented here...
             
error_reporting(E_ALL ^ E_NOTICE);

require_once("config/config.php");
require_once("classes/DB.php"); // Lets Use Static For Now

$MEMBERSONLY = $config["MEMBERSONLY"];
$MEMBERSONLY_WAIT = $config["MEMBERSONLY_WAIT"];

$_GET = array_map_recursive("unesc", $_GET);

//START FUNCTIONS
function array_map_recursive ($callback, $array) {
	$ret = array();

	if (!is_array($array))
		return $callback($array);

	foreach ($array as $key => $val) {
		$ret[$key] = array_map_recursive($callback, $val);
	}
	return $ret;
}


function unesc($x) {
		return stripslashes($x);
	return $x;
}

function is_valid_id($id) {
	return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

function sqlesc($x) {
    return "'".$x."'";
}

function err($msg) {
   return benc_resp_raw("d".benc_str("failure reason").benc_str($msg)."e");
}

function benc_str($s) {
	return strlen($s) . ":$s";
}

function benc_int($i) {
	return "i" . $i . "e";
}

function benc_resp_raw($x) {
	header("Content-Type: text/plain");
	header("Pragma: no-cache");

	if (extension_loaded('zlib') && !ini_get('zlib.output_compression') && $_SERVER["HTTP_ACCEPT_ENCODING"] == "gzip") {
		header("Content-Encoding: gzip");
		echo gzencode($x, 9, FORCE_GZIP);
	} else
		print($x);

	exit();
}

function gmtime() {
	return strtotime(get_date_time());
}

function get_date_time($timestamp = 0) {
	if ($timestamp)
		return date("Y-m-d H:i:s", $timestamp);
	else
	return gmdate("Y-m-d H:i:s");
}

function portblacklisted($port) {
	// direct connect
	if ($port >= 411 && $port <= 413) return true;

	// kazaa
	if ($port == 1214) return true;

	// gnutella
	if ($port >= 6346 && $port <= 6347) return true;

	// emule
	if ($port == 4662) return true;

	// winmx
	if ($port == 6699) return true;

	return false;
}

//////////////////////// NOW WE DO THE ANNOUNCE CODE ////////////////////////
                                               
// BLOCK ACCESS WITH WEB BROWSERS
$agent = $_SERVER["HTTP_USER_AGENT"];
if (preg_match("/^Mozilla|^Opera|^Links|^Lynx/i", $agent))
	die("No");

//GET DETAILS OF PEERS ANNOUNCE
foreach (array("passkey","info_hash","peer_id","ip","event") as $x) {
        $GLOBALS[$x] = $_GET[$x];
}

foreach (array("port","downloaded","uploaded","left") as $x)
    $GLOBALS[$x] = 0 + $_GET[$x];

if (strpos($passkey, "?")) {
    $tmp = substr($passkey, strpos($passkey, "?"));
    $passkey = substr($passkey, 0, strpos($passkey, "?"));
    $tmpname = substr($tmp, 1, strpos($tmp, "=")-1);
    $tmpvalue = substr($tmp, strpos($tmp, "=")+1);
    $GLOBALS[$tmpname] = $tmpvalue;
}

foreach (array("passkey","info_hash","peer_id","port","downloaded","uploaded","left") as $x)
	if (!isset($$x))
		err("Missing key: $x");

if (strlen($peer_id) != 20)
	err("Invalid peer_id");

$no_peer_id = (int) $_GET["no_peer_id"];

    if (strlen($GLOBALS['info_hash']) == 20)
        $GLOBALS['info_hash'] = bin2hex($GLOBALS['info_hash']);
    else if (strlen($GLOBALS['info_hash']) != 40)
        err("Invalid info hash value.");
    $GLOBALS['info_hash'] = strtolower($GLOBALS['info_hash']);

	if ($MEMBERSONLY){
		if (strlen($passkey) != 32)
			err("Invalid passkey (" . strlen($passkey) . " - $passkey)");
	}

$ip = $_SERVER["REMOTE_ADDR"];

foreach(array("num want", "numwant", "num_want") as $k)
{
    if (isset($_GET[$k]))
    {
        $rsize = (int) $_GET[$k];
        break;
    }
}

//PORT CHECK
if (!$port || $port > 0xffff)
    err("invalid port");

//TRACKER EVENT CHECK
if (!isset($event))
    $event = "";

$seeder = ($left == 0) ? "yes" : "no";

//Agent Ban Moved To DB
$agentarray = DB::run("SELECT agent_name FROM agents")->fetchAll(PDO::FETCH_COLUMN);
$useragent = substr($peer_id, 0, 8);
foreach($agentarray as $bannedclient)
if (@strpos($useragent, $bannedclient) !== false)
	err("Client is banned");
//End Agent Bans

if (portblacklisted($port))
	err("Port $port is blacklisted.");

$userfields = "u.id, u.class, u.uploaded, u.downloaded, u.ip, u.passkey, g.can_download"; //user details to get
             
$peerfields = "seeder, UNIX_TIMESTAMP(last_action) AS ez, peer_id, ip, port, uploaded, downloaded, userid, passkey"; //peers details to get

$torrentfields = "id, info_hash, banned, freeleech, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts, seeders, leechers, times_completed"; //torrent details to get

$userid = 0;
if ($MEMBERSONLY){
	//check passkey is valid, and get users details
	$res = DB::run("SELECT $userfields FROM users u INNER JOIN groups g ON u.class = g.group_id WHERE u.passkey=".sqlesc($passkey)." AND u.enabled = 'yes' AND u.status = 'confirmed' LIMIT 1") or err("Cannot Get User Details");
	$user = $res->fetch(PDO::FETCH_ASSOC);
	if (!$user)
		err("Cannot locate a user with that passkey!");
    if ($user["can_download"] == "no")
        err("You do not have permission to download.");
	$userid = $user["id"]; //etc
}


//check torrent is valid and get torrent fields
$res = DB::run("SELECT $torrentfields FROM torrents WHERE info_hash=".sqlesc($info_hash)) or err("Cannot Get Torrent Details");
$torrent = $res->fetch(PDO::FETCH_ASSOC);

if (!$torrent)
    err("Torrent not found on this tracker - hash = " . $info_hash);
if ($torrent["banned"]=='yes')
    err("Torrent has been banned - hash = " . $info_hash);
$torrentid = $torrent["id"];


//Now get data from peers table
$peerlimit = 50;
$numpeers = $torrent["numpeers"];
if ($numpeers > $peerlimit){
    $limit = "ORDER BY RAND() LIMIT $peerlimit";
}else{
    $limit = "";
}
$res = DB::run("SELECT $peerfields FROM peers WHERE torrent = $torrentid $limit") or err("Error Selecting Peers");

//DO SOME BENC STUFF TO THE PEERS CONNECTION
$resp = "d8:completei$torrent[seeders]e10:downloadedi$torrent[times_completed]e10:incompletei$torrent[leechers]e";
$resp .= benc_str("interval") . "i" . $config['announce_interval'] . "e" . benc_str("min interval") . "i300e" . benc_str("peers");
unset($self);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
	if ($row["peer_id"] === $peer_id) {
		$self = $row;
		continue;
	}

	$peers .= "d" . benc_str("ip") . benc_str($row["ip"]);
        if (!$no_peer_id)
		$peers .= benc_str("peer id") . benc_str($row["peer_id"]);
        $peers .= benc_str("port") . "i" . $row["port"] . "ee";
}
$resp .= "l{$peers}e";
$resp .= "ee";

$selfwhere = "torrent = $torrentid AND peer_id = ".sqlesc($peer_id);



// FILL $SELF WITH DETAILS FROM PEERS TABLE (CONNECTING PEERS DETAILS)
if (!isset($self)){

	if ($MEMBERSONLY){ // todo slots mod
		$countslot = DB::run("SELECT DISTINCT torrent FROM peers WHERE userid =?  AND seeder=?", [$_SESSION['id'], 'no']);
		$slot = $countslot->rowCount();
		$maxslot = DB::run("SELECT `maxslots` FROM `groups` WHERE `group_id` = $user[class]")->fetchColumn();
		if ($slot >= $maxslot)
		err("Maximum Slot exceeded! You may only download $maxslot torrent at a time.");
	}

	//check passkey isnt leaked
	if ($MEMBERSONLY) {
		$valid = DB::run("SELECT COUNT(*) FROM peers WHERE torrent=$torrentid AND passkey=" . sqlesc($passkey))->fetch();

		if ($valid[0] >= 1 && $seeder == 'no')
			err("Connection limit exceeded! You may only leech from one location at a time.");

		if ($valid[0] >= 3 && $seeder == 'yes')
			err("Connection limit exceeded!");
	}

	$res = DB::run("SELECT $peerfields FROM peers WHERE $selfwhere");
	$row = $res->fetch(PDO::FETCH_ASSOC);
	if ($row){
	        $self = $row;
	}
}
// END $SELF FILL


if (!isset($self)){ //IF PEER IS NOT IN PEERS TABLE DO THE WAIT TIME CHECK
	if ($MEMBERSONLY_WAIT && $MEMBERSONLY){
		//wait time check
		if($left > 0 && in_array($user["class"], explode(",",$config["WAIT_CLASS"]))){ //check only leechers and lowest user class
			$gigs = $user["uploaded"] / (1024*1024*1024);
			$elapsed = floor((gmtime() - $torrent["ts"]) / 3600); 
			$ratio = (($user["downloaded"] > 0) ? ($user["uploaded"] / $user["downloaded"]) : 1); 
			if ($ratio == 0 && $gigs == 0) $wait = $config["WAITA"];
			elseif ($ratio < $config["RATIOA"] || $gigs < $config["GIGSA"]) $wait = $config["WAITA"];
			elseif ($ratio < $config["RATIOB"] || $gigs < $config["GIGSB"]) $wait = $config["WAITB"];
			elseif ($ratio < $config["RATIOC"] || $gigs < $config["GIGSC"]) $wait = $config["WAITC"];
			elseif ($ratio < $config["RATIOD"] || $gigs < $config["GIGSD"]) $wait = $config["WAITD"];
			else $wait = 0;
		if ($elapsed < $wait)
			err("Wait Time (" . ($wait - $elapsed) . " hours) - Visit ".$config["SITEURL"]." for more info");
		}
	}
	$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
	if (!$sockres)
		$connectable = "no";
	else
		$connectable = "yes";
	@fclose($sockres);

}else{
	// snatch
	$elapsed = ($self['seeder'] == 'yes') ? $config['announce_interval'] - floor(($self['ez'] - time()) / 60) : 0; //
    $upthis = max(0, $uploaded - $self["uploaded"]);
    $downthis = max(0, $downloaded - $self["downloaded"]);

    if (($upthis > 0 || $downthis > 0 || $elapsed > 0) && is_valid_id($userid)){ // LIVE STATS!)
		if ($torrent["freeleech"] == 1){
            DB::run("UPDATE users SET uploaded = uploaded + $upthis WHERE id=$userid") or err("Tracker error: Unable to update stats");
		}else{
            DB::run("UPDATE users SET uploaded = uploaded + $upthis, downloaded = downloaded + $downthis WHERE id=$userid") or err("Tracker error: Unable to update stats");
			// snatch
			DB::run("UPDATE LOW_PRIORITY `snatched` SET `uload` = `uload` + '$upthis', `dload` = `dload` + '$downthis', `utime` = '".gmtime()."', `ltime` = `ltime` + '$elapsed' WHERE `tid` = '$torrentid' AND `uid` = '$userid'");
		}
    }
}//END WAIT AND STATS UPDATE

$updateset = array();

////////////////// NOW WE DO THE TRACKER EVENT UPDATES ///////////////////

if ($event == "stopped") { // UPDATE "STOPPED" EVENT
/* todo
// SNATCHED
$res_se = DB::run("SELECT uid, utime, tid FROM snatched WHERE tid = $torrentid AND uid = $userid");
while ($row_se = $res_se->fetch(PDO::FETCH_ASSOC)) {
	DB::run("UPDATE snatched SET utime  =? WHERE completed =? AND tid =? AND uid =?", [$dt, 'yes', $row_se['tid'], $row_se['uid']]);
}
*/

        $sql = DB::run("DELETE FROM peers WHERE $selfwhere");
        if ($sql){
            if ($self["seeder"] == "yes")
                $updateset[] = "seeders = seeders - 1";
            else
                $updateset[] = "leechers = leechers - 1";
        }
}

if ($event == "completed") { // UPDATE "COMPLETED" EVENT    
    $updateset[] = "times_completed = times_completed + 1";

	if ($MEMBERSONLY)
        DB::run("INSERT INTO completed (userid, torrentid, date) VALUES ($userid, $torrentid, '".get_date_time()."')");
	    // snatch
		DB::run("UPDATE LOW_PRIORITY `snatched` SET `completed` = '1' WHERE `tid` = '$torrentid' AND `uid` = '$userid' AND `utime` = '" . gmtime() . "'");
}//END COMPLETED

if (isset($self)){// NO EVENT? THEN WE MUST BE A NEW PEER OR ARE NOW SEEDING A COMPLETED TORRENT

    $peerupd = DB::run("UPDATE peers SET ip = " . sqlesc($ip) . ", passkey = " . sqlesc($passkey) . ", port = $port, uploaded = $uploaded, downloaded = $downloaded, to_go = $left, last_action = '".get_date_time()."', client = " . sqlesc($agent) . ", seeder = '$seeder' WHERE $selfwhere");

    if ($peerupd && $self["seeder"] != $seeder){
        if ($seeder == "yes"){
            $updateset[] = "seeders = seeders + 1";
            $updateset[] = "leechers = leechers - 1";
        } else {
            $updateset[] = "seeders = seeders - 1";
            $updateset[] = "leechers = leechers + 1";
        }
    }

} else {

    $ret = DB::run("INSERT INTO peers (connectable, torrent, peer_id, ip, passkey, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, client) VALUES ('$connectable', $torrentid, " . sqlesc($peer_id) . ", " . sqlesc($ip) . ", " . sqlesc($passkey) . ", $port, $uploaded, $downloaded, $left, '".get_date_time()."', '".get_date_time()."', '$seeder', '$userid', " . sqlesc($agent) . ")");
    
	// snatch
	if ( ($MEMBERSONLY) && (($seeder == 'no' && $torrent['freeleech'] == 0)) ) {
    DB::run("INSERT INTO `snatched` (`uid`, `tid`, `stime`, `utime`) VALUES ('$userid', '$torrentid', '".gmtime()."', '".gmtime()."') ON DUPLICATE KEY UPDATE `utime` = '" . gmtime() . "'");
    }
	
    if ($ret){
        if ($seeder == "yes")
            $updateset[] = "seeders = seeders + 1";
        else
            $updateset[] = "leechers = leechers + 1";
    }
}

//////////////////    END TRACKER EVENT UPDATES ///////////////////

// SEEDED, LETS MAKE IT VISIBLE THEN
if ($seeder == "yes") {
    if ($torrent["banned"] != "yes") // DONT MAKE BANNED ONES VISIBLE
        $updateset[] = "visible = 'yes'";
    $updateset[] = "last_action = '".get_date_time()."'";
}

// NOW WE UPDATE THE TORRENT AS PER ABOVE
if (count($updateset))
    DB::run("UPDATE torrents SET " . join(",", $updateset) . " WHERE id=$torrentid") or err("Tracker error: Unable to update torrent");

// NOW BENC THE DATA AND SEND TO CLIENT???
benc_resp_raw($resp);
//mysqli_close($GLOBALS["DBconnector"]);
?>