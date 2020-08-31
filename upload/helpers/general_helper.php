<?php
// Function To Display Error Messages taken from forum
function showerror($heading = "Error", $text, $sort = "Error")
{
    stdhead("$sort: $heading");
    begin_frame("<font class='error'>$sort: $heading</font>");
    echo $text;
    end_frame();
    stdfoot();
    die;
}

// Function To Display Error Messages
function show_error_msg($title, $message, $wrapper = "1")
{
    if ($wrapper) {
        ob_start();
        ob_clean();
        stdhead($title);
    }
    begin_frame("<font class='error'>" . htmlspecialchars($title) . "</font>");
    print("<center><b>" . $message . "</b></center>\n");
    end_frame();

    if ($wrapper) {
        stdfoot();
        die();
    }
}

// Function To Count A Data Established In A Data Table
function get_row_count($table, $suffix = "")
{
    global $pdo;
    $suffix = !empty($suffix) ? ' ' . $suffix : '';
    $row = $pdo->run("SELECT COUNT(*) FROM $table $suffix")->fetchColumn();
    return $row;
}

function array_map_recursive($callback, $array)
{
    $ret = array();
    if (!is_array($array)) {
        return $callback($array);
    }

    foreach ($array as $key => $val) {
        $ret[$key] = array_map_recursive($callback, $val);
    }
    return $ret;
}

// Automatic Original Redirection Function
function autolink($al_url, $al_msg)
{
    stdhead();
    begin_frame("");
    echo "\n<meta http-equiv=\"refresh\" content=\"3; url=$al_url\">\n";
    echo "<b>$al_msg</b>\n";
    echo "\n<b>Redirecting ...</b>\n";
    echo "\n[ <a href='$al_url'>link</a> ]\n";
    end_frame();
    stdfoot();
    exit;
}

function write_log($text)
{
    global $pdo;
    $text = $text;
    $added = get_date_time();
    $pdo->run("INSERT INTO log (added, txt) VALUES (?,?)", [$added, $text]);
}

/// each() replacement for php 7+. Change all instances of each() to thisEach() in all TT files. each() deprecated as of 7.2
function thisEach(&$arr)
{
    $key = key($arr);
    $result = ($key === null) ? false : [$key, current($arr), 'key' => $key, 'value' => current($arr)];
    next($arr);
    return $result;
}

function mksize($s, $precision = 2)
{
    $suf = array("B", "kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");

    for ($i = 1, $x = 0; $i <= count($suf); $i++, $x++) {
        if ($s < pow(1024, $i) || $i == count($suf)) // Change 1024 to 1000 if you want 0.98GB instead of 1,0000MB
        {
            return number_format($s / pow(1024, $x), $precision) . " " . $suf[$x];
        }

    }
}

function CutName($vTxt, $Car)
{
    if (strlen($vTxt) > $Car) {
        return substr($vTxt, 0, $Car) . "...";
    }
    return $vTxt;
}

