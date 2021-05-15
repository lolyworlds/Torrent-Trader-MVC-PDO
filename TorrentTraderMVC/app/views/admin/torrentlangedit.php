<?php Style::begin("Edit Language");
            print("<form method='post' action='".URLROOT."/admintorrentlang/torrentlangsedit?id=$data[id]&amp;save=1'>\n");
            while ($arr = $data['res']->fetch(PDO::FETCH_LAZY)) {
                print("<center><table border='0' cellspacing='0' cellpadding='5'>\n");
                print("<tr><td align='left'><b>Name: </b><input type='text' name='name' value=\"" . $arr['name'] . "\" /></td></tr>\n");
                print("<tr><td align='left'><b>Sort: </b><input type='text' name='sort_index' value=\"" . $arr['sort_index'] . "\" /></td></tr>\n");
                print("<tr><td align='left'><b>Image: </b><input type='text' name='image' value=\"" . $arr['image'] . "\" /> single filename</td></tr>\n");
                print("<tr><td align='center'><input type='submit' value='" . Lang::T("SUBMIT") . "' /></td></tr>\n");
                print("</table></center>\n");
            }
            print("</form>\n");
            Style::end();