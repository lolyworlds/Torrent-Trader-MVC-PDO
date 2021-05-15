<?php Style::begin("Delete Language");
            print("<form method='post' action='".URLROOT."/admintorrentlang/torrentlangsdelete?id=$data[id]&amp;sure=1'>\n");
            print("<center><table border='0' cellspacing='0' cellpadding='5'>\n");
            print("<tr><td align='left'><b>Language ID to move all Languages To: </b><input type='text' name='newlangid' /> (Lang ID)</td></tr>\n");
            print("<tr><td align='center'><input type='submit' value='" . Lang::T("SUBMIT") . "' /></td></tr>\n");
            print("</table></center>\n");
            print("</form>\n");
            Style::end();