<?php
// Function That Returns The Health Level Of A Torrent
function health($leechers, $seeders)
{
    if (($leechers == 0 && $seeders == 0) || ($leechers > 0 && $seeders == 0)) {
        return 0;
    } elseif ($seeders > $leechers) {
        return 10;
    }

    $ratio = $seeders / $leechers * 100;
    if ($ratio > 0 && $ratio < 15) {
        return 1;
    } elseif ($ratio >= 15 && $ratio < 25) {
        return 2;
    } elseif ($ratio >= 25 && $ratio < 35) {
        return 3;
    } elseif ($ratio >= 35 && $ratio < 45) {
        return 4;
    } elseif ($ratio >= 45 && $ratio < 55) {
        return 5;
    } elseif ($ratio >= 55 && $ratio < 65) {
        return 6;
    } elseif ($ratio >= 65 && $ratio < 75) {
        return 7;
    } elseif ($ratio >= 75 && $ratio < 85) {
        return 8;
    } elseif ($ratio >= 85 && $ratio < 95) {
        return 9;
    } else {
        return 10;
    }

}
// Transformation Function For Torrent URL
function torrent_scrape_url($scrape, $hash)
{
    if (function_exists("curl_exec")) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $scrape . '?info_hash=' . escape_url($hash));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $fp = curl_exec($ch);
        curl_close($ch);
    } else {
        ini_set('default_socket_timeout', 10);
        $fp = @file_get_contents($scrape . '?info_hash=' . escape_url($hash));
    }
    $ret = array();
    if ($fp) {
        $stats = BDecode($fp);
        $binhash = pack("H*", $hash);
        $binhash = addslashes($binhash);
        $seeds = $stats['files'][$binhash]['complete'];
        $peers = $stats['files'][$binhash]['incomplete'];
        $downloaded = $stats['files'][$binhash]['downloaded'];
        $ret['seeds'] = $seeds;
        $ret['peers'] = $peers;
        $ret['downloaded'] = $downloaded;
    }
    if ($ret['seeds'] === null) {
        $ret['seeds'] = -1;
        $ret['peers'] = -1;
        $ret['downloaded'] = -1;
    }
    return $ret;
}
// Function To Delete A Torrent
function deletetorrent($id)
{
    global $site_config, $pdo;

    $stmt = @$pdo->run("SELECT image1,image2 FROM torrents WHERE id=$id");
    $row = @$stmt->fetch(PDO::FETCH_ASSOC);

    foreach (explode(".", "peers.comments.ratings.files.announce") as $x) {
        $pdo->run("DELETE FROM $x WHERE torrent = $id");
    }

    $pdo->run("DELETE FROM completed WHERE torrentid = $id");

    if (file_exists($site_config["torrent_dir"] . "/$id.torrent")) {
        unlink($site_config["torrent_dir"] . "/$id.torrent");
    }

    if ($row["image1"]) {
        unlink($site_config['torrent_dir'] . "/images/" . $row["image1"]);
    }

    if ($row["image2"]) {
        unlink($site_config['torrent_dir'] . "/images/" . $row["image2"]);
    }

    @unlink($site_config["nfo_dir"] . "/$id.nfo");

    $pdo->run("DELETE FROM torrents WHERE id = $id");
    $pdo->run("DELETE FROM reports WHERE votedfor = $id AND type = 'torrent'");
    // snatch
    $pdo->run("DELETE FROM `snatched` WHERE `tid` = '$id'");
}

// Function To Retrieve Main Categories Of Torrents
function genrelist()
{
    global $pdo;
    $ret = array();
    $res = $pdo->run("SELECT id, name, parent_cat FROM categories ORDER BY parent_cat ASC, sort_index ASC");
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $ret[] = $row;
    }

    return $ret;
}
// Function To Edit The List Of Possible Languages For Torrents
function langlist()
{
    global $pdo;
    $ret = array();
    $stmt = $pdo->run("SELECT id, name, image FROM torrentlang ORDER BY sort_index, id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ret[] = $row;
    }
    return $ret;
}

