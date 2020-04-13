<?php
// Invite update function (Author: TorrentialStorm)
function autoinvites($interval, $minlimit, $maxlimit, $minratio, $invites, $maxinvites) {
	$time = gmtime() - ($interval*86400);
	$minlimit = $minlimit*1024*1024*1024;
	$maxlimit = $maxlimit*1024*1024*1024;
	$res = DB::run("SELECT id, username, class, invites FROM users WHERE enabled = 'yes' AND status = 'confirmed' AND downloaded >= $minlimit AND downloaded < $maxlimit AND uploaded / downloaded >= $minratio AND warned = 'no' AND UNIX_TIMESTAMP(invitedate) <= $time");
	if ($res->rowCount() > 0) {
		while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
			$maxninvites = $maxinvites[$arr['class']];
			if ($arr['invites'] >= $maxninvites)
				continue;
			if (($maxninvites-$arr['invites']) < $invites)
				$invites = $maxninvites - $arr['invites'];

			DB::run("UPDATE users SET invites = invites+$invites, invitedate = NOW() WHERE id=$arr[id]");
			write_log("Gave $invites invites to '$arr[username]' - Class: ".get_user_class_name($arr['class'])."");
		}
	}
}

function do_cleanup() {
	global $site_config;
 
	//LOCAL TORRENTS - GET PEERS DATA AND UPDATE BROWSE STATS
	//DELETE OLD NON-ACTIVE PEERS
    $deadtime = get_date_time(gmtime() - $site_config['announce_interval']);
    DB::run("DELETE FROM peers WHERE last_action < '$deadtime'");
    
	$torrents = array();
	$res = DB::run("SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder");
	while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
		if ($row["seeder"] == "yes")
			$key = "seeders";
		else
			$key = "leechers";
		$torrents[$row["torrent"]][$key] = $row["c"];
	}

	$res = DB::run("SELECT torrent, COUNT(torrent) as c FROM comments WHERE torrent > 0 GROUP BY torrent");
	while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
		$torrents[$row["torrent"]]["comments"] = $row["c"];
	}

	$fields = explode(":", "comments:leechers:seeders");
	$res = DB::run("SELECT id, external, seeders, leechers, comments FROM torrents WHERE banned = 'no'");
	while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
		$id = $row["id"];
		$torr = $torrents[$id];
		foreach ($fields as $field) {
			if (!isset($torr[$field]))
				$torr[$field] = 0;
		}
		$update = array();
		foreach ($fields as $field) {
			if ($row["external"] == "no" || $field == "comments") {
				if ($torr[$field] != $row[$field])
					$update[] = "$field = " . $torr[$field];
			}
		}
		if (count($update))
            DB::run("UPDATE torrents SET " . implode(",", $update) . " WHERE id = $id");
	}


//LOCAL TORRENTS - MAKE NON-ACTIVE/OLD TORRENTS INVISIBLE
$deadtime = gmtime() - $site_config["max_dead_torrent_time"];
DB::run("UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < FROM_UNIXTIME($deadtime) AND seeders = '0' AND leechers = '0' AND external !='yes'");


//DELETE PENDING USER ACCOUNTS OVER TIMOUT AGE
$deadtime = gmtime() - $site_config["signup_timeout"];
    DB::run("DELETE FROM users WHERE status = 'pending' AND added < FROM_UNIXTIME($deadtime)");

// DELETE OLD LOG ENTRIES
$ts = gmtime() - $site_config["LOGCLEAN"];
    DB::run("DELETE FROM log WHERE added < FROM_UNIXTIME($ts)");

//LEECHWARN USERS WITH LOW RATIO

