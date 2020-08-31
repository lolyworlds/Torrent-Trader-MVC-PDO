<?php
// Automatic System Update Function
function autoclean()
{
    global $config, $pdo;
    require_once "cleanup_helper.php";

    $now = gmtime();
    $docleanup = 0;

    $res = $pdo->run("SELECT last_time FROM tasks WHERE task='cleanup'");
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $pdo->run("INSERT INTO tasks (task, last_time) VALUES ('cleanup',$now)");
        return;
    }

    $ts = $row['last_time']; // $row['0'] returned null now int string
    if ($ts + $config["autoclean_interval"] > $now) {
        return;
    }

    $planned_clean = $pdo->run("UPDATE tasks SET last_time=? WHERE task=? AND last_time =?", [$now, 'cleanup', $ts]);
   if (!$planned_clean) {
        return;
    }

    do_cleanup();
}

// Invite update function (Author: TorrentialStorm)
function autoinvites($interval, $minlimit, $maxlimit, $minratio, $invites, $maxinvites)
{
    global $pdo;
    $time = gmtime() - ($interval * 86400);
    $minlimit = $minlimit * 1024 * 1024 * 1024;
    $maxlimit = $maxlimit * 1024 * 1024 * 1024;
    $res = $pdo->run("SELECT id, username, class, invites FROM users WHERE enabled = 'yes' AND status = 'confirmed' AND downloaded >= $minlimit AND downloaded < $maxlimit AND uploaded / downloaded >= $minratio AND warned = 'no' AND UNIX_TIMESTAMP(invitedate) <= $time");
    if ($res->rowCount() > 0) {
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            $maxninvites = $maxinvites[$arr['class']];
            if ($arr['invites'] >= $maxninvites) {
                continue;
            }

            if (($maxninvites - $arr['invites']) < $invites) {
                $invites = $maxninvites - $arr['invites'];
            }

            $pdo->run("UPDATE users SET invites = invites+$invites, invitedate = NOW() WHERE id=$arr[id]");
            write_log("Gave $invites invites to '$arr[username]' - Class: " . get_user_class_name($arr['class']) . "");
        }
    }
}

