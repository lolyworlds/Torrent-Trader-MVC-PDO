<?php
// Function that calculates the Hours Minutes Seconds of a Timestamp
function mkprettytime($s)
{
    if ($s < 0) {
        $s = 0;
    }

    $t = array();
    $t["day"] = floor($s / 86400);
    $s -= $t["day"] * 86400;

    $t["hour"] = floor($s / 3600);
    $s -= $t["hour"] * 3600;

    $t["min"] = floor($s / 60);
    $s -= $t["min"] * 60;

    $t["sec"] = $s;

    if ($t["day"]) {
        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    }

    if ($t["hour"]) {
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    }

    return sprintf("%d:%02d", $t["min"], $t["sec"]);
}

// Time to Time Conversion Function With Time Zone
function gmtime()
{
    return sql_timestamp_to_unix_timestamp(get_date_time());
}

// Function That Returns The UNIX Timestamp Of A Date
function sql_timestamp_to_unix_timestamp($s)
{
    return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

// Obtaining function Week Day Hour Minute Second According to a Timestamp
function get_elapsed_time($ts)
{
    $mins = floor((gmtime() - $ts) / 60);
    $hours = floor($mins / 60);
    $mins -= $hours * 60;
    $days = floor($hours / 24);
    $hours -= $days * 24;
    $weeks = floor($days / 7);
    $days -= $weeks * 7;
    $t = "";
    if ($weeks > 0) {
        return "$weeks wk" . ($weeks > 1 ? "s" : "");
    }

    if ($days > 0) {
        return "$days day" . ($days > 1 ? "s" : "");
    }

    if ($hours > 0) {
        return "$hours hr" . ($hours > 1 ? "s" : "");
    }

    if ($mins > 0) {
        return "$mins min" . ($mins > 1 ? "s" : "");
    }

    return "< 1 min";
}

// Obtain function Week Day Hour Minute Second According to Time T
function time_ago($addtime)
{
    $addtime = get_elapsed_time(sql_timestamp_to_unix_timestamp($addtime));
    return $addtime;
}

function get_date_time($timestamp = 0)
{
    if ($timestamp) {
        return date("Y-m-d H:i:s", $timestamp);
    } else {
        return gmdate("Y-m-d H:i:s");
    }

}

// Function which returns a date according to the member's time zone
function utc_to_tz($timestamp = 0)
{
    global $CURUSER, $tzs;

    if (method_exists("DateTime", "setTimezone")) {
        if (!$timestamp) {
            $timestamp = get_date_time();
        }

        $date = new DateTime($timestamp, new DateTimeZone("UTC"));

        $date->setTimezone(new DateTimeZone($CURUSER ? $tzs[$CURUSER["tzoffset"]][1] : "Europe/London"));
        return $date->format('Y-m-d H:i:s');
    }
    if (!is_numeric($timestamp)) {
        $timestamp = sql_timestamp_to_unix_timestamp($timestamp);
    }

    if ($timestamp == 0) {
        $timestamp = gmtime();
    }

    $timestamp = $timestamp + ($CURUSER['tzoffset'] * 60);
    if (date("I")) {
        $timestamp += 3600;
    }
    // DST Fix
    return date("Y-m-d H:i:s", $timestamp);
}

// Function That Returns A Timestamp According To The Member's Time Zone
function utc_to_tz_time($timestamp = 0)
{
    global $CURUSER, $tzs;

    if (method_exists("DateTime", "setTimezone")) {
        if (!$timestamp) {
            $timestamp = get_date_time();
        }

        $date = new DateTime($timestamp, new DateTimeZone("UTC"));
        $date->setTimezone(new DateTimeZone($CURUSER ? $tzs[$CURUSER["tzoffset"]][1] : "Europe/London"));
        return sql_timestamp_to_unix_timestamp($date->format('Y-m-d H:i:s'));
    }

    if (!is_numeric($timestamp)) {
        $timestamp = sql_timestamp_to_unix_timestamp($timestamp);
    }

    if ($timestamp == 0) {
        $timestamp = gmtime();
    }

    $timestamp = $timestamp + ($CURUSER['tzoffset'] * 60);
    if (date("I")) {
        $timestamp += 3600;
    }
    // DST Fix

    return $timestamp;
}

// Function To Make The Time Interval Between 2 Dates
function DateDiff($start, $end)
{
    if (!is_numeric($start)) {
        $start = sql_timestamp_to_unix_timestamp($start);
    }

    if (!is_numeric($end)) {
        $end = sql_timestamp_to_unix_timestamp($end);
    }

    return ($end - $start);
}
