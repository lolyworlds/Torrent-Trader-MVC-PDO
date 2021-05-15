<?php
// Error Reporting
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ignore_user_abort(true);
require '../app/config/config.php';
require '../app/libraries/Database.php';
require '../app/models/Logs.php';
require '../app/libraries/TimeDate.php';
// Register custom exception handler
include "../app/helpers/exception_helper.php";
set_exception_handler("handleUncaughtException");
// Used for testing
define('__INTERVAL', 60);
define('__TIMEOUT', 120);

//Do some input validation
function valdata($g, $fixed_size = false)
{
    if (!isset($_GET[$g])) {
        die(track('Invalid request, missing data'));
    }
    if (!is_string($_GET[$g])) {
        die(track('Invalid request, unknown data type'));
    }
    if ($fixed_size && strlen($_GET[$g]) != 20) {
        die(track('Invalid request, length on fixed argument not correct'));
    }
    if (strlen($_GET[$g]) > 80) { //128 chars should really be enough
        die(track('Request too long'));
    }
}

function isConnectable($ipadress, $port) {
    $fp = @fsockopen($ipadress, $port, $errno, $errstr, 0.1);
    if (!$fp) {
        return false;
    } else {
        fclose($fp);
        return true;
    }
}

function gmtime()
{
    return strtotime(get_date_time());
}

function get_date_time($timestamp = 0)
{
    if ($timestamp) {
        return date("Y-m-d H:i:s", $timestamp);
    } else {
        return gmdate("Y-m-d H:i:s");
    }
}


//Bencoding function, returns a bencoded dictionary (You may go ahead and enter custom keys in the dictionary)
function track($list, $c = 0, $i = 0)
{
    if (is_string($list)) { //Did we get a string? Return an error to the client
        return 'd14:failure reason' . strlen($list) . ':' . $list . 'e';
    }
    $p = ''; //Peer directory
    foreach ($list as $d) { //Runs for each client
        $pid = '';
        if (!isset($_GET['no_peer_id'])) { //Send out peer_ids in the reply
            $real_id = hex2bin($d[2]);
            $pid = '7:peer id' . strlen($real_id) . ':' . $real_id;
        }
        $p .= 'd2:ip' . strlen($d[0]) . ':' . $d[0] . $pid . '4:porti' . $d[1] . 'ee';
    }
    //Add some other paramters in the dictionary and merge with peer list
    $r = 'd8:intervali' . __INTERVAL . 'e12:min intervali' . __INTERVAL . 'e8:completei' . $c . 'e10:incompletei' . $i . 'e5:peersl' . $p . 'ee';
    return $r;
}


// Use the correct content-type
header("Content-type: Text/Plain");

