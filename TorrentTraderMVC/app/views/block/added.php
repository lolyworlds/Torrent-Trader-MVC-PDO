<?php

        
        
Style::begin(Lang::T("Uploaded Blocks"));
print("<a name=\"anb\"></a>");
print("<hr />");
//echo $success . $delmessage;
print("<table align=\"center\"><tr><td>");
print("<form name=\"addnewblock\" method=\"post\" action=\"" . URLROOT . "/blocks/submit\">" .
    "<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"650\">" .
    "<tr>" .
    "<th class=\"table_head\" align=\"center\"><font size=\"2\"><b>" . Lang::T("_BLC_AVAIL_") . "</b></font><br />(" . Lang::T("_IN_FOLDER_") . ")</th>" .
    "</tr>" .
    "</table><br />" .
    "<table width=\"650\" cellspacing=\"1\" class=\"table_table\" align=\"center\">" .
    "<tr>" .
    "<th class=\"table_head\">" . Lang::T("_NAMED_") . "<br />(" . Lang::T("_FL_NM_IF_NO_SET_") . ")</th>" .
    "<th class=\"table_head\">" . Lang::T("FILE") . "</th>" .
    "<th class=\"table_head\">" . Lang::T("DESCRIPTION") . "<br />(" . Lang::T("_MAX_") . " 255 " . Lang::T("_CHARS_") . ")</th>" .
    "<th class=\"table_head\">" . Lang::T("_ADD_") . "</th>" .
    "<th class=\"table_head\">" . Lang::T("_DEL_") . "</th>" .
    "</tr>");
/* loop over the blocks directory and take file names witch are not in database. */
if ($folder = opendir(APPROOT . '/views/blocks')) {
    $i = 0;
    while (false !== ($file = readdir($folder))) {
        if ($file != "." && $file != ".." && !in_array($file, $data['indb'])) {
            if (preg_match("/_block.php/i", $file)) {
                if (!$setclass) {
                    $class = "table_col2";
                    $setclass = true;} else {
                    $class = "table_col1";
                    $setclass = false;}
                print("<tr>" .
                    "" .
                    "<td class=\"$class\"><input type=\"hidden\" name=\"addblock_" . $i . "\" value=\"" . $file . "\" /><input type=\"text\" name=\"wantedname_" . $i . "\" value=\"" . str_replace("_block.php", "", $file) . "\"/></td>" .
                    "<td class=\"$class\">$file</td>" .
                    "<td class=\"$class\" align=\"center\"><textarea name=\"wanteddescription_" . $i . "\" rows='2' cols='20'></textarea></td>" .
                    "<td class=\"$class\" align=\"center\"><div id=\"addn_" . $i . "\" ><input type='checkbox' name='addnew[]' value=\"" . $i . "\" onclick=\"javascript: if(dltp_" . $i . ".style.display=='none'){dltp_" . $i . ".style.display='block'}else{dltp_" . $i . ".style.display='none'}; \" /></div></td>" .
                    "<td class=\"$class\" align=\"center\"><div id=\"dltp_" . $i . "\" ><input type='checkbox' name='deletepermanent[]' value=\"" . $file . "\" onclick=\"javascript: if(addn_" . $i . ".style.display=='none'){addn_" . $i . ".style.display='block'}else{addn_" . $i . ".style.display='none'}\" /></div></td>" .
                    "</tr>");
                $i++;
            }
        }
    }
    closedir($folder);
}
/* end loop over the blocks directory and take names. */

print("<tr>" .
    "<td colspan=\"5\" class=\"table_head\" align=\"center\"><input type=\"submit\" name=\"submit\" class=\"btn\" value=\"" . Lang::T("_BTN_DOIT_") . "\" />&nbsp;<input type=\"reset\" class=\"btn\" value=\"" . Lang::T("RESET") . "\" /></td>" .
    "</tr>" .
    "</table>" .
    "</form></td></tr></table>");
print("<center>(" . Lang::T("_DLT_WIL_PER_") . " <font color='#ff0000'>" . Lang::T("_NO_ADD_WAR_") . "</font>)</center><br />");
Style::end();