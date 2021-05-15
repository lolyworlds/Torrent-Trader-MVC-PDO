<?php

class Helper
{

    public function __construct()
    {}

    private function __clone()
    {}

    public static function hashPass($pass)
    {
        return password_hash($pass, PASSWORD_BCRYPT);
    }

    public static function validIP($ip)
    {
        if (strtolower($ip) === "unknown") {
            return false;
        }
        // generate ipv4 network address
        $ip = ip2long($ip);
        // if the ip is set and not equivalent to 255.255.255.255
        if ($ip !== false && $ip !== -1) {
            // make sure to get unsigned long representation of ip due to discrepancies
            // between 32 and 64 bit OSes and signed numbers (ints default to signed in PHP)
            $ip = sprintf("%u", $ip);
            // do private network range checking
            if ($ip >= 0 && $ip <= 50331647) {
                return false;
            }
            if ($ip >= 167772160 && $ip <= 184549375) {
                return false;
            }
            if ($ip >= 2130706432 && $ip <= 2147483647) {
                return false;
            }
            if ($ip >= 2851995648 && $ip <= 2852061183) {
                return false;
            }
            if ($ip >= 2886729728 && $ip <= 2887778303) {
                return false;
            }
            if ($ip >= 3221225984 && $ip <= 3221226239) {
                return false;
            }
            if ($ip >= 3232235520 && $ip <= 3232301055) {
                return false;
            }
            if ($ip >= 4294967040) {
                return false;
            }
        }
        return true;
    }

