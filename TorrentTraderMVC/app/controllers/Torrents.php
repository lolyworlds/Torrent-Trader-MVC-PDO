<?php
class Torrents extends Controller
{
    public function __construct()
    {
        Auth::user();
        $this->torrentModel = $this->model('Torrent');
        $this->valid = new Validation();
        $this->logs = $this->model('Logs');
    }

    public function index()
    {
    }

    public function read()
    {
        //check permissions
        if ($_SESSION["view_torrents"] != "yes") {
            Session::flash('info', Lang::T("NO_TORRENT_VIEW"), URLROOT . "/home");
        }
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            Session::flash("warning", Lang::T("THATS_NOT_A_VALID_ID"), URLROOT . "/home");
        }
        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.tube, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.imdb, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, torrents.vip, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        //DECIDE IF TORRENT EXISTS
        if (!$row || ($row["banned"] == "yes" && $_SESSION["edit_torrents"] == "no")) {
            Session::flash("info", Lang::T("TORRENT_NOT_FOUND"), URLROOT . "/home");
        }
        // vip
        $vip = $row["vip"];
        if ($vip == "yes") {
            $vip = "<b>Yes</b>";
        } else {
            $vip = "<b>No</b>";
        }
        // freeleech
        $freeleech = $row["freeleech"];
        if ($freeleech == 1) {
            $freeleech = "<font color=green><b>Yes</b></font>";
        } else {
            $freeleech = "<font color=red><b>No</b></font>";
        }
        //torrent is availiable so do some stuff
        if ($_GET["hit"]) {
            DB::run("UPDATE torrents SET views = views + 1 WHERE id = $id");
            Redirect::to(URLROOT . "/torrents/read?id=$id");
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
            $speedQ = DB::run("SELECT (SUM(p.downloaded)) / (UNIX_TIMESTAMP('" . TimeDate::get_date_time() . "') - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$id' GROUP BY t.id ORDER BY added ASC LIMIT 15");
            $a = $speedQ->fetch(PDO::FETCH_ASSOC);
            $totalspeed = mksize($a["totalspeed"]) . "/s";
        } else {
            $totalspeed = Lang::T("NO_ACTIVITY");
        }
        $torrent1 = $this->torrentModel->getAll($id);
        $title = Lang::T("DETAILS_FOR_TORRENT") . " \"" . $row["name"] . "\"";

