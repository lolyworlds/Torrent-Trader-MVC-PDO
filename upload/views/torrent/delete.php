<?php print("<center><form method='post' action='".TTURL."/torrents/delete?id=$id'>");
                    print("<input type='hidden' name='torrentid' value='$id' />\n");
                    print("<input type='hidden' name='torrentname' value='".htmlspecialchars($row["name"])."' />\n");
                    echo "<b>".T_("REASON_FOR_DELETE")."</b><input type='text' size='30' name='delreason' />";
                    echo "&nbsp;<input type='submit' value='".T_("DELETE_TORRENT")."' /></form></center>";