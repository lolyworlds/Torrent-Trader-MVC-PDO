<?php
Style::header($data['title']);
Style::begin($data['title']);
foreach ($data['selecttor'] as $torr):
torrentmenu($data['id'], $torr['external']);
?>
<div class="jumbotron">
<div class="row">
    <div class="col">

        <b><?php echo Lang::T("DESCRIPTION"); ?>:</b>&nbsp;<?php echo format_comment($torr['descr']); ?><br>
        <b><?php echo Lang::T("CATEGORY"); ?>:</b>&nbsp;<?php echo $torr["cat_parent"]; ?> -> <?php echo $torr["cat_name"]; ?><br>
        <?php
        if (empty($torr["lang_name"])) {
            $torr["lang_name"] = "Unknown/NA";
        }?>
        <b><?php echo Lang::T("LANG"); ?>:</b>&nbsp;<?php echo $torr["lang_name"]; ?><br>
         <?php
        if (isset($torr["lang_image"]) && $torr["lang_image"] != "") {
            print("&nbsp;<img border=\"0\" src=\"images/languages/" . $torr["lang_image"] . "\" alt=\"" . $torr["lang_name"] . "\" />");
        }?>
        <b><?php echo Lang::T("TOTAL_SIZE"); ?>:</b>&nbsp;<?php echo mksize($torr["size"]); ?><br>
        <b><?php echo Lang::T("INFO_HASH"); ?>:</b>&nbsp;<?php echo $torr["info_hash"]; ?><br>
        <?php if ($torr["anon"] == "yes" && !$torr['owned']) {?>
            <b><?php echo Lang::T("ADDED_BY"); ?>:</b>&nbsp; Anonymous<br>
        <?php } elseif ($torr["username"]) {?>
            <b><?php echo Lang::T("ADDED_BY"); ?>:</b>&nbsp;<a href='profile?id=<?php echo $torr["owner"]; ?>'><?php echo Users::coloredname($torr["username"]); ?></a><br>
        <?php } else {?>
            <b><?php echo Lang::T("ADDED_BY"); ?>:</b>&nbsp; Unknown<br>
        <?php }?>
        <b><?php echo Lang::T("FREELEECH"); ?>:</b>&nbsp;<?php echo $torr['freeleech']; ?><br>
        <b><?php echo Lang::T("DATE_ADDED"); ?>:</b>&nbsp;<?php echo date("d-m-Y H:i:s", TimeDate::utc_to_tz_time($torr["added"])); ?><br>
        <b><?php echo Lang::T("VIEWS"); ?>:</b>&nbsp;<?php echo number_format($torr["views"]); ?><br>
        <b><?php echo Lang::T("HITS"); ?>:</b>&nbsp;<?php echo number_format($torr["hits"]); ?><br>
        <?php // LIKE MOD
        if (ALLOWLIKES) {
            $data1 = DB::run("SELECT user FROM likes WHERE liked=? AND type=? AND user=? AND reaction=?", [$torr['id'], 'torrent', $_SESSION['id'], 'like']);
            $likes = $data1->fetch(PDO::FETCH_ASSOC);
            if ($likes) {?>
                <b>Reaction:</b>&nbsp;<a href='<?php echo URLROOT; ?>/likes/unliketorrent?id=<?php echo $torr['id']; ?>'><img src='<?php echo URLROOT; ?>/assets/images/unlike.png' width='80' height='40' border='0'></a><br>
            <?php } else {?>
                <b>Reaction:</b>&nbsp;<a href='<?php echo URLROOT; ?>/likes/liketorrent?id=<?php echo $torr['id']; ?>'><img src='<?php echo URLROOT; ?>/assets/images/like.png' width='80' height='40' border='0'></a><br>
            <?php }
        }
        if (ALLOWLIKES) {
            $data3 = DB::run("SELECT * FROM `users` AS u LEFT JOIN `likes` AS l ON(u.id = l.user) WHERE liked=? AND type=?", [$torr['id'], 'torrent']);
            print ('<b>Liked by</b>&nbsp;');
            foreach ($data3 as $stmt): 
                print ("<a href='".URLROOT."/profile?id=$stmt[id]'>".Users::coloredname($stmt['username'])."</a>&nbsp;");
            endforeach;
        }
        echo "<br />";
        if ($torr["external"] != 'yes' && $torr["freeleech"] == '1') {
            print("<b>" . Lang::T("FREE_LEECH") . ": </b><font color='#ff0000'>" . Lang::T("FREE_LEECH_MSG") . "</font><br />");
        }
        if ($torr["external"] != 'yes' && $torr["vip"] == 'yes') {
            print("<b>Torrent VIP: </b><font color='orange'>Torrent reserved for VIP</font><br><br>");
        }
        print("<b>" . Lang::T("LAST_CHECKED") . ": </b>" . date("d-m-Y H:i:s", TimeDate::utc_to_tz_time($torr["last_action"])) . "<br><br>");
        echo  Helper::ratingtor($data['id']) ;
		// Scrape External Torrents
        if ($torr["external"] == 'yes') {
        print("
        <b><font color='#FF2000'>" . Lang::T("EXTERNAL_TORRENT") . "</font></b>
            <form action='".URLROOT."/scrape?id=$data[id]' method='post'>
                <input type='submit' name='submit' value=" . Lang::T("UPDATE_STATS") . " />
            </form>");
        } 
        // End Scrape External Torrents
		?>

    </div>
    
    <div class="col">
        <?php
        if (!FORCETHANKS) {
        // Magnet
            if ($torr["external"] == 'yes') {
                print("<a href=\"" . URLROOT . "/download?id=$torr[id]&amp;name=" . rawurlencode($torr["filename"]) . "\"><button type='button' class='btn btn-sm btn-success'>" . Lang::T("DOWNLOAD_TORRENT") . "</button></a>");
                print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a><br>");
            } else {
                print("<a href=\"" . URLROOT . "/download?id=$torr[id]&amp;name=" . rawurlencode($torr["filename"]) . "\"><button type='button' class='btn btn-sm btn-success'>" . Lang::T("DOWNLOAD_TORRENT") . "</button></a>");
                print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a><br>");
            }
        } else {
            print("<a href=\"" . URLROOT . "/download?id=$torr[id]&amp;name=" . rawurlencode($torr["filename"]) . "\"><button type='button' class='btn btn-sm btn-success'>" . Lang::T("DOWNLOAD_TORRENT") . "</button></a>");
            $data = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$torr['id'], 'torrent', $_SESSION['id']]);
            $like = $data->fetch(PDO::FETCH_ASSOC);
            if ($like || $_SESSION["id"] != $torr["owner"]) {
              
                // magnet
                if ($torr["external"] == 'yes') {
                    if ($_SESSION["can_download"] == "yes") {
                    // magnet
                    print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a><br>");
                    } else {
                        echo '<br>';
                    }
                } else {
                    if ($_SESSION["can_download"] == "yes") {
                    // magnet button
                    print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a><br>");
                
                    } else {
                        echo '<br>';
                    }
                }

            } else {
                if($_SESSION["id"] != $torr["owner"]) {
                print("<a href='" . URLROOT . "/likes/details?id=$torr[id]'><button  class='btn btn-sm btn-danger'>Thanks</button></a><br>");
                } else {
                if ($torr["external"] == 'yes') {
                    // magnet
                    print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a><br>");
                } else {
                    print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a><br>");
                }
                }
            }
        }                   

                    /*
        if ($_SESSION["id"] != $torr["owner"]) {
            $data1 = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$torr['id'], 'torrent', $_SESSION['id']]);
            $like = $data->fetch(PDO::FETCH_ASSOC);
           if ($like) {
                // Magnet
                if ($torr["external"] == 'yes') {
                print("<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
               } else {
                print("<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
               }
            } else {
               print("<a href='" . URLROOT . "/likes/details?id=$data[id]'><button  class='btn btn-sm btn-danger'>Thanks</button></a>&nbsp;");
            }
        } else {
            if ($torr["external"] == 'yes') {
                print("<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
            } else {
                print("<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm btn-danger'>Magnet Download</button></a>");
            }
        }
            */
        

 ?>
        <b><?php echo Lang::T("HEALTH"); ?>: </b>
        <img src='<?php echo URLROOT; ?>/assets/images/health/health_<?php echo health($torr['leechers'], $torr['seeders']); ?>.gif' alt='' /><br>
        <b><?php echo Lang::T("SEEDS"); ?>: </b><font color='green'><?php echo number_format($torr["seeders"]); ?></font><br />
        <b><?php echo Lang::T("LEECHERS"); ?>: </b><font color='#ff0000'><?php echo number_format($torr["leechers"]); ?></font><br />
        <?php if ($torr["external"]!='yes') { ?>
            <b><?php echo Lang::T("SPEED"); ?>: </b><br />
        <?php } ?>
            <b><?php echo Lang::T("COMPLETED"); ?>:</b><?php echo  number_format($torr["times_completed"]); ?><br>
        <?php        
        if ($torr["external"] != "yes" && $torr["times_completed"] > 0) { ?>
            <a href='<?php echo URLROOT; ?>/completed?id=<?php echo $data['id']; ?>'><?php echo Lang::T("WHOS_COMPLETED"); ?></a>]<br>
        <?php } 
        if ($torr["seeders"] <= 1) { ?>
            [<a href='<?php echo URLROOT ?>/seed/reseed?id=<?php echo $torr['id']; ?>'><?php echo Lang::T("REQUEST_A_RE_SEED"); ?></a>]<br>
        <?php }
        if ($torr["external"]!='yes' && $torr["freeleech"]=='1'){ ?>
            <b><?php echo Lang::T("FREE_LEECH"); ?>: </b><font color='#ff0000'><?php echo Lang::T("FREE_LEECH_MSG"); ?></font><br>
        <?php } ?>
        <b><?php echo Lang::T("LAST_CHECKED"); ?>: </b><?php echo  date("d-m-Y H:i:s", TimeDate::utc_to_tz_time($torr["last_action"])); ?><br>
        <a href="<?php echo URLROOT; ?>/report/torrent?torrent=<?php echo $torr['id']; ?>"><button type='button' class='btn btn-sm btn-danger'><?php echo Lang::T("REPORT_TORRENT") ?></button></a>&nbsp;
        <?php if ($_SESSION["edit_torrents"] == "yes") {?>
        <?php echo "<a href='" . URLROOT . "/torrents/edit?id=$torr[id]&amp;returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "'><button type='button' class='btn btn-sm btn-success'><b>" . Lang::T("EDIT_TORRENT") . "</b></button></a>&nbsp;";
        }
        if ($_SESSION["edit_users"] == "yes") {?>
            <a href="<?php echo URLROOT; ?>/snatched?tid=<?php echo $torr['id']; ?>"><button type='button' class='btn btn-sm btn-warning'><?php echo Lang::T("SNATCHLIST") ?></button></a>
        <?php }?>
        <?php if ($_SESSION["delete_torrents"] == "yes") {?>
            <a href="<?php echo URLROOT; ?>/torrents/delete?id=<?php echo $torr['id']; ?>"><button type='button' class='btn btn-sm btn-warning'><?php echo Lang::T("Delete") ?></button></a>
        <?php } ?>

    </div>