function peerstable($res)
{
    global $site_config, $pdo;
    $ret = "<table align='center' cellpadding=\"3\" cellspacing=\"0\" class=\"table_table\" width=\"100%\" border=\"1\"><tr><th class='table_head'>" . T_("NAME") . "</th><th class='table_head'>" . T_("SIZE") . "</th><th class='table_head'>" . T_("UPLOADED") . "</th>\n<th class='table_head'>" . T_("DOWNLOADED") . "</th><th class='table_head'>" . T_("RATIO") . "</th></tr>\n";

    while ($arr = $res->fetch(PDO::FETCH_LAZY)) {
        $res2 = $pdo->run("SELECT name,size FROM torrents WHERE id=? ORDER BY name", [$arr['torrent']]);
        $arr2 = $res2->fetch(PDO::FETCH_LAZY);
        if ($arr["downloaded"] > 0) {
            $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
        } else {
            $ratio = "---";
        }
        $ret .= "<tr><td class='table_col1'><a href='$site_config[SITEURL]/torrents/details?id=$arr[torrent]&amp;hit=1'><b>" . htmlspecialchars($arr2["name"]) . "</b></a></td><td align='center' class='table_col2'>" . mksize($arr2["size"]) . "</td><td align='center' class='table_col1'>" . mksize($arr["uploaded"]) . "</td><td align='center' class='table_col2'>" . mksize($arr["downloaded"]) . "</td><td align='center' class='table_col1'>$ratio</td></tr>\n";
    }
    $ret .= "</table>\n";
    return $ret;
}

