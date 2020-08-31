<?php
// Function For Comment Table
function commenttable($res, $type = null)
{
    global $config, $THEME, $LANGUAGE, $pdo; //Define globals

    while ($row = $res->fetch(PDO::FETCH_LAZY)) {

        $postername = class_user_colour($row["username"]);
        if ($postername == "") {
            $postername = T_("DELUSER");
            $title = T_("DELETED_ACCOUNT");
            $avatar = "";
            $usersignature = "";
            $userdownloaded = "";
            $useruploaded = "";
        } else {
            $privacylevel = $row["privacy"];
            $avatar = htmlspecialchars($row["avatar"]);
            $title = format_comment($row["title"]);
            $usersignature = stripslashes(format_comment($row["signature"]));
            $userdownloaded = mksize($row["downloaded"]);
            $useruploaded = mksize($row["uploaded"]);
        }

        if ($row["downloaded"] > 0) {
            $userratio = number_format($row["uploaded"] / $row["downloaded"], 2);
        } else {
            $userratio = "---";
        }

        if (!$avatar) {
            $avatar = $config["SITEURL"] . "/images/default_avatar.png";
        }

        $commenttext = format_comment($row["text"]);

        $edit = null;
        if ($type == "torrent" && $_SESSION["edit_torrents"] == "yes" || $type == "news" && $_SESSION["edit_news"] == "yes" || $_SESSION['id'] == $row['user']) {
            $edit = '[<a href="' . $config['SITEURL'] . '/comments?id=' . $row["id"] . '&amp;type=' . $type . '&amp;edit=1">Edit</a>]&nbsp;';
        }

        $delete = null;
        if ($type == "torrent" && $_SESSION["delete_torrents"] == "yes" || $type == "news" && $_SESSION["delete_news"] == "yes") {
            $delete = '[<a href="' . $config['SITEURL'] . '/comments?id=' . $row["id"] . '&amp;type=' . $type . '&amp;delete=1">Delete</a>]&nbsp;';
        }

        print('<div class="container"><table class="table table-striped" style="border: 1px solid black" >');
        print('<thead><tr">');
        print('<th align="center" width="150"></th>');
        print('<th align="right">' . $edit . $delete . '[<a href="' . TTURL . '/report/comment?comment=' . $row["id"] . '">Report</a>] Posted: ' . date("d-m-Y \\a\\t H:i:s", utc_to_tz_time($row["added"])) . '<a id="comment' . $row["id"] . '"></a></th>');
        print('</tr></thead>');
        print('<tr valign="top">');
        if ($_SESSION['edit_users'] == 'no' && $privacylevel == 'strong') {
            print('<td align="left" width="150"><center><a href="' . $config['SITEURL'] . '/users/profile?id=' . $row['id'] . '"><b>' . $postername . '</b></a><br /><i>' . $title . '</i><br /><img width="80" height="80" src="' . $avatar . '" alt="" /><br /><br />Uploaded: ---<br />Downloaded: ---<br />Ratio: ---<br /><br /><a href="$config[SITEURL]/users/profile?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: $config['default_theme']) . '/forums/icon_profile.png" border="" alt="" /></a> <a href="$config[SITEURL]/messages/create?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: $config['default_theme']) . '/forums/icon_pm.png" border="0" alt="" /></a></center></td>');
        } else {
            print('<td align="left" width="150"><center><a href="' . $config['SITEURL'] . '/users/profile?id=' . $row['id'] . '"><b>' . $postername . '</b></a><br /><i>' . $title . '</i><br /><img width="80" height="80" src="' . $avatar . '" alt="" /><br /><br />Uploaded: ' . $useruploaded . '<br />Downloaded: ' . $userdownloaded . '<br />Ratio: ' . $userratio . '<br /><br /><a href="$config[SITEURL]/users/profile?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: $config['default_theme']) . '/forums/icon_profile.png" border="0" alt="" /></a> <a href="/messages/create?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: $config['default_theme']) . '/forums/icon_pm.png" border="0" alt="" /></a></center></td>');
        }

        print('<td>' . $commenttext . '<hr />' . $usersignature . '</td>');
        print('</tr>');
        print('</table></div>');
    }
}
// Request
function reqcommenttable($res, $type = null)
{
    global $config, $THEME, $LANGUAGE, $pdo; //Define globals
    $requestid = (int) $_GET["id"];
    while ($row = $res->fetch(PDO::FETCH_LAZY)) {
        $cid = $row['id'];
        $postername = class_user_colour($row["username"]);
        if ($postername == "") {
            $postername = T_("DELUSER");
            $title = T_("DELETED_ACCOUNT");
            $avatar = "";
            $usersignature = "";
            $userdownloaded = "";
            $useruploaded = "";
        } else {
            $privacylevel = $row["privacy"];
            $avatar = htmlspecialchars($row["avatar"]);
            $title = format_comment($row["title"]);
            $usersignature = stripslashes(format_comment($row["signature"]));
            $userdownloaded = mksize($row["downloaded"]);
            $useruploaded = mksize($row["uploaded"]);
        }
        if ($row["downloaded"] > 0) {
            $userratio = number_format($row["uploaded"] / $row["downloaded"], 2);
        } else {
            $userratio = "---";
        }
        if (!$avatar) {
            $avatar = $config["SITEURL"] . "/images/default_avatar.png";
        }
        $commenttext = format_comment($row["text"]);
        $edit = null;
        if ($type == "req" && $_SESSION["edit_torrents"] == "yes" || $_SESSION['id'] == $row['user']) {
            $edit = '[<a href="' . $config['SITEURL'] . '/request/reqcomment?action=edit&reqid=' . $requestid . '&cid=' . $cid . '">Edit</a>]&nbsp;';
        }
        $delete = null;
        if ($type == "req" && $_SESSION["delete_torrents"] == "yes" || $_SESSION['id'] == $row['user']) {
            $delete = '[<a href="' . $config['SITEURL'] . '/request/reqcomment?action=delete&reqid=' . $requestid . '&cid=' . $cid . '">Delete</a>]&nbsp;';
        }
        print('<div class="container"><table class="table table-striped" style="border: 1px solid black" >');
        print('<thead><tr">');
        print('<th align="center" width="150"></th>');
        print('<th align="right">' . $edit . $delete . ' Posted: ' . date("d-m-Y \\a\\t H:i:s", utc_to_tz_time($row["added"])) . '<a id="comment' . $row["id"] . '"></a></th>');
        print('</tr></thead>');
        print('<tr valign="top">');
        if ($_SESSION['edit_users'] == 'no' && $privacylevel == 'strong') {
            print('<td align="left" width="150"><center><a href="' . $config['SITEURL'] . '/users/profile?id=' . $row['id'] . '"><b>' . $postername . '</b></a><br /><i>' . $title . '</i><br /><img width="80" height="80" src="' . $avatar . '" alt="" /><br /><br />Uploaded: ---<br />Downloaded: ---<br />Ratio: ---<br /><br /><a href="$config[SITEURL]/users/profile?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: $config['default_theme']) . '/forums/icon_profile.png" border="" alt="" /></a> <a href="$config[SITEURL]/messages/create?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: $config['default_theme']) . '/forums/icon_pm.png" border="0" alt="" /></a></center></td>');
        } else {
            print('<td align="left" width="150"><center><a href="' . $config['SITEURL'] . '/users/profile?id=' . $row['id'] . '"><b>' . $postername . '</b></a><br /><i>' . $title . '</i><br /><img width="80" height="80" src="' . $avatar . '" alt="" /><br /><br />Uploaded: ' . $useruploaded . '<br />Downloaded: ' . $userdownloaded . '<br />Ratio: ' . $userratio . '<br /><br /><a href="$config[SITEURL]/users/profile?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: $config['default_theme']) . '/forums/icon_profile.png" border="0" alt="" /></a> <a href="/messages/create?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: $config['default_theme']) . '/forums/icon_pm.png" border="0" alt="" /></a></center></td>');
        }
        print('<td>' . $commenttext . '<hr />' . $usersignature . '</td>');
        print('</tr>');
        print('</table></div>');
    }
}