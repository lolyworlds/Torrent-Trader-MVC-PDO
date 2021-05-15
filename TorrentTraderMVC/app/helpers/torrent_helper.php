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
        // Bencode
        $stats = Bencode::decode($fp);
        //$stats = BDecode($fp);
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
    $db = new Database();
    $stmt = @$db->run("SELECT image1,image2 FROM torrents WHERE id=$id");
    $row = @$stmt->fetch(PDO::FETCH_ASSOC);
    foreach (explode(".", "peers.comments.ratings.files.announce") as $x) {
        $db->run("DELETE FROM $x WHERE torrent = $id");
    }
    $db->run("DELETE FROM completed WHERE torrentid = $id");
    if (file_exists(TORRENTDIR . "/$id.torrent")) {
        unlink(TORRENTDIR . "/$id.torrent");
    }
    if ($row["image1"]) {
        unlink(TORRENTDIR . "/images/" . $row["image1"]);
    }
    if ($row["image2"]) {
        unlink(TORRENTDIR . "/images/" . $row["image2"]);
    }
    @unlink(NFODIR . "/$id.nfo");
    $db->run("DELETE FROM torrents WHERE id = $id");
    $db->run("DELETE FROM reports WHERE votedfor = $id AND type = 'torrent'");
    // snatch
    $db->run("DELETE FROM `snatched` WHERE `tid` = '$id'");
}

// Function To Retrieve Main Categories Of Torrents
function genrelist()
{
    global $pdo;
    $ret = array();
    $res = DB::run("SELECT id, name, parent_cat FROM categories ORDER BY parent_cat ASC, sort_index ASC");
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $ret[] = $row;
    }

    return $ret;
}
// Function To Edit The List Of Possible Languages For Torrents
function langlist()
{
    $db = new Database();
    $ret = array();
    $stmt = $db->run("SELECT id, name, image FROM torrentlang ORDER BY sort_index, id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ret[] = $row;
    }
    return $ret;
}

function peerstable($res)
{
    global $config, $pdo;
    $ret = "<table align='center' cellpadding=\"3\" cellspacing=\"0\" class=\"table_table\" width=\"100%\" border=\"1\"><tr><th class='table_head'>" . Lang::T("NAME") . "</th><th class='table_head'>" . Lang::T("SIZE") . "</th><th class='table_head'>" . Lang::T("UPLOADED") . "</th>\n<th class='table_head'>" . Lang::T("DOWNLOADED") . "</th><th class='table_head'>" . Lang::T("RATIO") . "</th></tr>\n";

    while ($arr = $res->fetch(PDO::FETCH_LAZY)) {
        $res2 = DB::run("SELECT name,size FROM torrents WHERE id=? ORDER BY name", [$arr['torrent']]);
        $arr2 = $res2->fetch(PDO::FETCH_LAZY);
        if ($arr["downloaded"] > 0) {
            $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
        } else {
            $ratio = "---";
        }
        $ret .= "<tr><td class='table_col1'><a href=".URLROOT."torrents/read?id=$arr[torrent]&amp;hit=1'><b>" . htmlspecialchars($arr2["name"]) . "</b></a></td><td align='center' class='table_col2'>" . mksize($arr2["size"]) . "</td><td align='center' class='table_col1'>" . mksize($arr["uploaded"]) . "</td><td align='center' class='table_col2'>" . mksize($arr["downloaded"]) . "</td><td align='center' class='table_col1'>$ratio</td></tr>\n";
    }
    $ret .= "</table>\n";
    return $ret;
}

