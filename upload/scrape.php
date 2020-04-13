<?php
error_reporting(0); //disable error reporting

// check if client can handle gzip
if (stristr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip") && extension_loaded('zlib') && ini_get("zlib.output_compression") == 0) {
    if (ini_get('output_handler')!='ob_gzhandler') {
        ob_start("ob_gzhandler");
    } else {
        ob_start();
    }
}else{
     ob_start();
}
// end gzip controll

require_once("backend/config.php");

function dbconn() {
    global $conn, $site_config;
    try {
        $conn = new PDO('mysql:host='.$site_config['mysql_host'].';dbname='.$site_config['mysql_db'], $site_config['mysql_user'], $site_config['mysql_pass']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    unset($site_config['mysql_pass']); //security
}

function sqlesc($x) {
    return "'".$x."'";
}

dbconn();

$infohash = array();

foreach (explode("&", $_SERVER["QUERY_STRING"]) as $item) {
    if (preg_match("#^info_hash=(.+)\$#", $item, $m)) {
        $hash = urldecode($m[1]);
            $info_hash = stripslashes($hash);
  //        $info_hash = $hash;
        if (strlen($info_hash) == 20)
            $info_hash = bin2hex($info_hash);
        else if (strlen($info_hash) != 40)
            continue;
        $infohash[] = sqlesc(strtolower($info_hash));
    }
}

if (!count($infohash)) die("Invalid infohash.");
    $query = $conn->prepare("SELECT info_hash, seeders, leechers, times_completed, filename FROM torrents WHERE info_hash IN (".join(",", $infohash).")");
    $result="d5:filesd";

    while ($row = $query->fetch())
    {
        $hash = pack("H*", $row[0]);
        $result.="20:".$hash."d";
        $result.="8:completei".$row[1]."e";
        $result.="10:downloadedi".$row[3]."e";
        $result.="10:incompletei".$row[2]."e";
        $result.="4:name".strlen($row[4]).":".$row[4]."e";
        $result.="e";
    }

    $result.="ee";

    echo $result;
    ob_end_flush();
    $conn = null;
?>
