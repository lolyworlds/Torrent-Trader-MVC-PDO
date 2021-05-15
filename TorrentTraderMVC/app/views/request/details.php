<?php
Style::begin("Request: $data[s]");
        print("<a href=".URLROOT."/request><button  class='btn btn-sm btn-success'>All Request</button></a>&nbsp;
        <a href=".URLROOT."/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>");
        print("<center><table width=600 border=0 cellspacing=0 cellpadding=3>\n");
        print("<tr><td align=left><B>" . Lang::T('REQUEST') . ": </B></td><td width=70% align=left>$data[request]</td></tr>");
        print("<tr><td align=left><B>Category: </B></td><td width=70% align=left>commentpcat]: $data[ncat]</td></tr>");
        if ($data["descr"]) {
            print("<tr><td align=left><B>" . Lang::T('COMMENTS') . ": </B></td><td width=70% align=left>$data[descr]</td></tr>");
        }
        print("<tr><td align=left><B>" . Lang::T('DATE_ADDED') . ": </B></td><td width=70% align=left>$data[added]</td></tr>");

        print("<tr><td align=left><B>Requested by: </B></td><td width=70% align=left>$data[username]</td></tr>");
        if ($num["filled"] == null) {
            print("<tr><td align=left><B>" . Lang::T('VOTE_FOR_THIS') . ": </B></td><td width=50% align=left><a href=".URLROOT."/request/addvote?id=$id><b>" . Lang::T('VOTES') . "</b></a></tr></tr>");
            print("<form method=get action=".URLROOT."/request/reqfilled>");
            print("<tr><td align=left><B>To Fill This Request:</B> </td><td>Enter the <b>full</b> direct URL of the torrent i.e. http://infamoustracker.org/torrents-details.php?id=134 (just copy/paste from another window/tab) or modify the existing URL to have the correct ID number</td></tr>");
            print("</table>");
            print("<input type=text size=80 name=filledurl value=TYPE-DIRECT-URL-HERE>\n");
            print("<input type=hidden value=$data[id] name=requestid>");
            print("<button  class='btn btn-sm btn-success'>Fill Request</button></form>");
            print("<p><hr></p><form method=get action=".URLROOT."/request/makereq#add>Or <button  class='btn btn-sm btn-success'>Add A New Request</button></form></center>");
        } else {
            print("<tr><td align=left><B>URL: </B></td><td width=50% align=left><a href=$data[filled] target=_new>$data[filled]</a></td></tr>");
            print("</table>");
        }
        Style::end();
        Style::begin("comments");
        if ($data['commcount']) {
            $commentbar = "<p align=center><a class=index href=".URLROOT."/request/reqcomment?action=add&amp;tid=$data[id]>Add comment</a></p>\n";
            print($commentbar);
            reqcommenttable($data['commres'], 'req');
        } else {
            $commentbar = "<p align=center><a class=index href=".URLROOT."/request/reqcomment?action=add&amp;tid=$data[id]>Add comment</a></p>\n";
            print($commentbar);
            print("<br /><b>" . Lang::T("NOCOMMENTS") . "</b><br />\n");
        }
        Style::end();
        Style::footer();