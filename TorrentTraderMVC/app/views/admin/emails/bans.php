<?php
Style::begin(Lang::T("EMAILS_OR_DOMAINS_BANS"));
    print(Lang::T("EMAIL_BANS_INFO") . "<br /><br /><br /><b>" . Lang::T("ADD_EMAIL_BANS") . "</b>\n");
    print("<form method='post' action='".URLROOT."/adminemailban?add=1'>\n");
    print("<table border='0' cellspacing='0' cellpadding='5' align='center'>\n");
    print("<tr><td align='right'>" . Lang::T("EMAIL_ADDRESS") . Lang::T("DOMAIN_BANS") . "</td><td><input type='text' name='mail_domain' size='40' /></td></tr>\n");
    print("<tr><td align='right'>" . Lang::T("ADDCOMMENT") . "</td><td><input type='text' name='comment' size='40' /></td></tr>\n");
    print("<tr><td colspan='2' align='center'><input type='submit' value='" . Lang::T("ADD_BAN") . "' /></td></tr>\n");
    print("\n</table></form>\n<br />");
    print("<br /><b>" . Lang::T("EMAIL_BANS") . " ($data[count])</b>\n");
    if ($data['count'] == 0) {
        print("<p align='center'><b>" . Lang::T("NOTHING_FOUND") . "</b></p><br />\n");
    } else {
        echo $data['pagertop'];
        print("<table border='0' cellspacing='0' cellpadding='5' width='90%' align='center' class='table_table'>\n");
        print("<tr><th class='table_head'>Added</th><th class='table_head'>Mail Address Or Domain</th><th class='table_head'>Banned By</th><th class='table_head'>Comment</th><th class='table_head'>Remove</th></tr>\n");
        
        while ($arr = $data['res']->fetch(PDO::FETCH_LAZY)) {
            $r2 = DB::run("SELECT username FROM users WHERE id=$arr[userid]");
            $a2 = $r2->fetch(PDO::FETCH_ASSOC);
            $r4 = DB::run("SELECT username,id FROM users WHERE id=$arr[addedby]");
            $a4 = $r4->fetch(PDO::FETCH_ASSOC);
            print("<tr><td class='table_col1'>" . TimeDate::utc_to_tz($arr['added']) . "</td><td align='left' class='table_col2'>$arr[mail_domain]</td><td align='left' class='table_col1'><a href='" . URLROOT . "/users/profile?id=$a4[id]'>$a4[username]" . "</a></td><td align='left' class='table_col2'>$arr[comment]</td><td class='table_col1'><a href='".URLROOT."/adminemailban?remove=$arr[id]'>Remove</a></td></tr>\n");
        }
        print("</table>\n");
        echo $data['pagerbottom'];
        echo "<br />";
    }
    Style::end();