</div>
</div>

<?php
if ($torr['imdb'] != "") {
    $oIMDB = new IMDBnew($torr['imdb']);
    if ($oIMDB->isReady) {
    ?>
    <div class="container"><?php
        echo '<p><a href="'.$oIMDB->getUrl().'">'.$oIMDB->getTitle().'</a> got rated '.$oIMDB->getRating().'</p>';
        echo '<p><b>About the movie:</b>'.$oIMDB->getPlot().'</p>'; ?>
    </div>
    <div class="container">
    <div class="row">
        <div class="col">
            <?php echo '<p><img src="'.$oIMDB->getPoster('s', true).'" style="float:left;margin:4px 10px 10px 0;"></p>'; ?>
        </div>
        <div class="col">
        <?php echo '<b>Cast</b><br>';
        $castImages = $oIMDB->getCastImages(5, false, 'small');
        foreach (explode(' / ', $oIMDB->getCast(5, false)) as $name) {
            echo '<img src="' . $castImages[$name] . '" alt="' . $name . '">' . $name . '<br>';
        } ?>
        </div>
        <div class="col">
        <?php $posterlink = $oIMDB->getTrailerAsUrl(); ?>
            <iframe src="<?php echo $posterlink ?>imdb/embed?autoplay=false&width=450" style="float:right;margin:4px 10px 10px 0;" width="450" height="300" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true" frameborder="no" scrolling="no"></iframe>
            <?php  ?>
        </div>
    </div>
    </div><?php
    } else {
        echo '<p>Movie not found!</p>';
    }
} else { // https://www.imdb.com/title/tt0770828/
    ?>
    <div class="container">
    <div class="row">
        <div class="col">
        <?php
        if (!empty($torr["tube"])) {
            print("<embed src='" . str_replace("watch?v=", "v/", htmlspecialchars($torr["tube"])) . "' type=\"application/x-shockwave-flash\" width=\"400\" height=\"310\"></embed>");
        } ?>
        </div>
        <div class="col">
        <?php if ($torr["image1"] != "") {
            if ($torr["image1"] != "") {
                $img1 = "&nbsp;&nbsp;<img src='".data_uri(TORRENTDIR."/images/".$torr["image1"], $torr['image1'])."' height='300' width='200' border='0' alt='' />";
            }
            print("" . $img1 . "");
        } ?>
        </div>
        <div class="col">
        <?php if ($torr["image2"] != "") {
            if ($torr["image2"] != "") {
                $img2 = "<img src='".data_uri(TORRENTDIR."/images/".$torr["image2"], $torr['image2'])."' height='300' width='200' border='0' alt='' />";
            }
            print("" . $img2 . "");
        } ?>
        </div>
    </div>
    </div>
<?php
}
//DISPLAY NFO BLOCK
if ($torr["nfo"] == "yes") {
    $nfofilelocation = NFODIR."/$torr[id].nfo";
    $filegetcontents = file_get_contents($nfofilelocation);
    $nfo = $filegetcontents;
    if ($nfo) {
        $nfo = Helper::my_nfo_translate($nfo);
        echo "<br /><br /><b>NFO:</b><br />";
        print("<div><textarea class='nfo' style='width:98%;height:100%;' rows='20' cols='20' readonly='readonly'>" . stripslashes($nfo) . "</textarea></div>");
    } else {
        print(Lang::T("ERROR") . " reading .nfo file!");
    }
 }

endforeach;
Style::end();
Style::footer();