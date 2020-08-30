<?php
class Torrents extends Controller
{
    public function __construct()
    {
        $this->torrentModel = $this->model('Torrent');
    }

    public function index()
    {
        dbconn();
        global $config;
        loggedinonly();
        stdhead(T_("Torrents"));
        begin_frame(T_("Torrents"));
        ?>
        Torrent Controller - Landing Page<br>
        Try read by id <a href="<?php echo TTURL; ?>/torrents/read?id=2">Read Torrent</a><br>
        <?php
end_frame();
        stdfoot();
    }

    public function read()
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
        $res = $pdo->run("SELECT torrents.anon, torrents.seeders, torrents.tube, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.imdb, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, torrents.vip, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);

        //DECIDE IF TORRENT EXISTS
        if (!$row || ($row["banned"] == "yes" && $_SESSION["edit_torrents"] == "no")) {
            show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);
        }
        // vip
        $vip = $row["vip"];
        if ($vip == "yes") {
        $vip = "<b>Yes</b>";
        } else {
        $vip = "<b>No</b>";
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
		/*
        $data = [
        'row' => $row,
        'name' => $shortname,
        'speed' => $totalspeed,
        'id' => $id,
        ];
        $this->view('account/login', $data);
        */
        include("views/torrent/read.php");
        end_frame();

        stdfoot();
    }

    public function torrentfilelist()
    {
        dbconn();
        global $config;
        $id = (int) $_GET["id"];

        if (!is_valid_id($id)) {
            show_error_msg(T_("ERROR"), T_("THATS_NOT_A_VALID_ID"), 1);
        }
        //check permissions
        if ($config["MEMBERSONLY"]) {
            loggedinonly();
        }
        if ($_SESSION["view_torrents"] == "no") {
            show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
        }
        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $char1 = 50; //cut length
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);

        stdhead(T_("DETAILS_FOR_TORRENT") . " \"" . $row["name"] . "\"");
        begin_frame(T_("TORRENT_DETAILS_FOR") . " \"" . $shortname . "\"");
        echo '<table cellpadding="1" cellspacing="2" class="table_table"><tr>';

