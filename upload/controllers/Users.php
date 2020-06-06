<?php
class Users extends Controller
{

    public function __construct()
    {
        $this->countriesModel = $this->model('Countries');
        $this->groupsModel = $this->model('Groups');
    }

    // accountdetails/account/ucercp
    public function index()
    {
        dbconn();
        global $site_config, $CURUSER;
        loggedinonly();
        stdhead("User CP");
        $do = $_REQUEST["do"] ?? '';
        $id = (int) $_GET["id"];
        if (!is_valid_id($id)) {
            show_error_msg(T_("NO_SHOW_DETAILS"), "Bad ID.", 1);
        }

        $user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
        if (!$user) {
            show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID") . " $id.", 1);
        }

        //add invites check here
        if ($CURUSER["view_users"] == "no" && $CURUSER["id"] != $id) {
            show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);
        }

        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $CURUSER["edit_users"] == "no") {
            show_error_msg(T_("ERROR"), T_("NO_ACCESS_ACCOUNT_DISABLED"), 1);
        }

        $res = DB::run("SELECT name FROM countries WHERE id=? LIMIT 1", [$user['country']]);
        if ($res->rowCount() == 1) {
            $arr = $res->fetch();
            $country = "$arr[name]";
        }
        if (!$country) {
            $country = "<b>Unknown</b>";
        }

        //$ratio
        if ($user["downloaded"] > 0) {
            $ratio = $user["uploaded"] / $user["downloaded"];
        } else {
            $ratio = "---";
        }

        $numtorrents = get_row_count("torrents", "WHERE owner = $id");
        $numcomments = get_row_count("comments", "WHERE user = $id");
        $numforumposts = get_row_count("forum_posts", "WHERE userid = $id");

        $avatar = htmlspecialchars($user["avatar"]);
        if (!$avatar) {
            $avatar = $site_config["SITEURL"] . "/images/default_avatar.png";
        }

        //Layout
        begin_frame(sprintf(T_("USER_DETAILS_FOR"), class_user($user["username"])));
        usermenu();
        echo "<div>";

        if ($user["privacy"] != "strong" || ($CURUSER["control_panel"] == "yes") || ($CURUSER["id"] == $user["id"])) {
        ?>
        <table align="center" border="0" cellpadding="6" cellspacing="1" width="100%">
        <tr>
		<td width="50%"><b><?php echo T_("PROFILE"); ?></b></td>
		<td width="50%"><b><?php echo T_("ADDITIONAL_INFO"); ?></b></td>
        </tr>
        <tr valign="top">
		<td align="left">
		<?php echo T_("USERNAME"); ?>: <?php echo class_user($user["username"]) ?><br />
		<?php echo T_("USERCLASS"); ?>: <?php echo get_user_class_name($user["class"]) ?><br />
		<?php echo T_("TITLE"); ?>: <i><?php echo format_comment($user["title"]) ?></i><br />
		<?php echo T_("JOINED"); ?>: <?php echo htmlspecialchars(utc_to_tz($user["added"])) ?><br />
		<?php echo T_("LAST_VISIT"); ?>: <?php echo htmlspecialchars(utc_to_tz($user["last_access"])) ?><br />
		<?php echo T_("LAST_SEEN"); ?>: <?php echo htmlspecialchars($user["page"]); ?><br />
        </td>
        <td align="left">
		<?php echo T_("AGE"); ?>: <?php echo htmlspecialchars($user["age"]) ?><br />
		<?php echo T_("CLIENT"); ?>: <?php echo htmlspecialchars($user["client"]) ?><br />
		<?php echo T_("COUNTRY"); ?>: <?php echo $country ?><br />
		<?php echo T_("DONATED"); ?>  <?php echo $site_config['currency_symbol']; ?><?php echo number_format($user["donated"], 2); ?><br />
		<?php echo T_("WARNINGS"); ?>: <?php echo htmlspecialchars($user["warned"]) ?><br />
		<?php if ($CURUSER["edit_users"] == "yes") {echo T_("ACCOUNT_PRIVACY_LVL") . ": <b>" . T_($user["privacy"]) . "</b><br />";}?>
		</td>
        </tr>
        <tr>
		<td width="50%"><b><?php echo T_("STATISTICS"); ?></b></td>
        <td width="50%"><b><?php echo T_("OTHER"); ?></b></td>
		</tr>
		<tr valign="top">
		<td align="left">
		<?php echo T_("UPLOADED"); ?>: <?php echo mksize($user["uploaded"]); ?><br />
		<?php echo T_("DOWNLOADED"); ?>: <?php echo mksize($user["downloaded"]); ?><br />
		<?php echo T_("RATIO"); ?>: <?php echo $ratio; ?><br />
		<?php echo T_("AVG_DAILY_DL"); ?>: <?php echo mksize($user["downloaded"] / (DateDiff($user["added"], time()) / 86400)); ?><br />
		<?php echo T_("AVG_DAILY_UL"); ?>: <?php echo mksize($user["uploaded"] / (DateDiff($user["added"], time()) / 86400)); ?><br />
		<?php echo T_("TORRENTS_POSTED"); ?>: <?php echo number_format($numtorrents); ?><br />
		<?php echo T_("COMMENTS_POSTED"); ?>: <?php echo number_format($numcomments); ?><br />
        Forum Posts: <?php echo number_format($numforumposts); ?><br />
		</td>
		<td align="left">
		<img src="<?php echo $avatar; ?>" alt="" title="<?php echo class_user($user["username"]); ?>" height="80" width="80" /><br />
		<a href="<?php echo $site_config['SITEURL'] ?>/mailbox?compose&amp;id=<?php echo $user["id"] ?>"><button type='button' class='btn btn-sm btn-success'><?php echo T_("SEND_PM") ?></button></a><br />
		<!-- <a href=#>View Forum Posts</a><br />
		<a href=#>View Comments</a><br /> -->
		<a href="<?php echo $site_config['SITEURL'] ?>/report?user=<?php echo $user["id"] ?>"><button type='button' class='btn btn-sm btn-danger'><?php echo T_("REPORT_MEMBER") ?></button></a><br />
		<?php if ($CURUSER["edit_users"] == "yes") {?>
            <div style="margin-bottom:3px"><a href="<?php echo $site_config['SITEURL']; ?>/snatched?uid=<?php echo $user["id"] ?>"><button type='button' class='btn btn-sm btn-warning'><?php echo T_("SNATCHLIST") ?></button></a></div>
        <?php } ?>
		</td>
		</tr>
		<?php if ($CURUSER["edit_users"] == "yes") {?>
        <tr>
		<td width="50%"><b><?php echo T_("STAFF_ONLY_INFO"); ?></b></td>
		</tr>
		<tr valign="top">
		<td align="left">
		<?php
		if ($user["invited_by"]) {
            $invited = $user['invited_by'];
            $row = DB::run("SELECT username FROM users WHERE id=?", [$invited])->fetch();
            echo "<b>" . T_("INVITED_BY") . ":</b> <a href=\"$site_config[SITEURL]/users?id=$user[invited_by]\">" . class_user($row['username']) . "</a><br />";
        }
        echo "<b>" . T_("INVITES") . ":</b> " . number_format($user["invites"]) . "<br />";
        $invitees = array_reverse(explode(" ", $user["invitees"]));
        $rows = array();
        foreach ($invitees as $invitee) {
            $res = DB::run("SELECT id, username FROM users WHERE id=? and status=?", [$invitee, 'confirmed']);
            if ($row = $res->fetch()) {
                $rows[] = "<a href=\"$site_config[SITEURL]/users?id=$row[id]\">" . class_user($row['username']) . "</a>";
            }
        }
        if ($rows) {
            echo "<b>" . T_("INVITEES") . ":</b> " . implode(", ", $rows) . "<br />";
        }
        ?>
        </td></tr>
        <?php
        }
        //team
        $res = DB::run("SELECT name,image FROM teams WHERE id=? LIMIT 1", [$user['team']]);
        if ($res->rowCount() == 1) {
            $arr = $res->fetch();
            echo "<tr><td colspan='2' align='left'><b>Team Member Of:</b><br />";
            echo "<img src='" . htmlspecialchars($arr["image"]) . "' alt='' /><br />" . sqlesc($arr["name"]) . "<br /><br /><a href='$site_config[SITEURL]/teams'>[View " . T_("TEAMS") . "]</a></td></tr>";
        }
        ?>
        </table>
        <?php
        } else {
            echo sprintf(T_("REPORT_MEMBER_MSG"), $user["id"]);
        }
        echo "</div>";
        end_frame();
        stdfoot();
    }

    // MY TORRENTS
    public function mytorrents()
    {
        dbconn();
        global $site_config, $CURUSER, $pdo;
        loggedinonly();
        stdhead(T_("USERCP"));
        $do = $_REQUEST["do"];
        begin_frame(T_("YOUR_TORRENTS"));
        usermenu();
        //page numbers
        $page = (int) ($_GET['page'] ?? 0);
        $perpage = 200;

        $arr = $pdo->run("SELECT COUNT(*) FROM torrents WHERE torrents.owner = " . $CURUSER["id"] . "")->fetch();
        $pages = floor($arr[0] / $perpage);
        if ($pages * $perpage < $arr[0]) {
            ++$pages;
        }

        if ($page < 1) {
            $page = 1;
        } else
        if ($page > $pages) {
            $page = $pages;
        }

        for ($i = 1; $i <= $pages; ++$i) {
            if ($i == $page) {
                $pagemenu .= "$i\n";
            } else {
                $pagemenu .= "<a href='" . $site_config['SITEURL'] . "/users/mytorrents&amp;page=$i'>$i</a>\n";
            }
        }

        if ($page == 1) {
            $browsemenu .= "";
        } else {
            $browsemenu .= "<a href='" . $site_config['SITEURL'] . "/users/mytorrents&amp;page=" . ($page - 1) . "'>[Prev]</a>";
        }

        $browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        if ($page == $pages) {
            $browsemenu .= "";
        } else {
            $browsemenu .= "<a href='" . $site_config['SITEURL'] . "/users/mytorrents&amp;page=" . ($page + 1) . "'>[Next]</a>";
        }

        $offset = ($page * $perpage) - $perpage;
        //end page numbers

        $where = "WHERE torrents.owner = " . $CURUSER["id"] . "";
        $orderby = "ORDER BY added DESC";

        $query = $pdo->run("SELECT torrents.id, torrents.category, torrents.name, torrents.added, torrents.hits, torrents.banned, torrents.comments, torrents.seeders, torrents.leechers, torrents.times_completed, categories.name AS cat_name, categories.parent_cat AS cat_parent FROM torrents LEFT JOIN categories ON category = categories.id $where $orderby LIMIT $offset,$perpage");
        $allcats = $query->rowCount();
        if ($allcats == 0) {
            echo '<div class="f-border comment"><br /><b>' . T_("NO_UPLOADS") . '</b></div>';
        } else {
            print("<p align='center'>$pagemenu<br />$browsemenu</p>");
            ?>
        <table align="center" cellpadding="5" cellspacing="3" class="table_table" width="100%">
        <tr class="table_head">
        <th><?php echo T_("TYPE"); ?></th>
        <th><?php echo T_("NAME"); ?></th>
        <th><?php echo T_("COMMENTS"); ?></th>
        <th><?php echo T_("HITS"); ?></th>
        <th><?php echo T_("SEEDS"); ?></th>
        <th><?php echo T_("LEECHERS"); ?></th>
        <th><?php echo T_("COMPLETED"); ?></th>
        <th><?php echo T_("ADDED"); ?></th>
        <th><?php echo T_("EDIT"); ?></th>
        </tr>
        <?php
        while ($row = $query->fetch(PDO::FETCH_LAZY)) {
            $char1 = 35; //cut length
            $smallname = CutName(htmlspecialchars($row["name"]), $char1);
            echo "<tr><td class='table_col2' align='center'>$row[cat_parent]: $row[cat_name]</td><td class='table_col1' align='left'><a href='" . $site_config['SITEURL'] . "/torrents/details?id=$row[id]'>$smallname</a></td><td class='table_col2' align='center'><a href='$site_config[SITEURL]/comments?type=torrent&amp;id=$row[id]'>" . number_format($row["comments"]) . "</a></td><td class='table_col1' align='center'>" . number_format($row["hits"]) . "</td><td class='table_col2' align='center'>" . number_format($row["seeders"]) . "</td><td class='table_col1' align='center'>" . number_format($row["leechers"]) . "</td><td class='table_col2' align='center'>" . number_format($row["times_completed"]) . "</td><td class='table_col1' align='center'>" . get_elapsed_time(sql_timestamp_to_unix_timestamp($row["added"])) . "</td><td class='table_col2'><a href='$site_config[SITEURL]/torrents/edit?id=$row[id]'>EDIT</a></td></tr>\n";
        }
        echo "</table><br />";
         print("<p align='center'>$pagemenu<br />$browsemenu</p>");
        }

        end_frame();
        stdfoot();
    }

    // EDIT own SETTINGS
    public function editsettings()
    {
        dbconn();
        global $site_config, $CURUSER, $tzs, $pdo;
        loggedinonly();

        stdhead(T_("USERCP"));
        $do = $_REQUEST["do"];

        if ($do == "edit") {
        begin_frame(T_("EDIT_ACCOUNT_SETTINGS"));

        usermenu();
        ?>
        <form enctype="multipart/form-data" method="post" action="<?php echo $site_config["SITEURL"]; ?>/users/editsettings">
        <input type="hidden" name="action" value="edit_settings" />
        <input type="hidden" name="do" value="save_settings" />
        <table class="f-border" cellspacing="0" cellpadding="5" max-width="100%" align="center">
        <?php

        $ss_r = $pdo->run("SELECT * from stylesheets");
        $ss_sa = array();
        while ($ss_a = $ss_r->fetch(PDO::FETCH_LAZY)) {
            $ss_id = $ss_a["id"];
            $ss_name = $ss_a["name"];
            $ss_sa[$ss_name] = $ss_id;
        }
        ksort($ss_sa);
        reset($ss_sa);
        while (list($ss_name, $ss_id) = thisEach($ss_sa)) {
        if ($ss_id == $CURUSER["stylesheet"]) {
            $ss = " selected='selected'";
        } else {
            $ss = "";
        }
            $stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
        }

        $countries = "<option value='0'>----</option>\n";
        $ct_r = $pdo->run("SELECT id,name from countries ORDER BY name");
        while ($ct_a = $ct_r->fetch(PDO::FETCH_LAZY)) {
            $countries .= "<option value='$ct_a[id]'" . ($CURUSER["country"] == $ct_a['id'] ? " selected='selected'" : "") . ">$ct_a[name]</option>\n";
        }

        $teams = "<option value='0'>--- " . T_("NONE_SELECTED") . " ----</option>\n";
        $sashok = $pdo->run("SELECT id,name FROM teams ORDER BY name");
        while ($sasha = $sashok->fetch(PDO::FETCH_LAZY)) {
            $teams .= "<option value='$sasha[id]'" . ($CURUSER["team"] == $sasha['id'] ? " selected='selected'" : "") . ">$sasha[name]</option>\n";
        }

        $acceptpms = $CURUSER["acceptpms"] == "yes";
        print("<tr><td align='right' class='alt2'><b>" . T_("ACCEPT_PMS") . ":</b> </td><td class='alt2'><input type='radio' name='acceptpms'" . ($acceptpms ? " checked='checked'" : "") .
            " value='yes' /><b>" . T_("FROM_ALL") . "</b> <input type='radio' name='acceptpms'" .
            ($acceptpms ? "" : " checked='checked'") . " value='no' /><b>" . T_("FROM_STAFF_ONLY") . "</b><br /><i>" . T_("ACCEPTPM_WHICH_USERS") . "</i></td></tr>");
            $gender = "<option value='Male'" . ($CURUSER["gender"] == "Male" ? " selected='selected'" : "") . ">" . T_("MALE") . "</option>\n"
            . "<option value='Female'" . ($CURUSER["gender"] == "Female" ? " selected='selected'" : "") . ">" . T_("FEMALE") . "</option>\n";

        // START CAT LIST SQL
        $r = $pdo->run("SELECT id,name,parent_cat FROM categories ORDER BY parent_cat ASC, sort_index ASC");
        if ($r->rowCount() > 0) {
            $categories .= "<table><tr>\n";
            $i = 0;
            while ($a = $r->fetch(PDO::FETCH_LAZY)) {
                $categories .= ($i && $i % 2 == 0) ? "</tr><tr>" : "";
                $categories .= "<td class='bottom' style='padding-right: 5px'><input name='cat$a[id]' type=\"checkbox\" " . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked='checked'" : "") . " value='yes' />&nbsp;" . htmlspecialchars($a["parent_cat"]) . ": " . htmlspecialchars($a["name"]) . "</td>\n";
                ++$i;
            }
            $categories .= "</tr></table>\n";
        }

        // END CAT LIST SQL
        print("<tr><td align='right' class='alt3'><b>" . T_("ACCOUNT_PRIVACY_LVL") . ":</b> </td><td align='left' class='alt3'>" . priv("normal", "<b>" . T_("NORMAL") . "</b>") . " " . priv("low", "<b>" . T_("LOW") . "</b>") . " " . priv("strong", "<b>" . T_("STRONG") . "</b>") . "<br /><i>" . T_("ACCOUNT_PRIVACY_LVL_MSG") . "</i></td></tr>");
        print("<tr><td align='right' class='alt2'><b>" . T_("EMAIL_NOTIFICATION") . ":</b> </td><td align='left' class='alt2'><input type='checkbox' name='pmnotif' " . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked='checked'" : "") .
            " value='yes' /><b>" . T_("PM_NOTIFY_ME") . "</b><br /><i>" . T_("EMAIL_WHEN_PM") . "</i></td></tr>");
            //print("<tr><td align=right class=alt3 valign=top><b>".T_("CATEGORY_FILTER").": </b></td><td align=left class=alt3><i>The system will only display the following categories when browsing (uncheck all to disable filter).</i><br />".$categories."</td></tr>");

        print("<tr><td align='right' class='alt3'><b>" . T_("THEME") . ":</b> </td><td align='left' class='alt3'><select name='stylesheet'>\n$stylesheets\n</select></td></tr>");
        print("<tr><td align='right' class='alt2'><b>" . T_("PREFERRED_CLIENT") . ":</b> </td><td align='left' class='alt2'><input type='text' size='20' maxlength='20' name='client' value=\"" . htmlspecialchars($CURUSER["client"]) . "\" /></td></tr>");
        print("<tr><td align='right' class='alt3'><b>" . T_("AGE") . ":</b> </td><td align='left' class='alt3'><input type='text' size='3' maxlength='2' name='age' value=\"" . htmlspecialchars($CURUSER["age"]) . "\" /></td></tr>");
        print("<tr><td align='right' class='alt2'><b>" . T_("GENDER") . ":</b> </td><td align='left' class='alt2'><select size='1' name='gender'>\n$gender\n</select></td></tr>");
        print("<tr><td align='right' class='alt3'><b>" . T_("COUNTRY") . ":</b> </td><td align='left' class='alt3'><select name='country'>\n$countries\n</select></td></tr>");

        if ($CURUSER["class"] > 1) {
            print("<tr><td align='right' class='alt2'><b>" . T_("TEAM") . ":</b> </td><td align='left' class='alt2'><select name='teams'>\n$teams\n</select></td></tr>");
        }

        print("<tr><td align='right' class='alt3'><b>" . T_("AVATAR_UPLOAD") . ":</b> </td><td align='left' class='alt3'><input type='text' name='avatar' size='50' value=\"" . htmlspecialchars($CURUSER["avatar"]) .
            "\" /><br />\n<i>" . T_("AVATAR_LINK") . "</i><br /></td></tr>");
        print("<tr><td align='right' class='alt2'><b>" . T_("CUSTOM_TITLE") . ":</b> </td><td align='left' class='alt2'><input type='text' name='title' size='50' value=\"" . strip_tags($CURUSER["title"]) .
            "\" /><br />\n <i>" . T_("HTML_NOT_ALLOWED") . "</i></td></tr>");
        print("<tr><td align='right' class='alt3' valign='top'><b>" . T_("SIGNATURE") . ":</b> </td><td align='left' class='alt3'><textarea name='signature' cols='50' rows='10'>" . htmlspecialchars($CURUSER["signature"]) .
            "</textarea><br />\n <i>" . sprintf(T_("MAX_CHARS"), 150) . ", " . T_("HTML_NOT_ALLOWED") . "<a href='javascript:PopMoreTags();'>Click here</a> for available tags</a></i></td></tr>");
        print("<tr><td align='right' class='alt2'><b>" . T_("RESET_PASSKEY") . ":</b> </td><td align='left' class='alt2'><input type='checkbox' name='resetpasskey' value='1' />&nbsp;<i>" . T_("RESET_PASSKEY_MSG") . ".</i></td></tr>");

        if ($site_config["SHOUTBOX"]) {
            print("<tr><td align='right' class='table_col3'><b>" . T_("HIDE_SHOUT") . ":</b></td><td align='left' class='table_col3'><input type='checkbox' name='hideshoutbox' value='yes' " . ($CURUSER['hideshoutbox'] == 'yes' ? 'checked="checked"' : '') . " />&nbsp;<i>" . T_("HIDE_SHOUT") . "</i></td></tr> ");
        }

        print("<tr><td align='right' class='alt2'><b>" . T_("EMAIL") . ":</b> </td><td align='left' class='alt2'><input type=\"text\" name=\"email\" size=\"50\" value=\"" . htmlspecialchars($CURUSER["email"]) .
            "\" /><br />\n<i>" . T_("REPLY_TO_CONFIRM_EMAIL") . "</i><br /></td></tr>");

        ksort($tzs);
        reset($tzs);
        while (list($key, $val) = thisEach($tzs)) {
            if ($CURUSER["tzoffset"] == $key) {
                $tz .= "<option value=\"$key\" selected='selected'>$val[0]</option>\n";
            } else {
                $tz .= "<option value=\"$key\">$val[0]</option>\n";
            }
        }

        print("<tr><td align='right' class='alt3'><b>" . T_("TIMEZONE") . ":</b> </td><td align='left' class='alt3'><select name='tzoffset'>$tz</select></td></tr>");
        ?>
        <tr><td colspan="2" align="center"><button type='submit' class='btn btn-sm btn-primary'><?php echo T_("SUBMIT"); ?></button> <input type="reset" value="<?php echo T_("REVERT"); ?>" /></td></tr>
        </table></form>
        <?php
        end_frame();
        stdfoot();
        }

        if ($do == "save_settings") {
            begin_frame(T_("EDIT_ACCOUNT_SETTINGS"));

            usermenu();
            $set = array();
            $updateset = array();
            $changedemail = $newsecret = 0;

            $email = $_POST["email"];
            if ($email != $CURUSER["email"]) {
                if (!validemail($email)) {
                    $message = T_("NOT_VALID_EMAIL");
                }

                $changedemail = 1;
            }

            $acceptpms = $_POST["acceptpms"];
            $pmnotif = $_POST["pmnotif"];
            $privacy = $_POST["privacy"];
            // todo $notifs = ($pmnotif == 'yes' ? "[pm]" : "");
            $notifs = $pmnotif;
            $r = $pdo->run("SELECT id FROM categories");
            $rows = $r->rowCount();
            for ($i = 0; $i < $rows; ++$i) {
                $a = $r->fetch();
                if ($_POST["cat$a[id]"] == 'yes') {
                    $notifs .= "[cat$a[id]]";
                }

            }

            if ($_POST['resetpasskey']) {
                $updateset[] = "passkey=''";
            }

            $avatar = strip_tags($_POST["avatar"]);

            if ($avatar != null) {
                # Allowed Image Extenstions.
                $allowed_types = &$site_config["allowed_image_types"];

                # We force http://
                if (!preg_match("#^\w+://#i", $avatar)) {
                    $avatar = "http://" . $avatar;
                }

                # Clean Avatar Path.
                $avatar = cleanstr($avatar);

                # Validate Image.
                $im = @getimagesize($avatar);

                if (!$im[2] || !@array_key_exists($im['mime'], $allowed_types)) {
                    $message = "The avatar url was determined to be of a invalid nature.";
                }

                # Save New Avatar.
                $updateset[] = "avatar = $avatar";
            }

            $title = strip_tags($_POST["title"]);
            $signature = $_POST["signature"];
            $stylesheet = $_POST["stylesheet"];
            $language = $_POST["language"];
            $client = strip_tags($_POST["client"]);
            $age = $_POST["age"];
            $gender = $_POST["gender"];
            $country = $_POST["country"];
            $teams = $_POST["teams"];
            $privacy = $_POST["privacy"];
            $timezone = (int) $_POST['tzoffset'];

            if (is_valid_id($stylesheet)) {
                $updateset[] = "stylesheet = $stylesheet";
            }

            if (is_valid_id($language)) {
                $updateset[] = "language = $language";
            }

            if (is_valid_id($teams)) {
                $updateset[] = "team = $teams";
            }

            if (is_valid_id($country)) {
                $updateset[] = "country = $country";
            }

            if ($acceptpms == "yes") {
                $acceptpms = 'yes';
            } else {
                $acceptpms = 'no';
            }

            if (is_valid_id($age)) {
                $updateset[] = "age = $age";
            }

            $hideshoutbox = ($_POST["hideshoutbox"] == "yes") ? "yes" : "no";

            $updateset[] = "hideshoutbox = $hideshoutbox";
            $updateset[] = "acceptpms = $acceptpms";
            // todo $updateset[] = "commentpm = '" . $pmnotif == "yes" ? "yes" : "no" . "'";
            $updateset[] = "commentpm = $pmnotif";
            $updateset[] = "notifs = $notifs";
            $updateset[] = "privacy = $privacy";
            $updateset[] = "gender = $gender";
            $updateset[] = "client = $client";
            $updateset[] = "signature = $signature";
            $updateset[] = "title = $title";
            $updateset[] = "tzoffset = $timezone";

            /* ****** */

            if (!$message) {

                if ($changedemail) {
                    $sec = mksecret();
                    $hash = md5($sec . $email . $sec);
                    $obemail = rawurlencode($email);
                    $updateset[] = "editsecret = $sec";
                    $thishost = $_SERVER["HTTP_HOST"];
                    $thisdomain = preg_replace('/^www\./is', "", $thishost);
                    $body = <<<EOD
You have requested that your user profile (username {$CURUSER["username"]})
on {$site_config["SITEURL"]} should be updated with this email address ($email) as
user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To complete the update of your user profile, please follow this link:

{$site_config["SITEURL"]}/account/ce?id={$CURUSER["id"]}&secret=$hash&email=$obemail

Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

                    sendmail($email, "$site_config[SITENAME] profile update confirmation", $body, "From: $site_config[SITEEMAIL]", "-f$site_config[SITEEMAIL]");
                    $mailsent = 1;
                } //changedemail

                $pdo->run("UPDATE users SET ' . implode(", ", $updateset) . ' WHERE id =?", [$CURUSER['id']]); // new edit
                $edited = 1;
                echo "<br /><br /><center><b><font class='error'>Updated OK</font></b></center><br /><br />";
                if ($changedemail) {
                    echo "<br /><center><b>" . T_("EMAIL_CHANGE_SEND") . "</b></center><br /><br />";
                }
            } else {
                echo "<br /><br /><center><b><font class='error'>" . T_("ERROR") . ": " . $message . "</font></b></center><br /><br />";
            } // message

            end_frame();
            stdfoot();
        } // end do

    } //end action

    // change own password
    public function changepw()
    {
        dbconn();
        global $site_config, $CURUSER, $pdo;
        loggedinonly();

        stdhead(T_("USERCP"));
        $do = $_REQUEST["do"];

        if ($do == "newpassword") {

            $chpassword = $_POST['chpassword'];
            $passagain = $_POST['passagain'];

            if ($chpassword != "") {

                if (strlen($chpassword) < 6) {
                    $message = T_("PASS_TOO_SHORT");
                }

                if ($chpassword != $passagain) {
                    $message = T_("PASSWORDS_NOT_MATCH");
                }

                $chpassword = password_hash($chpassword, PASSWORD_BCRYPT);
                $secret = mksecret();
            }

            if ((!$chpassword) || (!$passagain)) {
                $message = "You must enter something!";
            }

            begin_frame();
            usermenu();

            if (!$message) {
                $pdo->run("UPDATE users SET password = ?, secret = ?  WHERE id =? ", [$chpassword, $secret, $CURUSER['id']]);
                echo "<br /><br /><center><b>" . T_("PASSWORD_CHANGED_OK") . "</b></center>";
                logoutcookie();
            } else {
                echo "<br /><br /><b><center>" . $message . "</center></b><br /><br />";
            }

            end_frame();
            stdfoot();
            die();
        } //do

        begin_frame(T_("CHANGE_YOUR_PASS"));

        usermenu();
        ?>
        <form method="post" action="<?php echo $site_config["SITEURL"]; ?>/users/changepw">
        <input type="hidden" name="do" value="newpassword" />
        <div class="f-border">
        <br />
        <table border="0" align="center" cellpadding="10">
        <tr class="alt3">
        <td align="right"><b><?php echo T_("NEW_PASSWORD"); ?>:</b></td>
        <td align="left"><input type="password" name="chpassword" size="40" /></td>
        </tr>
        <tr class="alt3">
        <td align="right"><b><?php echo T_("REPEAT"); ?>:</b></td>
        <td align="left"><input type="password" name="passagain" size="40" /></td>
        </tr>
        <tr class="alt2">
        <td colspan="2" align="center">
        <input type="reset" value="<?php echo T_("REVERT"); ?>" />
        <button type='submit' class='btn btn-sm btn-primary'><?php echo T_("SUBMIT"); ?></button>
        </td>
        </tr>
        </table>
        <br />
        </div>
        </form>
        <?php
        end_frame();
        stdfoot();
    }

    // all users - memberlists
    public function all()
    { 

        dbconn();
        global $site_config, $CURUSER, $pdo;
        loggedinonly();

        if ($CURUSER["view_users"] == "no") {
            show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);
        }

        $search = trim($_GET['search'] ?? '');
        $class = (int) ($_GET['class'] ?? 0);
        $letter = trim($_GET['letter'] ?? '');

        if (!$class) {
            unset($class);
        }

        $q = $query = null;
        if ($search) {
            $query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
            if ($search) {
                $q = "search=" . htmlspecialchars($search);
            }
        } elseif ($letter) {
            if (strlen($letter) > 1) {
                unset($letter);
            }

            if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
                unset($letter);
            } else {
                $query = "username LIKE '$letter%' AND status='confirmed'";
            }
            $q = "letter=$letter";
        }

        if (!$query) {
            $query = "status='confirmed'";
        }

        if ($class) {
            $query .= " AND class=$class";
            $q .= ($q ? "&amp;" : "") . "class=$class";
        }

        stdhead(T_("USERS"));
        begin_frame(T_("USERS"));

        print("<center><br /><form method='get' action='/users/all'>\n");
        print(T_("SEARCH") . ": <input type='text' size='30' name='search' />\n");
        print("<select name='class'>\n");
        print("<option value='-'>(any class)</option>\n");
        $res = $this->groupsModel->getGroups();
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            print("<option value='$row[group_id]'" . ($class && $class == $row['group_id'] ? " selected='selected'" : "") . ">" . htmlspecialchars($row['level']) . "</option>\n");
        }
        print("</select>\n");
        print("<button type='submit' class='btn btn-primary btn-sm'>" . T_("APPLY") . "</button>");
        print("</form></center>\n");

        print("<p align='center'>\n");

        print("<a href='$site_config[SITEURL]/users/all'><b>" . T_("ALL") . "</b></a> - \n");
        foreach (range("a", "z") as $l) {
            $L = strtoupper($l);
            if ($l == $letter) {
                print("<b>$L</b>\n");
            } else {
                print("<a href='$site_config[SITEURL]/users/all?letter=$l'><b>$L</b></a>\n");
            }

        }

        print("</p>\n");

        $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
        if ($page <= 0) {
            $page = 1;
        }

        $per_page = 5; // Set how many records do you want to display per page.
        $startpoint = ($page * $per_page) - $per_page;
        $statement = "`users` ORDER BY `id` ASC"; // Change `users` & 'id' according to your table name.
        $results = $this->groupsModel->getGroupsearch($query, $startpoint, $per_page);

        if ($results->rowCount()) {

        // call function for table
        print("<br />");

        print("<div class='table-responsive'> <table class='table table-striped'><thead><tr><thead><tr>
        <th>" . T_("USERNAME") . "</th>
        <th>" . T_("REGISTERED") . "</th>
        <th>" . T_("LAST_ACCESS") . "</th>
        <th>" . T_("CLASS") . "</th>
        <th>" . T_("COUNTRY") . "</th>
        </tr></thead>");

        while ($row = $results->fetch(PDO::FETCH_ASSOC)) {

            $cres = $this->countriesModel->getCountry($row);

            if ($carr = $cres->fetch(PDO::FETCH_ASSOC)) {
                $country = "<td><img src='$site_config[SITEURL]/images/languages/$carr[flagpic]' title='" . htmlspecialchars($carr['name']) . "' alt='" . htmlspecialchars($carr['name']) . "' /></td>";
            } else {
                $country = "<td><img src='$site_config[SITEURL]/images/languages/unknown.gif' alt='Unknown' /></td>";
            }

         print("<tbody><tr>
         <td><a href='$site_config[SITEURL]/users?id=$row[id]'><b>" . class_user($row['username']) . "</b></a>" . ($row["donated"] > 0 ? "<img src='$site_config[SITEURL]/images/star.png' border='0' alt='Donated' />" : "") . "</td>" . "
         <td>" . utc_to_tz($row["added"]) . "</td>
         <td>" . utc_to_tz($row["last_access"]) . "</td>" . "
         <td>" . T_($row["level"]) . "</td>$country</tr></tbody>");
            }
            print("</table></div>");
        } else {
            echo "No records are found.";
        }
        // displaying paginaiton function
        echo pagination($statement, $per_page, $page, $url = '?');

        end_frame();
        stdfoot();
    }

    // edit for staff
    public function edit()
    {
        dbconn();
        global $site_config, $CURUSER;
        loggedinonly();
        stdhead("User CP");
        $do = $_REQUEST["do"] ?? '';
        $id = (int) $_GET["id"];

        if (!is_valid_id($id)) {
            show_error_msg(T_("NO_SHOW_DETAILS"), "Bad ID.", 1);
        }

        $user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
        if (!$user) {
            show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID") . " $id.", 1);
        }
        //add invites check here
        if ($CURUSER["view_users"] == "no" && $CURUSER["id"] != $id) {
            show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);
        }

        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $CURUSER["edit_users"] == "no") {
            show_error_msg(T_("ERROR"), T_("NO_ACCESS_ACCOUNT_DISABLED"), 1);
        }
        //$country
        $res = DB::run("SELECT name FROM countries WHERE id=? LIMIT 1", [$user['country']]);
        if ($res->rowCount() == 1) {
            $arr = $res->fetch();
            $country = "$arr[name]";
        }
        if (!$country) {
            $country = "<b>Unknown</b>";
        }

        //$ratio
        if ($user["downloaded"] > 0) {
            $ratio = $user["uploaded"] / $user["downloaded"];
        } else {
            $ratio = "---";
        }

        $numtorrents = get_row_count("torrents", "WHERE owner = $id");
        $numcomments = get_row_count("comments", "WHERE user = $id");
        $numforumposts = get_row_count("forum_posts", "WHERE userid = $id");

        $avatar = htmlspecialchars($user["avatar"]);
        if (!$avatar) {
            $avatar = $site_config["SITEURL"] . "/images/default_avatar.png";
        }

        // edit
        begin_frame(sprintf(T_("USER_DETAILS_FOR"), class_user($user["username"])));
        usermenu();
        echo "<div>";
        if ($CURUSER["edit_users"] == "yes") {

        $avatar = htmlspecialchars($user["avatar"]);
        $signature = htmlspecialchars($user["signature"]);
        $uploaded = $user["uploaded"];
        $downloaded = $user["downloaded"];
        $enabled = $user["enabled"] == 'yes';
        $warned = $user["warned"] == 'yes';
        $forumbanned = $user["forumbanned"] == 'yes';
        $modcomment = htmlspecialchars($user["modcomment"]);

        print("<form method='post' action='adminmodtasks'>\n");
        print("<input type='hidden' name='action' value='edituser' />\n");
        print("<input type='hidden' name='userid' value='$id' />\n");
        print("<table border='0' cellspacing='0' cellpadding='3'>\n");
        print("<tr><td>" . T_("TITLE") . ": </td><td align='left'><input type='text' size='67' name='title' value=\"$user[title]\" /></td></tr>\n");
        print("<tr><td>" . T_("EMAIL") . "</td><td align='left'><input type='text' size='67' name='email' value=\"$user[email]\" /></td></tr>\n");
        print("<tr><td>" . T_("SIGNATURE") . ": </td><td align='left'><textarea cols='50' rows='10' name='signature'>" . htmlspecialchars($user["signature"]) . "</textarea></td></tr>\n");
        print("<tr><td>" . T_("UPLOADED") . ": </td><td align='left'><input type='text' size='30' name='uploaded' value=\"" . mksize($user["uploaded"], 9) . "\" /></td></tr>\n");
        print("<tr><td>" . T_("DOWNLOADED") . ": </td><td align='left'><input type='text' size='30' name='downloaded' value=\"" . mksize($user["downloaded"], 9) . "\" /></td></tr>\n");
        print("<tr><td>" . T_("AVATAR_URL") . "</td><td align='left'><input type='text' size='67' name='avatar' value=\"$avatar\" /></td></tr>\n");
        print("<tr><td>" . T_("IP_ADDRESS") . ": </td><td align='left'><input type='text' size='20' name='ip' value=\"$user[ip]\" /></td></tr>\n");
        print("<tr><td>" . T_("INVITES") . ": </td><td align='left'><input type='text' size='4' name='invites' value='" . $user["invites"] . "' /></td></tr>\n");

        if ($CURUSER["class"] > $user["class"]) {
            print("<tr><td>" . T_("CLASS") . ": </td><td align='left'><select name='class'>\n");
            $maxclass = $CURUSER["class"];
            for ($i = 1; $i < $maxclass; ++$i) {
                print("<option value='$i' " . ($user["class"] == $i ? " selected='selected'" : "") . ">$prefix" . get_user_class_name($i) . "\n");
            }

            print("</select></td></tr>\n");
        }

        print("<tr><td>" . T_("DONATED_US") . ": </td><td align='left'><input type='text' size='4' name='donated' value='$user[donated]' /></td></tr>\n");
        print("<tr><td>" . T_("PASSWORD") . ": </td><td align='left'><input type='password' size='67' name='password' value=\"$user[password]\" /></td></tr>\n");
        print("<tr><td>" . T_("CHANGE_PASS") . ": </td><td align='left'><input type='checkbox' name='chgpasswd' value='yes'/></td></tr>");
        print("<tr><td>" . T_("MOD_COMMENT") . ": </td><td align='left'><textarea cols='50' rows='10' name='modcomment'>$modcomment</textarea></td></tr>\n");
        print("<tr><td>" . T_("ACCOUNT_STATUS") . ": </td><td align='left'><input name='enabled' value='yes' type='radio' " . ($enabled ? " checked='checked'" : "") . " />Enabled <input name='enabled' value='no' type='radio' " . (!$enabled ? " checked='checked' " : "") . " />Disabled</td></tr>\n");
        print("<tr><td>" . T_("WARNED") . ": </td><td align='left'><input name='warned' value='yes' type='radio' " . ($warned ? " checked='checked'" : "") . " />Yes <input name='warned' value='no' type='radio' " . (!$warned ? " checked='checked'" : "") . " />No</td></tr>\n");
        print("<tr><td>" . T_("FORUM_BANNED") . ": </td><td align='left'><input name='forumbanned' value='yes' type='radio' " . ($forumbanned ? " checked='checked'" : "") . " />Yes <input name='forumbanned' value='no' type='radio' " . (!$forumbanned ? " checked='checked'" : "") . " />No</td></tr>\n");
        print("<tr><td>" . T_("PASSKEY") . ": </td><td align='left'>$user[passkey]<br /><input name='resetpasskey' value='yes' type='checkbox' />" . T_("RESET_PASSKEY") . " (" . T_("RESET_PASSKEY_MSG") . ")</td></tr>\n");
        print("<tr><td colspan='2' align='center'><input type='submit' value='" . T_("SUBMIT") . "' /></td></tr>\n");
        print("</table>\n");
        print("</form>\n");

        }
        echo "</div>";
        end_frame();
        stdfoot();
    }

    // warnings
    public function warning()
    {
        dbconn();
        global $site_config, $CURUSER;
        loggedinonly();
        stdhead("User CP");
        $do = $_REQUEST["do"] ?? '';
        $id = (int) $_GET["id"];

        if (!is_valid_id($id)) {
            show_error_msg(T_("NO_SHOW_DETAILS"), "Bad ID.", 1);
        }
        begin_frame(sprintf(T_("USER_DETAILS_FOR"), class_user($user["username"])));
        usermenu();
        echo "<div>";
        if($CURUSER["edit_users"]=="yes"){
            print '<a name="warnings"></a>';
            $res = DB::run("SELECT * FROM warnings WHERE userid=? ORDER BY id DESC", [$id]);

            if ($res->rowCount() > 0){
                ?>
            <b>Warnings:</b><br />
            <table border="1" cellpadding="3" cellspacing="0" width="80%" align="center" class="table_table">
            <tr>
            <th class="table_head">Added</th>
            <th class="table_head"><?php echo T_("EXPIRE"); ?></th>
            <th class="table_head"><?php echo T_("REASON"); ?></th>
            <th class="table_head"><?php echo T_("WARNED_BY"); ?></th>
            <th class="table_head"><?php echo T_("TYPE"); ?></th>
            </tr>
            <?php
            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                if ($arr["warnedby"] == 0) {
                    $wusername = T_("SYSTEM");
                } else {
                    $res2 = DB::run("SELECT id,username FROM users WHERE id =?", [$arr['warnedby']]);
                    $arr2 = $res2->fetch();
                    $wusername = class_user($arr2["username"]);
                }
                $arr['added'] = utc_to_tz($arr['added']);
                $arr['expiry'] = utc_to_tz($arr['expiry']);

                $addeddate = substr($arr['added'], 0, strpos($arr['added'], " "));
                $expirydate = substr($arr['expiry'], 0, strpos($arr['expiry'], " "));
                print("<tr><td class='table_col1' align='center'>$addeddate</td><td class='table_col2' align='center'>$expirydate</td><td class='table_col1'>".format_comment($arr['reason'])."</td><td class='table_col2' align='center'><a href='$site_config[SITEURL]/users?id=".$arr2['id']."'>".$wusername."</a></td><td class='table_col1' align='center'>".$arr['type']."</td></tr>\n");
            }
            echo "</table>\n";
        }else{
            echo T_("NO_WARNINGS");
        }

        print("<form method='post' action='$site_config[SITEURL]/adminmodtasks'>\n");
        print("<input type='hidden' name='action' value='addwarning' />\n");
        print("<input type='hidden' name='userid' value='$id' />\n");
        echo "<br /><br /><center><table border='0'><tr><td align='right'><b>".T_("REASON").":</b> </td><td align='left'><textarea cols='40' rows='5' name='reason'></textarea></td></tr>";
        echo "<tr><td align='right'><b>".T_("EXPIRE").":</b> </td><td align='left'><input type='text' size='4' name='expiry' />(days)</td></tr>";
        echo "<tr><td align='right'><b>".T_("TYPE").":</b> </td><td align='left'><input type='text' size='10' name='type' /></td></tr>";
        echo "<tr><td colspan='2' align='center'><button type='submit' class='btn btn-sm btn-success'><b>" .T_("ADD_WARNING"). "</b></button></td></tr></table></center></form>";

        if($CURUSER["delete_users"] == "yes"){
            print("<hr /><center><form method='post' action='$site_config[SITEURL]/adminmodtasks'>\n");
            print("<input type='hidden' name='action' value='deleteaccount' />\n");
            print("<input type='hidden' name='userid' value='$id' />\n");
            print("<input type='hidden' name='username' value='".$user["username"]."' />\n");
            echo "<b>".T_("REASON").":</b><input type='text' size='30' name='delreason' />";
            echo "<button type='submit' class='btn btn-sm btn-danger'><b>" .T_("DELETE_ACCOUNT"). "</b></button></form></center>";
        }
        }
        echo "</div>";
        end_frame();
        stdfoot();
    }

}