// Make sure we have something to use as a passkey
$passkey = $_GET['passkey'] ?? '';
// Standard Information Fields
$info_hash = bin2hex($_GET['info_hash']);
$peerid = $_GET['peer_id'];
$port = intval($_GET['port']);
$downloaded = isset($_GET['uploaded']) && is_numeric($_GET['uploaded']) ? intval($_GET['uploaded']) : 0;
$uploaded = isset($_GET['uploaded']) && is_numeric($_GET['uploaded']) ? intval($_GET['uploaded']) : 0;
$left = isset($_GET['left']) && is_numeric($_GET['uploaded']) ? intval($_GET['left']) : 0;
$seeder = ($left == 0) ? "yes" : "no";
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) && is_string($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 80) : "N/A";
$ipadress = is_string($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : die("Weird ip adress");
$event = $_GET['event'] ?? '';
// Extra Information Fields
$no_peer_id = $_GET["no_peer_id"];
$compact = ($_GET["compact"] && $_GET["compact"] == 1) ? true : false;
$numwant = $_GET["numwant"] ? (int) $_GET["numwant"] : PEERLIMIT;

// Inputs that are needed, do not continue without these
valdata('peer_id', true);
valdata('port');
valdata('info_hash', true);
// Do we have a valid client port, support compact, is a browser?
if (!ctype_digit($_GET['port']) || $_GET['port'] < 1 || $_GET['port'] > 65535) {die(track('Invalid client port'));}
if (!$compact) {die(track("Your client doesn't support compact, please update your client"));}
//if (preg_match("/^Mozilla|^Opera|^Links|^Lynx/i", $user_agent)) { die("No");}
if (strlen($passkey) != 32) {die(track("Invalid passkey (" . strlen($passkey) . " - $passkey)"));}

// Get db connection
$dbh = new Database() or die(track('Database connection failed'));

// Client Ban
$stmt = $dbh->prepare("SELECT agent_name FROM agents");
$agentarray = $stmt->fetchAll(PDO::FETCH_COLUMN);
$useragent = substr($peerid, 0, 8);
foreach ($agentarray as $bannedclient) {
    if (@strpos($useragent, $bannedclient) !== false) {
        die(track('Client is banned'));
    }
}

// Get User (select user/group vars here)
$sql = "SELECT u.id, u.class, u.uploaded, u.downloaded, u.ip, u.passkey, g.can_download
	    FROM users u
	    INNER JOIN groups g
	    ON u.class = g.group_id
	    WHERE u.passkey=? AND u.enabled = ? AND u.status = ? LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->execute([$passkey, 'yes', 'confirmed']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get Torrent (select torrent vars here)  note = torrent['numpeers']
$sql = "SELECT id, info_hash, banned, freeleech, seeders + leechers
        AS numpeers, UNIX_TIMESTAMP(added)
	    AS ts, seeders, leechers, times_completed
	    FROM torrents WHERE info_hash=?";
$stmt = $dbh->prepare($sql);
$stmt->execute([$info_hash]);
$torrent = $stmt->fetch(PDO::FETCH_ASSOC);

// Do some more checks
if (!$user) {die(track("Cannot locate a user with that passkey!"));}
if ($user["can_download"] == "no") {die(track("You do not have permission to download."));}
if (!$torrent) {die(track("Torrent not found on this tracker - hash = " . $info_hash));}
if ($torrent["banned"] == 'yes') {die(track("Torrent has been banned - hash = " . $info_hash));}
$sql = $dbh->run("SELECT COUNT(*)
                  FROM peers
                  WHERE torrent=? AND passkey=?", [$torrent['id'], $passkey]);
$valid = $sql->rowCount();
if ($valid >= 1 && $seeder == 'no') {
    die(track("Connection limit exceeded! You may only leech from one location at a time."));
}
 $connect = isConnectable($ipadress, $port);
 if ($connect) {
    $connectable = 'yes';
 }else{
    $connectable = 'no';
 }

// Prepare Peer Array
$peer_params = [
    'torrentid' => $torrent['id'],
    'peerid' => $peerid,
    'ipadress' => $ipadress,
    'port' => $port,
    'uploaded' => $uploaded,
    'downloaded' => $downloaded,
    'left' => $left,
    'seeder' => $seeder,
    'started' => TimeDate::get_date_time(),
    'lastaction' => TimeDate::get_date_time(),
    'connectable' => $connectable,
    'useragent' => $user_agent,
    'userid' => $user['id'],
    'passkey' => $passkey];

// Now Insert Peer Array or Update on Duplicate
$insert_peer = $dbh->prepare('INSERT INTO `peers` (`torrent`, `peer_id`, `ip`, `port`, `uploaded`, `downloaded`, `to_go`, `seeder`, `started`, `last_action`, `connectable`, `client`, `userid`, `passkey`) '
    . "VALUES (:torrentid, :peerid, :ipadress, :port, :uploaded, :downloaded, :left, :seeder, :started, :lastaction, :connectable, :useragent, :userid, :passkey) "
    . 'ON DUPLICATE KEY UPDATE `peer_id`=VALUES(`peer_id`), `ip` = VALUES(`ip`), `passkey`=VALUES(`passkey`), `port` = VALUES(`port`), `uploaded` = VALUES(`uploaded`), `downloaded` = VALUES(`downloaded`), `to_go` = VALUES(`to_go`), `seeder` = VALUES(`seeder`), `started` = VALUES(`started`), `last_action` = VALUES(`last_action`), `connectable` = VALUES(`connectable`), `client` = VALUES(`client`), `userid` = VALUES(`userid`), `passkey` = VALUES(`passkey`),`id` = LAST_INSERT_ID(`peers`.`id`)');
$insert_peer->execute($peer_params);
$pk_peer = $dbh->lastInsertId();

// Select ip, port, peer_id to send back to client
$sql = "SELECT seeder, UNIX_TIMESTAMP(last_action) AS ez, peer_id, INET_NTOA(ip), port, uploaded, downloaded, userid, passkey FROM peers WHERE torrent=? AND passkey=? LIMIT $numwant";
$select_peer_torrent = $dbh->prepare($sql);
$select_peer_torrent->execute([$torrent['id'], $passkey]);
$reply = array(); //To be encoded and sent to the client
while ($r = $select_peer_torrent->fetch(PDO::FETCH_ASSOC)) { //Runs for every client with the same infohash
    $reply[] = array($r['ip'], $r['port'], $r['peer_id']);
    /*
    if ($r['peer_id'] === $peerid) {
        $currentpeer = $r; // the current connection = already in table(self)
    }
    */
}

// The Events
if ($event == 'started') { // when a connection is made or restarted after turn off
    if (($seeder == 'yes' && $torrent['freeleech'] == 0)) {
        $dbh->run("INSERT INTO `snatched` (`uid`, `tid`, `stime`, `utime`) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE `utime` = ?", [$user['id'], $torrent['id'], gmtime(), gmtime(), gmtime()]);
    }
} elseif ($event == 'completed') { // when a download finishes in client
    $updateset[] = "times_completed = $torrent[times_completed] + 1";
    $dbh->run("INSERT INTO completed (userid, torrentid, date) VALUES (?,?,?)", [$user['id'], $torrent['id'], get_date_time()]);
    $dbh->run("UPDATE LOW_PRIORITY `snatched` SET `completed` = '1' WHERE `tid` = '$torrent[id]' AND `uid` = '$user[id]' AND `utime` = '" . gmtime() . "'");
} elseif ($event == 'stopped') { // when client is stopped correctly
    $dbh->run("DELETE FROM peers WHERE torrent = ? AND peer_id = ?", [$torrent['id'], $peer['id']]);
}

// update user
$elapsed = ($seeder == 'yes') ? ANNOUNCEINTERVAL - floor(($self['ez'] - time()) / 60) : 0; //
if ($torrent["freeleech"] == 1) {
    $dbh->run("UPDATE users SET uploaded =? WHERE id=?", [$user['uploaded'] + $downloaded, $user['id']]) or die(track("Tracker error: Unable to update stats"));
} else {
    $dbh->run("UPDATE users SET uploaded =?, downloaded =? WHERE id=?", [$user['uploaded '] + $uploaded, $user['downloaded'] + $downloaded, $user['id']]) or die(track("Tracker error: Unable to update stats"));
    // snatch
    $dbh->run("UPDATE LOW_PRIORITY `snatched` SET `uload` = `uload` + '$uploaded', `dload` = `dload` + '$downloaded', `utime` = '" . gmtime() . "', `ltime` = `ltime` + '$elapsed' WHERE `tid` = '$torrent[id]' AND `uid` = '$user[id]'");
}


// Count the peers from to_go for seeders/leechers
$select_peers = $dbh->prepare('SELECT IFNULL(SUM(peers.to_go > 0), 0) AS leech, IFNULL(SUM(peers.to_go = 0), 0) AS seed '
    . 'FROM peers '
    . "WHERE peers.torrent = ? "
    . 'AND peers.last_action >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL ' . (__INTERVAL + __INTERVAL) . ' SECOND) '
    . 'GROUP BY `peers`.`torrent`');
$select_peers->execute([$torrent['id']]);
$seeders = 0;
$leechers = 0;
if ($r = $select_peers->fetch(PDO::FETCH_NUM)) {
    $seeders = $r[1];
    $leechers = $r[0];
}

// Update Torrent - 
if ($seeder == "yes") {
    if ($torrent["banned"] != "yes") { // DONT MAKE BANNED ONES VISIBLE {
        $updateset[] = "visible = 'yes'";
    }
    $updateset[] = "seeders = $seeders";
    $updateset[] = "leechers = $leechers";
    $updateset[] = "last_action = '" . get_date_time() . "'";
}
if (count($updateset)) {
    $dbh->run("UPDATE torrents SET " . join(",", $updateset) . " WHERE id=$torrent[id]") or die(track("Tracker error: Unable to update torrent"));
}
// Print out response
die(track($reply, $seeders[0], $leechers[0]));