    public static function getIP()
    {
        // check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && self::validIP($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        // check for IPs passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // check if multiple ips exist in var
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                foreach ($iplist as $ip) {
                    if (self::validIP($ip)) {
                        return $ip;
                    }
                }
            } else {
                if (self::validIP($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && self::validIP($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::validIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && self::validIP($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED']) && self::validIP($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        // return unreliable ip since all else failed
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function escape($string)
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Convert bytes to readable format
     *
     * @param $s
     *   integer: bytes
     * @param int $calc
     *   (optional) integer: decimal precision (default: 2)
     * @return string: formatted size
     */
    public static function makeSize($s, $calc = 2)
    {
        $size = [' B', ' kB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB'];
        for ($i = 1, $x = 0; $i <= count($size); $i++, $x++) {
            if ($s < pow(1024, $i) || $i == count($size)) {
                // Change 1024 to 1000 if you want 0.98GB instead of 1,0000MB
                return number_format($s / pow(1024, $x), $calc) . $size[$x];
            }
        }
    }

    public static function tempoDecorrido($datetime, $full = false)
    {
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'ano',
            'm' => 'mês',
            'w' => 'semana',
            'd' => 'dia',
            'h' => 'hora',
            'i' => 'minuto',
            's' => 'segundo',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
        if (!$full) {
            $string = array_slice($string, 0, 1);
        }
        return $string ? implode(', ', $string) . ' atrás' : ' agora mesmo';
    }

    public static function escapeUrl($url)
    {
        $ret = '';
        for ($i = 0; $i < strlen($url); $i += 2) {
            $ret .= '&' . $url[$i] . $url[$i + 1];
        }
        return $ret;
    }

    public static function htmlsafechars($txt = '')
    {
        $txt = preg_replace("/&(?!#[0-9]+;)(?:amp;)?/s", '&amp;', $txt);
        $txt = str_replace(["<", ">", '"', "'"], ["&lt;", "&gt;", "&quot;", '&#039;'], $txt);
        return $txt;
    }

    public static function validID($id)
    {
        return is_numeric($id) && ($id > 0) && (floor($id) == $id) ? true : false;
    }

    public static function validINT($id)
    {
        return is_numeric($id) && (floor($id) == $id) ? true : false;
    }

    public static function browser()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public static function codeAtivacao()
    {
        return sha1(time() . microtime());
    }

    public static function dateTime()
    {
        return date("Y-m-d H:i:s");
    }

    public static function md5Gen()
    {
        return md5(uniqid() . time() . microtime());
    }

    public static function data()
    {
        return date("Y-m-d");
    }

    public static function get_date_time($timestamp = 0)
    {
        if ($timestamp) {
            return date("Y-m-d H:i:s", $timestamp);
        } else {
            return date("Y-m-d H:i:s");
        }
    }

    public static function validFilename($name)
    {
        //only works with single ''
        return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
    }

    public static function sql_timestamp_to_unix_timestamp($s)
    {
        return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
    }

    public static function gmtime()
    {
        return self::sql_timestamp_to_unix_timestamp(self::dateTime());
    }

    public static function getGuests()
    {
        $db = new Database();
        $past = (self::gmtime() - 2400);
        $db->run("DELETE FROM `guests` WHERE `time` < $past");
        return $db->get_row_count("guests");
    }

    public static function health($seeders, $leechers)
    {
        if ($leechers == 0 && $seeders == 0 || $leechers > 0 && $seeders == 0) {
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

    public static function plus7Days()
    {
        return date("Y-m-d", strtotime("+7 days"));
    }

// Function Who Finds Where The Member Is
    public static function where($where, $userid, $update = 1)
    {
        $db = new Database();
        if (!self::validID($userid)) {
            die;
        }
        if (empty($where)) {
            $where = "Unknown Location...";
        }
        if ($update) {
            $db->run("UPDATE users SET page=? WHERE id=?", [$where, $userid]);
        }
        if (!$update) {
            return $where;
        } else {
            return;
        }
    }

    public static function userColour($name)
    {
        $db = new Database();
        $classy = $db->run("SELECT u.class, u.donated, u.warned, u.enabled, g.Color, g.level, u.uploaded, u.downloaded FROM `users` `u` INNER JOIN `groups` `g` ON g.group_id=u.class WHERE username ='" . $name . "'")->fetch();
        $gcolor = $classy->Color;
        if ($classy->donated > 0) {
            $star = "<img src='assets/images/donor.png' alt='donated' border='0' width='15' height='15'>";
        } else {
            $star = "";
        }
        if ($classy->warned == "yes") {
            $warn = "<img src='assets/images/warn.png' alt='Warn' border='0'>";
        } else {
            $warn = "";
        }
        if ($classy->enabled == "no") {
            $disabled = "<img src='assets/images/disabled.png' title='Disabled' border='0'>";
        } else {
            $disabled = "";
        }
        return stripslashes("<font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font>");
    }

/// each() replacement for php 7+. Change all instances of each() to thisEach() in all TT files. each() deprecated as of 7.2
    public static function thisEach(&$arr)
    {
        $key = key($arr);
        $result = ($key === null) ? false : [$key, current($arr), 'key' => $key, 'value' => current($arr)];
        next($arr);
        return $result;
    }

    public static function CutName($vTxt, $Car)
    {
        if (strlen($vTxt) > $Car) {
            return substr($vTxt, 0, $Car) . "...";
        }
        return $vTxt;
    }
    public static function guestadd()
    {
        $ip = Helper::getIP();
        $time = TimeDate::gmtime();
        DB::run("INSERT INTO `guests` (`ip`, `time`) VALUES ('$ip', '$time') ON DUPLICATE KEY UPDATE `time` = '$time'");
    }
// Function That Returns A Timestamp According To The Member's Time Zone
    public static function utc_to_tz_time($timestamp = 0)
    {
        global $tzs;

        if (method_exists("DateTime", "setTimezone")) {
            if (!$timestamp) {
                $timestamp = self::get_date_time();
            }

            $date = new DateTime($timestamp, new DateTimeZone("UTC"));
            $date->setTimezone(new DateTimeZone($tzs = [$_SESSION["tzoffset"]][1] ?: "Europe/London"));
            //$date->setTimezone(new DateTimeZone($tzs[$_SESSION["tzoffset"]] ?: "Europe/London"));
            return self::sql_timestamp_to_unix_timestamp($date->format('Y-m-d H:i:s'));
        }

        if (!is_numeric($timestamp)) {
            $timestamp = self::sql_timestamp_to_unix_timestamp($timestamp);
        }

        if ($timestamp == 0) {
            $timestamp = self::gmtime();
        }

        $timestamp = $timestamp + ($_SESSION['tzoffset'] * 60);
        if (date("I")) {
            $timestamp += 3600;
        }
        // DST Fix

        return $timestamp;
    }

    public static function formatUrls($s)
    {
        return preg_replace(
            "/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^<>\s]+)/i",
            "\\1<a href='\\2' target='_blank'>\\2</a>", $s);
    }

    public static function formatComment($text)
    {
        global $smilies, $pdo;

        $s = $text;

        $s = htmlspecialchars($s);
        $s = self::formatUrls($s);

        // [*]
        $s = preg_replace("/\[\*\]/", "<li>", $s);

        // [b]Bold[/b]
        $s = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $s);

        // [i]Italic[/i]
        $s = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $s);

        // [u]Underline[/u]
        $s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $s);

        // [u]Underline[/u]
        $s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/i", "<u>\\1</u>", $s);

        // [img]http://www/image.gif[/img]
        $s = preg_replace("/\[img\]((http|https):\/\/[^\s'\"<>]+(\.gif|\.jpg|\.png|\.bmp|\.jpeg))\[\/img\]/i", "<img border='0' src=\"\\1\" alt='' />", $s);

        // [img=http://www/image.gif]
        $s = preg_replace("/\[img=((http|https):\/\/[^\s'\"<>]+(\.gif|\.jpg|\.png|\.bmp|\.jpeg))\]/i", "<img border='0' src=\"\\1\" alt='' />", $s);

        // [color=blue]Text[/color]
        $s = preg_replace(
            "/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i",
            "<font color='\\1'>\\2</font>", $s);

        // [color=#ffcc99]Text[/color]
        $s = preg_replace(
            "/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/i",
            "<font color='\\1'>\\2</font>", $s);

        // [url=http://www.example.com]Text[/url]
        $s = preg_replace(
            "/\[url=((http|ftp|https|ftps|irc):\/\/[^<>\s]+?)\]((\s|.)+?)\[\/url\]/i",
            "<a href='\\1' target='_blank'>\\3</a>", $s);

        // [url]http://www.example.com[/url]
        $s = preg_replace(
            "/\[url\]((http|ftp|https|ftps|irc):\/\/[^<>\s]+?)\[\/url\]/i",
            "<a href='\\1' target='_blank'>\\1</a>", $s);

        // [size=4]Text[/size]
        $s = preg_replace(
            "/\[size=([1-7])\]((\s|.)+?)\[\/size\]/i",
            "<font size='\\1'>\\2</font>", $s);

        // [font=Arial]Text[/font]
        $s = preg_replace(
            "/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i",
            "<font face=\"\\1\">\\2</font>", $s);

        //[quote]Text[/quote]
        while (preg_match("/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i", $s)) {
            $s = preg_replace(
                "/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
                "<p class='sub'><b>Quote:</b></p><table class='main' border='1' cellspacing='0' cellpadding='10'><tr><td style='border: 1px black dotted'>\\1</td></tr></table><br />", $s);
        }

        //[quote=Author]Text[/quote]
        while (preg_match("/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i", $s)) {
            $s = preg_replace(
                "/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
                "<p class='sub'><b>\\1 wrote:</b></p><table class='main' border='1' cellspacing='0' cellpadding='10'><tr><td style='border: 1px black dotted'>\\2</td></tr></table><br />", $s);
        }

        // [spoiler]Text[/spoiler]
        $r = substr(md5($text), 0, 4);
        $i = 0;
        while (preg_match("/\[spoiler\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/i", $s)) {
            $s = preg_replace("/\[spoiler\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/i",
                "<br /><img src='assets/images/plus.gif' id='pic$r$i' title='Spoiler' onclick='klappe_torrent(\"$r$i\")' alt='' /><div id='k$r$i' style='display: none;'>\\1<br /></div>", $s);
            $i++;
        }

        // [spoiler=Heading]Text[/spoiler]
        while (preg_match("/\[spoiler=(.+?)\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/i", $s)) {
            $s = preg_replace("/\[spoiler=(.+?)\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/i",
                "<br /><img src='assets/images/plus.gif' id='pic$r$i' title='Spoiler' onclick='klappe_torrent(\"$r$i\")' alt='' /><b>\\1</b><div id='k$r$i' style='display: none;'>\\2<br /></div>", $s);
            $i++;
        }

        //[hide]Link[/hide]
        if (HIDEBBCODE) {
            $db = new Database();
            $id = (int) $_GET["topicid"];
            $reply = $db->run("SELECT * FROM forum_posts WHERE topicid=$id AND userid=$_SESSION[id]");
            if ($reply->rowCount() == 0) {
                $s = preg_replace(
                    "/\[hide\]\s*((\s|.)+?)\s*\[\/hide\]\s*/i",
                    "<p style='border: 3px solid red; '><font color=red><b>Please reply to view Links</b></font></p>",
                    $s
                );
            }
        }

        //[hr]
        $s = preg_replace("/\[hr\]/i", "<hr />", $s);

        //[hr=#ffffff] [hr=red]
        $s = preg_replace("/\[hr=((#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])|([a-zA-z]+))\]/i", "<hr color=\"\\1\"/>", $s);

        //[swf]http://somesite.com/test.swf[/swf]
        $s = preg_replace("/\[swf\]((www.|http:\/\/|https:\/\/)[^\s]+(\.swf))\[\/swf\]/i",
            "<param name='movie' value='\\1'/><embed width='470' height='310' src='\\1'></embed>", $s);

        //[swf=http://somesite.com/test.swf]
        $s = preg_replace("/\[swf=((www.|http:\/\/|https:\/\/)[^\s]+(\.swf))\]/i",
            "<param name='movie' value='\\1'/><embed width='470' height='310' src='\\1'></embed>", $s);

        // Linebreaks
        $s = nl2br($s);

        // Maintain spacing
        $s = str_replace("  ", " &nbsp;", $s);

        // Smilies
        // require_once "smilies_helper.php";
        reset($smilies);
        while (list($code, $url) = Helper::thisEach($smilies)) {
            $s = str_replace($code, '<img border="0" src="' . URLROOT . '/assets/images/smilies/' . $url . '" alt="' . $code . '" title="' . $code . '" />', $s);
        }

        if (OLD_CENSOR) {
            $r = $pdo->run("SELECT * FROM censor");
            while ($rr = $r->fetch(PDO::FETCH_LAZY)) {
                $s = preg_replace("/" . preg_quote($rr[0]) . "/i", $rr[1], $s);
            }

        } else {

            $f = @fopen("censor.txt", "r");

            if ($f && filesize("censor.txt") != 0) {

                $bw = fread($f, filesize("censor.txt"));
                $badwords = explode("\n", $bw);

                for ($i = 0; $i < count($badwords); ++$i) {
                    $badwords[$i] = trim($badwords[$i]);
                }

                $s = str_replace($badwords, "<img src='" . URLROOT . "/assets/images/censored.png' border='0' alt='Censored' title='Censored' />", $s);
            }
            @fclose($f);
        }

        return $s;
    }

// Function To Retrieve Main Categories Of Torrents
    public static function genrelist()
    {
        $db = new Database();
        $ret = array();
        $res = $db->run("SELECT id, name, parent_cat FROM categories ORDER BY parent_cat ASC, sort_index ASC");
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $ret[] = $row;
        }

        return $ret;
    }
    public static function encodehtml($s, $linebreaks = true)
    {
        $s = str_replace("<", "&lt;", str_replace("&", "&amp;", $s));
        if ($linebreaks) {
            $s = nl2br($s);
        }

        return $s;
    }
    public static function showflag($country)
    {
        $db = new Database;
        $cres = $db->run("
    SELECT name,flagpic FROM countries WHERE id=?", [$country]);
        if ($carr = $cres->fetch(PDO::FETCH_ASSOC)) {
            return $country = "<img src='" . URLROOT . "/assets/images/languages/$carr[flagpic]' title='" . htmlspecialchars($carr['name']) . "' alt='" . htmlspecialchars($carr['name']) . "' />";
        } else {
            return $country = "<img src='" . URLROOT . "/assets/images/languages/unknown.gif' alt='Unknown' />";
        }
    }

// try move message detail array
    public static function msginboxdetails($arr = [])
    {
        $valid = new Validation();
        if ($arr["sender"] == $_SESSION['id']) {
            $sender = "Yourself";
        } elseif ($valid->validId($arr["sender"])) {
            $res2 = DB::run("SELECT username FROM users WHERE `id` = $arr[sender]");
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $sender = "<a href=\"/profile?id=$arr[sender]\">" . ($arr2["username"] ? Users::coloredname($arr2["username"]) : "[Deleted]") . "</a>";
        } else {
            $sender = Lang::T("SYSTEM");
        }
        if ($arr["receiver"] == $_SESSION['id']) {
            $receiver = "Yourself";
        } elseif ($valid->validId($arr["receiver"])) {
            $res2 = DB::run("SELECT username FROM users WHERE `id` = $arr[receiver]");
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $receiver = "<a href=\"" . URLROOT . "/profile?id=$arr[receiver]\">" . ($arr2["username"] ? Users::coloredname($arr2["username"]) : "[Deleted]") . "</a>";
        } else {
            $receiver = Lang::T("SYSTEM");
        }
        $subject = "<a href='" . URLROOT . "/messages/read?inbox&amp;id=" . $arr["id"] . "'><b>" . format_comment($arr["subject"]) . "</b></a>";
        $added = TimeDate::utc_to_tz($arr["added"]);
        if ($arr["unread"] == "yes") {
            $unread = "<img src='" . URLROOT . "/assets/images/forum/folder_new.png' alt='read' width='25' height='25'>";
        } else {
            $unread = "<img src='" . URLROOT . "/assets/images/forum/folder.png' alt='unread' width='25' height='25'>";
        }
        $arr = array($sender, $receiver, $subject, $unread, $added);
        return $arr;
    }

// try move message detail array
    public static function msgoutboxdetails($arr = [])
    {
        $valid = new Validation();
        if ($arr["sender"] == $_SESSION['id']) {
            $sender = "Yourself";
        } elseif ($valid->validId($arr["sender"])) {
            $res2 = DB::run("SELECT username FROM users WHERE `id` = $arr[sender]");
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $sender = "<a href=\"" . URLROOT . "/profile?id=$arr[sender]\">" . ($arr2["username"] ? Users::coloredname($arr2["username"]) : "[Deleted]") . "</a>";
        } else {
            $sender = Lang::T("SYSTEM");
        }
        if ($arr["receiver"] == $_SESSION['id']) {
            $receiver = "Yourself";
        } elseif ($valid->validId($arr["receiver"])) {
            $res2 = DB::run("SELECT username FROM users WHERE `id` = $arr[receiver]");
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $receiver = "<a href=\"" . URLROOT . "/profile?id=$arr[receiver]\">" . ($arr2["username"] ? Users::coloredname($arr2["username"]) : "[Deleted]") . "</a>";
        } else {
            $receiver = Lang::T("SYSTEM");
        }
        $subject = "<a href='" . URLROOT . "/messages/read?inbox&amp;id=" . $arr["id"] . "'><b>" . format_comment($arr["subject"]) . "</b></a>";
        $added = TimeDate::utc_to_tz($arr["added"]);
        if ($arr["unread"] == "yes") {
            $unread = "<img src='" . URLROOT . "/assets/images/forum/folder_new.png' alt='read' width='25' height='25'>";
        } else {
            $unread = "<img src='" . URLROOT . "/assets/images/forum/folder.png' alt='unread' width='25' height='25'>";
        }
        $arr = array($sender, $receiver, $subject, $unread, $added);
        return $arr;
    }

    public static function echouser($id)
    {
        if ($id != '') {
            $username = DB::run("SELECT username FROM users WHERE id=$id")->fetchColumn();
            $user = "<option value=\"$id\">$username</option>\n";
        } else {
            $user = "<option value=\"0\">---- " . Lang::T("NONE_SELECTED") . " ----</option>\n";
        }
        $stmt = DB::run("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($stmt as $arr) {
            $user .= "<option value=\"$arr[id]\">$arr[username]</option>\n";
        }
        echo $user;
    }

    public static function echotemplates()
    {
        $templates = "<option value=\"0\">---- " . Lang::T("NONE_SELECTED") . " ----</option>\n";
        $stmt = DB::run("SELECT * FROM `messages` WHERE `sender` = $_SESSION[id] AND `location` = 'template' ORDER BY `subject`");
        foreach ($stmt as $arr) {
            $templates .= "<option value=\"$arr[id]\">$arr[subject]</option>\n";
        }
        echo $templates;
    }

//DISPLAY NFO BLOCK
    public static function my_nfo_translate($nfo)
    {
        $trans = array(
            "\x80" => "&#199;", "\x81" => "&#252;", "\x82" => "&#233;", "\x83" => "&#226;", "\x84" => "&#228;", "\x85" => "&#224;", "\x86" => "&#229;", "\x87" => "&#231;", "\x88" => "&#234;", "\x89" => "&#235;", "\x8a" => "&#232;", "\x8b" => "&#239;", "\x8c" => "&#238;", "\x8d" => "&#236;", "\x8e" => "&#196;", "\x8f" => "&#197;", "\x90" => "&#201;",
            "\x91" => "&#230;", "\x92" => "&#198;", "\x93" => "&#244;", "\x94" => "&#246;", "\x95" => "&#242;", "\x96" => "&#251;", "\x97" => "&#249;", "\x98" => "&#255;", "\x99" => "&#214;", "\x9a" => "&#220;", "\x9b" => "&#162;", "\x9c" => "&#163;", "\x9d" => "&#165;", "\x9e" => "&#8359;", "\x9f" => "&#402;", "\xa0" => "&#225;", "\xa1" => "&#237;",
            "\xa2" => "&#243;", "\xa3" => "&#250;", "\xa4" => "&#241;", "\xa5" => "&#209;", "\xa6" => "&#170;", "\xa7" => "&#186;", "\xa8" => "&#191;", "\xa9" => "&#8976;", "\xaa" => "&#172;", "\xab" => "&#189;", "\xac" => "&#188;", "\xad" => "&#161;", "\xae" => "&#171;", "\xaf" => "&#187;", "\xb0" => "&#9617;", "\xb1" => "&#9618;", "\xb2" => "&#9619;",
            "\xb3" => "&#9474;", "\xb4" => "&#9508;", "\xb5" => "&#9569;", "\xb6" => "&#9570;", "\xb7" => "&#9558;", "\xb8" => "&#9557;", "\xb9" => "&#9571;", "\xba" => "&#9553;", "\xbb" => "&#9559;", "\xbc" => "&#9565;", "\xbd" => "&#9564;", "\xbe" => "&#9563;", "\xbf" => "&#9488;", "\xc0" => "&#9492;", "\xc1" => "&#9524;", "\xc2" => "&#9516;", "\xc3" => "&#9500;",
            "\xc4" => "&#9472;", "\xc5" => "&#9532;", "\xc6" => "&#9566;", "\xc7" => "&#9567;", "\xc8" => "&#9562;", "\xc9" => "&#9556;", "\xca" => "&#9577;", "\xcb" => "&#9574;", "\xcc" => "&#9568;", "\xcd" => "&#9552;", "\xce" => "&#9580;", "\xcf" => "&#9575;", "\xd0" => "&#9576;", "\xd1" => "&#9572;", "\xd2" => "&#9573;", "\xd3" => "&#9561;", "\xd4" => "&#9560;",
            "\xd5" => "&#9554;", "\xd6" => "&#9555;", "\xd7" => "&#9579;", "\xd8" => "&#9578;", "\xd9" => "&#9496;", "\xda" => "&#9484;", "\xdb" => "&#9608;", "\xdc" => "&#9604;", "\xdd" => "&#9612;", "\xde" => "&#9616;", "\xdf" => "&#9600;", "\xe0" => "&#945;", "\xe1" => "&#223;", "\xe2" => "&#915;", "\xe3" => "&#960;", "\xe4" => "&#931;", "\xe5" => "&#963;",
            "\xe6" => "&#181;", "\xe7" => "&#964;", "\xe8" => "&#934;", "\xe9" => "&#920;", "\xea" => "&#937;", "\xeb" => "&#948;", "\xec" => "&#8734;", "\xed" => "&#966;", "\xee" => "&#949;", "\xef" => "&#8745;", "\xf0" => "&#8801;", "\xf1" => "&#177;", "\xf2" => "&#8805;", "\xf3" => "&#8804;", "\xf4" => "&#8992;", "\xf5" => "&#8993;", "\xf6" => "&#247;",
            "\xf7" => "&#8776;", "\xf8" => "&#176;", "\xf9" => "&#8729;", "\xfa" => "&#183;", "\xfb" => "&#8730;", "\xfc" => "&#8319;", "\xfd" => "&#178;", "\xfe" => "&#9632;", "\xff" => "&#160;",
        );
        $trans2 = array("\xe4" => "&auml;", "\xF6" => "&ouml;", "\xFC" => "&uuml;", "\xC4" => "&Auml;", "\xD6" => "&Ouml;", "\xDC" => "&Uuml;", "\xDF" => "&szlig;");
        $all_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $last_was_ascii = false;
        $tmp = "";
        $nfo = $nfo . "\00";
        for ($i = 0; $i < (strlen($nfo) - 1); $i++) {
            $char = $nfo[$i];
            if (isset($trans2[$char]) and ($last_was_ascii or strpos($all_chars, ($nfo[$i + 1])))) {
                $tmp = $tmp . $trans2[$char];
                $last_was_ascii = true;
            } else {
                if (isset($trans[$char])) {
                    $tmp = $tmp . $trans[$char];
                } else {
                    $tmp = $tmp . $char;
                }
                $last_was_ascii = strpos($all_chars, $char);
            }
        }
        return $tmp;
    }

    public static function echogenrelist()
    {
        $cats = genrelist();
        $category = "<option value=\"0\">---- " . Lang::T("NONE_SELECTED") . " ----</option>\n";
        foreach ($cats as $row) {
            $category .= "<option value=\"" . $row["id"] . "\">" . $row["name"] . "</option>\n";
        }
        echo $category;
    }

    public static function echolanglist()
    {
        $langs = langlist();
        $language = "<option value=\"0\">---- " . Lang::T("NONE_SELECTED") . " ----</option>\n";
        foreach ($langs as $row) {
            $language .= "<option value=\"" . $row["id"] . "\">" . $row["name"] . "</option>\n";
        }
        echo $language;
    }

    public static function ratingtor($id)
    {
        $xres = DB::run("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $_SESSION["id"]);
        $xrow = $xres->fetch(PDO::FETCH_ASSOC);
        $srating = "";
        $srating .= "<br><b>" . Lang::T("RATINGS") . ":</b><br>";
        if (!isset($xrow["rating"])) {
            $srating .= "<br><b>Not Yet Rated</b><br>";
        } else {
            $rpic = ratingpic($xrow["rating"]);
            if (!isset($rpic)) {
                $srating .= "invalid?";
            } else {
                $srating .= "$rpic (" . $xrow["rating"] . " " . Lang::T("OUT_OF") . " 5) " . $xrow["numratings"] . " " . Lang::T("USERS_HAVE_RATED");
            }

        }
        $ratings = array(
            5 => Lang::T("COOL"),
            4 => Lang::T("PRETTY_GOOD"),
            3 => Lang::T("DECENT"),
            2 => Lang::T("PRETTY_BAD"),
            1 => Lang::T("SUCKS"),
        );
        $xres = DB::run("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $_SESSION["id"]);
        $xrow = $xres->fetch(PDO::FETCH_ASSOC);
        if ($xrow) {
            $srating .= "<br /><i>(" . Lang::T("YOU_RATED") . " \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")</i>";
        } else {
            $srating .= "<form style=\"display:inline;\" method=\"post\" action=\"".URLROOT."/rating?id=$id&takerating=yes\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
            $srating .= "<select name=\"rating\">\n";
            $srating .= "<option value=\"0\">(" . Lang::T("ADD_RATING") . ")</option>\n";
            foreach ($ratings as $k => $v) {
                $srating .= "<option value=\"$k\">$k - $v</option>\n";
            }
            $srating .= "</select>\n";
            $srating .= "<input type=\"submit\" value=\"" . Lang::T("VOTE") . "\" />";
            $srating .= "</form>\n";
        }

        return $srating; // rating
    }

public static function catdrop($cat){
    //UPDATE CATEGORY DROPDOWN
    $catdropdown = "<select name=\"type\">\n";
    $cats = genrelist();
    foreach ($cats as $catdropdownubrow) {
        $catdropdown .= "<option value=\"" . $catdropdownubrow["id"] . "\"";
        if ($catdropdownubrow["id"] == $cat) {
            $catdropdown .= " selected=\"selected\"";
        }
        $catdropdown .= ">" . htmlspecialchars($catdropdownubrow["parent_cat"]) . ": " . htmlspecialchars($catdropdownubrow["name"]) . "</option>\n";
    }
    $catdropdown .= "</select>\n";
    //END CATDROPDOWN
    return $catdropdown;
}

public static function langdrop($torrentlang){
        //UPDATE TORRENTLANG DROPDOWN
        $langdropdown = "<select name=\"language\"><option value='0'>Unknown</option>\n";
        $lang = langlist();
        foreach ($lang as $lang) {
            $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
            if ($lang["id"] == $torrentlang) {
                $langdropdown .= " selected=\"selected\"";
            }
            $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
        }
        $langdropdown .= "</select>\n";
        //END TORRENTLANG
        return $langdropdown;
    }
     
}
