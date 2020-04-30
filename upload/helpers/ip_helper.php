<?php

// Check IP for ban and Redirect
function checkipban($ip)
{
    global $pdo;
    $res = $pdo->run('SELECT * FROM bans WHERE true');
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $banned = false;
        if (is_ipv6($row["first"]) && is_ipv6($row["last"]) && is_ipv6($ip)) {
            $row["first"] = ip2long6($row["first"]);
            $row["last"] = ip2long6($row["last"]);
            $banned = bccomp($row["first"], $nip) != -1 && bccomp($row["last"], $nip) != -1;
        } else {
            $row["first"] = ip2long($row["first"]);
            $row["last"] = ip2long($row["last"]);
            $banned = $nip >= $row["first"] && $nip <= $row["last"];
        }
        if ($banned) {
        header("HTTP/1.0 403 Forbidden");
        echo '<html><head><title>Forbidden</title> </head><body> <h1>Forbidden</h1>Unauthorized IP address.<br> </body></html>';
        die;
        }
    }
}

// IP Validation Function
function validip($ip)
{
    if (extension_loaded("filter")) {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    if (preg_match('![:a-f0-9]!i', $ip)) {
        return true;
    }

    if (!empty($ip) && $ip == long2ip(ip2long($ip))) {
        $reserved_ips = array(
            array('0.0.0.0', '2.255.255.255'),
            array('10.0.0.0', '10.255.255.255'),
            array('127.0.0.0', '127.255.255.255'),
            array('169.254.0.0', '169.254.255.255'),
            array('172.16.0.0', '172.31.255.255'),
            array('192.0.2.0', '192.0.2.255'),
            array('192.168.0.0', '192.168.255.255'),
            array('255.255.255.0', '255.255.255.255'),
        );

        foreach ($reserved_ips as $r) {
            $min = ip2long($r[0]);
            $max = ip2long($r[1]);
            if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) {
                return false;
            }

        }
        return true;
    }
    return false;
}

function getip()
{
    return $_SERVER['REMOTE_ADDR'];
}

// Function For Verification If IP Address IPV6 Format
function is_ipv6($s)
{
    return is_int(strpos($s, ":"));
}

// Taken from php.net comments
function ip2long6($ipv6)
{
    $ip_n = inet_pton($ipv6);
    $bits = 15; // 16 x 8 bit = 128bit
    while ($bits >= 0) {
        $bin = sprintf("%08b", (ord($ip_n[$bits])));
        $ipv6long = $bin . $ipv6long;
        $bits--;
    }
    // Causes error on xampp
    return gmp_strval(gmp_init($ipv6long, 2), 10);
}
// Function To Convert An IP Address (IPv6) To A Digital IP Address
function long2ip6($ipv6long)
{

    $bin = gmp_strval(gmp_init($ipv6long, 10), 2);
    if (strlen($bin) < 128) {
        $pad = 128 - strlen($bin);
        for ($i = 1; $i <= $pad; $i++) {
            $bin = "0" . $bin;
        }
    }
    $bits = 0;
    while ($bits <= 7) {
        $bin_part = substr($bin, ($bits * 16), 16);
        $ipv6 .= dechex(bindec($bin_part)) . ":";
        $bits++;
    }
    // compress

    return inet_ntop(inet_pton(substr($ipv6, 0, -1)));
}
