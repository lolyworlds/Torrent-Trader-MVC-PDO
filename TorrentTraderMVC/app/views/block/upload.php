<?php
        Style::begin(Lang::T("Upload A Blocks"));
        // ---- <table> for upload block -------------------------------------------------
        print("<a name=\"upload\"></a>");
        print("<hr />");
        print("<table align=\"center\" width=\"350\"><tr><td>");
        print("<form enctype=\"multipart/form-data\"  action=\"" . URLROOT . "/blocks/submit\" method=\"post\" >" .
            "<input type=\"hidden\" name=\"upload\" value=\"true\" />" .
            "<table class='table table-striped'><thead>" .
            "<tr>" .
            "<td align=\"center\"><font size=\"2\"><b>" . Lang::T("_BLC_UPL_") . "</b></font><br /></td>" .
            "</tr></thead><tbody>" .
            "</table><br />" .
            "<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">" .
            "<tr>" .
            "<td class=\"table_col1\" valign=\"middle\">" . Lang::T("_NAMED_") . "</td>" .
            "<td class=\"table_col2\" valign=\"top\"><input type=\"text\" size=\"33\" name=\"wantedname\" /><br />(" . Lang::T("_FL_NM_IF_NO_SET_") . ")</td>" .
            "</tr>" .
            "<tr>" .
            "<td class=\"table_col1\" valign=\"middle\">" . Lang::T("DESCRIPTION") . "</td>" .
            "<td class=\"table_col2\" valign=\"top\"><textarea name=\"description\" rows=\"2\" cols=\"25\"></textarea><br />(" . Lang::T("_MAX_") . " 255 " . Lang::T("_CHARS_") . ")</td>" .
            "</tr>" .
            "<tr>" .
            "<td class=\"table_col1\" valign=\"middle\">" . Lang::T("FILE") . "</td>" .
            "<td class=\"table_col2\" valign=\"top\"><input type=\"file\" name=\"blockupl\" /></td>" .
            "</tr></tbody>" .
            "</table><br />" .
            "<div id=\"pos\">" .
            "<table class=\"table_table\" cellspacing=\"0\" align=\"center\" width=\"100%\">" .
            "<tr>" .
            "<th class=\"table_head\" colspan=\"3\">" . Lang::T("_POSITION_") . "</th>" .
            "<th class=\"table_head\">" . Lang::T("_SORT_") . "</th>" .
            "<th class=\"table_head\">" . Lang::T("ENABLED") . "</th>" .
            "<th class=\"table_head\">" . Lang::T("_JUST_UPL_") . "</th>" .
            "</tr>" .
            "<tr>" .
            "<td align=\"center\" class=\"table_col2\"><input type=\"radio\" name=\"position\" checked=\"checked\" value=\"left\" onclick=\"javascript: if(enabledyes.checked){uplsort.value = '$data1[nextleft]';}else{uplsort.value = '0';} \" /></td>" .
            "<td align=\"center\" class=\"table_col2\"><input type=\"radio\" name=\"position\" value=\"middle\" onclick=\"javascript: if(enabledyes.checked){uplsort.value = '$data1[nextmiddle]';}else{uplsort.value = '0';} \" /></td>" .
            "<td align=\"center\" class=\"table_col2\"><input type=\"radio\" name=\"position\" value=\"right\" onclick=\"javascript: if(enabledyes.checked){uplsort.value = '$data1[nextright]';}else{uplsort.value = '0';} \" /></td>" .
            "<td rowspan=\"2\" align=\"center\" class=\"table_col2\"><input type=\"text\" name=\"uplsort\" size=\"1\" readonly=\"readonly\" value=\"0\" style=\"text-align: center;\" onclick=\"javascript: alert('" . Lang::T("_CLICK_POS_") . "');\" /></td>" .
            "<td rowspan=\"2\" align=\"center\" class=\"table_col2\"><input type=\"checkbox\" name=\"enabledyes\" onclick=\"javascript: uploadonly.disabled = enabledyes.checked; if(enabledyesnotice.style.display == 'block'){enabledyesnotice.style.display = 'none'}else{enabledyesnotice.style.display = 'block'}; if(!checked){uplsort.value = '0'}\"   /></td>" .
            "<td rowspan=\"2\" align=\"center\" class=\"table_col2\"><input type=\"checkbox\" name=\"uploadonly\" onclick=\"javascript: wantedname.disabled = enabledyes.disabled = description.disabled = pos.disabled = uploadonly.checked; if(uploadonlynotice.style.display == 'block'){uploadonlynotice.style.display = 'none'}else{uploadonlynotice.style.display = 'block'};\"   /></td>" .
            "</tr>" .
            "<tr>" .
            "<td align=\"center\" class=\"table_col1\">[" . Lang::T("L") . "]</td>" .
            "<td align=\"center\" class=\"table_col1\">[" . Lang::T("_M_") . "]</td>" .
            "<td align=\"center\" class=\"table_col1\">[" . Lang::T("_R_") . "]</td>" .
            "</tr>" .
            "<tr>" .
            "<td colspan=\"6\" align=\"center\" class=\"table_head\"><input type=\"submit\" class=\"btn\" value=\"" . Lang::T("UPLOAD") . "\" /><div id=\"uploadonlynotice\" style=\"display: none;\">(" . Lang::T("_UPL_ONLY_") . ")</div><div id=\"enabledyesnotice\" style=\"display: none;\">(" . Lang::T("_UPL_ADD_") . ")</div></td>" .
            "</tr>" .
            "</table>" .
            "</div>" .
            "</form>");
        print("</td></tr></table>");
        Style::end();
        // ---- </table> for upload block -------------------------------------------------