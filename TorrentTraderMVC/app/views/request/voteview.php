<?php
$arr2 = $data['res2']->fetch(PDO::FETCH_ASSOC);
        Style::begin("" . Lang::T('VOTES') . ": <a href=".URLROOT."/request/reqdetails?id=$data[requestid]>$arr2[request]</a>");
        print("<a href=".URLROOT."/request><button  class='btn btn-sm btn-success'>All Request</button></a>&nbsp;
        <a href=".URLROOT."/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>");
        print("<p><center><a href=".URLROOT."/request/addvote?id=$data[requestid]><b>" . Lang::T('VOTE_FOR_THIS') . " " . Lang::T('REQUEST') . "</b></a></p>");

            print("<center><div class='table-responsive'> <table class='table table-striped' width='60%'><thead><tr>");
            print("<th>" . Lang::T('USERNAME') . "</th><th>" . Lang::T('UPLOADED') . "</td>
                   <th>" . Lang::T('DOWNLOADED') . "</th><th>" . Lang::T('RATIO') . "</th></tr></thead>");
            while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
                if ($arr["downloaded"] > 0) {
                    $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
                    $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
                } else
                if ($arr["uploaded"] > 0) {
                    $ratio = "Inf.";
                } else {
                    $ratio = "---";
                }
                $uploaded = mksize($arr["uploaded"]);
                $joindate = "$arr[added] (" . TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr["added"])) . " ago)";
                $downloaded = mksize($arr["downloaded"]);
                if ($arr["enabled"] == 'no') {
                    $enabled = "<font color = red>No</font>";
                } else {
                    $enabled = "<font color = green>Yes</font>";
                }
                print("<tr><td class=table_col1><a href=".URLROOT."/profile?id=$arr[userid]><b>$arr[username]</b></a></td><td align=left class=table_col2>$uploaded</td><td align=left class=table_col1>$downloaded</td><td align=left class=table_col2>$ratio</td></tr>\n");
            }
            print("</table></center><BR><BR>\n");
            Style::end();