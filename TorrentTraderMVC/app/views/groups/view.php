<?php
Style::begin(Lang::T("USER_GROUPS"));
    print("<center><a href='".URLROOT."/admingroups/groupsadd'>" . Lang::T("Add New Group") . "</a></center>\n");
    print("<div class='table-responsive'>
    <table class='table'><thead>");
    print("<tr>");
    print("<th class='table_head'>" . Lang::T("NAME") . "</th>\n");
    print("<th class='table_head'>" . Lang::T("TORRENTS") . "<br />" . Lang::T("GROUPS_VIEW_EDIT_DEL") . "</th>\n");
    print("<th class='table_head'>" . Lang::T("MEMBERS") . "<br />" . Lang::T("GROUPS_VIEW_EDIT_DEL") . "</th>\n");
    print("<th class='table_head'>" . Lang::T("NEWS") . "<br />" . Lang::T("GROUPS_VIEW_EDIT_DEL") . "</th>\n");
    print("<th class='table_head'>" . Lang::T("FORUM") . "<br />" . Lang::T("GROUPS_VIEW_EDIT_DEL") . "</th>\n");
    print("<th class='table_head'>" . Lang::T("UPLOAD") . "</th>\n");
    print("<th class='table_head'>" . Lang::T("DOWNLOAD") . "</th>\n");
    print("<th class='table_head'>" . Lang::T("SLOTS") . "</th>\n");
    print("<th class='table_head'>" . Lang::T("CP_VIEW") . "</th>\n");
    print("<th class='table_head'>" . Lang::T("CP_STAFF_PAGE") . "</th>");
    print("<th class='table_head'>" . Lang::T("CP_STAFF_PUBLIC") . "</th>");
    print("<th class='table_head'>" . Lang::T("CP_STAFF_SORT") . "</th>");
    print("<th class='table_head'>" . Lang::T("DEL") . "</th>\n");
    print("</tr></thead><tbody>");
    while ($level = $data['getlevel']->fetch(PDO::FETCH_LAZY)) {
        print("<tr>\n");
        print("<td class='table_col1'><a href=".URLROOT."/admingroups/groupsedit?group_id=" . $level["group_id"] . "><font color=\"$level[Color]\">" . $level["level"] . "</font></td>\n");
        print("<td class='table_col2'>" . $level["view_torrents"] . "/" . $level["edit_torrents"] . "/" . $level["delete_torrents"] . "</td>\n");
        print("<td class='table_col1'>" . $level["view_users"] . "/" . $level["edit_users"] . "/" . $level["delete_users"] . "</td>\n");
        print("<td class='table_col2'>" . $level["view_news"] . "/" . $level["edit_news"] . "/" . $level["delete_news"] . "</td>\n");
        print("<td class='table_col1'>" . $level["view_forum"] . "/" . $level["edit_forum"] . "/" . $level["delete_forum"] . "</td>\n");
        print("<td class='table_col2'>" . $level["can_upload"] . "</td>\n");
        print("<td class='table_col1'>" . $level["can_download"] . "</td>\n");
        print("<td class='table_col1'>" . $level["maxslots"] . "</td>\n");
        print("<td class='table_col2'>" . $level["control_panel"] . "</td>\n");
        print("<td class='table_col1'>" . $level["staff_page"] . "</td>\n");
        print("<td class='table_col2'>" . $level["staff_public"] . "</td>\n");
        print("<td class='table_col1'>" . $level["staff_sort"] . "</td>\n");
        print("<td class='table_col1'><a href='".URLROOT."/admingroups/groupsdelete?group_id=" . $level["group_id"] . "'>Del</a></td>\n");
        print("</tr>\n");
    }
    print("</tbody></table></div><br /><br />");
    Style::end();