function searchfield($s)
{
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function strtobytes($str)
{
    $str = trim($str);
    if (!preg_match('!^([\d\.]+)\s*(\w\w)?$!', $str, $matches)) {
        return 0;
    }

    $num = $matches[1];
    $suffix = strtolower($matches[2]);
    switch ($suffix) {
        case "tb": // TeraByte
            return $num * 1099511627776;
        case "gb": // GigaByte
            return $num * 1073741824;
        case "mb": // MegaByte
            return $num * 1048576;
        case "kb": // KiloByte
            return $num * 1024;
        case "b": // Byte
        default:
            return $num;
    }
}

function usermenu($id)
{
    global $config;
    ?>
    <a href='<?php echo TTURL; ?>/users/profile?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Profile</button></a>
    <?php if ($_SESSION["id"] == $id or $_SESSION["class"] > $config['Uploader']) {?>
    <a href='<?php echo TTURL; ?>/users/details?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Edit</button></a>
    <a href='<?php echo TTURL; ?>/users/preferences?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Preferences</button></a>&nbsp;
    <a href='<?php echo TTURL; ?>/users/other?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Other</button></a>&nbsp;
    <?php }?>
    <?php if ($_SESSION["id"] == $id) {?>
    <a href='<?php echo TTURL; ?>/users/changepw?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Password</button></a>
    <a href='<?php echo TTURL; ?>/users/email?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Email</button></a>
    <a href='<?php echo TTURL; ?>/messages'><button type="button" class="btn btn-sm btn-primary">Messages</button></a>
    <a href='<?php echo TTURL; ?>/bonus'><button type="button" class="btn btn-sm btn-primary">Seed Bonus</button></a>
    <?php }?>
    <?php if ($_SESSION["view_users"]) {?>
    <a href='<?php echo TTURL; ?>/friends'><button type="button" class="btn btn-sm btn-primary">Your Friends</button></a>
    <?php }?>
    <?php if ($_SESSION["view_torrents"]) {?>
    <a href='<?php echo TTURL; ?>/peers/seeding?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Seeding</button></a>
    <a href='<?php echo TTURL; ?>/peers/uploaded?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Uploaded</button></a>
    <?php }?>
    <?php if ($_SESSION["class"] > $config['Uploader']) {?>
    <a href='<?php echo TTURL; ?>/warning?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Warn</button></a>
    <a href='<?php echo TTURL; ?>/users/admin?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-success">Admin</button></a>
    <?php }
} //end func

function uploadimage($x, $imgname, $tid)
{
    global $config;

    $imagesdir = $config["torrent_dir"] . "/images";

    $allowed_types = &$config["allowed_image_types"];

    if (!($_FILES["image$x"]["name"] == "")) {
        if ($imgname != "") {
            $img = "$imagesdir/$imgname";
            $del = unlink($img);
        }

        $y = $x + 1;

        $im = getimagesize($_FILES["image$x"]["tmp_name"]);

        if (!$im[2]) {
            show_error_msg(T_("ERROR"), "Invalid Image $y.", 1);
        }

        if (!array_key_exists($im['mime'], $allowed_types)) {
            show_error_msg(T_("ERROR"), T_("INVALID_FILETYPE_IMAGE"), 1);
        }

        if ($_FILES["image$x"]["size"] > $config['image_max_filesize']) {
            show_error_msg(T_("ERROR"), sprintf(T_("INVAILD_FILE_SIZE_IMAGE"), $y), 1);
        }

        $uploaddir = "$imagesdir/";

        $ifilename = $tid . $x . $allowed_types[$im['mime']];

        $copy = copy($_FILES["image$x"]["tmp_name"], $uploaddir . $ifilename);

        if (!$copy) {
            show_error_msg(T_("ERROR"), sprintf(T_("ERROR_UPLOADING_IMAGE"), $y), 1);
        }

        return $ifilename;
    }
} //end func

// Polls
function get_poolsleft($i)
{
    global $config;
    if ($i == 0 || $i == 5 || $i == 10 || $i == 15) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar1-l.png border=0>";
    }

    if ($i == 1 || $i == 6 || $i == 11 || $i == 16) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar2-l.png border=0>";
    }

    if ($i == 2 || $i == 7 || $i == 12 || $i == 17) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar3-l.png border=0>";
    }

    if ($i == 3 || $i == 8 || $i == 13 || $i == 18) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar4-l.png border=0>";
    }

    if ($i == 4 || $i == 9 || $i == 14 || $i == 19) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar5-l.png border=0>";
    }

    return "";
}
function get_poolsmiddle($i)
{
    global $config;
    if ($i == 0 || $i == 5 || $i == 10 || $i == 15) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar1.png border=0";
    }

    if ($i == 1 || $i == 6 || $i == 11 || $i == 16) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar2.png border=0";
    }

    if ($i == 2 || $i == 7 || $i == 12 || $i == 17) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar3.png border=0";
    }

    if ($i == 3 || $i == 8 || $i == 13 || $i == 18) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar4.png border=0";
    }

    if ($i == 4 || $i == 9 || $i == 14 || $i == 19) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar5.png border=0";
    }

    return "";
}
function get_poolsright($i)
{
    global $config;
    if ($i == 0 || $i == 5 || $i == 10 || $i == 15) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar1-r.png border=0>";
    }

    if ($i == 1 || $i == 6 || $i == 11 || $i == 16) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar2-r.png border=0>";
    }

    if ($i == 2 || $i == 7 || $i == 12 || $i == 17) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar3-r.png border=0>";
    }

    if ($i == 3 || $i == 8 || $i == 13 || $i == 18) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar4-r.png border=0>";
    }

    if ($i == 4 || $i == 9 || $i == 14 || $i == 19) {
        return "<img src=" . $config['SITEURL'] . "/images/polls/bar5-r.png border=0>";
    }

    return "";
}

// Error Block
function block_error_msg($title, $message, $wrapper = "1")
{
    if ($wrapper) {
        ob_start();
        ob_clean();
        stdhead($title);
    }
    begin_block("<font class='error'>" . htmlspecialchars($title) . "</font>");
    print("<center><b>" . $message . "</b></center>\n");
    end_block();

    if ($wrapper) {
        stdfoot();
        die();
    }
}