// Function To Display Tables Of Torrents
//function torrenttable($query)
function torrenttable($res)
{
    global $config, $THEME, $LANGUAGE, $pdo; //Define globals
    //$res = DB::run($query);
    if (MEMBERSONLY_WAIT && MEMBERSONLY && in_array($_SESSION["class"], explode(",", CLASS_WAIT))) {
        $gigs = $_SESSION["uploaded"] / (1024 * 1024 * 1024);
        $ratio = (($_SESSION["downloaded"] > 0) ? ($_SESSION["uploaded"] / $_SESSION["downloaded"]) : 0);
        if ($ratio < 0 || $gigs < 0) {
            $wait = A_WAIT;
        } elseif ($ratio < RATIOA || $gigs < GIGSA) {
            $wait = A_WAIT;
        } elseif ($ratio < RATIOB || $gigs < GIGSB) {
            $wait = B_WAIT;
        } elseif ($ratio < RATIOC || $gigs < GIGSC) {
            $wait = C_WAIT;
        } elseif ($ratio < RATIOD || $gigs < GIGSD) {
            $wait = D_WAIT;
        } else {
            $wait = 0;
        }

    }
    $wait = '';
    // Columns
    $cols = explode(",", TORRENTTABLE_COLUMNS);
    $cols = array_map("strtolower", $cols);
    $cols = array_map("trim", $cols);
    $colspan = count($cols);
    // End

    // Expanding Area
    $expandrows = array();
    if (!empty(TORRENTTABLE_EXPAND)) {
        $expandrows = explode(",", TORRENTTABLE_EXPAND);
        $expandrows = array_map("strtolower", $expandrows);
        $expandrows = array_map("trim", $expandrows);
    }
    // End
    echo '<div class="table-responsive"><table class="table table-striped"><thead><tr>';

    foreach ($cols as $col) {
        switch ($col) {
            case 'category':
                echo "<th>" . Lang::T("TYPE") . "</th>";
                break;
            case 'name':
                echo "<th>" . Lang::T("NAME") . "</th>";
                break;
            case 'dl':
                echo "<th>" . Lang::T("DL") . "</th>";
                break;
            case 'magnet':
                echo "<th>" . Lang::T("MAGNET2") . "</th>";
                break;
            case 'uploader':
                echo "<th>" . Lang::T("UPLOADER") . "</th>";
                break;
            case 'tube':
                echo "<th>" . Lang::T("YOUTUBE") . "</th>";
                break;
            case 'imdb':
                echo "<th>IMDB</th>";
                break;
            case 'comments':
                echo "<th>" . Lang::T("COMM") . "</th>";
                break;
            case 'nfo':
                echo "<th>" . Lang::T("NFO") . "</th>";
                break;
            case 'size':
                echo "<th>" . Lang::T("SIZE") . "</th>";
                break;
            case 'completed':
                echo "<th>" . Lang::T("C") . "</th>";
                break;
            case 'seeders':
                echo "<th>" . Lang::T("S") . "</th>";
                break;
            case 'leechers':
                echo "<th>" . Lang::T("L") . "</th>";
                break;
            case 'health':
                echo "<th>" . Lang::T("HEALTH") . "</th>";
                break;
            case 'external':
                if ($config["ALLOWEXTERNAL"]) {
                    echo "<th>" . Lang::T("L/E") . "</th>";
                }

                break;
            case 'added':
                echo "<th>" . Lang::T("ADDED") . "</th>";
                break;
            case 'speed':
                echo "<th>" . Lang::T("SPEED") . "</th>";
                break;
            case 'wait':
                if ($wait) {
                    echo "<th>" . Lang::T("WAIT") . "</th>";
                }

                break;
            case 'rating':
                echo "<th>" . Lang::T("RATINGS") . "</th>";
                break;
        }
    }
    if ($wait && !in_array("wait", $cols)) {
        echo "<th>" . Lang::T("WAIT") . "</th>";
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
                        print("<a href=\"".URLROOT."/search/browse?cat=" . $row["category"] . "\">");
                        if (!empty($row["cat_pic"]) && $row["cat_pic"] != "") {
                            print("<img border=\"0\"src=\"".URLROOT."/assets/images/categories/" . $row["cat_pic"] . "\" alt=\"" . $row["cat_name"] . "\" />");
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
                    $char1 = 50; //cut name length
                    $smallname = htmlspecialchars(CutName($row["name"], $char1));
                    $dispname = "<b>" . $smallname . "</b>";

                    $last_access = $_SESSION["last_browse"];
                    $time_now = TimeDate::gmtime();
                    if ($last_access > $time_now || !is_numeric($last_access)) {
                        $last_access = $time_now;
                    }

                    if (TimeDate::sql_timestamp_to_unix_timestamp($row["added"]) >= $last_access) {
                        $dispname .= "<b><font color='#ff0000'> - (" . Lang::T("NEW") . "!)</font></b>";
                    }

                    if ($row["freeleech"] == 1) {
                        $dispname .= " <img src='".URLROOT."/assets/images/free.gif' border='0' alt='' />";
                    }
                    if ($row["vip"] == "yes") {
                        $dispname .= " <img src='".URLROOT."/assets/images/vip.gif' border='0' alt='' />";
                    }
                    if ($row["sticky"] == "yes") {
                        $dispname .= " <img src='".URLROOT."/assets/images/sticky.gif' bored='0' alt='sticky' title='sticky'>";
                    }
                    print("<td class='ttable_col$x' nowrap='nowrap'>" . (count($expandrows) ? "<a href=\"javascript: klappe_torrent('t" . $row['id'] . "')\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/plus.gif\" id=\"pict" . $row['id'] . "\" alt=\"Show/Hide\" class=\"showthecross\" /></a>" : "") . "&nbsp;<a title=\"" . $row["name"] . "\" href=\"" . URLROOT . "/torrents/read?id=$id&amp;hit=1\">$dispname</a></td>");

                    break;
                case 'dl':
                    print("<td class='ttable_col$x' align='center'><a href=\"".URLROOT."/download?id=$id&amp;name=" . rawurlencode($row["filename"]) . "\"><img src='" . URLROOT . "/assets/images/icon_download.gif' border='0' alt=\"Download .torrent\" /></a></td>");
                    break;
                case 'magnet':
                    $magnet = DB::run("SELECT info_hash FROM torrents WHERE id=?", [$id])->fetch();
                    // Like Mod
                    if (!FORCETHANKS) {
                        print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . $_SESSION['passkey'] . "\"><img src='" . URLROOT . "/assets/images/magnetique.png' border='0' title='Download via Magnet' /></a></td>");
                    } elseif (FORCETHANKS) {
                        $data = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$id, 'torrent', $_SESSION['id']]);
                        $like = $data->fetch(PDO::FETCH_ASSOC);
                        if ($like) {
                            if ($_SESSION["can_download"] != "no") {
                            print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . $_SESSION['passkey'] . "\"><img src='" . URLROOT . "/assets/images/magnetique.png' border='0' title='Download via Magnet' /></a></td>");
                            } else {
                            print("<td class='ttable_col$x' align='center'></td>");
                            }
						} elseif ($_SESSION["id"] == $row["owner"]) {
                            print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . $_SESSION['passkey'] . "\"><img src='" . URLROOT . "/assets/images/magnetique.png' border='0' title='Download via Magnet' /></a></td>");
                        } else {
                            print("<td class='ttable_col$x' align='center'><a href=".URLROOT."likes/index?id=$id' ><button  class='btn btn-sm btn-danger'>Thanks</button></td>");
                        }
                    }
                    break;
                case 'uploader':
                    echo "<td class='ttable_col$x' align='center'>";
                    if (($row["anon"] == "yes" || $row["privacy"] == "strong") && $_SESSION["id"] != $row["owner"] && $_SESSION["edit_torrents"] != "yes") {
                        echo "Anonymous";
                    } elseif ($row["username"]) {
                        echo "<a href='" . URLROOT . "/profile?id=$row[owner]'>" . Users::coloredname($row['username']) . "</a>";
                    } else {
                        echo "Unknown";
                    }

                    echo "</td>";
                    break;
                case 'tube':
                    if ($row["tube"]) {
                        print("<td class='ttable_col$x' align='center'><a rel=\"prettyPhoto\"  href=" . $row['tube'] . " ><" . htmlspecialchars($row['tube']) . "><img src='" . URLROOT . "/assets/images/youtube1.png'  border='0' width='20' height='20' alt=\"\" /></a></td>");
                    } else {
                        print("<td class='ttable_colx' align='center'>-</td>");
                    }

                    break;
                case 'imdb':
                    if ($row["imdb"]) {
                        print("<td class='ttable_col$x' align='center'><a href=" . $row['imdb'] . " target='_blank'><" . htmlspecialchars($row['imdb']) . "><img src='" . URLROOT . "/assets/images/imdb.png'  border='0' width='20' height='20' alt=\"\" /></a></td>");
                    } else {
                        print("<td class='ttable_colx' align='center'>-</td>");
                    }

                    break;
                case 'comments':
                    print("<td class='ttable_col$x' align='center'><font size='1' face='verdana'><a href=".URLROOT."comments?type=torrent&amp;id=$id'>" . number_format($row["comments"]) . "</a></font></td>\n");
                    break;
                case 'nfo':
                    if ($row["nfo"] == "yes") {
                        print("<td class='ttable_col$x' align='center'><a href=".URLROOT."nfo?id=$row[id]'><img src='" . URLROOT . "/assets/images/icon_nfo.gif' border='0' alt='View NFO' /></a></td>");
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
                    print("<td class='ttable_col$x' align='center'><img src='" . URLROOT . "/assets/images/health/health_" . health($row["leechers"], $row["seeders"]) . ".gif' alt='' /></td>\n");
                    break;
                case 'external':
                    if ($config["ALLOWEXTERNAL"]) {
                        if ($row["external"] == 'yes') {
                            print("<td class='ttable_col$x' align='center'>" . Lang::T("E") . "</td>\n");
                        } else {
                            print("<td class='ttable_col$x' align='center'>" . Lang::T("L") . "</td>\n");
                        }

                    }
                    break;
                case 'added':
                    //print("<td class='ttable_col$x' align='center'>" . date("d-m-Y H:i:s", TimeDate::utc_to_tz_time($row['added'])) . "</td>");
                    print("<td class='ttable_col$x' align='center'>" . TimeDate::get_time_elapsed($row['added']) . "</td>");
                    break;
                case 'speed':
                    if ($row["external"] != "yes" && $row["leechers"] >= 1) {
                        $speedQ = $pdo->run("SELECT (SUM(downloaded)) / (UNIX_TIMESTAMP('" . TimeDate::get_date_time() . "') - UNIX_TIMESTAMP(started)) AS totalspeed FROM peers WHERE seeder = 'no' AND torrent = '$id' ORDER BY started ASC");
                        $a = $speedQ->fetch(PDO::FETCH_LAZY);
                        $totalspeed = mksize($a["totalspeed"]) . "/s";
                    } else {
                        $totalspeed = "--";
                    }

                    print("<td class='ttable_col$x' align='center'>$totalspeed</td>");
                    break;
                case 'wait':
                    if ($wait) {
                        $elapsed = floor((TimeDate::gmtime() - strtotime($row["added"])) / 3600);
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
            $elapsed = floor((TimeDate::gmtime() - strtotime($row["added"])) / 3600);
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
                        print("<tr><td><b>" . Lang::T("SIZE") . "</b>: " . mksize($row['size']) . "</td></tr>");
                        break;
                    case 'speed':
                        if ($row["external"] != "yes" && $row["leechers"] >= 1) {
                            $speedQ = $pdo->run("SELECT (SUM(downloaded)) / (UNIX_TIMESTAMP('" . TimeDate::get_date_time() . "') - UNIX_TIMESTAMP(started)) AS totalspeed FROM peers WHERE seeder = 'no' AND torrent = '$id' ORDER BY started ASC");
                            $a = $speedQ->fetch(PDO::FETCH_LAZY);
                            $totalspeed = mksize($a["totalspeed"]) . "/s";
                            print("<tr><td><b>" . Lang::T("SPEED") . ":</b> $totalspeed</td></tr>");
                        }
                        break;
                    case 'added':
                        print("<tr><td><b>" . Lang::T("ADDED") . ":</b> " . date("d-m-Y \\a\\t H:i:s", TimeDate::utc_to_tz_time($row['added'])) . "</td></tr>");
                        break;
                    case 'tracker':
                        if ($row["external"] == "yes") {
                            print("<tr><td><b>" . Lang::T("TRACKER") . ":</b> " . htmlspecialchars($row["announce"]) . "</td></tr>");
                        }

                        break;
                    case 'completed':
                        print("<tr><td><b>" . Lang::T("COMPLETED") . "</b>: " . number_format($row['times_completed']) . "</td></tr>");
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

    return "<img src=\"" . URLROOT . "/assets/images/rating/$r.png\" border=\"0\" alt=\"rating: $num/5\" title=\"rating: $num/5\" />";
}

// Function To Display Tables Of Torrents
function torrenttable1($query)
{
    $res = DB::run($query);
    if (MEMBERSONLY_WAIT && MEMBERSONLY && in_array($_SESSION["class"], explode(",", CLASS_WAIT))) {
        $gigs = $_SESSION["uploaded"] / (1024 * 1024 * 1024);
        $ratio = (($_SESSION["downloaded"] > 0) ? ($_SESSION["uploaded"] / $_SESSION["downloaded"]) : 0);
        if ($ratio < 0 || $gigs < 0) {
            $wait = A_WAIT;
        } elseif ($ratio < RATIOA || $gigs < GIGSA) {
            $wait = A_WAIT;
        } elseif ($ratio < RATIOB || $gigs < GIGSB) {
            $wait = B_WAIT;
        } elseif ($ratio < RATIOC || $gigs < GIGSC) {
            $wait = C_WAIT;
        } elseif ($ratio < RATIOD || $gigs < GIGSD) {
            $wait = D_WAIT;
        } else {
            $wait = 0;
        }
    }
    $wait = '';
    // Columns
    $cols = explode(",", TORRENTTABLE_COLUMNS);
    $cols = array_map("strtolower", $cols);
    $cols = array_map("trim", $cols);
    $colspan = count($cols);
    // End

    // Expanding Area
    $expandrows = array();
    if (!empty(TORRENTTABLE_EXPAND)) {
        $expandrows = explode(",", TORRENTTABLE_EXPAND);
        $expandrows = array_map("strtolower", $expandrows);
        $expandrows = array_map("trim", $expandrows);
    }
    // End
    echo '<div class="table-responsive"><table class="table table-striped"><thead><tr>';

    foreach ($cols as $col) {
        switch ($col) {
            case 'category':
                echo "<th>" . Lang::T("TYPE") . "</th>";
                break;
            case 'name':
                echo "<th>" . Lang::T("NAME") . "</th>";
                break;
            case 'dl':
                echo "<th>" . Lang::T("DL") . "</th>";
                break;
            case 'magnet':
                echo "<th>" . Lang::T("MAGNET2") . "</th>";
                break;
            case 'uploader':
                echo "<th>" . Lang::T("UPLOADER") . "</th>";
                break;
            case 'tube':
                echo "<th>" . Lang::T("YOUTUBE") . "</th>";
                break;
            case 'imdb':
                echo "<th>IMDB</th>";
                break;
            case 'comments':
                echo "<th>" . Lang::T("COMM") . "</th>";
                break;
            case 'nfo':
                echo "<th>" . Lang::T("NFO") . "</th>";
                break;
            case 'size':
                echo "<th>" . Lang::T("SIZE") . "</th>";
                break;
            case 'completed':
                echo "<th>" . Lang::T("C") . "</th>";
                break;
            case 'seeders':
                echo "<th>" . Lang::T("S") . "</th>";
                break;
            case 'leechers':
                echo "<th>" . Lang::T("L") . "</th>";
                break;
            case 'health':
                echo "<th>" . Lang::T("HEALTH") . "</th>";
                break;
            case 'external':
                if (ALLOWEXTERNAL) {
                    echo "<th>" . Lang::T("L/E") . "</th>";
                }

                break;
            case 'added':
                echo "<th>" . Lang::T("ADDED") . "</th>";
                break;
            case 'speed':
                echo "<th>" . Lang::T("SPEED") . "</th>";
                break;
            case 'wait':
                if ($wait) {
                    echo "<th>" . Lang::T("WAIT") . "</th>";
                }

                break;
            case 'rating':
                echo "<th>" . Lang::T("RATINGS") . "</th>";
                break;
        }
    }
    if ($wait && !in_array("wait", $cols)) {
        echo "<th>" . Lang::T("WAIT") . "</th>";
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
                        print("<a href=\"".URLROOT."/search/browse?cat=" . $row["category"] . "\">");
                        if (!empty($row["cat_pic"]) && $row["cat_pic"] != "") {
                            print("<img border=\"0\"src=\"".URLROOT."/assets/images/categories/" . $row["cat_pic"] . "\" alt=\"" . $row["cat_name"] . "\" />");
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
                    $char1 = 50; //cut name length
                    $smallname = htmlspecialchars(CutName($row["name"], $char1));
                    $dispname = "<b>" . $smallname . "</b>";
                    $last_access = $_SESSION["last_browse"];
                    $time_now = TimeDate::gmtime();
                    if ($last_access > $time_now || !is_numeric($last_access)) {
                        $last_access = $time_now;
                    }
                    if (TimeDate::sql_timestamp_to_unix_timestamp($row["added"]) >= $last_access) {
                        $dispname .= "<b><font color='#ff0000'> - (" . Lang::T("NEW") . "!)</font></b>";
                    }
                    if ($row["freeleech"] == 1) {
                        $dispname .= " <img src='".URLROOT."/assets/images/free.gif' border='0' alt='' />";
                    }
                    if ($row["vip"] == "yes") {
                        $dispname .= " <img src='".URLROOT."/assets/images/vip.gif' border='0' alt='' />";
                    }
                    if ($row["sticky"] == "yes") {
                        $dispname .= " <img src='".URLROOT."/assets/images/sticky.gif' bored='0' alt='sticky' title='sticky'>";
                    }
                    print("<td class='ttable_col$x' nowrap='nowrap'>" . (count($expandrows) ? "<a href=\"javascript: klappe_torrent('t" . $row['id'] . "')\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/plus.gif\" id=\"pict" . $row['id'] . "\" alt=\"Show/Hide\" class=\"showthecross\" /></a>" : "") . "&nbsp;<a title=\"" . $row["name"] . "\" href=\"" . URLROOT . "/torrents/read?id=$id&amp;hit=1\">$dispname</a></td>");

                    break;
                case 'dl':
                    print("<td class='ttable_col$x' align='center'><a href=\"".URLROOT."//download?id=$id&amp;name=" . rawurlencode($row["filename"]) . "\"><img src='" . URLROOT . "/assets/images/icon_download.gif' border='0' alt=\"Download .torrent\" /></a></td>");
                    break;
                case 'magnet':
                    $magnet = DB::run("SELECT info_hash FROM torrents WHERE id=?", [$id])->fetch();
                    // Like Mod
                    if (!FORCETHANKS) {
                        print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . $_SESSION['passkey'] . "\"><img src='" . URLROOT . "/assets/images/magnetique.png' border='0' title='Download via Magnet' /></a></td>");
                    } elseif (FORCETHANKS) {
                        $data = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$id, 'torrent', $_SESSION['id']]);
                        $like = $data->fetch(PDO::FETCH_ASSOC);
                        if ($like) {
                        if ($_SESSION["can_download"] != "no") {
                            print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . $_SESSION['passkey'] . "\"><img src='" . URLROOT . "/assets/images/magnetique.png' border='0' title='Download via Magnet' /></a></td>");
                            } else {
                            print("<td class='ttable_col$x' align='center'></td>");
                            }
						} elseif ($_SESSION["id"] == $row["owner"]) {
                            print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . $_SESSION['passkey'] . "\"><img src='" . URLROOT . "/assets/images/magnetique.png' border='0' title='Download via Magnet' /></a></td>");
                        } else {
                            print("<td class='ttable_col$x' align='center'><a href='".URLROOT."/likes/index?id=$id' ><button  class='btn btn-sm btn-danger'>Thanks</button></td>");
                        }
                    }
                    break;
                case 'uploader':
                    echo "<td class='ttable_col$x' align='center'>";
                    if (($row["anon"] == "yes" || $row["privacy"] == "strong") && $_SESSION["id"] != $row["owner"] && $_SESSION["edit_torrents"] != "yes") {
                        echo "Anonymous";
                    } elseif ($row["username"]) {
                        echo "<a href='" . URLROOT . "/profile?id=$row[owner]'>" . Users::coloredname($row['username']) . "</a>";
                    } else {
                        echo "Unknown";
                    }

                    echo "</td>";
                    break;
                case 'tube':
                    if ($row["tube"]) {
                        print("<td class='ttable_col$x' align='center'><a rel=\"prettyPhoto\"  href=" . $row['tube'] . " ><" . htmlspecialchars($row['tube']) . "><img src='" . URLROOT . "/assets/images/youtube1.png'  border='0' width='20' height='20' alt=\"\" /></a></td>");
                    } else {
                        print("<td class='ttable_colx' align='center'>-</td>");
                    }

                    break;
                case 'imdb':
                    if ($row["imdb"]) {
                        print("<td class='ttable_col$x' align='center'><a href=" . $row['imdb'] . " target='_blank'><" . htmlspecialchars($row['imdb']) . "><img src='" . URLROOT . "/assets/images/imdb.png'  border='0' width='20' height='20' alt=\"\" /></a></td>");
                    } else {
                        print("<td class='ttable_colx' align='center'>-</td>");
                    }

                    break;
                case 'comments':
                    print("<td class='ttable_col$x' align='center'><font size='1' face='verdana'><a href='".URLROOT."/comments?type=torrent&amp;id=$id'>" . number_format($row["comments"]) . "</a></font></td>\n");
                    break;
                case 'nfo':
                    if ($row["nfo"] == "yes") {
                        print("<td class='ttable_col$x' align='center'><a href='".URLROOT."/nfo?id=$row[id]'><img src='" . URLROOT . "/assets/images/icon_nfo.gif' border='0' alt='View NFO' /></a></td>");
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
                    print("<td class='ttable_col$x' align='center'><img src='" . URLROOT . "/assets/images/health/health_" . health($row["leechers"], $row["seeders"]) . ".gif' alt='' /></td>\n");
                    break;
                case 'external':
                    if (ALLOWEXTERNAL) {
                        if ($row["external"] == 'yes') {
                            print("<td class='ttable_col$x' align='center'>" . Lang::T("E") . "</td>\n");
                        } else {
                            print("<td class='ttable_col$x' align='center'>" . Lang::T("L") . "</td>\n");
                        }

                    }
                    break;
                case 'added':
                    //print("<td class='ttable_col$x' align='center'>" . date("d-m-Y H:i:s", TimeDate::utc_to_tz_time($row['added'])) . "</td>");
                    print("<td class='ttable_col$x' align='center'>" . TimeDate::get_time_elapsed($row['added']) . "</td>");
                    break;
                case 'speed':
                    if ($row["external"] != "yes" && $row["leechers"] >= 1) {
                        $speedQ = DB::run("SELECT (SUM(downloaded)) / (UNIX_TIMESTAMP('" . TimeDate::get_date_time() . "') - UNIX_TIMESTAMP(started)) AS totalspeed FROM peers WHERE seeder = 'no' AND torrent = '$id' ORDER BY started ASC");
                        $a = $speedQ->fetch(PDO::FETCH_LAZY);
                        $totalspeed = mksize($a["totalspeed"]) . "/s";
                    } else {
                        $totalspeed = "--";
                    }
                    print("<td class='ttable_col$x' align='center'>$totalspeed</td>");
                    break;
                case 'wait':
                    if ($wait) {
                        $elapsed = floor((TimeDate::gmtime() - strtotime($row["added"])) / 3600);
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
            $elapsed = floor((TimeDate::gmtime() - strtotime($row["added"])) / 3600);
            if ($elapsed < $wait && $row["external"] != "yes") {
                $color = dechex(floor(127 * ($wait - $elapsed) / 48 + 128) * 65536);
                print("<td class='ttable_col$x' align='center'><a href=\"".URLROOT."/faq\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></td>\n");
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
                        print("<tr><td><b>" . Lang::T("SIZE") . "</b>: " . mksize($row['size']) . "</td></tr>");
                        break;
                    case 'speed':
                        if ($row["external"] != "yes" && $row["leechers"] >= 1) {
                            $speedQ = DB::run("SELECT (SUM(downloaded)) / (UNIX_TIMESTAMP('" . TimeDate::get_date_time() . "') - UNIX_TIMESTAMP(started)) AS totalspeed FROM peers WHERE seeder = 'no' AND torrent = '$id' ORDER BY started ASC");
                            $a = $speedQ->fetch(PDO::FETCH_LAZY);
                            $totalspeed = mksize($a["totalspeed"]) . "/s";
                            print("<tr><td><b>" . Lang::T("SPEED") . ":</b> $totalspeed</td></tr>");
                        }
                        break;
                    case 'added':
                        print("<tr><td><b>" . Lang::T("ADDED") . ":</b> " . date("d-m-Y \\a\\t H:i:s", TimeDate::utc_to_tz_time($row['added'])) . "</td></tr>");
                        break;
                    case 'tracker':
                        if ($row["external"] == "yes") {
                            print("<tr><td><b>" . Lang::T("TRACKER") . ":</b> " . htmlspecialchars($row["announce"]) . "</td></tr>");
                        }
                        break;
                    case 'completed':
                        print("<tr><td><b>" . Lang::T("COMPLETED") . "</b>: " . number_format($row['times_completed']) . "</td></tr>");
                        break;
                }
            }
            print("</table></div></td></tr>\n");
        }
        //End Expanding Area
    }
    print("</table></div><br />\n");
}