// Function To Display Tables Of Torrents
function torrenttable($res)
{
    global $site_config, $CURUSER, $THEME, $LANGUAGE, $pdo; //Define globals

    if ($site_config["MEMBERSONLY_WAIT"] && $site_config["MEMBERSONLY"] && in_array($CURUSER["class"], explode(",", $site_config["WAIT_CLASS"]))) {
        $gigs = $CURUSER["uploaded"] / (1024 * 1024 * 1024);
        $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
        if ($ratio < 0 || $gigs < 0) {
            $wait = $site_config["WAITA"];
        } elseif ($ratio < $site_config["RATIOA"] || $gigs < $site_config["GIGSA"]) {
            $wait = $site_config["WAITA"];
        } elseif ($ratio < $site_config["RATIOB"] || $gigs < $site_config["GIGSB"]) {
            $wait = $site_config["WAITB"];
        } elseif ($ratio < $site_config["RATIOC"] || $gigs < $site_config["GIGSC"]) {
            $wait = $site_config["WAITC"];
        } elseif ($ratio < $site_config["RATIOD"] || $gigs < $site_config["GIGSD"]) {
            $wait = $site_config["WAITD"];
        } else {
            $wait = 0;
        }

    }
    $wait = '';
    // Columns
    $cols = explode(",", $site_config["torrenttable_columns"]);
    $cols = array_map("strtolower", $cols);
    $cols = array_map("trim", $cols);
    $colspan = count($cols);
    // End

    // Expanding Area
    $expandrows = array();
    if (!empty($site_config["torrenttable_expand"])) {
        $expandrows = explode(",", $site_config["torrenttable_expand"]);
        $expandrows = array_map("strtolower", $expandrows);
        $expandrows = array_map("trim", $expandrows);
    }
    // End
    echo '<div class="table-responsive"><table class="table table-striped"><thead><tr>';

    foreach ($cols as $col) {
        switch ($col) {
            case 'category':
                echo "<th>" . T_("TYPE") . "</th>";
                break;
            case 'name':
                echo "<th>" . T_("NAME") . "</th>";
                break;
            case 'dl':
                echo "<th>" . T_("DL") . "</th>";
                break;
            case 'magnet':
                echo "<th>" . T_("MAGNET2") . "</th>";
                break;
            case 'uploader':
                echo "<th>" . T_("UPLOADER") . "</th>";
                break;
            case 'comments':
                echo "<th>" . T_("COMM") . "</th>";
                break;
            case 'nfo':
                echo "<th>" . T_("NFO") . "</th>";
                break;
            case 'size':
                echo "<th>" . T_("SIZE") . "</th>";
                break;
            case 'completed':
                echo "<th>" . T_("C") . "</th>";
                break;
            case 'seeders':
                echo "<th>" . T_("S") . "</th>";
                break;
            case 'leechers':
                echo "<th>" . T_("L") . "</th>";
                break;
            case 'health':
                echo "<th>" . T_("HEALTH") . "</th>";
                break;
            case 'external':
                if ($site_config["ALLOWEXTERNAL"]) {
                    echo "<th>" . T_("L/E") . "</th>";
                }

                break;
            case 'added':
                echo "<th>" . T_("ADDED") . "</th>";
                break;
            case 'speed':
                echo "<th>" . T_("SPEED") . "</th>";
                break;
            case 'wait':
                if ($wait) {
                    echo "<th>" . T_("WAIT") . "</th>";
                }

                break;
            case 'rating':
                echo "<th>" . T_("RATINGS") . "</th>";
                break;
        }
    }
    if ($wait && !in_array("wait", $cols)) {
        echo "<th>" . T_("WAIT") . "</th>";
    }

    echo "</tr></thead>";

    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $id = $row["id"];

        print("<tr class='t-row'>\n");

        $x = 1;

        foreach ($cols as $col) {
            switch ($col) {
                case 'category':
                    print("<td class='ttable_col$x' align='center' valign='middle'>");
                    if (!empty($row["cat_name"])) {
                        print("<a href=\"torrents/browse?cat=" . $row["category"] . "\">");
                        if (!empty($row["cat_pic"]) && $row["cat_pic"] != "") {
                            print("<img border=\"0\"src=\"" . $site_config['SITEURL'] . "/images/categories/" . $row["cat_pic"] . "\" alt=\"" . $row["cat_name"] . "\" />");
                        } else {
                            print($row["cat_parent"] . ": " . $row["cat_name"]);
                        }

                        print("</a>");
                    } else {
                        print("-");
                    }

                    print("</td>\n");
                    break;
                case 'name':
                    $char1 = 35; //cut name length
                    $smallname = htmlspecialchars(CutName($row["name"], $char1));
                    $dispname = "<b>" . $smallname . "</b>";

                    $last_access = $CURUSER["last_browse"];
                    $time_now = gmtime();
                    if ($last_access > $time_now || !is_numeric($last_access)) {
                        $last_access = $time_now;
                    }

                    if (sql_timestamp_to_unix_timestamp($row["added"]) >= $last_access) {
                        $dispname .= "<b><font color='#ff0000'> - (" . T_("NEW") . "!)</font></b>";
                    }

                    if ($row["freeleech"] == 1) {
                        $dispname .= " <img src='images/free.gif' border='0' alt='' />";
                    }

                    print("<td class='ttable_col$x' nowrap='nowrap'>" . (count($expandrows) ? "<a href=\"javascript: klappe_torrent('t" . $row['id'] . "')\"><img border=\"0\" src=\"" . $site_config["SITEURL"] . "/images/plus.gif\" id=\"pict" . $row['id'] . "\" alt=\"Show/Hide\" class=\"showthecross\" /></a>" : "") . "&nbsp;<a title=\"" . $row["name"] . "\" href=\"".$site_config['SITEURL']."/torrents/details?id=$id&amp;hit=1\">$dispname</a></td>");

                    break;
                case 'dl':
                    print("<td class='ttable_col$x' align='center'><a href=\"/download?id=$id&amp;name=" . rawurlencode($row["filename"]) . "\"><img src='" . $site_config['SITEURL'] . "/images/icon_download.gif' border='0' alt=\"Download .torrent\" /></a></td>");
                    break;
                case 'magnet':
                    $magnet = $pdo->run("SELECT info_hash FROM torrents WHERE id=?", [$id])->fetch();
                    // Like Mod
                    if(!$site_config["forcethanks"]) {
                    print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . $CURUSER['passkey'] . "\"><img src='" . $site_config['SITEURL'] . "/images/magnetique.png' border='0' title='Download via Magnet' /></a></td>");
                    }
                    if($CURUSER["id"] != $row["owner"] && $site_config["forcethanks"]) {
                    $data = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$id, 'torrent', $CURUSER['id']]);
                    $like = $data->fetch(PDO::FETCH_ASSOC);
                    if($like){
                    print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . $CURUSER['passkey'] . "\"><img src='" . $site_config['SITEURL'] . "/images/magnetique.png' border='0' title='Download via Magnet' /></a></td>");
                    }else {
                    print ("<td class='ttable_col$x' align='center'><a href='$site_config[SITEURL]/likes/index?id=$id' ><button  class='btn btn-sm btn-danger'>Thanks</button></td>");
                    }
                    }else{
                        print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . $CURUSER['passkey'] . "\"><img src='" . $site_config['SITEURL'] . "/images/magnetique.png' border='0' title='Download via Magnet' /></a></td>");
                    }
                    break;
                case 'uploader':
                    echo "<td class='ttable_col$x' align='center'>";
                    if (($row["anon"] == "yes" || $row["privacy"] == "strong") && $CURUSER["id"] != $row["owner"] && $CURUSER["edit_torrents"] != "yes") {
                        echo "Anonymous";
                    } elseif ($row["username"]) {
                        echo "<a href='".$site_config['SITEURL']."/accountdetails?id=$row[owner]'>" . class_user($row['username']) . "</a>";
                    } else {
                        echo "Unknown";
                    }

                    echo "</td>";
                    break;
                case 'comments':
                    print("<td class='ttable_col$x' align='center'><font size='1' face='verdana'><a href='$site_config[SITEURL]/comments?type=torrent&amp;id=$id'>" . number_format($row["comments"]) . "</a></font></td>\n");
                    break;
                case 'nfo':
                    if ($row["nfo"] == "yes") {
                        print("<td class='ttable_col$x' align='center'><a href='$site_config[SITEURL]/nfo/view?id=$row[id]'><img src='" . $site_config['SITEURL'] . "/images/icon_nfo.gif' border='0' alt='View NFO' /></a></td>");
                    } else {
                        print("<td class='ttable_col$x' align='center'>-</td>");
                    }

                    break;
                case 'size':
                    print("<td class='ttable_col$x' align='center'>" . mksize($row["size"]) . "</td>\n");
                    break;
                case 'completed':
                    print("<td class='ttable_col$x' align='center'><font color='orange'><b>" . number_format($row["times_completed"]) . "</b></font></td>");
                    break;
                case 'seeders':
                    print("<td class='ttable_col$x' align='center'><font color='green'><b>" . number_format($row["seeders"]) . "</b></font></td>\n");
                    break;
                case 'leechers':
                    print("<td class='ttable_col$x' align='center'><font color='#ff0000'><b>" . number_format($row["leechers"]) . "</b></font></td>\n");
                    break;
                case 'health':
                    print("<td class='ttable_col$x' align='center'><img src='" . $site_config["SITEURL"] . "/images/health/health_" . health($row["leechers"], $row["seeders"]) . ".gif' alt='' /></td>\n");
                    break;
                case 'external':
                    if ($site_config["ALLOWEXTERNAL"]) {
                        if ($row["external"] == 'yes') {
                            print("<td class='ttable_col$x' align='center'>" . T_("E") . "</td>\n");
                        } else {
                            print("<td class='ttable_col$x' align='center'>" . T_("L") . "</td>\n");
                        }

                    }
                    break;
                case 'added':
                    print("<td class='ttable_col$x' align='center'>" . date("d-m-Y H:i:s", utc_to_tz_time($row['added'])) . "</td>");
                    break;
                case 'speed':
                    if ($row["external"] != "yes" && $row["leechers"] >= 1) {
                        $speedQ = $pdo->run("SELECT (SUM(downloaded)) / (UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(started)) AS totalspeed FROM peers WHERE seeder = 'no' AND torrent = '$id' ORDER BY started ASC");
                        $a = $speedQ->fetch(PDO::FETCH_LAZY);
                        $totalspeed = mksize($a["totalspeed"]) . "/s";
                    } else {
                        $totalspeed = "--";
                    }

                    print("<td class='ttable_col$x' align='center'>$totalspeed</td>");
                    break;
                case 'wait':
                    if ($wait) {
                        $elapsed = floor((gmtime() - strtotime($row["added"])) / 3600);
                        if ($elapsed < $wait && $row["external"] != "yes") {
                            $color = dechex(floor(127 * ($wait - $elapsed) / 48 + 128) * 65536);
                            print("<td class='ttable_col$x' align='center'><a href=\"/faq#section46\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></td>\n");
                        } else {
                            print("<td class='ttable_col$x' align='center'>--</td>\n");
                        }

                    }
                    break;
                case 'rating':
                    if (!$row["rating"]) {
                        $rating = "--";
                    } else {
                        $rating = "<a title='$row[rating]/5'>" . ratingpic($row["rating"]) . "</a>";
                    }

                    //$rating = ratingpic($row["rating"]);
                    //$srating .= "$rpic (" . $row["rating"] . " out of 5) " . $row["numratings"] . " users have rated this torrent";
                    print("<td class='ttable_col$x' align='center'>$rating</td>");
                    break;
            }
            if ($x == 2) {
                $x--;
            } else {
                $x++;
            }

        }

        //Wait Time Check
        if ($wait && !in_array("wait", $cols)) {
            $elapsed = floor((gmtime() - strtotime($row["added"])) / 3600);
            if ($elapsed < $wait && $row["external"] != "yes") {
                $color = dechex(floor(127 * ($wait - $elapsed) / 48 + 128) * 65536);
                print("<td class='ttable_col$x' align='center'><a href=\"/faq\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></td>\n");
            } else {
                print("<td class='ttable_col$x' align='center'>--</td>\n");
            }

            $colspan++;
            if ($x == 2) {
                $x--;
            } else {
                $x++;
            }

        }

        print("</tr>\n");

        //Expanding area
        if (count($expandrows)) {
            print("<tr class='t-row'><td class='ttable_col$x' colspan='$colspan'><div id=\"kt" . $row['id'] . "\" style=\"margin-left: 2px; display: none;\">");
            print("<table width='100%' border='0' cellspacing='0' cellpadding='0'>");
            foreach ($expandrows as $expandrow) {
                switch ($expandrow) {
                    case 'size':
                        print("<tr><td><b>" . T_("SIZE") . "</b>: " . mksize($row['size']) . "</td></tr>");
                        break;
                    case 'speed':
                        if ($row["external"] != "yes" && $row["leechers"] >= 1) {
                            $speedQ = $pdo->run("SELECT (SUM(downloaded)) / (UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(started)) AS totalspeed FROM peers WHERE seeder = 'no' AND torrent = '$id' ORDER BY started ASC");
                            $a = $speedQ->fetch(PDO::FETCH_LAZY);
                            $totalspeed = mksize($a["totalspeed"]) . "/s";
                            print("<tr><td><b>" . T_("SPEED") . ":</b> $totalspeed</td></tr>");
                        }
                        break;
                    case 'added':
                        print("<tr><td><b>" . T_("ADDED") . ":</b> " . date("d-m-Y \\a\\t H:i:s", utc_to_tz_time($row['added'])) . "</td></tr>");
                        break;
                    case 'tracker':
                        if ($row["external"] == "yes") {
                            print("<tr><td><b>" . T_("TRACKER") . ":</b> " . htmlspecialchars($row["announce"]) . "</td></tr>");
                        }

                        break;
                    case 'completed':
                        print("<tr><td><b>" . T_("COMPLETED") . "</b>: " . number_format($row['times_completed']) . "</td></tr>");
                        break;
                }
            }
            print("</table></div></td></tr>\n");
        }
        //End Expanding Area

    }

    print("</table></div><br />\n");

}

