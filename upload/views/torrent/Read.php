<?php
if ($row["banned"] == "yes") {
            print("<center><b>" . T_("DOWNLOAD") . ": </b>BANNED!</center>");
        } else {
            ?>
        <table width="100%">
        <tr>
            <td width="50%"><b><?php echo T_("Details"); ?></b></td>
            <td width="50%"><b><?php echo T_("Connection"); ?></b></td>
        </tr>

        <tr valign="top">
            <td align="left">
            <b><?php echo T_("NAME"); ?>:</b>&nbsp;<?php echo $shortname; ?><br>
            <b><?php echo T_("DESCRIPTION"); ?>:</b>&nbsp;<?php echo format_comment($row['descr']); ?><br>
            <b><?php echo T_("CATEGORY"); ?>:</b>&nbsp;<?php echo $row["cat_parent"]; ?> > <?php echo $row["cat_name"]; ?><br>
        <?php
if (empty($row["lang_name"])) {
                $row["lang_name"] = "Unknown/NA";
            }?>
        <b><?php echo T_("LANG"); ?>:</b>&nbsp;<?php echo $row["lang_name"]; ?><br>
        <?php
if (isset($row["lang_image"]) && $row["lang_image"] != "") {
                print("&nbsp;<img border=\"0\" src=\"" . $site_config['SITEURL'] . "/images/languages/" . $row["lang_image"] . "\" alt=\"" . $row["lang_name"] . "\" />");
            }?>

        <b><?php echo T_("TOTAL_SIZE"); ?>:</b>&nbsp;<?php echo mksize($row["size"]); ?><br>
        <b><?php echo T_("INFO_HASH"); ?>:</b>&nbsp;<?php echo $row["info_hash"]; ?><br>

        <?php if ($row["anon"] == "yes" && !$owned) {?>
        <b><?php echo T_("ADDED_BY"); ?>:</b>&nbsp; Anonymous<br>
        <?php } elseif ($row["username"]) {?>
        <b><?php echo T_("ADDED_BY"); ?>:</b>&nbsp;<a href='$site_config[SITEURL]/users/profile?id=<?php echo $row["owner"]; ?>'><?php echo class_user_colour($row["username"]); ?></a><br>
        <?php } else {?>
        <b><?php echo T_("ADDED_BY"); ?>:</b>&nbsp; Unknown<br>
        <?php }?>

        <b><?php echo T_("DATE_ADDED"); ?>:</b>&nbsp;<?php echo date("d-m-Y H:i:s", utc_to_tz_time($row["added"])); ?><br>
        <b><?php echo T_("VIEWS"); ?>:</b>&nbsp;<?php echo number_format($row["views"]); ?><br>
        <b><?php echo T_("HITS"); ?>:</b>&nbsp;<?php echo number_format($row["hits"]); ?><br>
        <?php
// LIKE MOD
            if ($site_config["allowlikes"]) {
                $data = DB::run("SELECT user FROM likes WHERE liked=? AND type=? AND user=? AND reaction=?", [$id, 'torrent', $CURUSER['id'], 'like']);
                $likes = $data->fetch(PDO::FETCH_ASSOC);
                if ($likes) {?>
                <b>Reaction:</b>&nbsp;<a href='<?php echo TTURL; ?>/likes/unliketorrent?id=<?php echo $id; ?>'><img src='<?php echo TTURL; ?>/images/unlike.png' width='80' height='40' border='0'></a><br>
        <?php } else {?>
                <b>Reaction:</b>&nbsp;<a href='<?php echo TTURL; ?>/likes/liketorrent?id=<?php echo $id; ?>'><img src='<?php echo TTURL; ?>/images/like.png' width='80' height='40' border='0'></a><br>
        <?php }
            }
            ?>
        </td><br><br>
        <td align="left">
        <?php // peers
            print("<b>" . T_("HEALTH") . ": </b><img src='" . $site_config["SITEURL"] . "/images/health/health_" . health($row["leechers"], $row["seeders"]) . ".gif' alt='' /><br />");
            print("<b>" . T_("SEEDS") . ": </b><font color='green'>" . number_format($row["seeders"]) . "</font><br />");
            print("<b>" . T_("LEECHERS") . ": </b><font color='#ff0000'>" . number_format($row["leechers"]) . "</font><br />");
            if ($row["external"] != 'yes') {
                print("<b>" . T_("SPEED") . ": </b>" . $totalspeed . "<br />");
            }
            print("<b>" . T_("COMPLETED") . ":</b> " . number_format($row["times_completed"]) . "&nbsp;");
            if ($row["external"] != "yes" && $row["times_completed"] > 0) {
                echo ("[<a href='$site_config[SITEURL]/torrents/completed?id=$id'>" . T_("WHOS_COMPLETED") . "</a>] ");
                if ($row["seeders"] <= 1) {
                    echo ("[<a href='$site_config[SITEURL]/torrents/reseed?id=$id'>" . T_("REQUEST_A_RE_SEED") . "</a>]");
                }
            }
            echo "<br />";

            if ($row["external"] != 'yes' && $row["freeleech"] == '1') {
                print("<b>" . T_("FREE_LEECH") . ": </b><font color='#ff0000'>" . T_("FREE_LEECH_MSG") . "</font><br />");
            }

            print("<b>" . T_("LAST_CHECKED") . ": </b>" . date("d-m-Y H:i:s", utc_to_tz_time($row["last_action"])) . "<br><br>");
            // Like Mod
            if (!$site_config["forcethanks"]) {
                // Magnet
                if ($row["external"] == 'yes') {
                    print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                } else {
                    print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=" . $site_config['SITEURL'] . "/announce.php?passkey=" . $CURUSER["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                }
            }
            if ($CURUSER["id"] != $row["owner"] && $site_config["forcethanks"]) {
                $data = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$id, 'torrent', $CURUSER['id']]);
                $like = $data->fetch(PDO::FETCH_ASSOC);
                if ($like) {
                    // Magnet
                    if ($row["external"] == 'yes') {
                        print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                    } else {
                        print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=" . $site_config['SITEURL'] . "/announce.php?passkey=" . $CURUSER["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                    }
                } else {
                    print("<a href='$site_config[SITEURL]/likes/details?id=$id'><button  class='btn btn-sm btn-danger'>Thanks</button></a>&nbsp;");
                }
            } else {
                if ($row["external"] == 'yes') {
                    print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                } else {
                    print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=" . $site_config['SITEURL'] . "/announce.php?passkey=" . $CURUSER["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                }
            }

            print("&nbsp;<a href=\"$site_config[SITEURL]/download?id=$id&amp;name=" . rawurlencode($row["filename"]) . "\"><button type='button' class='btn btn-sm btn-success'>" . T_("DOWNLOAD_TORRENT") . "</button></a><br><br>");
            if ($row["image1"] != "" or $row["image2"] != "") {
                if ($row["image1"] != "") {
                    $img1 = "<img src='" . $site_config["SITEURL"] . "/uploads/images/$row[image1]' height='80' width='80' border='0' alt='' />";
                }
                if ($row["image2"] != "") {
                    $img2 = "<img src='" . $site_config["SITEURL"] . "/uploads/images/$row[image2]' height='80' width='80' border='0' alt='' />";
                }
                print("" . $img1 . "&nbsp;&nbsp;" . $img2 . "<br />");
            }

            if (!empty($row["tube"])) {
                print ("<br><embed src='". str_replace("watch?v=", "v/", htmlspecialchars($row["tube"])) ."' type=\"application/x-shockwave-flash\" width=\"400\" height=\"310\"></embed>");
                    }
            ?>
            </td>
        </tr>

        <tr>
        <td width="50%"><br><b><?php echo T_("Actions"); ?></b></td>
        <?php if ($row["external"] == 'yes') { ?>
            <td width="50%"><br><b><?php echo T_("EXTERNAL"); ?></b></td>
        <?php } ?>
        </tr>
  <!--otherstart-->
        <tr valign="top">
        <td align="left">
            <?php if ($owned) {
                echo "<a href='$site_config[SITEURL]/torrents/edit?id=$row[id]&amp;returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "'><button type='button' class='btn btn-sm btn-success'><b>" . T_("EDIT_TORRENT") . "</b></button></a>&nbsp;";
            }?>
            <a href="<?php echo $site_config['SITEURL'] ?>/report/torrent?torrent=<?php echo $id; ?>"><button type='button' class='btn btn-sm btn-danger'><?php echo T_("REPORT_TORRENT") ?></button></a>&nbsp;
        <?php if ($CURUSER["edit_users"] == "yes") {?>
      <a href="<?php echo $site_config['SITEURL']; ?>/snatched?tid=<?php echo $row['id']; ?>"><button type='button' class='btn btn-sm btn-warning'><?php echo T_("SNATCHLIST") ?></button></a>
    <?php }?>
    <?php if ($CURUSER["delete_torrents"] == "yes") {?>
      <a href="<?php echo $site_config['SITEURL']; ?>/torrents/delete?id=<?php echo $row['id']; ?>"><button type='button' class='btn btn-sm btn-warning'><?php echo T_("Delete") ?></button></a>
    <?php }?>

