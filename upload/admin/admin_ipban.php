<?php
// IP Bans (TorrentialStorm)
if ($action == "ipbans") {
    stdhead(T_("BANNED_IPS"));
    adminnavmenu();

    if ($do == "del") {
        if (!@count($_POST["delids"])) show_error_msg(T_("ERROR"), T_("NONE_SELECTED"), 1);
        $delids = array_map('intval', $_POST["delids"]);
        $delids = implode(', ', $delids);
        $res = DB::run("SELECT * FROM bans WHERE id IN ($delids)");
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            DB::run("DELETE FROM bans WHERE id=$row[id]");
            
            # Needs to be tested...
            if (is_ipv6($row["first"]) && is_ipv6($row["last"])) {
                $first = long2ip6($row["first"]);
                $last  = long2ip6($row["last"]);
            } else {
                $first = long2ip($row["first"]);
                $last  = long2ip($row["last"]);
            }
            
            write_log("IP Ban ($first - $last) was removed by $CURUSER[id] ($CURUSER[username])");
        }
        show_error_msg(T_("SUCCESS"), "Ban(s) deleted.", 0);
    }

    if ($do == "add") {
        $first = trim($_POST["first"]);
        $last = trim($_POST["last"]);
        $comment = trim($_POST["comment"]);
        if ($first == "" || $last == "" || $comment == "")
            show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA").". Go back and try again", 1);

	if (!validip($first) || !validip($last))
            show_error_msg(T_("ERROR"), "Bad IP address.");
        $comment = $comment;
        $added = get_date_time();
        $bins = DB::run("INSERT INTO bans (added, addedby, first, last, comment) VALUES(?,?,?,?,?)", [$added, $CURUSER['id'], $first, $last, $comment]);
        $err = $bins->errorCode();
        switch ($err) {
            case 1062:
                show_error_msg(T_("ERROR"), "Duplicate ban.", 0);
            break;
            case 0:
                show_error_msg(T_("SUCCESS"), "Ban added.", 0);
            break;
            default:
                show_error_msg(T_("ERROR"), T_("THEME_DATEBASE_ERROR")." ".htmlspecialchars($bins->errorInfo()), 0);
        }
    }

    begin_frame(T_("BANNED_IPS"));
    echo "<p align=\"justify\">This page allows you to prevent individual users or groups of users from accessing your tracker by placing a block on their IP or IP range.<br />
    If you wish to temporarily disable an account, but still wish a user to be able to view your tracker, you can use the 'Disable Account' option which is found in the user's profile page.</p><br />";

    $count = get_row_count("bans");
    if ($count == 0)
    print("<b>No Bans Found</b><br />\n");
    else {
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, "/admincp?action=ipbans&amp;"); // 50 per page
        echo $pagertop;

        echo "<form id='ipbans' action='/admincp?action=ipbans&amp;do=del' method='post'><table width='98%' cellspacing='0' cellpadding='5' align='center' class='table_table'>
        <tr>
            <th class='table_head'>".T_("DATE_ADDED")."</th>
            <th class='table_head'>First IP</th>
            <th class='table_head'>Last IP</th>
            <th class='table_head'>".T_("ADDED_BY")."</th>
            <th class='table_head'>Comment</th>
            <th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
        </tr>";

        $res = DB::run("SELECT bans.*, users.username FROM bans LEFT JOIN users ON bans.addedby=users.id ORDER BY added $limit");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                <td align='center' class='table_col1'>".date('d/m/Y H:i:s', utc_to_tz_time($arr["added"]))."</td>
                <td align='center' class='table_col2'>$arr[first]</td>
                <td align='center' class='table_col1'>$arr[last]</td>
                <td align='center' class='table_col2'><a href='".TTURL."/users?id=$arr[addedby]'>$arr[username]</a></td>
                <td align='center' class='table_col1'>$arr[comment]</td>
                <td align='center' class='table_col2'><input type='checkbox' name='delids[]' value='$arr[id]' /></td>
            </tr>";
        }
        echo "</table><br /><center><input type='submit' value='Delete Checked' /></center></form><br />";
        echo $pagerbottom;
    }

    echo "<br />";
    print("<form method='post' action='/admincp?action=ipbans&amp;do=add'>\n");
    print("<table cellspacing='0' cellpadding='5' align='center' class='table_table' width='98%'>\n");
    print("<tr><th class='table_head' align='center'>Add Ban</th></tr>\n");
    print("<tr><td class='table_col1' align='center'>First IP:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='first' size='40' /></td></tr>\n");
    print("<tr><td class='table_col1' align='center'>Last IP:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='last' size='40' /></td></tr>\n");
    print("<tr><td class='table_col1' align='center'>Comment: <input type='text' name='comment' size='40' /></td></tr>\n");
    print("<tr><td class='table_head' align='center'><input type='submit' value='Okay' /></td></tr>\n");
    print("</table></form><br />\n");

    end_frame();
    stdfoot();
}
// End IP Bans (TorrentialStorm)