if ($site_config["ratiowarn_enable"]){
	$minratio = $site_config["ratiowarn_minratio"];
	$downloaded = $site_config["ratiowarn_mingigs"]*1024*1024*1024;
	$length = $site_config["ratiowarn_daystowarn"];

	//ADD WARNING
	$res = DB::run("SELECT id,username FROM users WHERE class = 1 AND warned = 'no' AND enabled='yes' AND uploaded / downloaded < $minratio AND downloaded >= $downloaded");

	if ($res->rowCount() > 0){
		$timenow = get_date_time();
		$reason = "You have been warned because of having low ratio. You need to get a ".$minratio." before next ".$length." days or your account may be banned.";

		$expiretime = gmdate("Y-m-d H:i:s", gmtime() + (86400 * $length));

		while ($arr = $res->fetch(PDO::FETCH_ASSOC)){
			DB::run("INSERT INTO warnings (userid, reason, added, expiry, warnedby, type) VALUES ('".$arr["id"]."','".$reason."','".$timenow."','".$expiretime."','0','Poor Ratio')");
			DB::run("UPDATE users SET warned='yes' WHERE id='".$arr["id"]."'");
			DB::run("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ('0', '".$arr["id"]."', '".$timenow."', '".$reason."', '0')");
			write_log("Auto Leech warning has been <b>added</b> for: <a href='account-details.php?id=".$arr["id"]."'>".class_user($arr["username"])."</a>");
		}
	}

    //REMOVE WARNING
	$res1 = DB::run("SELECT users.id, users.username FROM users INNER JOIN warnings ON users.id=warnings.userid WHERE type='Poor Ratio' AND active = 'yes' AND warned = 'yes'  AND enabled='yes' AND uploaded / downloaded >= $minratio AND downloaded >= $downloaded");
	if ($res1->rowCount() > 0){
		$timenow = get_date_time();
		$reason = "Your warning of low ratio has been removed. We highly recommend you to keep a your ratio up to not be warned again.\n";

		while ($arr1 = $res1->fetch(PDO::FETCH_ASSOC)){
			write_log("Auto Leech warning has been removed for: <a href='account-details.php?id=".$arr1["id"]."'>".class_user($arr1["username"])."</a>"); 
				
			DB::run("UPDATE users SET warned = 'no' WHERE id = '".$arr1["id"]."'");
			DB::run("UPDATE warnings SET expiry = '$timenow', active = 'no' WHERE userid = $arr1[id]");
			DB::run("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ('0', '".$arr1["id"]."', '".$timenow."', '".$reason."', '0')");
		}
	}

	//BAN WARNED USERS
	$res = DB::run("SELECT users.id, users.username, UNIX_TIMESTAMP(warnings.expiry) AS expiry FROM users INNER JOIN warnings ON users.id=warnings.userid WHERE type='Poor Ratio' AND active = 'yes' AND class = 1 AND enabled='yes' AND warned = 'yes' AND uploaded / downloaded < $minratio AND downloaded >= $downloaded");

	if ($res->rowCount() > 0){
		$timenow = get_date_time();
		$expires = (86400 * $length);
		while ($arr = $res->fetch(PDO::FETCH_ASSOC)){
			if (gmtime() - $arr["expiry"] >= 0) {
                DB::run("UPDATE users SET enabled='no', warned='no' WHERE id='".$arr["id"]."'");
				write_log("User <a href='account-details.php?id=".$arr["id"]."'>".class_user($arr["username"])."</a> has been banned (Auto Leech warning).");
			}
		}
	}
	
}//check if warning system is on
// REMOVE WARNINGS
$res = DB::run("SELECT users.id, users.username, warnings.expiry FROM users INNER JOIN warnings ON users.id=warnings.userid WHERE type != 'Poor Ratio' AND warned = 'yes'  AND enabled='yes' AND warnings.active = 'yes' AND warnings.expiry < '".get_date_time()."'");
while ($arr1 = $res->fetch(PDO::FETCH_ASSOC)){
	DB::run("UPDATE users SET warned = 'no' WHERE id = $arr1[id]");
	DB::run("UPDATE warnings SET active = 'no' WHERE userid = $arr1[id] AND expiry < '".get_date_time()."'");
	write_log("Removed warning for $arr1[username]. Expiry: $arr1[expiry]");
}
// WARN USERS THAT STILL HAVE ACTIVE WARNINGS
    DB::run("UPDATE users SET warned = 'yes' WHERE warned = 'no' AND id IN (SELECT userid FROM warnings WHERE active = 'yes')");
//END//


	// START INVITES UPDATE
	// SET INVITE AMOUNTS ACCORDING TO RATIO/GIGS ETC
	// autoinvites(interval to give invites (days), min downloaded GB, max downloaded GB, min ratio, invites to give, max invites allowed (array))
	// $maxinvites[CLASS ID] = max # of invites;
	$maxinvites[1] = 5;   // User
	$maxinvites[2] = 10;  // Power User
	$maxinvites[3] = 20;  // VIP
	$maxinvites[4] = 25;  // Uploader
	$maxinvites[5] = 100; // Moderator
	$maxinvites[6] = 100; // Super Moderator
	$maxinvites[7] = 400; // Administrator

	// Give 1 invite every 21 days to users with > 1GB downloaded AND < 4GB downloaded AND ratio > 0.50
	autoinvites(21, 1, 4, 0.50, 1, $maxinvites);
	autoinvites(14, 1, 4, 0.90, 2, $maxinvites);
	autoinvites(14, 4, 7, 0.95, 2, $maxinvites);

	$maxinvites[1] = 7; // User
	autoinvites(14, 7, 10, 1.00, 3, $maxinvites);

	$maxinvites[1] = 10; // User
	autoinvites(14, 10, 100000, 1.05, 4, $maxinvites);
	//END INVITES

    //ORIGINAL OPTIMIZE TABLES
    
//    $res = DB::run("SHOW TABLES");
//   
//    while ( $table = $res->fetch() )
//    {
//        SQL_Query_exec("OPTIMIZE TABLE `$table[0]`;");
//    }

	//NEW OPTIMIZE TABLES
	    $res = DB::run("SHOW TABLES");
   
    while ( $table = $res->fetch(PDO::FETCH_LAZY) )
    {
            // Get rid of overhead.
        DB::run("REPAIR TABLE `$table[0]`;");
            // Analyze table for faster indexing.
        DB::run("ANALYZE TABLE `$table[0]`;");
            // Optimize table to minimize thrashing.
        DB::run("OPTIMIZE TABLE `$table[0]`;");
    }
}

?>