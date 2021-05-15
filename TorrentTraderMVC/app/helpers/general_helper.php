<?php
// get image embeded image
///////////////////////////////////////////////////////////////////
function data_uri($file, $mime) 
{  
    $contents = file_get_contents($file);
    $base64   = base64_encode($contents); 
    return ('data:' . $mime . ';base64,' . $base64);
    }
////////////////////////////////////////////////////////////////

function test()
{
    'http://localhost/TorrentTraderMVC';
}

// Function To Display Error Messages taken from forum
function showerror($text, $heading = "Error", $sort = "Error")
{
    Style::header("$sort: $heading");
    Style::begin("<font class='error'>$sort: $heading</font>");
    echo $text;
    Style::end();
    Style::footer();
    die;
}

// Function To Display Error Messages
function show_error_msg($title, $message, $wrapper = "1")
{
    if ($wrapper) {
        ob_start();
        ob_clean();
        Style::header($title);
    }

    Style::begin("<font class='error'>" . htmlspecialchars($title) . "</font>");
    echo '<div class="alert alert-info">'.$message.'</div>';
    Style::end();

    if ($wrapper) {
        Style::footer();
        die();
    }
}

// Function To Count A Data Established In A Data Table
function get_row_count($table, $suffix = "")
{
    global $pdo;
    $suffix = !empty($suffix) ? ' ' . $suffix : '';
    $row = DB::run("SELECT COUNT(*) FROM $table $suffix")->fetchColumn();
    return $row;
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
     ?>
    <a href='<?php echo URLROOT; ?>/profile?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Profile</button></a>
    <?php if ($_SESSION["id"] == $id or $_SESSION["class"] > _UPLOADER) {?>
    <a href='<?php echo URLROOT; ?>/profile/edit?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Edit</button></a>&nbsp;
    <?php }?>
    <?php if ($_SESSION["id"] == $id) {?>
    <a href='<?php echo URLROOT; ?>/account/changepw?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Password</button></a>
    <a href='<?php echo URLROOT; ?>/account/email?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Email</button></a>
    <a href='<?php echo URLROOT; ?>/messages'><button type="button" class="btn btn-sm btn-primary">Messages</button></a>
    <a href='<?php echo URLROOT; ?>/bonus'><button type="button" class="btn btn-sm btn-primary">Seed Bonus</button></a>
    <?php }?>
    <?php if ($_SESSION["view_users"]) {?>
    <a href='<?php echo URLROOT; ?>/friends?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Your Friends</button></a>
    <?php }?>
    <?php if ($_SESSION["view_torrents"]) {?>
    <a href='<?php echo URLROOT; ?>/peers/seeding?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Seeding</button></a>
    <a href='<?php echo URLROOT; ?>/peers/uploaded?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Uploaded</button></a>
    <?php }?>
    <?php if ($_SESSION["class"] > _UPLOADER) {?>
    <a href='<?php echo URLROOT; ?>/warning?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Warn</button></a>
    <a href='<?php echo URLROOT; ?>/profile/admin?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-success">Admin</button></a>
	<?php } ?>
    <br><br><?php
} //end func

function torrentmenu($id, $external)
{
     ?>
<a href='<?php echo URLROOT; ?>/torrents/read?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Back</button></a>
<?php if ($_SESSION["id"] == $id or $_SESSION["edit_torrents"] == 'yes') {?>
<a href='<?php echo URLROOT; ?>/torrents/edit?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Edit</button></a>
<?php }?>
<a href='<?php echo URLROOT; ?>/comments?type=torrent&amp;id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Comments</button></a>
<a href='<?php echo URLROOT; ?>/torrents/torrentfilelist?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Files</button></a>

<?php if ($external != 'yes') {?>
     <a href='<?php echo URLROOT; ?>/peers/peerlist?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Peers</button></a>
<?php }?>

<?php if ($external == 'yes') {?>
     <a href='<?php echo URLROOT; ?>/torrents/torrenttrackerlist?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Trackers</button></a>
<?php } ?>
<br><br>
<?php 
}

function uploadimage($x, $imgname, $tid)
{
    $imagesdir = TORRENTDIR . "/images";
    $allowed_types = ALLOWEDIMAGETYPES;
    if (!($_FILES["image$x"]["name"] == "")) {
        if ($imgname != "") {
            $img = "$imagesdir/$imgname";
            $del = unlink($img);
        }
        $y = $x + 1;
        $im = getimagesize($_FILES["image$x"]["tmp_name"]);
        if (!$im[2]) {
            show_error_msg(Lang::T("ERROR"), "Invalid Image $y.", 1);
        }
        if (!array_key_exists($im['mime'], $allowed_types)) {
            show_error_msg(Lang::T("ERROR"), Lang::T("INVALID_FILETYPE_IMAGE"), 1);
        }
        if ($_FILES["image$x"]["size"] > IMAGEMAXFILESIZE) {
            show_error_msg(Lang::T("ERROR"), sprintf(Lang::T("INVAILD_FILE_SIZE_IMAGE"), $y), 1);
        }
        $uploaddir = "$imagesdir/";
        $ifilename = $tid . $x . $allowed_types[$im['mime']];
        $copy = copy($_FILES["image$x"]["tmp_name"], $uploaddir . $ifilename);
        if (!$copy) {
            show_error_msg(Lang::T("ERROR"), sprintf(Lang::T("ERROR_UPLOADING_IMAGE"), $y), 1);
        }
        return $ifilename;
    }
}