        $data = [
            'title' => $title,
            'row' => $row,
            'owned' => $owned,
            'shortname' => $shortname,
            'speed' => $totalspeed,
            'id' => $id,
            'selecttor' => $torrent1,
        ];
        $this->view('torrent/read', $data);
    }

    public function edit()
    {
        $id = (int) $_REQUEST["id"];
        if (!$this->valid->validId($id)) {
            Session::flash("info", Lang::T("INVALID_ID"), URLROOT . "/home");
        }
        $action = $_REQUEST["action"];
        $row = DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
        if ($_SESSION["edit_torrents"] == "no" && $_SESSION['id'] != $row['owner']) {
            Session::flash("info", Lang::T("NO_TORRENT_EDIT_PERMISSION"), URLROOT . "/torrents/read?id=$id");
        }
        //GET DATA FROM DB
        $row = DB::run("SELECT * FROM torrents WHERE id =?", [$id])->fetch();
        if (!$row) {
            Session::flash("info", Lang::T("TORRENT_ID_GONE"), URLROOT . "/torrents/read?id=$id");
        }

        $torrent_dir = TORRENTDIR;
        $nfo_dir = NFODIR;
        //UPDATE CATEGORY DROPDOWN
        $catdropdown = "<select name=\"type\">\n";
        $cats = genrelist();
        foreach ($cats as $catdropdownubrow) {
            $catdropdown .= "<option value=\"" . $catdropdownubrow["id"] . "\"";
            if ($catdropdownubrow["id"] == $row["category"]) {
                $catdropdown .= " selected=\"selected\"";
            }
            $catdropdown .= ">" . htmlspecialchars($catdropdownubrow["parent_cat"]) . ": " . htmlspecialchars($catdropdownubrow["name"]) . "</option>\n";
        }
        $catdropdown .= "</select>\n";
        //END CATDROPDOWN

        //UPDATE TORRENTLANG DROPDOWN
        $langdropdown = "<select name=\"language\"><option value='0'>Unknown</option>\n";
        $lang = langlist();
        foreach ($lang as $lang) {
            $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
            if ($lang["id"] == $row["torrentlang"]) {
                $langdropdown .= " selected=\"selected\"";
            }
            $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
        }
        $langdropdown .= "</select>\n";
        //END TORRENTLANG

        $char1 = 55;
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);
        if ($_GET["edited"]) { // todo
            show_error_msg("Edited OK", Lang::T("TORRENT_EDITED_OK"), 1);
        }

        $torrent1 = $this->torrentModel->getAll($id);
        $title = Lang::T("EDIT_TORRENT") . " \"$shortname\"";
        $data = [
            'title' => $title,
            'row' => $row,
            'catdrop' => $catdropdown,
            'shortname' => $shortname,
            'langdrop' => $langdropdown,
            'id' => $id,
            'selecttor' => $torrent1,
        ];
        $this->view('torrent/edit', $data, true);
    }

    public function submit()
    {
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            Session::flash("info", Lang::T("INVALID_ID"), URLROOT . "/home");
        }
        $row = DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
        if ($_SESSION["edit_torrents"] == "no" && $_SESSION['id'] != $row['owner']) {
            Session::flash("info", Lang::T("NO_TORRENT_EDIT_PERMISSION"), URLROOT . "/home");
        }
        //GET DATA FROM DB
        $row = DB::run("SELECT * FROM torrents WHERE id =?", [$id])->fetch();
        if (!$row) {
            Session::flash("info", Lang::T("TORRENT_ID_GONE"), URLROOT . "/torrents/read?id=$id");
        }
        $torrent_dir = TORRENTDIR;
        $nfo_dir = NFODIR;
        //DO THE SAVE TO DB HERE
        if (Input::exist()) {
            $updateset = array();
            $nfoaction = $_POST['nfoaction'];
            if ($nfoaction == "update") {
                $nfofile = $_FILES['nfofile'];
                if (!$nfofile) {
                    die("No data " . var_dump($_FILES));
                }
                if ($nfofile['size'] > 65535) {
                    Session::flash("info", "NFO is too big! Max 65,535 bytes.", URLROOT . "/torrents/read?id=$id");
                }
                $nfofilename = $nfofile['tmp_name'];
                if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0) {
                    @move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
                    $updateset[] = "nfo = 'yes'";
                } //success
            }
            if (!empty($_POST["name"])) {
                $updateset[] = "name = " . sqlesc($_POST["name"]);
            }
            // IMDB
            if ($_POST['imdb'] != $row['imdb']) {
                $updateset[] = "imdb = " . sqlesc($_POST["imdb"]);
                $TTCache = new Cache();
                $TTCache->Delete("imdb/$id");
            }
            $updateset[] = "descr = " . sqlesc($_POST["descr"]);
            $updateset[] = "category = " . (int) $_POST["type"];
            if ($_SESSION["class"] >= 5) { // lowest class to make torrent sticky.
                if ($_POST["sticky"] == "yes") {
                    $updateset[] = "sticky = 'yes'";
                } else {
                    $updateset[] = "sticky = 'no'";
                }
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
            if (!empty($_POST['tube'])) {
                $tube = unesc($_POST['tube']);
            }

            $updateset[] = "tube = " . sqlesc($tube);

            if ($_SESSION["edit_torrents"] == "yes") {
                $updateset[] = "freeleech = '" . ($_POST["freeleech"] ? "1" : "0") . "'";
            }

            $updateset[] = "vip = '" . ($_POST["vip"] ? "yes" : "no") . "'";
            $updateset[] = "anon = '" . ($_POST["anon"] ? "yes" : "no") . "'";

            //update images
            $img1action = $_POST['img1action'];
            if ($img1action == "update") {
                $updateset[] = "image1 = " . sqlesc(uploadimage(0, $row["image1"], $id));
            }

            if ($img1action == "delete") {
                if ($row['image1']) {
                    $del = unlink(TORRENTDIR . "/images/$row[image1]");
                    $updateset[] = "image1 = ''";
                }
            }

            $img2action = $_POST['img2action'];
            if ($img2action == "update") {
                $updateset[] = "image2 = " . sqlesc(uploadimage(1, $row["image2"], $id));
            }

            if ($img2action == "delete") {
                if ($row['image2']) {
                    $del = unlink(TORRENTDIR . "/images/$row[image2]");
                    $updateset[] = "image2 = ''";
                }
            }

            DB::run("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");
            Logs::write("Torrent $id (" . htmlspecialchars($_POST["name"]) . ") was edited by $_SESSION[username]");
            Redirect::to(URLROOT . "/torrents/read?id=$id");
            die();
        } //END SAVE TO DB
    }

    public function delete()
    {
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            Session::flash("info", Lang::T("INVALID_ID"), URLROOT . "/torrents/read?id=$id");
        }
        $row = DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
        if ($_SESSION["delete_torrents"] == "no" && $_SESSION['id'] != $row['owner']) {
            Session::flash("info", Lang::T("NO_TORRENT_DELETE_PERMISSION"), URLROOT . "/torrents/read?id=$id");
        }
        $owner = $row['owner'];
        //GET DATA FROM DB
        $row = DB::run("SELECT * FROM torrents WHERE id =?", [$id])->fetch();
        if (!$row) {
            Session::flash("info", Lang::T("TORRENT_ID_GONE"), URLROOT . "/torrents/read?id=$id");
        }
        $torrname = $row['owner'];
        $char1 = 55;
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);
        $title = Lang::T("DELETE_TORRENT") . " \"$shortname\"";
        $data = [
            'title' => $title,
            'owner' => $owner,
            'id' => $id,
            'name' => $torrname,
        ];
        $this->view('torrent/delete', $data, true);
    }

    public function deleteok()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $torrentid = (int) $_POST["torrentid"];
            $delreason = $_POST["delreason"];
            $torrentname = $_POST["torrentname"];
            if (!$this->valid->validId($torrentid)) {
                Session::flash("info", Lang::T("INVALID_TORRENT_ID"), URLROOT . "/torrents/delete?id=$torrentid");
            }
            if (!$delreason) {
                Session::flash("info", Lang::T("MISSING_FORM_DATA"), URLROOT . "/torrents/delete?id=$torrentid");
            }
            deletetorrent($torrentid);
            DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$torrentid])->fetch();
            Logs::write($_SESSION['username'] . " has deleted torrent: ID:$torrentid - " . htmlspecialchars($torrentname) . " - Reason: " . htmlspecialchars($delreason));
            if ($_SESSION['id'] != $torrentid) {
                $delreason = $_POST["delreason"];
                DB::run("INSERT INTO messages (sender, receiver, added, subject, msg, unread, location)
                         VALUES(?,?,?,?,?,?,?)",
                    [0, $torrentid, TimeDate::get_date_time(), 'Your torrent ' . $torrentname . ' has been deleted by ' . $_SESSION['username'], $torrentname . ' was deleted by ' . $_SESSION['username'] . ' Reason: $delreason', 'yes', 'in']);
            }
            Session::flash("info", htmlspecialchars($torrentname) . " " . Lang::T("HAS_BEEN_DEL_DB"), URLROOT . "/torrents/read?id=$torrentid");
            die;
        }
    }

    public function torrentfilelist()
    {
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            Session::flash("info", Lang::T("THATS_NOT_A_VALID_ID"), URLROOT . "/torrents/read?id=$id");
        }
        //check permissions
        if ($_SESSION["view_torrents"] == "no") {
            Session::flash("info", Lang::T("NO_TORRENT_VIEW"), URLROOT . "/home");
        }
        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $fres = DB::run("SELECT * FROM `files` WHERE `torrent` = $id ORDER BY `path` ASC");
        $char1 = 50; //cut length
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);
        $title = Lang::T("TORRENT_DETAILS_FOR") . " \"" . $shortname . "\"";
        $data = [
            'title' => $title,
            'row' => $row,
            'shortname' => $shortname,
            'id' => $id,
            'name' => $row["name"],
            'size' => $row["size"],
            'fres' => $fres
        ];
        $this->view('torrent/filelist', $data, true);
    }

    public function torrenttrackerlist()
    {
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            Session::flash("info", Lang::T("THATS_NOT_A_VALID_ID"), URLROOT . "/torrents/read?id=$id");
        }
        if ($_SESSION["view_torrents"] == "no") {
            Session::flash("info", Lang::T("NO_TORRENT_VIEW"), URLROOT . "/home");
        }
        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $tres = DB::run("SELECT * FROM `announce` WHERE `torrent` = $id");
        $data = [
            'id' => $id,
            'res' => $res,
            'tres' => $tres,
        ];
        $this->view('torrent/trackerlist', $data, true);
    }

}