function do_cleanup()
{
    global $config, $pdo;

    //LOCAL TORRENTS - GET PEERS DATA AND UPDATE BROWSE STATS
    //DELETE OLD NON-ACTIVE PEERS
    $deadtime = get_date_time(gmtime() - $config['announce_interval']);
    $pdo->run("DELETE FROM peers WHERE last_action < '$deadtime'");

    $torrents = array();
    $res = $pdo->run("SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder");
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        if ($row["seeder"] == "yes") {
            $key = "seeders";
        } else {
            $key = "leechers";
        }

        $torrents[$row["torrent"]][$key] = $row["c"];
    }

    $res = $pdo->run("SELECT torrent, COUNT(torrent) as c FROM comments WHERE torrent > 0 GROUP BY torrent");
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $torrents[$row["torrent"]]["comments"] = $row["c"];
    }

    $fields = explode(":", "comments:leechers:seeders");
    $res = $pdo->run("SELECT id, external, seeders, leechers, comments FROM torrents WHERE banned = 'no'");
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $id = $row["id"];
        $torr = $torrents[$id];
        foreach ($fields as $field) {
            if (!isset($torr[$field])) {
                $torr[$field] = 0;
            }
        }
        $update = array();
        foreach ($fields as $field) {
            if ($row["external"] == "no" || $field == "comments") {
                if ($torr[$field] != $row[$field]) {
                    $update[] = "$field = " . $torr[$field];
                }
            }
        }
        if (count($update)) {
            $pdo->run("UPDATE torrents SET " . implode(",", $update) . " WHERE id = $id");
        }
    }

    //LOCAL TORRENTS - MAKE NON-ACTIVE/OLD TORRENTS INVISIBLE
    $deadtime = gmtime() - $config["max_dead_torrent_time"];
    $pdo->run("UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < FROM_UNIXTIME($deadtime) AND seeders = '0' AND leechers = '0' AND external !='yes'");

    // Seedbonus Mod
    $now = gmtime();
    $dobonus = 0;

    $res = DB::run("SELECT last_time FROM tasks WHERE task='bonus'");
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $pdo->run("INSERT INTO tasks (task, last_time) VALUES ('bonus',$now)");
        // write_log("theres was no row silly we need one to start");
    }

    $ts = $row['last_time']; // $row['0'] returned null

    if ($ts + $config['add_bonus'] < $now) {
        $qry = "SELECT DISTINCT userid as peer, (
    SELECT DISTINCT COUNT( torrent )
    FROM peers
    WHERE seeder = 'yes'  AND userid = peer) AS count
    FROM peers WHERE seeder = 'yes'";

        $res1 = DB::run($qry);
        while ($row = $res1->fetch(PDO::FETCH_LAZY)) {
            DB::run("UPDATE users SET seedbonus = seedbonus + '" . ($config["bonuspertime"] * $row->count) . "' WHERE id = '" . $row->peer . "'");
            DB::run("UPDATE tasks SET last_time=$now WHERE task='bonus'");
            // write_log("bonus and task inserted every hour");
        }
    }
    // End
    // Start Vipuntil mod vip
    $timenow = get_date_time();

    $subject = 'Your VIP class stay has just expired';
    $msg = 'Your VIP class stay has just expired';

    $resv = DB::run("SELECT id, oldclass FROM users WHERE vipuntil < ? AND vipuntil <> ?", [$timenow, '0000-00-00 00:00:00']);

    if ($resv->rowCount()) {
        $rowv = $resv->fetch(PDO::FETCH_LAZY);
        $id = $rowv->id;
        $oldclass = $rowv->oldclass;
        DB::run("UPDATE users SET class =?, oldclass=?, vipuntil =? WHERE vipuntil < ? AND vipuntil <> ?", [$oldclass, 1, '0000-00-00 00:00:00', $timenow, '0000-00-00 00:00:00']);
        DB::run("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES(?, ?, ?, ?, ?, ?)", [0, $id, $timenow, $subject, $msg, 0]);
    }
    // End Remove Vipuntil mod vip

    //DELETE PENDING USER ACCOUNTS OVER TIMOUT AGE
    $deadtime = gmtime() - $config["signup_timeout"];
    $pdo->run("DELETE FROM users WHERE status = 'pending' AND added < FROM_UNIXTIME($deadtime)");

    // DELETE OLD LOG ENTRIES
    $ts = gmtime() - $config["LOGCLEAN"];
    $pdo->run("DELETE FROM log WHERE added < FROM_UNIXTIME($ts)");

    //LEECHWARN USERS WITH LOW RATIO

    if ($config["ratiowarn_enable"]) {
        $minratio = $config["ratiowarn_minratio"];
        $downloaded = $config["ratiowarn_mingigs"] * 1024 * 1024 * 1024;
        $length = $config["ratiowarn_daystowarn"];

        //ADD WARNING
        $res = $pdo->run("SELECT id,username FROM users WHERE class = 1 AND warned = 'no' AND enabled='yes' AND uploaded / downloaded < $minratio AND downloaded >= $downloaded");

        if ($res->rowCount() > 0) {
            $timenow = get_date_time();
            $reason = "You have been warned because of having low ratio. You need to get a " . $minratio . " before next " . $length . " days or your account may be banned.";

            $expiretime = gmdate("Y-m-d H:i:s", gmtime() + (86400 * $length));

            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                $pdo->run("INSERT INTO warnings (userid, reason, added, expiry, warnedby, type) VALUES ('" . $arr["id"] . "','" . $reason . "','" . $timenow . "','" . $expiretime . "','0','Poor Ratio')");
                $pdo->run("UPDATE users SET warned='yes' WHERE id='" . $arr["id"] . "'");
                $pdo->run("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ('0', '" . $arr["id"] . "', '" . $timenow . "', '" . $reason . "', '0')");
                write_log("Auto Leech warning has been <b>added</b> for: <a href='$config[SITEURL]/users/profile?id=" . $arr["id"] . "'>" . class_user_colour($arr["username"]) . "</a>");
            }
        }

        //REMOVE WARNING
        $res1 = $pdo->run("SELECT users.id, users.username FROM users INNER JOIN warnings ON users.id=warnings.userid WHERE type='Poor Ratio' AND active = 'yes' AND warned = 'yes'  AND enabled='yes' AND uploaded / downloaded >= $minratio AND downloaded >= $downloaded");
        if ($res1->rowCount() > 0) {
            $timenow = get_date_time();
            $reason = "Your warning of low ratio has been removed. We highly recommend you to keep a your ratio up to not be warned again.\n";

            while ($arr1 = $res1->fetch(PDO::FETCH_ASSOC)) {
                write_log("Auto Leech warning has been removed for: <a href='$config[SITEURL]/users/profile?id=" . $arr1["id"] . "'>" . class_user_colour($arr1["username"]) . "</a>");

                $pdo->run("UPDATE users SET warned = 'no' WHERE id = '" . $arr1["id"] . "'");
                $pdo->run("UPDATE warnings SET expiry = '$timenow', active = 'no' WHERE userid = $arr1[id]");
                $pdo->run("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ('0', '" . $arr1["id"] . "', '" . $timenow . "', '" . $reason . "', '0')");
            }
        }

        //BAN WARNED USERS
        $res = $pdo->run("SELECT users.id, users.username, UNIX_TIMESTAMP(warnings.expiry) AS expiry FROM users INNER JOIN warnings ON users.id=warnings.userid WHERE type='Poor Ratio' AND active = 'yes' AND class = 1 AND enabled='yes' AND warned = 'yes' AND uploaded / downloaded < $minratio AND downloaded >= $downloaded");

        if ($res->rowCount() > 0) {
            $timenow = get_date_time();
            $expires = (86400 * $length);
            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                if (gmtime() - $arr["expiry"] >= 0) {
                    $pdo->run("UPDATE users SET enabled='no', warned='no' WHERE id='" . $arr["id"] . "'");
                    write_log("User <a href='$config[SITEURL]/users/profile?id=" . $arr["id"] . "'>" . class_user_colour($arr["username"]) . "</a> has been banned (Auto Leech warning).");
                }
            }
        }
    } //check if warning system is on
    // REMOVE WARNINGS
    $res = $pdo->run("SELECT users.id, users.username, warnings.expiry FROM users INNER JOIN warnings ON users.id=warnings.userid WHERE type != 'Poor Ratio' AND warned = 'yes'  AND enabled='yes' AND warnings.active = 'yes' AND warnings.expiry < '" . get_date_time() . "'");
    while ($arr1 = $res->fetch(PDO::FETCH_ASSOC)) {
        $pdo->run("UPDATE users SET warned = 'no' WHERE id = $arr1[id]");
        $pdo->run("UPDATE warnings SET active = 'no' WHERE userid = $arr1[id] AND expiry < '" . get_date_time() . "'");
        write_log("Removed warning for $arr1[username]. Expiry: $arr1[expiry]");
    }
    // WARN USERS THAT STILL HAVE ACTIVE WARNINGS
    $pdo->run("UPDATE users SET warned = 'yes' WHERE warned = 'no' AND id IN (SELECT userid FROM warnings WHERE active = 'yes')");
    //END//

    // set freeleech
    if ($config['freeleechgbon']);{
        $gigs = $config['freeleechgb'];
        $query = $pdo->run("SELECT `id`, `name` FROM `torrents` WHERE `banned` = 'no' AND `freeleech` = '0' AND `size` >= $gigs");
        if ($query->rowCount() > 0) {
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $pdo->run("UPDATE `torrents` SET `freeleech` = '1' WHERE `id` = '$row[id]'");
                write_log("Freeleech added on  <a href='torrents/read?id=$row[id]'>$row[name]</a> because it is bigger than  8gb.");
            }
        }
    }
    // START INVITES UPDATE
    // SET INVITE AMOUNTS ACCORDING TO RATIO/GIGS ETC
    // autoinvites(interval to give invites (days), min downloaded GB, max downloaded GB, min ratio, invites to give, max invites allowed (array))
    // $maxinvites[CLASS ID] = max # of invites;
    $maxinvites[1] = 5; // User
    $maxinvites[2] = 10; // Power User
    $maxinvites[3] = 20; // VIP
    $maxinvites[4] = 25; // Uploader
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

    //HIT & RUN mod
    if ($config["hnr_on"]) {
        $timenow = gmtime();
        DB::run("UPDATE snatched SET hnr = 'yes' WHERE completed = '1' AND hnr = 'no' AND uload < dload AND $timenow - $config[hnr_deadline] > stime AND $config[hnr_seedtime] > ltime AND done='no'");
        DB::run("UPDATE `snatched` SET `hnr` = 'no' WHERE `hnr` = 'yes' AND uload >= dload");
        DB::run("UPDATE `snatched` SET `hnr` = 'no' WHERE `hnr` = 'yes' AND ltime >= $config[hnr_seedtime]");
        $a = DB::run("SELECT DISTINCT uid FROM snatched WHERE hnr = 'yes' AND done='no'");
        if ($a->rowCount() > 0):
            while ($b = $a->fetch(PDO::FETCH_ASSOC)):
                $c = DB::run("SELECT COUNT( hnr ) FROM snatched WHERE uid = $b[0] AND hnr = 'yes'");
                $d = $c->fetch(PDO::FETCH_ASSOC);
                $count = $d[0];
                $user = $b[0];

                $length = $config["hnr_disabled"];
                $expiretime = gmdate("Y-m-d H:i:s", $timenow + $length);

                $e = DB::run("SELECT type, active FROM warnings WHERE userid = '$user'");
                $f = $e->fetch(PDO::FETCH_ASSOC);
                $type = $f[0];
                $active = $f[1];
                //warn
                if ($count >= $config["hnr_warn"] && $type != "HnR"):
                    $reason = "" . T_("CLEANUP_WARNING_FOR_ACCUMULATING") . " " . $config['hnr_warn'] . " H&R.";
                    $subject = "" . T_("CLEANUP_WARNING_FOR_H&R") . "";
                    $msg = "" . T_("CLEANUP_YOU_HAVE_BEEN_WARNEWD_ACCUMULATED") . " " . $config['hnr_warn'] . " " . T_("CLEANUP_H&R_INVITE_CHECK_RULE") . "\n[color=red]" . T_("CLEANUP_MSG_WARNING_7_DAYS_BANNED") . "[/color]";

                    $rev = DB::run("SELECT enabled FROM users WHERE id = $user");
                    $rov = mysqli_fetch_assoc($rev);
                    if ($rov["enabled"] == "yes"):
                        DB::run("UPDATE users SET warned = 'yes' WHERE id = $user");
                        DB::run("INSERT INTO warnings (userid, reason, added, expiry, warnedby, type) VALUES ($user," . sqlesc($reason) . ",'" . get_date_time() . "','" . $expiretime . "','0','HnR')");
                        DB::run("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES (0, $user, '" . get_date_time() . "', " . sqlesc($subject) . ", " . sqlesc($msg) . ", 1)");
                    endif;
                endif;
                //Unwarned
                if ($count < $config["hnr_warn"] && $type == "HnR"):
                    $subject = "" . T_("CLEANUP_REMOVAL_OF_H&R_WARNING") . "";
                    $msg = "" . T_("CLEANUP_YOU_NOW_HAVE_LESS_THAN") . " " . $config['hnr_warn'] . " H&R.\n" . T_("CLEANUP_YOUR_WARNING_FOR_H&R_HAS_REMOVED") . "";
                    DB::run("UPDATE users SET warned = 'no' WHERE id = $user");
                    DB::run("DELETE FROM warnings WHERE userid = $user AND type = 'HnR'");
                    DB::run("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES (0, $user, '" . get_date_time() . "', " . sqlesc($subject) . ", " . sqlesc($msg) . ", 1)");
                endif;
                //Ban
                if ($count >= $config["hnr_ban"]):
                    $g = DB::run("SELECT username, email, modcomment FROM users WHERE id = $user");
                    $h = mysqli_fetch_row($g);
                    $modcomment = $h[2];
                    $modcomment = gmdate("d/m/Y") . " - " . T_("CLEANUP_BANNED_FOR") . " " . $count . " H&R.\n " . $modcomment;
                    DB::run("UPDATE users SET enabled = 'no', warned = 'no', modcomment = '$modcomment' WHERE id = $user");
                    DB::run("DELETE FROM warnings WHERE userid = $user AND type = 'HnR'");
                    write_log(T_("CLEANUP_THE_MEMBER") . " <a href='account-details.php?id=" . $user . "'>" . $h[0] . "</a> " . T_("CLEANUP_HAS_BEEN_BANNED_REASON") . " " . $count . " H&R.");
                    $subject = "" . T_("CLEANUP_YOUR_ACCOUNT") . " $config[SITENAME] " . T_("CLEANUP_HAS_BEEN_DISABLED") . "";
                    $body = "" . T_("CLEANUP_YOU_WERE_BANNED_FOLLOWING") . "\n
						------------------------------
						\n/" . T_("CLEANUP_YOU_HAVE_ACCUMULATED") . " $count H&R.\n
						------------------------------
						\n" . T_("CLEANUP_YOU_CAN_CONTACT_BY_LINK") . " :
						" . $config['SITEURL'] . "/contact.php
						\n\n\n" . $config['SITENAME'] . " " . T_("ADMIN");
                    $TTMail = new TTMail();
                    $TTMail->Send($h[1], "$subject", "$body", "" . T_("OF") . ": $config[SITEEMAIL]", "-f$config[SITEEMAIL]");
                endif;
            endwhile;
        endif;
    }
    // END HIT & RUN

    // NEW OPTIMIZE TABLES
    $res = $pdo->run("SHOW TABLES");

    while ($table = $res->fetch(PDO::FETCH_LAZY)) {
        // Get rid of overhead.
        $pdo->run("REPAIR TABLE `$table[0]`;");
        // Analyze table for faster indexing.
        $pdo->run("ANALYZE TABLE `$table[0]`;");
        // Optimize table to minimize thrashing.
        $pdo->run("OPTIMIZE TABLE `$table[0]`;");
    }
}
