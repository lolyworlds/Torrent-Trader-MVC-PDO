<?php
Style::begin(Lang::T("BANNED_IPS"));
    echo "<p align=\"justify\">This page allows you to prevent individual users or groups of users from accessing your tracker by placing a block on their IP or IP range.<br />
    If you wish to temporarily disable an account, but still wish a user to be able to view your tracker, you can use the 'Disable Account' option which is found in the user's profile page.</p><br />";
    if ($data['count'] == 0) {
        print("<b>No Bans Found</b><br />\n");
    } else {
        echo $data['pagertop'];
        echo "<form id='ipbans' action='".URLROOT."/adminipban?do=del' method='post'><table width='98%' cellspacing='0' cellpadding='5' align='center' class='table_table'>
        <tr>
            <th class='table_head'>" . Lang::T("DATE_ADDED") . "</th>
            <th class='table_head'>First IP</th>
            <th class='table_head'>Last IP</th>
            <th class='table_head'>" . Lang::T("ADDED_BY") . "</th>
            <th class='table_head'>Comment</th>
            <th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
        </tr>";
        while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                <td align='center' class='table_col1'>" . date('d/m/Y H:i:s', TimeDate::utc_to_tz_time($arr["added"])) . "</td>
                <td align='center' class='table_col2'>$arr[first]</td>
                <td align='center' class='table_col1'>$arr[last]</td>
                <td align='center' class='table_col2'><a href='" . URLROOT . "/profile?id=$arr[addedby]'>$arr[username]</a></td>
                <td align='center' class='table_col1'>$arr[comment]</td>
                <td align='center' class='table_col2'><input type='checkbox' name='delids[]' value='$arr[id]' /></td>
            </tr>";
        }
        echo "</table><br /><center><input type='submit' value='Delete Checked' /></center></form><br />";
        echo $data['pagerbottom'];
    }
    echo "<br />";
    print("<form method='post' action='".URLROOT."/adminipban?do=add'>\n");
    print("<table cellspacing='0' cellpadding='5' align='center' class='table_table' width='98%'>\n");
    print("<tr><th class='table_head' align='center'>Add Ban</th></tr>\n");
    print("<tr><td class='table_col1' align='center'>First IP:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='first' size='40' /></td></tr>\n");
    print("<tr><td class='table_col1' align='center'>Last IP:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='last' size='40' /></td></tr>\n");
    print("<tr><td class='table_col1' align='center'>Comment: <input type='text' name='comment' size='40' /></td></tr>\n");
    print("<tr><td class='table_head' align='center'><input type='submit' value='Okay' /></td></tr>\n");
    print("</table></form><br />\n");
    Style::end();