<?php
class Ip
{

    // Check IP for ban and Redirect
    public static function checkipban($ip)
    {
        $db = Database::instance();
        $res = $db->run('SELECT * FROM bans WHERE true');
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $banned = false;
            if (self::is_ipv6($row["first"]) && self::is_ipv6($row["last"]) && self::is_ipv6($ip)) {
                $row["first"] = self::ip2long6($row["first"]);
                $row["last"] = self::ip2long6($row["last"]);
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

    // Function For Verification If IP Address IPV6 Format
    public static function is_ipv6($s)
    {
        return is_int(strpos($s, ":"));
    }

    // Taken from php.net comments
    public static function ip2long6($ipv6)
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
    public static function long2ip6($ipv6long)
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
}
