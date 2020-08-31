<?php
if ($action == "news" && $do == "view") {
    $title = T_("NEWS_MANAGEMENT");
    require 'views/admin/header.php';
    adminnavmenu();

    begin_frame(T_("NEWS"));
    echo "<center><a href='$config[SITEURL]/admincp?action=news&amp;do=add'><b>" . T_("CP_NEWS_ADD_ITEM") . "</b></a></center><br />";

    $res = DB::run("SELECT * FROM news ORDER BY added DESC");
    if ($res->rowCount() > 0) {

        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            $newsid = $arr["id"];
            $body = format_comment($arr["body"]);
            $title = $arr["title"];
            $userid = $arr["userid"];
            $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

            $arr2 = DB::run("SELECT username FROM users WHERE id =?", [$userid])->fetch();
            $postername = class_user_colour($arr2["username"]);

            if ($postername == "") {
                $by = "Unknown";
            } else {
                $by = "<a href='" . TTURL . "/users/profile?id=$userid'><b>$postername</b></a>";
            }

            print("<table border='0' cellspacing='0' cellpadding='0'><tr><td>");
            print("$added&nbsp;---&nbsp;by&nbsp;$by");
            print(" - [<a href='$config[SITEURL]/admincp?action=news&amp;do=edit&amp;newsid=$newsid'><b>" . T_("EDIT") . "</b></a>]");
            print(" - [<a href='$config[SITEURL]/admincp?action=news&amp;do=delete&amp;newsid=$newsid'><b>" . T_("DEL") . "</b></a>]");
            print("</td></tr>\n");

            print("<tr valign='top'><td><b>$title</b><br />$body</td></tr></table><br />\n");
        }

    } else {
        echo "No News Posted";
    }

    end_frame();
    require 'views/admin/footer.php';
}

if ($action == "news" && $do == "takeadd") {
    $body = $_POST["body"];

    if (!$body) {
        show_error_msg(T_("ERROR"), T_("ERR_NEWS_ITEM_CAN_NOT_BE_EMPTY"), 1);
    }

    $title = $_POST['title'];

    if (!$title) {
        show_error_msg(T_("ERROR"), T_("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"), 1);
    }

    $added = $_POST["added"];

    if (!$added) {
        $added = get_date_time();
    }

    $afr = DB::run("INSERT INTO news (userid, added, body, title) VALUES (?,?,?,?)", [$_SESSION['id'], $added, $body, $title]);
    if ($afr) {
        autolink(TTURL . "/admincp?action=news&do=view", T_("CP_NEWS_ITEM_ADDED_SUCCESS"));
    } else {
        show_error_msg(T_("ERROR"), T_("CP_NEWS_UNABLE_TO_ADD"), 1);
    }

}

if ($action == "news" && $do == "add") {
    $title = T_("NEWS_MANAGEMENT");
    require 'views/admin/header.php';
    adminnavmenu();

    begin_frame(T_("CP_NEWS_ADD"));
    print("<center><form method='post' action='$config[SITEURL]/admincp' name='news'>\n");
    print("<input type='hidden' name='action' value='news' />\n");
    print("<input type='hidden' name='do' value='takeadd' />\n");

    print("<b>" . T_("CP_NEWS_TITLE") . ":</b> <input type='text' name='title' /><br />\n");

    echo "<br />" . textbbcode("news", "body") . "<br />";

    print("<br /><br /><input type='submit' value='" . T_("SUBMIT") . "' />\n");

    print("</form><br /><br /></center>\n");
    end_frame();
    require 'views/admin/footer.php';
}

if ($action == "news" && $do == "edit") {
    $title = T_("NEWS_MANAGEMENT");
    require 'views/admin/header.php';
    adminnavmenu();

    $newsid = (int) $_GET["newsid"];

    if (!is_valid_id($newsid)) {
        show_error_msg(T_("ERROR"), sprintf(T_("CP_NEWS_INVAILD_ITEM_ID"), $newsid), 1);
    }

    $res = DB::run("SELECT * FROM news WHERE id=?", [$newsid]);
    if ($res->rowCount() != 1) {
        show_error_msg(T_("ERROR"), sprintf(T_("CP_NEWS_NO_ITEM_WITH_ID"), $newsid), 1);
    }

    $arr = $res->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $body = $_POST['body'];

        if ($body == "") {
            show_error_msg(T_("ERROR"), T_("FORUMS_BODY_CANNOT_BE_EMPTY"), 1);
        }

        $title = $_POST['title'];

        if ($title == "") {
            show_error_msg(T_("ERROR"), T_("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"), 1);
        }

        $body = $body;

        $editedat = get_date_time();

        DB::run("UPDATE news SET body=?, title=? WHERE id=?", [$body, $title, $newsid]);

        $returnto = $_POST['returnto'];

        if ($returnto != "") {
            header("Location: $returnto");
        } else {
            autolink(TTURL . "/admincp?action=news&do=view", T_("CP_NEWS_ITEM_WAS_EDITED_SUCCESS"));
        }

    } else {
        $returnto = htmlspecialchars($_GET['returnto']);
        begin_frame(T_("CP_NEWS_EDIT"));
        print("<form method='post' action='$config[SITEURL]/admincp?action=news&amp;do=edit&amp;newsid=$newsid' name='news'>\n");
        print("<center>");
        print("<input type='hidden' name='returnto' value='$returnto' />\n");
        print("<b>" . T_("CP_NEWS_TITLE") . ": </b><input type='text' name='title' value=\"" . $arr['title'] . "\" /><br /><br />\n");
        echo "<br />" . textbbcode("news", "body", $arr["body"]) . "<br />";
        print("<br /><input type='submit' value='Okay' />\n");
        print("</center>\n");
        print("</form>\n");
    }
    end_frame();
    require 'views/admin/footer.php';
}

if ($action == "news" && $do == "delete") {

    $newsid = (int) $_GET["newsid"];

    if (!is_valid_id($newsid)) {
        show_error_msg(T_("ERROR"), sprintf(T_("CP_NEWS_INVAILD_ITEM_ID"), $newsid), 1);
    }

    DB::run("DELETE FROM news WHERE id=?", [$newsid]);
    DB::run("DELETE FROM comments WHERE news =?", [$newsid]);

    autolink(TTURL . "/admincp?action=news&do=view", T_("CP_NEWS_ITEM_DEL_SUCCESS"));
}
