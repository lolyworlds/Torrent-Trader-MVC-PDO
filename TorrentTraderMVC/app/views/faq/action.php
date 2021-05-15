<?php
Style::begin();

print("<h1 align=\"center\">Edit Section or Item</h1>");


while ($arr = $data['res']->fetch(PDO::FETCH_BOTH)) {
    $arr['question'] = stripslashes(htmlspecialchars($arr['question']));
    $arr['answer'] = stripslashes(htmlspecialchars($arr['answer']));
    if ($arr['type'] == "item") {
        print("<form method=\"post\" action=\"" . URLROOT . "/faq/actions?action=edititem\">");
        print("<table border=\"0\" class=\"table_table\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
        print("<tr><td class='table_col1'>ID:</td><td class='table_col1'>$arr[id] <input type=\"hidden\" name=\"id\" value=\"$arr[id]\" /></td></tr>\n");
        print("<tr><td class='table_col2'>Question:</td><td class='table_col2'><input style=\"width: 300px;\" type=\"text\" name=\"question\" value=\"$arr[question]\" /></td></tr>\n");
        print("<tr><td class='table_col1' style=\"vertical-align: top;\">Answer:</td><td class='table_col1'><textarea rows='3' cols='35' name=\"answer\">$arr[answer]</textarea></td></tr>\n");
        if ($arr['flag'] == "0") {
            print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\" selected=\"selected\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
        } elseif ($arr['flag'] == "2") {
            print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\" selected=\"selected\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
        } elseif ($arr['flag'] == "3") {
            print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\" selected=\"selected\">New</option></select></td></tr>");
        } else {
            print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
        }
        print("<tr><td class='table_col1'>Category:</td><td class='table_col1'><select style=\"width: 300px;\" name=\"categ\">");
        
        while ($arr2 = $data['res2']->fetch(PDO::FETCH_BOTH)) {
            $selected = ($arr2['id'] == $arr['categ']) ? " selected=\"selected\"" : "";
            print("<option value=\"$arr2[id]\"" . $selected . ">$arr2[question]</option>");
        }
        print("</select></td></tr>\n");
        print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Edit\" style=\"width: 60px;\" /></td></tr>\n");
        print("</table></form>");
    } elseif ($arr['type'] == "categ") {
        print("<form method=\"post\" action=\"" . URLROOT . "/faq/actions?action=editsect\">");
        print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
        print("<tr><td class='table_col1'>ID:</td><td class='table_col1'>$arr[id] <input type=\"hidden\" name=\"id\" value=\"$arr[id]\" /></td></tr>\n");
        print("<tr><td class='table_col2'>Title:</td><td class='table_col2'><input style=\"width: 300px;\" type=\"text\" name=\"title\" value=\"$arr[question]\" /></td></tr>\n");
        if ($arr['flag'] == "0") {
            print("<tr><td class='table_col1'>Status:</td><td class='table_col1'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\" selected=\"selected\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option></select></td></tr>");
        } else {
            print("<tr><td class='table_col1'>Status:</td><td class='table_col1'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option></select></td></tr>");
        }
        print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Edit\" style=\"width: 60px;\" /></td></tr>\n");
        print("</table></form>");
    }
}

Style::end();