function get_ratio_color($ratio)
{
    if ($ratio < 0.1) {
        return "#ff0000";
    }

    if ($ratio < 0.2) {
        return "#ee0000";
    }

    if ($ratio < 0.3) {
        return "#dd0000";
    }

    if ($ratio < 0.4) {
        return "#cc0000";
    }

    if ($ratio < 0.5) {
        return "#bb0000";
    }

    if ($ratio < 0.6) {
        return "#aa0000";
    }

    if ($ratio < 0.7) {
        return "#990000";
    }

    if ($ratio < 0.8) {
        return "#880000";
    }

    if ($ratio < 0.9) {
        return "#770000";
    }

    if ($ratio < 1) {
        return "#660000";
    }

    return "#000000";
}

function ratingpic($num)
{
    global $site_config;
    $r = round($num * 2) / 2;
    if ($r != $num) {
        $n = $num - $r;
        if ($n < .25) {
            $n = 0;
        } elseif ($n >= .25 && $n < .75) {
            $n = .5;
        }

        $r += $n;
    }
    if ($r < 1 || $r > 5) {
        return;
    }

    return "<img src=\"" . $site_config["SITEURL"] . "/images/rating/$r.png\" border=\"0\" alt=\"rating: $num/5\" title=\"rating: $num/5\" />";
}

