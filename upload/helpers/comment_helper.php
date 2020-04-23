<?php
// Function For Comment Table
function commenttable($res, $type = null)
{
    global $site_config, $CURUSER, $THEME, $LANGUAGE, $pdo; //Define globals

    while ($row = $res->fetch(PDO::FETCH_LAZY)) {

        $postername = class_user($row["username"]);
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
            $avatar = $site_config["SITEURL"] . "/images/default_avatar.png";
        }

        $commenttext = format_comment($row["text"]);

        $edit = null;
        if ($type == "torrent" && $CURUSER["edit_torrents"] == "yes" || $type == "news" && $CURUSER["edit_news"] == "yes" || $CURUSER['id'] == $row['user']) {
            $edit = '[<a href="$site_config[SITEURL]/comments?id=' . $row["id"] . '&amp;type=' . $type . '&amp;edit=1">Edit</a>]&nbsp;';
        }

        $delete = null;
        if ($type == "torrent" && $CURUSER["delete_torrents"] == "yes" || $type == "news" && $CURUSER["delete_news"] == "yes") {
            $delete = '[<a href="$site_config[SITEURL]/comments?id=' . $row["id"] . '&amp;type=' . $type . '&amp;delete=1">Delete</a>]&nbsp;';
        }

        print('<div class="f-post f-border"><table cellspacing="0" width="100%">');
        print('<tr class="p-title">');
        print('<th align="center" width="150"></th>');
        print('<th align="right">' . $edit . $delete . '[<a href="$site_config[SITEURL]/report?comment=' . $row["id"] . '">Report</a>] Posted: ' . date("d-m-Y \\a\\t H:i:s", utc_to_tz_time($row["added"])) . '<a id="comment' . $row["id"] . '"></a></th>');
        print('</tr>');
        print('<tr valign="top">');
        if ($CURUSER['edit_users'] == 'no' && $privacylevel == 'strong') {
            print('<td class="f-border comment-details" align="left" width="150"><center><b>' . $postername . '</b><br /><i>' . $title . '</i><br /><img width="80" height="80" src="' . $avatar . '" alt="" /><br /><br />Uploaded: ---<br />Downloaded: ---<br />Ratio: ---<br /><br /><a href="$site_config[SITEURL]/accountdetails?id=' . $row["user"] . '"><img src="themes/' . $THEME . '/forums/icon_profile.png" border="" alt="" /></a> <a href="$site_config[SITEURL]/mailbox?compose&amp;id=' . $row["user"] . '"><img src="themes/' . $THEME . '/forums/icon_pm.png" border="0" alt="" /></a></center></td>');
        } else {
            print('<td class="f-border comment-details" align="left" width="150"><center><b>' . $postername . '</b><br /><i>' . $title . '</i><br /><img width="80" height="80" src="' . $avatar . '" alt="" /><br /><br />Uploaded: ' . $useruploaded . '<br />Downloaded: ' . $userdownloaded . '<br />Ratio: ' . $userratio . '<br /><br /><a href="$site_config[SITEURL]/accountdetails?id=' . $row["user"] . '"><img src="themes/' . $THEME . '/forums/icon_profile.png" border="0" alt="" /></a> <a href="/mailbox?compose&amp;id=' . $row["user"] . '"><img src="themes/' . $THEME . '/forums/icon_pm.png" border="0" alt="" /></a></center></td>');
        }

        print('<td class="f-border comment">' . $commenttext . '<hr />' . $usersignature . '</td>');
        print('</tr>');
        print('</table></div>');
        print('<br />');
    }
}
