<?php Style::begin("Add Language");
        print("<center><form method='post' action=" . URLROOT . "/admintorrentlang/torrentlangstakeadd>\n");
        print("<input type='hidden' name='action' value='torrentlangs' />\n");
        print("<input type='hidden' name='do' value='takeadd' />\n");
        print("<table border='0' cellspacing='0' cellpadding='5'>\n");
        print("<tr><td align='left'><b>Name:</b> <input type='text' name='name' /></td></tr>\n");
        print("<tr><td align='left'><b>Sort:</b> <input type='text' name='sort_index' /></td></tr>\n");
        print("<tr><td align='left'><b>Image:</b> <input type='text' name='image' /></td></tr>\n");
        print("<tr><td colspan='2'><input type='submit' value='" . Lang::T("SUBMIT") . "' /></td></tr>\n");
        print("</table></form><br /><br /></center>\n");
        Style::end();