/*array info for ref:
announce
infohash
creation date
intenal name
torrentsize
filecount
announceruls
comment
filelist
 */

// Torrent Information Retrieval Function With File Decoding
function ParseTorrent($filename)
{
    require_once "classes/BEcode.php";

    $TorrentInfo = array();

    global $array;

    //check file type is a torrent
    $torrent = explode(".", $filename);
    $fileend = end($torrent);
    $fileend = strtolower($fileend);

    if ($fileend == "torrent") {
        $parseme = @file_get_contents("$filename");

        if ($parseme == false) {
            show_error_msg(T_("ERROR"), T_("PARSE_CONTENTS"), 1);
        }

        if (!isset($parseme)) {
            show_error_msg(T_("ERROR"), T_("PARSE_OPEN"), 1);
        } else {
            $array = BDecode($parseme);
            if ($array === false) {
                show_error_msg(T_("ERROR"), T_("PARSE_DECODE"), 1);
            } else {
                if (!@count($array['info'])) {
                    show_error_msg(T_("ERROR"), T_("PARSE_OPEN"), 1);
                } else {
                    //Get Announce URL
                    $TorrentInfo[0] = $array["announce"];

                    //Get Announce List Array
                    if (isset($array["announce-list"])) {
                        $TorrentInfo[6] = $array["announce-list"];
                    }

                    //Read info, store as (infovariable)
                    $infovariable = $array["info"];

                    // Calculates SHA1 Hash
                    $infohash = sha1(BEncode($infovariable));
                    $TorrentInfo[1] = $infohash;

                    // Calculates date from UNIX Epoch
                    $makedate = date('r', $array["creation date"]);
                    $TorrentInfo[2] = $makedate;

                    // The name of the torrent is different to the file name
                    $TorrentInfo[3] = $infovariable['name'];

                    //Get File List
                    if (isset($infovariable["files"])) {
                        // Multi File Torrent
                        $filecount = "";

                        //Get filenames here
                        $TorrentInfo[8] = $infovariable["files"];

                        foreach ($infovariable["files"] as $file) {
                            if (is_numeric($filecount)) {
                                $filecount += "1";
                            }

                            $multiname = $file['path']; //Not needed here really
                            $multitorrentsize = $file['length'];
                            $torrentsize += $file['length'];
                        }

                        $TorrentInfo[4] = $torrentsize; //Add all parts sizes to get total
                        $TorrentInfo[5] = $filecount; //Get file count
                    } else {
                        // Single File Torrent
                        $torrentsize = $infovariable['length'];
                        $TorrentInfo[4] = $torrentsize; //Get file count
                        $TorrentInfo[5] = "1";
                    }

                    // Get Torrent Comment
                    if (isset($array['comment'])) {
                        $TorrentInfo[7] = $array['comment'];
                    }
                }
            }
        }
    }
    return $TorrentInfo;
} //End Function

// snatch
function seedtime($ts = 0)
{
    $days = floor($ts / 86400);
    $hours = floor($ts / 3600) % 24;
    $mins = floor($ts / 60) % 60;
    $secs = $ts % 60;
    return sprintf('%d days, %d hours, %d minutes, %d seconds...', $days, $hours, $mins, $secs);
}
