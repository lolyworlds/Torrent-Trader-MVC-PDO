<?php
class Imdbs extends Controller
{
    public function __construct()
    {
        $this->torrentModel = $this->model('Torrent');
    }

        public function index()
        {
            dbconn();
            global $config, $pdo;
            require_once("classes/BDecode.php");
            require_once("classes/BEncode.php");
            $torrent_dir = $config["torrent_dir"];
            $nfo_dir = $config["nfo_dir"];
    
            //check permissions
            if ($config["MEMBERSONLY"]) {
                loggedinonly();
            }
            if ($_SESSION["view_torrents"] == "no") {
                show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
            }
    
            $id = (int) $_GET["id"];
            $scrape = (int) $_GET["scrape"];
    
            if (!is_valid_id($id)) {
                show_error_msg(T_("ERROR"), T_("THATS_NOT_A_VALID_ID"), 1);
            }
    
            //GET ALL MYSQL VALUES FOR THIS TORRENT
            $res = $pdo->run("SELECT torrents.anon, torrents.seeders, torrents.tube, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.imdb, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
            $row = $res->fetch(PDO::FETCH_ASSOC);
    
            //DECIDE IF TORRENT EXISTS
            if (!$row || ($row["banned"] == "yes" && $_SESSION["edit_torrents"] == "no")) {
                show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);
            }
    
            //torrent is availiable so do some stuff
            if ($_GET["hit"]) {
                $pdo->run("UPDATE torrents SET views = views + 1 WHERE id = $id");
                header("Location: " . TTURL . "/torrents/read?id=$id");
                die;
            }
    
            if ($_SESSION["id"] == $row["owner"] || $_SESSION["edit_torrents"] == "yes") {
                $owned = 1;
            } else {
                $owned = 0;
            }
            $char1 = 50; //cut length
            $shortname = CutName(htmlspecialchars($row["name"]), $char1);
    
            // Calculate local torrent speed test
            if ($row["leechers"] >= 1 && $row["seeders"] >= 1 && $row["external"] != 'yes') {
                $speedQ = $pdo->run("SELECT (SUM(p.downloaded)) / (UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$id' GROUP BY t.id ORDER BY added ASC LIMIT 15");
                $a = $speedQ->fetch(PDO::FETCH_ASSOC);
                $totalspeed = mksize($a["totalspeed"]) . "/s";
            } else {
                $totalspeed = T_("NO_ACTIVITY");
            }
       
            stdhead(T_("DETAILS_FOR_TORRENT") . " \"" . $row["name"] . "\"");
            begin_frame(T_("TORRENT_DETAILS_FOR") . " \"" . $shortname . "\"");
            include("views/torrent/torrentnavbar.php");
            if ($config["imdb"]) {        
            $TTIMDB = new TTIMDB();
            $TTCache = new Cache();
            if ((($_data = $TTCache->Get("imdb/$id", 900)) === false) && ($_data = $TTIMDB->Get($row['imdb']))) {
                $_data->Poster = $TTIMDB->getImage($_data->Poster, $id);
                if (! isset($_data->imdbTime)) {
                    $_data->imdbTime = time();
                    $_data->Alias = 'N/A';
                    $_data->imdbVideo = null;
                }
                $TTCache->Set("imdb/$id", $_data, 900);
            }
            if (is_object($_data)): ?>
             <fieldset class="download">
             <table border="0" cellpadding="3" cellspacing="2" width="100%">
         <tr>
             <td width="15%" valign="top"><br><br><img src="<?php echo $_data->Poster; ?>" alt="<?php echo $_data->Title; ?>" title="<?php echo $_data->Title; ?>" height="317px" width="214px" /><br>
         <?php    print("<br><b>" . T_("HEALTH") . ": </b><img src='" . $config["SITEURL"] . "/images/health/health_" . health($row["leechers"], $row["seeders"]) . ".gif' alt='' /><br />");
            print("<b>" . T_("SEEDS") . ": </b><font color='green'>" . number_format($row["seeders"]) . "</font><br />");
            print("<b>" . T_("LEECHERS") . ": </b><font color='#ff0000'>" . number_format($row["leechers"]) . "</font><br />");
            if ($row["external"] != 'yes') {
                print("<b>" . T_("SPEED") . ": </b>" . $totalspeed . "<br />");
            }
            print("<b>" . T_("COMPLETED") . ":</b> " . number_format($row["times_completed"]) . "&nbsp;");
            if ($row["external"] != "yes" && $row["times_completed"] > 0) {
                echo ("[<a href='$config[SITEURL]/torrents/completed?id=$id'>" . T_("WHOS_COMPLETED") . "</a>] ");
                if ($row["seeders"] <= 1) {
                    echo ("[<a href='$config[SITEURL]/torrents/reseed?id=$id'>" . T_("REQUEST_A_RE_SEED") . "</a>]");
                }
            }
            echo "<br />";

            if ($row["external"] != 'yes' && $row["freeleech"] == '1') {
                print("<b>" . T_("FREE_LEECH") . ": </b><font color='#ff0000'>" . T_("FREE_LEECH_MSG") . "</font><br />");
            }

            print("<b>" . T_("LAST_CHECKED") . ": </b>" . date("d-m-Y H:i:s", utc_to_tz_time($row["last_action"])) . "<br><br>");
                            // Like Mod
            if (!$config["forcethanks"]) {
                // Magnet
                if ($row["external"] == 'yes') {
                    print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                } else {
                    print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=" . $config['SITEURL'] . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                }
            }
            if ($_SESSION["id"] != $row["owner"] && $config["forcethanks"]) {
                $data = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$id, 'torrent', $_SESSION['id']]);
                $like = $data->fetch(PDO::FETCH_ASSOC);
                if ($like) {
                    // Magnet
                    if ($row["external"] == 'yes') {
                        print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                    } else {
                        print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=" . $config['SITEURL'] . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                    }
                } else {
                    print("<a href='$config[SITEURL]/likes/details?id=$id'><button  class='btn btn-sm btn-danger'>Thanks</button></a>&nbsp;");
                }
            } else {
                if ($row["external"] == 'yes') {
                    print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                } else {
                    print("<a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . $row["filename"] . "&tr=" . $config['SITEURL'] . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
                }
            }

            print("&nbsp;<a href=\"$config[SITEURL]/download?id=$id&amp;name=" . rawurlencode($row["filename"]) . "\"><button type='button' class='btn btn-sm btn-success'>" . T_("DOWNLOAD_TORRENT") . "</button></a><br><br>");
            if ($row["image1"] != "" or $row["image2"] != "") {
                if ($row["image1"] != "") {
                    $img1 = "<img src='" . $config["SITEURL"] . "/uploads/images/$row[image1]' height='80' width='80' border='0' alt='' />";
                }
                if ($row["image2"] != "") {
                    $img2 = "<img src='" . $config["SITEURL"] . "/uploads/images/$row[image2]' height='80' width='80' border='0' alt='' />";
                }
                print("" . $img1 . "&nbsp;&nbsp;" . $img2 . "<br />");
            }        
?>
             <br>
             </td>
             <td  width="60%" valign="top">
             <b><?php echo T_("LINK"); ?></b><br> <a href="<?php echo $row['imdb']; ?>" target="_blank"><?php echo htmlspecialchars($row['imdb']); ?></a><br>
             <b><?php echo T_("RATED"); ?></b><br> <?php echo $TTIMDB->getRated($_data->Rated); ?><br>
             <b><?php echo T_("ALIAS"); ?></b><br> <?php echo $_data->Alias; ?><br>
             <b><?php echo T_("RELEASED"); ?></b><br> <?php echo $TTIMDB->getReleased($_data->Released); ?><br>
             <b><?php echo T_("YEAR"); ?></b><br> <?php echo $_data->Year; ?><br>
             <b><?php echo T_("RUNTIME"); ?></b><br> <?php echo $_data->Runtime; ?><br>
             <b><?php echo T_("GENRE"); ?></b><br> <?php echo $_data->Genre; ?><br>
             <b><?php echo T_("DIRECTOR"); ?></b><br> <?php echo $_data->Director; ?><br>
             <b><?php echo T_("WRITER"); ?></b><br> <?php echo $_data->Writer; ?><br>
             <b><?php echo T_("ACTORS"); ?></b><br> <?php echo $_data->Actors; ?><br>
             <b><?php echo T_("PLOT"); ?></b><br> <?php echo $_data->Plot; ?>
             </td>
             
             <td width="25%" valign="top"><center>
           <?php      if (($rating = $TTIMDB->getRating($_data->imdbRating)) !== null):
    echo $rating; ?>&nbsp;
             <b><?php echo T_("VOTES"); ?></b> <?php echo $_data->imdbVotes; ?></center><br>
             <?php endif;
              if (!empty($row["tube"])) { 
             print ("<embed src='". str_replace("watch?v=", "v/", htmlspecialchars($row["tube"])) ."' type=\"application/x-shockwave-flash\" width=\"400\" height=\"310\"></embed>&nbsp;&nbsp;&nbsp;<br>");
               } ?>

             </td>
            </tr>
          <tr>
             <td align="right" colspan="3">
             <b><?php echo T_("Last Updated"); ?></b> <i><?php echo $TTIMDB->getUpdated($_data->imdbTime); ?></i>
          </td>
          </tr>
          </table>
          </fieldset>
    
          <?php if ($owned) {
                echo "<a href='$config[SITEURL]/torrents/edit?id=$row[id]&amp;returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "'><button type='button' class='btn btn-sm btn-success'><b>" . T_("EDIT_TORRENT") . "</b></button></a>&nbsp;";
            }?>
            <a href="<?php echo $config['SITEURL'] ?>/report/torrent?torrent=<?php echo $id; ?>"><button type='button' class='btn btn-sm btn-danger'><?php echo T_("REPORT_TORRENT") ?></button></a>&nbsp;
        <?php if ($_SESSION["edit_users"] == "yes") {?>
      <a href="<?php echo $config['SITEURL']; ?>/snatched?tid=<?php echo $row['id']; ?>"><button type='button' class='btn btn-sm btn-warning'><?php echo T_("SNATCHLIST") ?></button></a>
    <?php }?>
    <?php if ($_SESSION["delete_torrents"] == "yes") {?>
      <a href="<?php echo $config['SITEURL']; ?>/torrents/delete?id=<?php echo $row['id']; ?>"><button type='button' class='btn btn-sm btn-warning'><?php echo T_("Delete") ?></button></a>
    <?php }?>

    <?php if (strlen($_data->imdbVideo) > 3): ?>
             <center><iframe src="http://www.imdb.com/video/imdb/<?php echo $_data->imdbVideo; ?>/player?stop=1" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" height="480" width="643"></iframe></center><br />
    <?php endif;
            endif;
        }
            end_frame();
    
            stdfoot();
        }
}