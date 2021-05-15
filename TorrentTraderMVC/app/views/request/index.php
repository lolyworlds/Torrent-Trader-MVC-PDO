            <?php
            Style::header("Requests");
            Style::begin(Lang::T('REQUESTS'));
            print("<a href=".URLROOT."/request/makereq><button  class='btn btn-sm btn-success'>Add New Request</button></a>&nbsp;
                   <a href=".URLROOT."/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>&nbsp;
                   <a href=".URLROOT."/request><button  class='btn btn-sm btn-success'>All requests</button>");
            print("<br><br><CENTER><form method=get action=".URLROOT."/request>");
            print(Lang::T('SEARCH') . ": <input type=text size=30 name=search>");
            print("<input type=submit align=center value=" . Lang::T('SEARCH') . " style='height: 22px'>\n");
            print("</form></CENTER><br>");

            echo $data['pagertop'];
            echo "<Table border=0 width=100% cellspacing=0 cellpadding=0><TR><TD width=50% align=left valign=bottom>";
            print("<p>" . Lang::T('SORT_BY') . " <a href=" . URLROOT . "/request?category=" . $_GET['category'] . "&filter=" . $_GET['filter'] . "&sort=votes>" . Lang::T('VOTES') . "</a>,
                 <a href=" . URLROOT . "/request?category=" . $_GET['category'] . "&filter=" . $_GET['filter'] . "&sort=request>Request Name</a>, or
                 <a href=" . URLROOT . "/request?category=" . $_GET['category'] . "&filter=" . $_GET['filter'] . "&sort=added>" . Lang::T('DATE_ADDED') . "</a>.</p>");
            print("<form method=get action=".URLROOT."/request>");
            ?>
             </td><td width=100% align=right valign=bottom>
             <select name="category">
              <option value="0"><?php print("" . Lang::T('SHOW_ALL') . "\n");?></option>
            <?php
            $cats = genrelist();
            $catdropdown = "";
            foreach ($cats as $cat) {
                $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
                $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
            }
            ?>
               <?php $catdropdown?>
               </select>
            <?php
            print("<input type=submit align=center value=" . Lang::T('DISPLAY') . " style='height: 22px'>\n");
            print("</form></td></tr></table>");
            print("<form method=post action=".URLROOT."/request/takedelreq>");
            print("<div class='table-responsive'> <table class='table table-striped'><thead><tr>");
            print("<th>" . Lang::T('REQUESTS') . "</th>
                   <th>" . Lang::T('TYPE') . "</th>
                   <th>" . Lang::T('DATE_ADDED') . "</th>
                   <th>" . Lang::T('ADDED_BY') . "</th>
                   <th>" . Lang::T('FILLED') . "</th>
                   <th>" . Lang::T('FILLED_BY') . "</th>
                   <th>" . Lang::T('VOTES') . "</th>
                   <th>Comm</th>
                   <th>" . Lang::T('DEL') . "</th></tr></thead>");


                   for ($i = 0; $i < $data['num']; ++$i) {
                       $arr = $data['res']->fetch(PDO::FETCH_ASSOC);
                       $privacylevel = $arr["privacy"];
                       if ($arr["downloaded"] > 0) {
                           $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
                           $ratio = "<font color=" . get_ratio_color($ratio) . "><b>$ratio</b></font>";
                       } elseif ($arr["uploaded"] > 0) {
                           $ratio = "Inf.";
                       } else {
                           $ratio = "---";
                       }
                       $res2 = DB::run("SELECT username from users where id=" . $arr['filledby']);
                       $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
                       if ($arr2['username']) {
                           $filledby = $arr2['username'];
                       } else {
                           $filledby = " ";
                       }
    
                       if ($privacylevel == "strong") {
                           if ($_SESSION["class"] >= 5) {
                               $addedby = "<td class=table_col2 align=center><a href=".URLROOT."/profile?id=$arr[userid]><b>$arr[username] ($ratio)</b></a></td>";
                           } else {
                               $addedby = "<td class=table_col2 align=center><a href=".URLROOT."/profile?id=$arr[userid]><b>$arr[username] (----)</b></a></td>";
                           }
                       } else {
                           $addedby = "<td class=table_col2 align=center><a href=".URLROOT."/profile?id=$arr[userid]><b>$arr[username] ($ratio)</b></a></td>";
                       }
                       $filled = $arr['filled'];
                       if ($filled) {
                           $filled = "<a href=$filled><font color=green><b>Yes</b></font></a>";
                           $filledbydata = "<a href=".URLROOT."/profile?id=$arr[filledby]><b>$arr2[username]</b></a>";
                       } else {
                           $filled = "<a href=".URLROOT."/request/reqdetails?id=$arr[id]><font color=red><b>No</b></font></a>";
                           $filledbydata = "<i>nobody</i>";
                       }
                       print("<tr><td class=table_col1 align=left><a href=".URLROOT."/request/reqdetails?id=$arr[id]><b>$arr[request]</b></a></td>" .
                        "<td class=table_col2 align=center>$arr[parent_cat]: $arr[cat]</td><td align=center
                          class=table_col1>$arr[added]</td>$addedby<td
                          class=table_col2>$filled</td>
                          <td class=table_col1>$filledbydata</td>
                          <td class=table_col2><a href=".URLROOT."/request/votesview?requestid=$arr[id]><b>$arr[hits]</b></a></td>
                          <td class=table_col1 align=center><a href=".URLROOT."/request/reqdetails?id=$arr[id]><b>" . $arr["comments"] . "");
                       if ($_SESSION['id'] == $arr['userid'] || $_SESSION["class"] > 5) {
                           print("<td class=table_col1><input type=\"checkbox\" name=\"delreq[]\" value=\"" . $arr['id'] . "\" />&nbsp;<a href='".URLROOT."/request/takereqedit?id=$arr[id]'><img src='".URLROOT."/assets/images/requests/edit.png' title=" . Lang::T("EDIT") . " alt=" . Lang::T("EDIT") . "></a></td>");
                       } else {
                           print("<td class=table_col1>&nbsp;</td>");
                       }


                       print("</tr>\n");
                   }
                       print("</table></div>");
                       print("<p align=right><input type=submit value=" . Lang::T('DO_DELETE') . "></p>");
                       print("</form>");
                       echo $data['pagerbottom'];
                       Style::end();
                       Style::footer();
                   