<!--buttonsstart-->
<td align="left"> <?php
// scrape
            if ($row["external"] == 'yes') {

                if ($scrape == '1') {
                    print("<b>Tracked: </b>EXTERNAL<br>");
                    $seeders1 = $leechers1 = $downloaded1 = null;
                    $tres = $pdo->run("SELECT url FROM announce WHERE torrent=$id");
                    while ($trow = $tres->fetch(PDO::FETCH_ASSOC)) {
                        $ann = $trow["url"];
                        $tracker = explode("/", $ann);
                        $path = array_pop($tracker);
                        $oldpath = $path;
                        $path = preg_replace("/^announce/", "scrape", $path);
                        $tracker = implode("/", $tracker) . "/" . $path;

                        if ($oldpath == $path) {
                            continue; // Scrape not supported, ignored
                        }

                        // TPB's tracker is dead. Use openbittorrent instead
                        if (preg_match("/thepiratebay.org/i", $tracker) || preg_match("/prq.to/", $tracker)) {
                            $tracker = "http://tracker.openbittorrent.com/scrape";
                        }

                        $stats = torrent_scrape_url($tracker, $row["info_hash"]);
                        if ($stats['seeds'] != -1) {
                            $seeders1 += $stats['seeds'];
                            $leechers1 += $stats['peers'];
                            $downloaded1 += $stats['downloaded'];
                            $pdo->run("UPDATE `announce` SET `online` = 'yes', `seeders` = $stats[seeds], `leechers` = $stats[peers], `times_completed` = $stats[downloaded] WHERE `url` = " . sqlesc($ann) . " AND `torrent` = $id");
                        } else {
                            $pdo->run("UPDATE `announce` SET `online` = 'no' WHERE `url` = " . sqlesc($ann) . " AND `torrent` = $id");

                        }
                    }

                    if ($seeders1 !== null) { //only update stats if data is received
                        print("<b>" . T_("LIVE_STATS") . ": </b><br>");
                        print("Seeders: " . number_format($seeders1) . "<br>");
                        print("Leechers: " . number_format($leechers1) . "<br>");
                        print(T_("COMPLETED") . ": " . number_format($downloaded1) . "<br>");

                        $pdo->run("UPDATE torrents SET leechers='" . $leechers1 . "', seeders='" . $seeders1 . "', times_completed='" . $downloaded1 . "',last_action= '" . get_date_time() . "',visible='yes' WHERE id='" . $row['id'] . "'");
                    } else {
                        print("<b>" . T_("LIVE_STATS") . ": </b><br />");
                        print("<font color='#ff0000'>Tracker Timeout<br />Please retry later</font><br />");
                    }

                    print("<form action='read?id=$id&amp;scrape=1' method='post'><input type=\"submit\" name=\"submit\" value=\"Update Stats\" /></form></td>");
                } else {
                    print("<b>Tracked:</b> EXTERNAL<br><form action='read?id=$id&amp;scrape=1' method='post'><input type=\"submit\" name=\"submit\" value=\"Update Stats\" /></form></td>");
                }
            }?>
            </td>
            </td>
        </tr>
        </table>
        <?php
}

        //DISPLAY NFO BLOCK
        if ($row["nfo"] == "yes") {
            $nfofilelocation = "$nfo_dir/$row[id].nfo";
            $filegetcontents = file_get_contents($nfofilelocation);
            // needs filtering better todo
            //    $nfo = htmlspecialchars($filegetcontents);
            $nfo = $filegetcontents;
            if ($nfo) {
                $nfo = my_nfo_translate($nfo);
                echo "<br /><br /><b>NFO:</b><br />";
                print("<div><textarea class='nfo' style='width:98%;height:100%;' rows='20' cols='20' readonly='readonly'>" . stripslashes($nfo) . "</textarea></div>");
            } else {
                print(T_("ERROR") . " reading .nfo file!");
            }
        }