        echo "<b>" . T_("FILE_LIST") . ":</b>&nbsp;<img src='images/plus.gif' id='pic1' onclick='klappe_torrent(1)' alt='' /><div id='k1' style='display: none;'><table align='center' cellpadding='0' cellspacing='0' class='table_table' border='1' width='100%'><tr><th class='table_head' align='left'>&nbsp;" . T_("FILE") . "</th><th width='50' class='table_head'>&nbsp;" . T_("SIZE") . "</th></tr>";
        $fres = DB::run("SELECT * FROM `files` WHERE `torrent` = $id ORDER BY `path` ASC");
        if ($fres->rowCount()) {
            while ($frow = $fres->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td class='table_col1'>" . htmlspecialchars($frow['path']) . "</td><td class='table_col2'>" . mksize($frow['filesize']) . "</td></tr>";
            }
        } else {
            echo "<tr><td class='table_col1'>" . htmlspecialchars($row["name"]) . "</td><td class='table_col2'>" . mksize($row["size"]) . "</td></tr>";
        }
        echo "</table>";
        end_frame();
        stdfoot();
    }

    public function torrenttrackerlist()
    {
        dbconn();
        global $config;
        $id = (int) $_GET["id"];

        if (!is_valid_id($id)) {
            show_error_msg(T_("ERROR"), T_("THATS_NOT_A_VALID_ID"), 1);
        }
        //check permissions
        if ($config["MEMBERSONLY"]) {
            loggedinonly();
        }
        if ($_SESSION["view_torrents"] == "no") {
            show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
        }
        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $char1 = 50; //cut length
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);

        stdhead(T_("DETAILS_FOR_TORRENT") . " \"" . $row["name"] . "\"");
        begin_frame(T_("TORRENT_DETAILS_FOR") . " \"" . $shortname . "\"");
        if ($row["external"] == 'yes') {
            print("<b>Tracker:</b><br /> " . htmlspecialchars($row['announce']) . "<br />");
        }

        $tres = DB::run("SELECT * FROM `announce` WHERE `torrent` = $id");
        if ($tres->rowCount() > 1) {
            echo "<br /><b>" . T_("THIS_TORRENT_HAS_BACKUP_TRACKERS") . "</b><br />";
            echo '<table cellpadding="1" cellspacing="2" class="table_table"><tr>';
            echo '<th class="table_head">URL</th><th class="table_head">' . T_("SEEDERS") . '</th><th class="table_head">' . T_("LEECHERS") . '</th><th class="table_head">' . T_("COMPLETED") . '</th></tr>';
            $x = 1;
            while ($trow = $tres->fetch(PDO::FETCH_ASSOC)) {
                $colour = $trow["online"] == "yes" ? "green" : "red";
                echo "<tr class=\"table_col$x\"><td><font color=\"$colour\"><b>" . htmlspecialchars($trow['url']) . "</b></font></td><td align=\"center\">" . number_format($trow["seeders"]) . "</td><td align=\"center\">" . number_format($trow["leechers"]) . "</td><td align=\"center\">" . number_format($trow["times_completed"]) . "</td></tr>";
                $x = $x == 1 ? 2 : 1;
            }
            echo '</table>';
        }
        end_frame();
        stdfoot();
    }




    public function edit(){
        dbconn();
        global $config, $pdo;
        loggedinonly();
        
        $id = (int) $_REQUEST["id"];
        if (!is_valid_id($id)) show_error_msg(T_("ERROR"), T_("INVALID_ID"), 1);
        $action = $_REQUEST["action"];
        
        $row = DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
        if($_SESSION["edit_torrents"]=="no" && $_SESSION['id'] != $row['owner'])
            show_error_msg(T_("ERROR"), T_("NO_TORRENT_EDIT_PERMISSION"), 1);
        
        //GET DATA FROM DB
        $row = DB::run("SELECT * FROM torrents WHERE id =?", [$id])->fetch();
        if (!$row){
            show_error_msg(T_("ERROR"), T_("TORRENT_ID_GONE"), 1);
        }
        
        $torrent_dir = $config["torrent_dir"];    
        $nfo_dir = $config["nfo_dir"];    
        
        //DO THE SAVE TO DB HERE
        if ($action=="doedit"){
            $updateset = array();
        
            $nfoaction = $_POST['nfoaction'];
            if ($nfoaction == "update"){
              $nfofile = $_FILES['nfofile'];
              if (!$nfofile) die("No data " . var_dump($_FILES));
              if ($nfofile['size'] > 65535)
                show_error_msg("NFO is too big!", "Max 65,535 bytes.",1);
              $nfofilename = $nfofile['tmp_name'];
              if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0){
                    @move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
                    $updateset[] = "nfo = 'yes'";
                }//success
            }

     
            if (!empty($_POST["name"]))
                 $updateset[] = "name = " . sqlesc($_POST["name"]);
                 // IMDB
                 if ( $_POST['imdb'] != $row['imdb'] ){
                    $updateset[] = "imdb = " . sqlesc($_POST["imdb"]);
                    $TTCache = new Cache();
                    $TTCache->Delete("imdb/$id");
               }           
            $updateset[] = "descr = " . sqlesc($_POST["descr"]);
            $updateset[] = "category = " . (int) $_POST["type"];
            if(get_user_class() >= 5){   // lowest class to make torrent sticky.
                if ($_POST["sticky"] == "yes")
                          $updateset[] = "sticky = 'yes'";
                   else
                          $updateset[] = "sticky = 'no'";
                }
            $updateset[] = "torrentlang = " . (int) $_POST["language"];
        
            if ($_SESSION["edit_torrents"] == "yes") {
                if ($_POST["banned"]) {
                    $updateset[] = "banned = 'yes'";
                    $_POST["visible"] = 0;
                } else {
                    $updateset[] = "banned = 'no'";
                }
            }
        
            $updateset[] = "visible = '" . ($_POST["visible"] ? "yes" : "no") . "'";
            
            // youtube
            if (!empty($_POST['tube']))
            $tube = unesc($_POST['tube']);
            $updateset[] = "tube = " . sqlesc($tube);

            if ($_SESSION["edit_torrents"] == "yes")
                $updateset[] = "freeleech = '".($_POST["freeleech"] ? "1" : "0")."'";
                $updateset[] = "vip = '" . ($_POST["vip"] ? "yes" : "no") . "'";
            $updateset[] = "anon = '" . ($_POST["anon"] ? "yes" : "no") . "'";
        
            //update images
            $img1action = $_POST['img1action'];
            if ($img1action == "update")
                $updateset[] = "image1 = " .sqlesc(uploadimage(0, $row["image1"], $id));
            if ($img1action == "delete") {
                if ($row['image1']) {
                    $del = unlink($config["torrent_dir"]."/images/$row[image1]");
                    $updateset[] = "image1 = ''";
                }
            }
        
            $img2action = $_POST['img2action'];
            if ($img2action == "update")
                $updateset[] = "image2 = " .sqlesc(uploadimage(1, $row["image2"], $id));
            if ($img2action == "delete") {
                if ($row['image2']) {
                    $del = unlink($config["torrent_dir"]."/images/$row[image2]");
                    $updateset[] = "image2 = ''";
                }
            }
        
        
            DB::run("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");
                
            write_log("Torrent $id (".htmlspecialchars($_POST["name"]).") was edited by $_SESSION[username]");
        
            header("Location: ".TTURL."/torrents/read?id=$id");
            die();
        }//END SAVE TO DB
        
        //UPDATE CATEGORY DROPDOWN
        $catdropdown = "<select name=\"type\">\n";
        $cats = genrelist();
            foreach ($cats as $catdropdownubrow) {
                $catdropdown .= "<option value=\"" . $catdropdownubrow["id"] . "\"";
                if ($catdropdownubrow["id"] == $row["category"])
                    $catdropdown .= " selected=\"selected\"";
                $catdropdown .= ">" . htmlspecialchars($catdropdownubrow["parent_cat"]) . ": " . htmlspecialchars($catdropdownubrow["name"]) . "</option>\n";
            }
        $catdropdown .= "</select>\n";
        //END CATDROPDOWN
        
        //UPDATE TORRENTLANG DROPDOWN
        $langdropdown = "<select name=\"language\"><option value='0'>Unknown</option>\n";
        $lang = langlist();
        foreach ($lang as $lang) {
            $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
            if ($lang["id"] == $row["torrentlang"])
                $langdropdown .= " selected=\"selected\"";
            $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
        }
        $langdropdown .= "</select>\n";
        //END TORRENTLANG
        
        
        $char1 = 55;
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);
        
        if ($_GET["edited"]){
            show_error_msg("Edited OK", T_("TORRENT_EDITED_OK"), 1);
        }
        
        stdhead(T_("EDIT_TORRENT")." \"$shortname\"");
        
        begin_frame(T_("EDIT_TORRENT")." \"$shortname\"");
        include("views/torrent/edit.php");
        end_frame();
        stdfoot();
        }




        public function delete(){
            dbconn();
            global $config, $pdo;
            loggedinonly();
            
            $id = (int) $_GET["id"];
            if (!is_valid_id($id)) {
                show_error_msg(T_("ERROR"), T_("INVALID_ID"), 1);
            }

            $row = DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
            if($_SESSION["delete_torrents"]=="no" && $_SESSION['id'] != $row['owner'])
                show_error_msg(T_("ERROR"), T_("NO_TORRENT_DELETE_PERMISSION"), 1);
            
            //GET DATA FROM DB
            $row = DB::run("SELECT * FROM torrents WHERE id =?", [$id])->fetch();
            if (!$row){
                show_error_msg(T_("ERROR"), T_("TORRENT_ID_GONE"), 1);
            }

            //DELETE TORRENT
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $torrentid = (int) $_POST["torrentid"];
                $delreason = sqlesc($_POST["delreason"]);
                $torrentname = $_POST["torrentname"];
            
                if (!is_valid_id($torrentid))
                    show_error_msg(T_("FAILED"), T_("INVALID_TORRENT_ID"), 1);
            
                if (!$delreason){
                    show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA"), 1);
                }
            
                deletetorrent($torrentid);
            
                write_log($_SESSION['username']." has deleted torrent: ID:$torrentid - ".htmlspecialchars($torrentname)." - Reason: ".htmlspecialchars($delreason));
                if ($_SESSION['id'] != $row['owner']) {
                $delreason = $_POST["delreason"];
                    DB::run("INSERT INTO messages (sender, receiver, added, subject, msg, unread, location) VALUES(0, ".$row['owner'].", '".get_date_time()."', 'Your torrent \'$torrentname\' has been deleted by ".$_SESSION['username']."', ".sqlesc("'$torrentname' was deleted by ".$_SESSION['username']."\n\nReason: $delreason").", 'yes', 'in')");
                }
            
                show_error_msg(T_("COMPLETED"), htmlspecialchars($torrentname)." ".T_("HAS_BEEN_DEL_DB"),1);
                //autolink(TTURL."/torrents/read?id=$id", T_("HAS_BEEN_DEL_DB"));
                die;
            }
    
            $char1 = 55;
            $shortname = CutName(htmlspecialchars($row["name"]), $char1);

            stdhead(T_("DELETE_TORRENT")." \"$shortname\"");           
            begin_frame(T_("DELETE_TORRENT"));
            include("views/torrent/delete.php");
            end_frame();
            
            stdfoot();
            }


            public function create(){

                dbconn();
                global $config;
                // check access and rights
                if ($config["MEMBERSONLY"]){
                    loggedinonly();
                
                    if($_SESSION["can_upload"]=="no")
                        show_error_msg(T_("ERROR"), T_("UPLOAD_NO_PERMISSION"), 1);
                    if ($config["UPLOADERSONLY"] && $_SESSION["class"] < 4)
                        show_error_msg(T_("ERROR"), T_("UPLOAD_ONLY_FOR_UPLOADERS"), 1);
                }
                
                $announce_urls = explode(",", strtolower($config["announce_list"]));  //generate announce_urls[] from config.php
                
                if ($_POST["takeupload"] == "yes") {
                
                    // Check form data.
                    if ( ! isset($_POST['type'], $_POST['name'], $_FILES['torrent']) )
                          $message = T_('MISSING_FORM_DATA'); 
                    
                    if (($num = $_FILES['torrent']['error']))
                         show_error_msg(T_('ERROR'), T_("UPLOAD_ERR[$num]"), 1);
                
                    $f = $_FILES["torrent"];
                    $fname = $f["name"];
                
                    if (empty($fname))
                        $message = T_("EMPTY_FILENAME");
                
                    $nfo = 'no';
                        
                    if ($_FILES['nfo']['size'] != 0) {
                        $nfofile = $_FILES['nfo'];
                
                        if ($nfofile['name'] == '')
                            $message = T_("NO_NFO_UPLOADED");
                            
                        if (!preg_match('/^(.+)\.nfo$/si', $nfofile['name'], $fmatches))
                            $message = T_("UPLOAD_NOT_NFO");
                
                        if ($nfofile['size'] == 0)
                            $message = T_("NO_NFO_SIZE");
                
                        if ($nfofile['size'] > 65535)
                            $message = T_("NFO_UPLOAD_SIZE");
                
                        $nfofilename = $nfofile['tmp_name'];
                
                        if (($num = $_FILES['nfo']['error']))
                             $message = T_("UPLOAD_ERR[$num]");
                        
                        $nfo = 'yes';
                    }
                
                    $descr = $_POST["descr"];
                    
                    if (!$descr) {
                        $descr = T_("UPLOAD_NO_DESC");
                    }
                    $vip = $_POST["vip"];
                    $langid = (int) $_POST["lang"];
                    
                    /*if (!is_valid_id($langid))
                        $message = "Please be sure to select a torrent language";*/
                
                    $catid = (int) $_POST["type"];
                
                    if (!is_valid_id($catid))
                        $message = T_("UPLOAD_NO_CAT");

                    if (!empty($_POST['tube']))
                        $tube = unesc($_POST['tube']);  

                    if (!validfilename($fname))
                        $message = T_("UPLOAD_INVALID_FILENAME");
                
                    if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
                        $message = T_("UPLOAD_INVALID_FILENAME_NOT_TORRENT");
                
                        $shortfname = $torrent = $matches[1];
                
                    if (!empty($_POST["name"]))
                        $name = $_POST["name"];
// IMDB                
if (!empty($_POST['imdb']))
            $imdb = $_POST['imdb'];
					
                    $tmpname = $f['tmp_name'];
                
                    //end check form data
                
                    if (!$message) {
                    //parse torrent file
                    $torrent_dir = $config["torrent_dir"];	
                    $nfo_dir = $config["nfo_dir"];	
                
                    //if(!copy($f, "$torrent_dir/$fname"))
                    if(!move_uploaded_file($tmpname, "$torrent_dir/$fname"))
                        show_error_msg(T_("ERROR"), T_("ERROR"). ": " . T_("UPLOAD_COULD_NOT_BE_COPIED")." $tmpname - $torrent_dir - $fname",1);
                
                    $TorrentInfo = array();
                    $TorrentInfo = ParseTorrent("$torrent_dir/$fname");
                
                
                    $announce = $TorrentInfo[0];
                    $infohash = $TorrentInfo[1];
                    $creationdate = $TorrentInfo[2];
                    $internalname = $TorrentInfo[3];
                    $torrentsize = $TorrentInfo[4];
                    $filecount = $TorrentInfo[5];
                    $annlist = $TorrentInfo[6];
                    $comment = $TorrentInfo[7];
                    $filelist = $TorrentInfo[8];
                
                /*
                //for debug...
                    print ("<br /><br />announce: ".$announce."");
                    print ("<br /><br />infohash: ".$infohash."");
                    print ("<br /><br />creationdate: ".$creationdate."");
                    print ("<br /><br />internalname: ".$internalname."");
                    print ("<br /><br />torrentsize: ".$torrentsize."");
                    print ("<br /><br />filecount: ".$filecount."");
                    print ("<br /><br />annlist: ".$annlist."");
                    print ("<br /><br />comment: ".$comment."");
                */
                    
                    //check announce url is local or external
                    if (!in_array($announce, $announce_urls, 1)){
                        $external='yes';
                    }else{
                        $external='no';
                    }
                
                    //if externals is turned off
                    if (!$config["ALLOWEXTERNAL"] && $external == 'yes')
                        $message = T_("UPLOAD_NO_TRACKER_ANNOUNCE");
                    }
                    if ($message) {
                        @unlink("$torrent_dir/$fname");
                        @unlink($tmpname);
                        @unlink("$nfo_dir/$nfofilename");
                        show_error_msg(T_("UPLOAD_FAILED"), $message,1);
                    }
                
                    //release name check and adjust
                    if ($name ==""){
                        $name = $internalname;
                    }
                    $name = str_replace(".torrent","",$name);
                    $name = str_replace("_", " ", $name);
                
                    //upload images
                    $allowed_types = &$config["allowed_image_types"];
                
                    $inames = array();
                    for ($x=0; $x < 2; $x++) {
                        if (!($_FILES['image'.$x]['name'] == "")) {
                            $y = $x + 1;
                
                            //if (!preg_match('/^(.+)\.(jpg|gif|png)$/si', $_FILES[image.$x]['name']))
                            //	show_error_msg(T_("INVAILD_IMAGE"), T_("THIS_FILETYPE_NOT_IMAGE"), 1);
                
                            if ($_FILES['image$x']['size'] > $config['image_max_filesize'])
                                show_error_msg(T_("ERROR"), T_("INVAILD_FILE_SIZE_IMAGE"), 1);
                
                            $uploaddir = $config["torrent_dir"]."/images/";
                
                            $ifile = $_FILES['image'.$x]['tmp_name'];
                
                            $im = getimagesize($ifile);
                
                            if (!$im[2])
                                show_error_msg(T_("ERROR"), sprintf(T_("INVALID_IMAGE"), $y), 1);
                
                            if (!array_key_exists($im['mime'], $allowed_types))
                                show_error_msg(T_("ERROR"), T_("INVALID_FILETYPE_IMAGE"), 1);
                
                            $row = DB::run("SHOW TABLE STATUS LIKE 'torrents'")->fetch();
                            $next_id = $row['Auto_increment'];
                
                            $ifilename = $next_id . $x . $allowed_types[$im['mime']];
                
                            $copy = copy($ifile, $uploaddir.$ifilename);
                
                            if (!$copy)
                                show_error_msg(T_("ERROR"), sprintf(T_("IMAGE_UPLOAD_FAILED"), $y), 1);
                
                            $inames[] = $ifilename;
                
                        }
                
                    }
                    //end upload images
                
                    //anonymous upload
                    $anonyupload = $_POST["anonycheck"]; 
                    if ($anonyupload == "yes") {
                        $anon = "yes";
                    }else{
                        $anon = "no";
                    }
                
					$filecounts = (int)$filecount;
					
					try {
                    $ret = DB::run("INSERT INTO torrents (filename, owner, name, vip, descr, image1, image2, category, tube, added, info_hash, size, numfiles, save_as, announce, external, nfo, torrentlang, anon, last_action, imdb) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [$fname, $_SESSION['id'], $name, $vip, $descr, $inames[0], $inames[1], $catid, $tube, get_date_time(), $infohash, $torrentsize, $filecounts, $fname, $announce, $external, $nfo, $langid, $anon, get_date_time(), $imdb]);
                    } catch (PDOException $e) {
                        rename("$torrent_dir/$fname", "$torrent_dir/duplicate.torrent"); // todo
                        autolink(TTURL.'/index.php', 'Torrent already added. Duplicate Hash');
                    }
                    $id = DB::lastInsertId();
                    
                    if($id == 0){
                        unlink("$torrent_dir/$fname");
                        $message = T_("UPLOAD_NO_ID");
                        show_error_msg(T_("UPLOAD_FAILED"), $message, 1);
                    }
                    
                    rename("$torrent_dir/$fname", "$torrent_dir/$id.torrent"); 
                
                    if (is_array($filelist)) {
                        foreach ($filelist as $file) {
                            $dir = '';
                            $size = $file["length"];
                            $count = count($file["path"]);
                            for ($i=0; $i<$count;$i++) {
                                if (($i+1) == $count)
                                    $fname = $dir.$file["path"][$i];
                                else
                                    $dir .= $file["path"][$i]."/";
                            }
                            DB::run("INSERT INTO `files` (`torrent`, `path`, `filesize`) VALUES (?, ?, ?)", [$id, $fname, $size]);
                        }
                    } else {
                        DB::run("INSERT INTO `files` (`torrent`, `path`, `filesize`) VALUES (?, ?, ?)", [$id, $TorrentInfo[3], $torrentsize]);
                    }
                
                    if (!is_array($annlist)) {
                        $annlist = array(array($announce));
                    }
                    foreach ($annlist as $ann) {
                        foreach ($ann as $val) {
                            if (strtolower(substr($val, 0, 4)) != "udp:") {
                                DB::run("INSERT INTO `announce` (`torrent`, `url`) VALUES (?, ?)", [$id, $val]);
                            }
                        }
                    }
                
                    if ($nfo == 'yes') { 
                            move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo"); 
                    } 
                
                    //EXTERNAL SCRAPE
                    if ($external=='yes' && $config['UPLOADSCRAPE']){
                        $tracker=str_replace("/announce","/scrape",$announce);	
                        $stats 			= torrent_scrape_url($tracker, $infohash);
                        $seeders 		= (int) strip_tags($stats['seeds']);
                        $leechers 		= (int) strip_tags($stats['peers']);
                        $downloaded 	= (int) strip_tags($stats['downloaded']);
                
                        DB::run("UPDATE torrents SET leechers='".$leechers."', seeders='".$seeders."',times_completed='".$downloaded."',last_action= '".get_date_time()."',visible='yes' WHERE id='".$id."'");
                    }
                    //END SCRAPE
                
                    write_log( sprintf(T_("TORRENT_UPLOADED"), htmlspecialchars($name), $_SESSION["username"]) );
                    // Shout new torrent
                    $msg_shout = "New Torrent: [url=".$config['SITEURL']."/torrents/read?id=".$id."]".$torrent."[/url] has been uploaded ".($anon == 'no' ? "by [url=".$config['SITEURL']."/account-details.php?id=".$_SESSION['id']."]" .$_SESSION['username']. "[/url]" : "")."";
                    DB::run("INSERT INTO shoutbox (userid, date, user, message) VALUES(?,?,?,?)", [0, get_date_time(), 'System' , $msg_shout]);
                    //Uploaded ok message (update later)
                    if ($external=='no')
                        $message = sprintf( T_("TORRENT_UPLOAD_LOCAL"), $name, $id, $id );
                    else
                        $message = sprintf( T_("TORRENT_UPLOAD_EXTERNAL"), $name, $id );
                    show_error_msg(T_("UPLOAD_COMPLETE"), $message, 1);
                
                    die();
                }//takeupload
                
                
                ///////////////////// FORMAT PAGE ////////////////////////
                
                stdhead(T_("UPLOAD"));
                
                begin_frame(T_("UPLOAD_RULES"));
                    echo "<b>".stripslashes($config["UPLOADRULES"])."</b>";
                    echo "<br />";
                end_frame();
                
                begin_frame(T_("UPLOAD"));
                include("views/torrent/create.php");
                end_frame();
                stdfoot();
                }



                public function needseed(){
                    dbconn();
                   global $config, $pdo;
                    // Check permissions
                    if ($config["MEMBERSONLY"]) {
                        loggedinonly();
                        
                        if ($_SESSION["view_torrents"] == "no")
                            show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
                    }  
                    
                    $res = DB::run("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size, torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, users.username FROM torrents LEFT JOIN users ON torrents.owner = users.id WHERE torrents.banned = 'no' AND torrents.leechers > 0 AND torrents.seeders <= 1 ORDER BY torrents.seeders");
                    
                    if ($res->rowCount() == 0)
                        show_error_msg(T_("ERROR"), T_("NO_TORRENT_NEED_SEED"), 1);
                        
                        stdhead(T_("TORRENT_NEED_SEED"));
                        begin_frame(T_("TORRENT_NEED_SEED"));
                        include("views/torrent/needseed.php");             
                        end_frame();
                        stdfoot();
                       }
                       
                       
                           public function import(){
                   dbconn();
                   global $config, $pdo;
                   $dir = "import";
                   
                   //ini_set("upload_max_filesize",$max_torrent_size);
                   
                   $files = array();
                   $dh = opendir("$dir/");
                   while (false !== ($file=readdir($dh))) {
                       if (preg_match("/\.torrent$/i", $file))
                           $files[] = $file;
                   }
                   closedir($dh);
                   
                   
                   // check access and rights
                   if ($_SESSION["edit_torrents"] != "yes")
                       show_error_msg(T_("ERROR"), T_("ACCESS_DENIED"), 1);
                   
                   $announce_urls = explode(",", strtolower($config["announce_list"]));  //generate announce_urls[] from config.php
                   
                   if ($_POST["takeupload"] == "yes") {
                       set_time_limit(0);
                       stdhead(T_("UPLOAD_COMPLETE"));
                       begin_frame(T_("UPLOAD_COMPLETE"));
                       echo "<center>";
                   
                       //check form data
                       $catid = (int)$_POST["type"];
                   
                       if (!is_valid_id($catid))
                           $message = T_("UPLOAD_NO_CAT");
                       
                       if (empty($message)) {
                           $r = DB::run("SELECT name, parent_cat FROM categories WHERE id=$catid")->fetch();
                           echo "<b>Category:</b> ".htmlspecialchars($r[1])." -> ".htmlspecialchars($r[0])."<br />";
                           for ($i=0;$i<count($files);$i++) {
                               $fname = $files[$i];
                   
                               $descr = T_("UPLOAD_NO_DESC");
                   
                               $langid = (int)$_POST["lang"];
                       
                               preg_match('/^(.+)\.torrent$/si', $fname, $matches);
                               $shortfname = $torrent = $matches[1];
                   
                               //parse torrent file
                               $torrent_dir = $config["torrent_dir"];	
                   
                               $TorrentInfo = array();
                               $TorrentInfo = ParseTorrent("$dir/$fname");
                   
                   
                               $announce = strtolower($TorrentInfo[0]);
                               $infohash = $TorrentInfo[1];
                               $creationdate = $TorrentInfo[2];
                               $internalname = $TorrentInfo[3];
                               $torrentsize = $TorrentInfo[4];
                               $filecount = $TorrentInfo[5];
                               $annlist = $TorrentInfo[6];
                               $comment = $TorrentInfo[7];
                               
                               $message = "<br /><br /><hr /><br /><b>$internalname</b><br /><br />fname: ".htmlspecialchars($fname)."<br />message: ";
                   
                               //check announce url is local or external
                               if (!in_array($announce, $announce_urls, 1))
                                   $external='yes';
                               else
                                   $external='no';
                   
                               if (!$config["ALLOWEXTERNAL"] && $external == 'yes') {
                                   $message .= T_("UPLOAD_NO_TRACKER_ANNOUNCE");
                                   echo $message;
                                   continue;
                               }
                   
                               $name = $internalname;
                               $name = str_replace(".torrent","",$name);
                               $name = str_replace("_", " ", $name);
                   
                               //anonymous upload
                               $anonyupload = $_POST["anonycheck"]; 
                               if ($anonyupload == "yes")
                                   $anon = "yes";
                               else
                                   $anon = "no";
                   
                               $ret = DB::run("INSERT INTO torrents (filename, owner, name, descr, category, added, info_hash, size, numfiles, save_as, announce, external, torrentlang, anon, last_action) VALUES (".sqlesc($fname).", '".$_SESSION['id']."', ".sqlesc($name).", ".sqlesc($descr).", '".$catid."', '" . get_date_time() . "', '".$infohash."', '".$torrentsize."', '".$filecount."', ".sqlesc($fname).", '".$announce."', '".$external."', '".$langid."','$anon', '".get_date_time()."')");
                   
                               $id = $ret->lastInsertId();
                   
                               if ($ret->errorCode() == 1062) {
                                   $message .= T_("UPLOAD_ALREADY_UPLOADED");
                                   echo $message;
                                   continue;
                               }
                   
                               if($id == 0){
                                   $message .= T_("UPLOAD_NO_ID");
                                   echo $message;
                                   continue;
                               }
                       
                               copy("$dir/$files[$i]", "uploads/$id.torrent");
                   
                               //EXTERNAL SCRAPE
                               if ($external=='yes' && $config['UPLOADSCRAPE']) {  
                                   $tracker        = str_replace("/announce","/scrape",$announce);	
                                   $stats 			= torrent_scrape_url($tracker, $infohash);
                                   $seeders 		= strip_tags($stats['seeds']);
                                   $leechers 		= strip_tags($stats['peers']);
                                   $downloaded 	= strip_tags($stats['downloaded']);
                   
                                   DB::run("UPDATE torrents SET leechers='".$leechers."', seeders='".$seeders."',times_completed='".$downloaded."',last_action= '".get_date_time()."',visible='yes' WHERE id='".$id."'");
                               }
                               //END SCRAPE
                   
                               write_log("Torrent $id ($name) was Uploaded by $_SESSION[username]");
                   
                               $message .= "<br /><b>".T_("UPLOAD_OK")."</b><br /><a href='$config[SITEURL]/torrents/read?id=".$id."'>".T_("UPLOAD_VIEW_DL")."</a><br /><br />";
                               echo $message;
                               @unlink("$dir/$fname");
                           }
                       echo "</center>";
                       end_frame();
                       stdfoot();
                       die;
                       }else
                           show_error_msg(T_("UPLOAD_FAILED"), $message, 1);
                   
                   }//takeupload
                   
                   stdhead(T_("UPLOAD"));
                   begin_frame(T_("UPLOAD"));
                   include("views/torrent/import.php");
                   end_frame();
                   stdfoot();
                   }               


                   public function completed(){
                    dbconn();
                     global $config, $pdo;
                  $db = new Database;    
                    if ($config["MEMBERSONLY"]) {
                        loggedinonly();
                        
                        if ($_SESSION["view_torrents"] == "no")
                            show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
                    }
                                    
                    $id = (int) $_GET["id"];
                    
                    $res = DB::run("SELECT name, external, banned FROM torrents WHERE id =?", [$id]);
                    $row = $res->fetch(PDO::FETCH_ASSOC);
                    
                    if ((!$row) || ($row["banned"] == "yes" && $_SESSION["edit_torrents"] == "no"))
                         show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);
                    if ($row["external"] == "yes")
                         show_error_msg(T_("ERROR"), T_("THIS_TORRENT_IS_EXTERNALLY_TRACKED"), 1);
                  
                    $res = DB::run("SELECT users.id, users.username, users.uploaded, users.downloaded, users.privacy, completed.date FROM users LEFT JOIN completed ON users.id = completed.userid WHERE users.enabled = 'yes' AND completed.torrentid = '$id'");
                    if ($res->rowCount() == 0)
                        show_error_msg(T_("ERROR"), T_("NO_DOWNLOADS_YET"), 1);
                    
                    $title = sprintf(T_("COMPLETED_DOWNLOADS"), CutName($row["name"], 40));   
                    
                    stdhead($title);
                    begin_frame($title);
                    ?>
                    
                    <table cellpadding="3" cellspacing="0" align="center" class="table_table">
                    <tr>
                       <th class="table_head"><?php echo T_("USERNAME"); ?></th>
                       <th class="table_head"><?php echo T_("CURRENTLY_SEEDING"); ?></th>
                       <th class="table_head"><?php echo T_("DATE_COMPLETED"); ?></th>
                       <th class="table_head"><?php echo T_("RATIO"); ?></th>
                    </tr>
                    <?php 
                         while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                             
                             if (($row["privacy"] == "strong") && ($_SESSION["edit_users"] == "no"))
                                  continue;
                             
                             $ratio = ($row["downloaded"] > 0) ? $row["uploaded"] / $row["downloaded"] : 0;
                             $peers = (get_row_count("peers", "WHERE torrent = '$id' AND userid = '$row[id]' AND seeder = 'yes'")) ? "<font color='green'>" . T_("YES") . "</font>" : "<font color='#ff0000'>" . T_("NO") . "</font>";
                    ?>
                         <tr>
                             <td class="table_col1"><a href="<?php echo TTURL; ?>/users/profile?id=<?php echo $row["id"]; ?>"><?php echo class_user_colour($row['username']); ?></a></td>
                             <td class="table_col2"><?php echo $peers; ?></td>
                             <td class="table_col1"><?php echo utc_to_tz($row["date"]); ?></td>
                             <td class="table_col2"><?php echo number_format($ratio, 2); ?></td>
                         </tr>
                    <?php } ?>
                    </table>
                    
                    <center><a href="<?php echo TTURL; ?>/torrents/read?id=<?php echo $id; ?>"><?php echo T_("BACK_TO_DETAILS"); ?></a></center>
                    
                    <?php
                    end_frame();
                    stdfoot();
                         }
                         
                             public function today(){
                  
                  dbconn();
                  global $config, $pdo;
                  //check permissions
                  if ($config["MEMBERSONLY"]){
                      loggedinonly();
                  
                      if($_SESSION["view_torrents"]=="no")
                          show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
                  }
                  
                  stdhead(T_("TODAYS_TORRENTS"));
                  
                  begin_frame(T_("TODAYS_TORRENTS"));
                  
                  $date_time=get_date_time(gmtime()-(3600*24)); // the 24 is the hours you want listed
                  
                      $catresult = $this->torrentModel->getCatSort();
                  
                          while($cat = $catresult->fetch(PDO::FETCH_ASSOC))
                          {
                              $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC"; //Order
                              $where = "WHERE banned = 'no' AND category='$cat[id]' AND visible='yes'";
                              $limit = "LIMIT 10"; //Limit
                  
                              $res = $this->torrentModel->getCatSortAll($where, $date_time, $orderby, $limit);
                              $numtor = $res->rowCount();
                  
                              if ($numtor != 0) {
                                      echo "<b><a href='$config[SITEURL]/torrents/browse?cat=".$cat["id"]."'>$cat[name]</a></b>";
                                      # Got to think of a nice way to display this.
                                      #list($pagertop, $pagerbottom, $limit) = pager(1000, $count, "torrents/browse"); //adjust pager to match LIMIT
                                      torrenttable($res);
                                      echo "<br />";
                              }
                          
                  
                          }
                  end_frame();
                  stdfoot();
                      }
                      
                      
                      public function reseed(){
                    dbconn();
                  global $config, $pdo;
                    loggedinonly();
                    
                    if ($_SESSION["view_torrents"] == "no")
                        show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1); 
                  
                    $id = (int) $_GET["id"];
                    
                    if (isset($_COOKIE["reseed$id"]))
                        show_error_msg(T_("ERROR"), T_("RESEED_ALREADY_ASK"), 1);
                        
                    $res = DB::run("SELECT `owner`, `banned`, `external` FROM `torrents` WHERE `id` = $id");
                    $row = $res->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$row || $row["banned"] == "yes" || $row["external"] == "yes")
                         show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);  
                    
                    $res2 = DB::run("SELECT users.id FROM completed LEFT JOIN users ON completed.userid = users.id WHERE users.enabled = 'yes' AND users.status = 'confirmed' AND completed.torrentid = $id");
                  
                    $message = sprintf(T_('RESEED_MESSAGE'), $_SESSION['username'], $config['SITEURL'], $id);
                    
                    while ( $row2 = $res2->fetch(PDO::FETCH_ASSOC) )
                    {
                        DB::run("INSERT INTO `messages` (`subject`, `sender`, `receiver`, `added`, `msg`) VALUES ('".T_("RESEED_MES_SUBJECT")."', '".$_SESSION['id']."', '".$row2['id']."', '".get_date_time()."', ".sqlesc($message).")");
                    }
                    
                    if ($row["owner"] && $row["owner"] != $_SESSION["id"])
                        DB::run("INSERT INTO `messages` (`subject`, `sender`, `receiver`, `added`, `msg`) VALUES ('Torrent Reseed Request', '".$_SESSION['id']."', '".$row['owner']."', '".get_date_time()."', ".sqlesc($message).")");
                        
                    setcookie("reseed$id", $id, time() + 86400, '/');
                    
                    show_error_msg("Complete", T_("RESEED_SENT"), 1);
                  }

                  public function search(){
                    // Set Current User
                    // $_SESSION = $this->userModel->setCurrentUser();
                    // Set Current User
                    // $db = new Database;
            dbconn();
            global $config;
            //check permissions
            if ($config["MEMBERSONLY"]){
                loggedinonly();
            
                if($_SESSION["view_torrents"]=="no")
                    show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
            }
            
            
            //GET SEARCH STRING
            $searchstr = trim($_GET["search"] ?? '');
            $cleansearchstr = searchfield($searchstr);
            if (empty($cleansearchstr))
            unset($cleansearchstr);
            
            $thisurl = "torrents/search?";
            
            $addparam = "";
            $wherea = array();
            $wherecatina = array();
            $wherea[] = "banned = 'no'";
            
            $wherecatina = array();
            $wherecatin = "";
            $res = $this->torrentModel->getCatById();
            while($row = $res->fetch(PDO::FETCH_LAZY)){
                if (isset($_GET["c$row[id]"])) {
                    $wherecatina[] = $row['id'];
                    $addparam .= "c$row[id]=1&amp;";
                    $addparam .= "c$row[id]=1&amp;";
                    $thisurl .= "c$row[id]=1&amp;";
                }
                $wherecatin = implode(", ", $wherecatina);
            }
            if ($wherecatin)
                $wherea[] = "category IN ($wherecatin)";
            
            $_GET['incldead'] = (int) ($_GET['incldead'] ?? 0);
            $_GET['freeleech'] = (int) ($_GET['freeleech'] ?? 0);
            $_GET['inclexternal'] = (int) ($_GET['inclexternal'] ?? 0);
            $_GET['cat'] = $_GET['cat'] ?? '';
            
            //include dead
            if ($_GET["incldead"] == 1) {
                $addparam .= "incldead=1&amp;";
                $thisurl .= "incldead=1&amp;";
            }elseif ($_GET["incldead"] == 2){
                $wherea[] = "visible = 'no'";
                $addparam .= "incldead=2&amp;";
                $thisurl .= "incldead=2&amp;";
            }else
                $wherea[] = "visible = 'yes'";
            
            // Include freeleech
            if ($_GET["freeleech"] == 1) {
                $addparam .= "freeleech=1&amp;";
                $thisurl .= "freeleech=1&amp;";
                $wherea[] = "freeleech = '0'";
            } elseif ($_GET["freeleech"] == 2) {
                $addparam .= "freeleech=2&amp;";
                $thisurl .= "freeleech=2&amp;";
                $wherea[] = "freeleech = '1'";
            }
            
            
            
            //include external
            if ($_GET["inclexternal"] == 1) {
                $addparam .= "inclexternal=1&amp;";
                $wherea[] = "external = 'no'";
            }
            
            if ($_GET["inclexternal"] == 2) {
                $addparam .= "inclexternal=2&amp;";
                $wherea[] = "external = 'yes'";
            }
            
            //cat
            if ($_GET["cat"]) { 
                    $wherea[] = "category = " . sqlesc($_GET["cat"]);
                    $wherecatina[] = sqlesc($_GET["cat"]);
                    $addparam .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
                $thisurl .= "cat=".urlencode($_GET["cat"])."&amp;";
            }
            
            //language
            if ($_GET["lang"] ?? '') {
                $wherea[] = "torrentlang = " . sqlesc($_GET["lang"]);
                $addparam .= "lang=" . urlencode($_GET["lang"]) . "&amp;";
                $thisurl .= "lang=".urlencode($_GET["lang"])."&amp;";
            }
            
            //parent cat
            if ($_GET["parent_cat"] ?? '') {
                $addparam .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&amp;";
                $thisurl .= "parent_cat=".urlencode($_GET["parent_cat"])."&amp;";
            }
            
            $parent_cat = $_GET["parent_cat"] ?? '';
            
            $wherebase = $wherea;
            
            if (isset($cleansearchstr)) {
                $wherea[] = "MATCH (torrents.name) AGAINST ('".$searchstr."' IN BOOLEAN MODE)";
            
                $addparam .= "search=" . urlencode($searchstr) . "&amp;";
                $thisurl .= "search=".urlencode($searchstr)."&amp;";
            }
            
            //order by
            if ($_GET['sort'] ?? '' && $_GET['order']) {
                $column = '';
                $ascdesc = '';
                switch($_GET['sort']) {
                    case 'id': $column = "id"; break;
                    case 'name': $column = "name"; break;
                    case 'comments': $column = "comments"; break;
                    case 'size': $column = "size"; break;
                    case 'times_completed': $column = "times_completed"; break;
                    case 'seeders': $column = "seeders"; break;
                    case 'leechers': $column = "leechers"; break;
                    case 'category': $column = "category"; break;
                    default: $column = "id"; break;
                }
            
                switch($_GET['order']) {
                    case 'asc': $ascdesc = "ASC"; break;
                    case 'desc': $ascdesc = "DESC"; break;
                    default: $ascdesc = "DESC"; break;
                }
            } else {
                $_GET["sort"] = "id";
                $_GET["order"] = "desc";
                $column = "id";
                $ascdesc = "DESC";
            }
            
                $orderby = "ORDER BY torrents." . $column . " " . $ascdesc;
                $pagerlink = "sort=" . $_GET['sort'] . "&amp;order=" . $_GET['order'] . "&amp;";
            
            if (is_valid_id($_GET["page"] ?? ''))
                $thisurl .= "page=$_GET[page]&amp;";
            
            
            $where = implode(" AND ", $wherea);
            
            if ($where != "")
                $where = "WHERE $where";
            
            $parent_check = "";
            if ($parent_cat){
                $parent_check = " AND categories.parent_cat=".sqlesc($parent_cat);
            }
            
            
            //GET NUMBER FOUND FOR PAGER
            $count = $this->torrentModel->getTorrentWhere ($where, $parent_check);
            // $count = $row[0];
            
            
            if (!$count && isset($cleansearchstr)) {
                $wherea = $wherebase;
                $searcha = explode(" ", $cleansearchstr);
                $sc = 0;
                foreach ($searcha as $searchss) {
                    if (strlen($searchss) <= 1)
                    continue;
                    $sc++;
                    if ($sc > 5)
                    break;
                    $ssa = array();
                    foreach (array("torrents.name") as $sss)
                    $ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
                    $wherea[] = "(" . implode(" OR ", $ssa) . ")";
                }
                if ($sc) {
                    $where = implode(" AND ", $wherea);
                    if ($where != "")
                    $where = "WHERE $where";
                    $row = DB::run("SELECT COUNT(*) FROM torrents $where $parent_check")->fetch();
                    $count = $row[0];
                }
            }
            
            //Sort by
            if ($addparam != "") { 
                if ($pagerlink != "") {
                    if ($addparam[strlen($addparam)-1] != ";") { // & = &amp;
                        $addparam = $addparam . "&amp;" . $pagerlink;
                    } else {
                        $addparam = $addparam . $pagerlink;
                    }
                }
            } else {
                $addparam = $pagerlink;
            }
            
            
            
            if ($count) {
            
                //SEARCH QUERIES! 
                list($pagertop, $pagerbottom, $limit) = pager(20, $count, "torrents/search?" . $addparam);
            $res = $this->torrentModel->getTorrentByCat ($where, $parent_check, $orderby, $limit) ;
            
                }else{
                    unset($res);
            }
            
            if (isset($cleansearchstr))
                stdhead(T_("SEARCH_RESULTS_FOR")." \"" . htmlspecialchars($searchstr) . "\"");
            else
                stdhead(T_("BROWSE_TORRENTS"));
            
            begin_frame(T_("SEARCH_TORRENTS"));
            
            // get all parent cats
            echo "<center><b>".T_("CATEGORIES").":</b> ";
            $catsquery = $this->torrentModel->getCatByParent () ;
            echo " - <a href='$config[SITEURL]torrents/browse'>".T_("SHOWALL")."</a>";
            while($catsrow = $catsquery->fetch(PDO::FETCH_ASSOC)){
                    echo " - <a href='$config[SITEURL]torrents/browse?parent_cat=".urlencode($catsrow['parent_cat'])."'>$catsrow[parent_cat]</a>";
            }
            echo "</center>";
            
            ?>
            <br /><br />
            
            <center>
            <form method="get" action="<?php echo TTURL; ?>/torrents/search">
            <table border="0" align="center">
            <tr align='right'>
            <?php
            $i = 0;
            $cats = $this->torrentModel->getCatByParentName();
            while ($cat = $cats->fetch(PDO::FETCH_ASSOC)) {
                $catsperrow = 5;
                print(($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : "");
                print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a href='$config[SITEURL]/torrents/browse?cat={$cat["id"]}'>".htmlspecialchars($cat["parent_cat"])." - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) || $_GET["cat"] == $cat["id"]  ? "checked='checked' " : "") . "value='1' /></td>\n");
                $i++;                                                                                                                                                                                                                                                                                                                 
            }
            echo "</tr></table>";
            
            //if we are browsing, display all subcats that are in same cat
            if ($parent_cat){
                echo "<br /><br /><b>".T_("YOU_ARE_IN").":</b> <a href='$config[SITEURL]/torrents/browse?parent_cat=$parent_cat'>$parent_cat</a><br /><b>".T_("SUB_CATS").":</b> ";
                $subcatsquery = $this->torrentModel->getSubCatByParentName ($parent_cat) ;
                while($subcatsrow = $subcatsquery->fetch(PDO::FETCH_ASSOC)){
                    $name = $subcatsrow['name'];
                    echo " - <a href='$config[SITEURL]/torrents/browse?cat=$subcatsrow[id]'>$name</a>";
                }
            }	
            
            echo "<br /><br />";//some spacing
            
            ?>
            
                
                <?php print(T_("SEARCH")); ?>
                <input type="text" name="search" size="40" value="<?php echo  stripslashes(htmlspecialchars($searchstr)) ?>" />
                <?php print(T_("IN")); ?>
                <select name="cat">
                <option value="0"><?php echo "(".T_("ALL")." ".T_("TYPES").")";?></option>
                <?php
            
            
                $cats = genrelist();
                $catdropdown = "";
                foreach ($cats as $cat) {
                
                    $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
                    if ($cat["id"] == $_GET["cat"])
                        $catdropdown .= " selected=\"selected\"";
                    $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
                }
            
                ?>
                <?php echo  $catdropdown ?>
                </select>
            
                <br /><br />
                <select name="incldead">
                 <option value="0"><?php echo T_("ACTIVE_TRANSFERS"); ?></option>
                <option value="1" <?php if ($_GET["incldead"] == 1) echo "selected='selected'"; ?>><?php echo T_("INC_DEAD");?></option>
                <option value="2" <?php if ($_GET["incldead"] == 2) echo "selected='selected'"; ?>><?php echo T_("ONLY_DEAD");?></option>
                </select>
                <select name="freeleech">
                <option value="0"><?php echo T_("ALL"); ?></option>
                <option value="1" <?php if ($_GET["freeleech"] == 1) echo "selected='selected'"; ?>><?php echo T_("NOT_FREELEECH");?></option>
                <option value="2" <?php if ($_GET["freeleech"] == 2) echo "selected='selected'"; ?>><?php echo T_("ONLY_FREELEECH");?></option>
                 </select>
            
                <?php if ($config["ALLOWEXTERNAL"]){?>
                    <select name="inclexternal">
                     <option value="0"><?php echo T_("LOCAL_EXTERNAL"); ?></option>
                    <option value="1" <?php if ($_GET["inclexternal"] == 1) echo "selected='selected'"; ?>><?php echo T_("LOCAL_ONLY");?></option>
                    <option value="2" <?php if ($_GET["inclexternal"] == 2) echo "selected='selected'"; ?>><?php echo T_("EXTERNAL_ONLY");?></option>
                     </select>
                <?php } ?>
            
                <select name="lang">
                <option value="0"><?php echo "(".T_("ALL").")";?></option>
                <?php
                $lang = langlist();
                $langdropdown = "";
                foreach ($lang as $lang) {
                    $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
                    if ($lang["id"] == $_GET["lang"])
                        $langdropdown .= " selected=\"selected\"";
                    $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
                }
                
                ?>
                <?php echo  $langdropdown ?>
                </select>
                <button type='submit' class='btn btn-sm btn-primary'><?php print T_("SEARCH"); ?></button>
                <br />
                </form>
                <?php print T_("SEARCH_RULES"); ?><br />
                </center>
                
            <?php
            
            if ($count) {
            // New code (TorrentialStorm)
                echo "<form id='sort' action=''><div align='right'>".T_("SORT_BY").": <select name='sort' onchange='window.location=\"{$thisurl}sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
                echo "<option value='id'" . ($_GET["sort"] == "id" ? " selected='selected'" : "") . ">".T_("ADDED")."</option>";
                echo "<option value='name'" . ($_GET["sort"] == "name" ? " selected='selected'" : "") . ">".T_("NAME")."</option>";
                echo "<option value='comments'" . ($_GET["sort"] == "comments" ? " selected='selected'" : "") . ">".T_("COMMENTS")."</option>";
                echo "<option value='size'" . ($_GET["sort"] == "size" ? " selected='selected'" : "") . ">".T_("SIZE")."</option>";
                echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") . ">".T_("COMPLETED")."</option>";
                echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? " selected='selected'" : "") . ">".T_("SEEDERS")."</option>";
                echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? " selected='selected'" : "") . ">".T_("LEECHERS")."</option>";
                echo "</select>&nbsp;";
                echo "<select name='order' onchange='window.location=\"{$thisurl}order=\"+this.options[this.selectedIndex].value+\"&amp;sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value'>";
                echo "<option selected='selected' value='asc'" . ($_GET["order"] == "asc" ? " selected='selected'" : "") . ">".T_("ASCEND")."</option>";
                echo "<option value='desc'" . ($_GET["order"] == "desc" ? " selected='selected'" : "") . ">".T_("DESCEND")."</option>";
                echo "</select>";
                echo "</div>";
                echo "</form>";
            
            // End
                torrenttable($res);
                print($pagerbottom);
            }else {
                
                 print("<div class='f-border'>");
                 print("<div class='f-cat' width='100%'>".T_("NOTHING_FOUND")."</div>");
                 print("<div>");
                 print T_("NO_RESULTS");
                 print("</div>");
                 print("</div>");
                 
            }
            
            if ($_SESSION['loggedin']  == true)
                DB::run("UPDATE users SET last_browse=".gmtime()." WHERE id=$_SESSION[id]");
            end_frame();
            stdfoot();
                }

                public function browse(){

                    dbconn();
                    global $config, $pdo;
                    //check permissions
                    if ($config["MEMBERSONLY"]){
                        loggedinonly();
                    
                        if($_SESSION["view_torrents"]=="no")
                            show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
                    }
                    
                    //get http vars
                    $addparam = "";
                    $wherea = array();
                    $wherea[] = "visible = 'yes'";
                    $thisurl = "torrents/browse?";
                    
                    if ($_GET["cat"]) {
                        $wherea[] = "category = " . sqlesc($_GET["cat"]);
                        $addparam .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
                        $thisurl .= "cat=".urlencode($_GET["cat"])."&amp;";
                    }
                    
                    if ($_GET["parent_cat"]) {
                        $addparam .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&amp;";
                        $thisurl .= "parent_cat=".urlencode($_GET["parent_cat"])."&amp;";
                        $wherea[] = "categories.parent_cat=".sqlesc($_GET["parent_cat"]);
                    }
                    
                    $parent_cat = $_GET["parent_cat"];
                    $category = (int) $_GET["cat"];
                    
                    $where = implode(" AND ", $wherea);
                    $wherecatina = array();
                    $wherecatin = "";
                    $res =$this->torrentModel->getCatById ();
                    while($row = $res->fetch(PDO::FETCH_LAZY)){
                        if ($_GET["c$row[id]"]) {
                            $wherecatina[] = $row["id"];
                            $addparam .= "c$row[id]=1&amp;";
                            $thisurl .= "c$row[id]=1&amp;";
                        }
                        $wherecatin = implode(", ", $wherecatina);
                    }
                    
                    if ($wherecatin)
                        $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";
                    
                    if ($where != "")
                        $where = "WHERE $where";
                    
                    if ($_GET["sort"] || $_GET["order"]) {
                    
                        switch ($_GET["sort"]) {
                            case 'name': $sort = "torrents.name"; $addparam .= "sort=name&amp;"; break;
                            case 'times_completed':    $sort = "torrents.times_completed"; $addparam .= "sort=times_completed&amp;"; break;
                            case 'seeders':    $sort = "torrents.seeders"; $addparam .= "sort=seeders&amp;"; break;
                            case 'leechers': $sort = "torrents.leechers"; $addparam .= "sort=leechers&amp;"; break;
                            case 'comments': $sort = "torrents.comments"; $addparam .= "sort=comments&amp;"; break;
                            case 'size': $sort = "torrents.size"; $addparam .= "sort=size&amp;"; break;
                            default: $sort = "torrents.id";
                        }
                    
                        if ($_GET["order"] == "asc" || ($_GET["sort"] != "id" && !$_GET["order"])) {
                            $sort .= " ASC";
                            $addparam .= "order=asc&amp;";
                        } else {
                            $sort .= " DESC";
                            $addparam .= "order=desc&amp;";
                        }
                    
                        $orderby = "ORDER BY $sort";
                    
                        }else{
                            $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC";
                            $_GET["sort"] = "id";
                            $_GET["order"] = "desc";
                        }
                    
                    //Get Total For Pager
                    $count = $this->torrentModel->getCatwhere($where);
                    
                    //get sql info
                    if ($count) {
                        list($pagertop, $pagerbottom, $limit) = pager(20, $count, "torrents/browse?" . $addparam);
                        $query = "SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.sticky, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.tube, torrents.imdb, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
                        $res = DB::run($query);
                    }else{
                        unset($res);
                    }
                    
                    stdhead(T_("BROWSE_TORRENTS"));
                    begin_frame(T_("BROWSE_TORRENTS"));
                    
                    // get all parent cats
                    echo "<center><b>".T_("CATEGORIES").":</b> ";
                    $catsquery = $this->torrentModel->getCatByParent () ;
                    echo " - <a href='$config[SITEURL]/torrents/browse'>".T_("SHOW_ALL")."</a>";
                    while($catsrow = $catsquery->fetch(PDO::FETCH_ASSOC)){
                            echo " - <a href='$config[SITEURL]/torrents/browse/?parent_cat=".urlencode($catsrow['parent_cat'])."'>$catsrow[parent_cat]</a>";
                    }
                    
                    ?>
                    <br /><br />
                    <form method="get" action="torrents/browse">
                    <table align="center">
                    <tr align='right'>
                    <?php
                    $i = 0;
                    
                    $cats = $this->torrentModel->getCatByParentName () ;
                    while ($cat = $cats->fetch(PDO::FETCH_ASSOC)) {
                        $catsperrow = 5;
                        print(($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : "");
                        print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a href='$config[SITEURL]/torrents/browse?cat={$cat["id"]}'>".htmlspecialchars($cat["parent_cat"])." - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) || $_GET["cat"] == $cat["id"] ? "checked='checked' " : "") . "value='1' /></td>\n");
                        $i++;
                    }
                    echo "</tr><tr align='center'><td colspan='$catsperrow' align='center'><input type='submit' value='".T_("GO")."' /></td></tr>";
                    echo "</table></form>";
                    
                    //if we are browsing, display all subcats that are in same cat
                    if ($parent_cat){
                        $thisurl .= "parent_cat=".urlencode($parent_cat)."&amp;";
                        echo "<br /><br /><b>".T_("YOU_ARE_IN").":</b> <a href='torrents/browse?parent_cat=".urlencode($parent_cat)."'>".htmlspecialchars($parent_cat)."</a><br /><b>".T_("SUB_CATS").":</b> ";
                        $subcatsquery = DB::run("SELECT id, name, parent_cat FROM categories WHERE parent_cat=".sqlesc($parent_cat)." ORDER BY name");
                        while($subcatsrow = $subcatsquery->fetch(PDO::FETCH_ASSOC)){
                            $name = $subcatsrow['name'];
                            echo " - <a href='$config[SITEURL]/torrents/browse?cat=$subcatsrow[id]'>$name</a>";
                        }
                    }
                    
                    if (is_valid_id($_GET["page"]))
                        $thisurl .= "page=$_GET[page]&amp;";
                    
                    echo "</center><br /><br />";//some spacing
                    
                    // New code (TorrentialStorm)
                        echo "<div align='right'><form id='sort' action=''>".T_("SORT_BY").": <select name='sort' onchange='window.location=\"{$thisurl}sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
                        echo "<option value='id'" . ($_GET["sort"] == "id" ? " selected='selected'" : "") . ">".T_("ADDED")."</option>";
                        echo "<option value='name'" . ($_GET["sort"] == "name" ? " selected='selected'" : "") . ">".T_("NAME")."</option>";
                        echo "<option value='comments'" . ($_GET["sort"] == "comments" ? " selected='selected'" : "") . ">".T_("COMMENTS")."</option>";
                        echo "<option value='size'" . ($_GET["sort"] == "size" ? " selected='selected'" : "") . ">".T_("SIZE")."</option>";
                        echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") . ">".T_("COMPLETED")."</option>";
                        echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? " selected='selected'" : "") . ">".T_("SEEDERS")."</option>";
                        echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? " selected='selected'" : "") . ">".T_("LEECHERS")."</option>";
                        echo "</select>&nbsp;";
                        echo "<select name='order' onchange='window.location=\"{$thisurl}order=\"+this.options[this.selectedIndex].value+\"&amp;sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value'>";
                        echo "<option selected='selected' value='asc'" . ($_GET["order"] == "asc" ? " selected='selected'" : "") . ">".T_("ASCEND")."</option>";
                        echo "<option value='desc'" . ($_GET["order"] == "desc" ? " selected='selected'" : "") . ">".T_("DESCEND")."</option>";
                        echo "</select>";
                        echo "</form></div>";
                    
                    // End
                    
                    if ($count) {
                        torrenttable($res);
                        print($pagerbottom);
                    }else {
                        
                         print("<div class='f-border'>");
                         print("<div class='f-cat' width='100%'>".T_("NOTHING_FOUND")."</div>");
                         print("<div>");
                         print T_("NO_UPLOADS");
                         print("</div>");
                         print("</div>");
                        
                    }
                    
                    if ($_SESSION)
                        DB::run("UPDATE users SET last_browse=? WHERE id=?", [gmtime(), $_SESSION['id']]);
                    
                    end_frame();
                    stdfoot();
                    }
}