<?php
class Request extends Controller
{

    public function index()
    {
        dbconn(true);
        global $config;
        if ($config["MEMBERSONLY"]) {
            loggedinonly();
            if ($_SESSION["view_torrents"] == "no") {
                show_error_msg("Error!", "You do not have the proper rights to view requests!", 1);
            }
        }
        if ($config["REQUESTSON"]) {
            stdhead("Requests");
            begin_frame(T_('REQUESTS'));
            print("<a href=$config[SITEURL]/request/makereq><button  class='btn btn-sm btn-success'>Add New Request</button></a>&nbsp;
                   <a href=$config[SITEURL]/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>&nbsp;
                   <a href=$config[SITEURL]/request><button  class='btn btn-sm btn-success'>All requests</button>");
            $categ = (int) $_GET["category"];
            $requestorid = (int) $_GET["requestorid"];
            $sort = $_GET["sort"];
            $search = $_GET["search"];
            $filter = $_GET["filter"];
            $search = " AND requests.request like '%$search%' ";
            if ($sort == "votes") {
                $sort = " order by hits desc ";
            } else if ($sort == "request") {
                $sort = " order by request ";
            } else {
                $sort = " order by filled asc ";
            }
            if ($filter == "true") {
                $filter = " AND requests.filledby = 0 ";
            } else {
                $filter = "";
            }
            if ($requestorid != null) {
                if (($categ != null) && ($categ != 0)) {
                    $categ = "WHERE requests.cat = " . $categ . " AND requests.userid = " . $requestorid;
                } else {
                    $categ = "WHERE requests.userid = " . $requestorid;
                }
            } else if ($categ == 0) {
                $categ = '';
            } else {
                $categ = "WHERE requests.cat = " . $categ;
            }
            $res = DB::run("SELECT count(requests.id) FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ $filter $search");
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $count = $row[0];
            $perpage = 50;
            list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $config['SITEURL'] . "/request?" . "category=" . $_GET["category"] . "&sort=" . $_GET["sort"] . "&");
            $res = DB::run("SELECT users.downloaded, users.uploaded, users.username, users.privacy, requests.filled, requests.comments,
            requests.filledby, requests.id, requests.userid, requests.request, requests.added, requests.hits, categories.name as cat,
             categories.parent_cat as parent_cat
             FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ
              $filter $search $sort $limit");
            $num = $res->rowCount();
            print("<br><br><CENTER><form method=get action=$config[SITEURL]/request>");
            print(T_('SEARCH') . ": <input type=text size=30 name=search>");
            print("<input type=submit align=center value=" . T_('SEARCH') . " style='height: 22px'>\n");
            print("</form></CENTER><br>");
            echo $pagertop;
            echo "<Table border=0 width=100% cellspacing=0 cellpadding=0><TR><TD width=50% align=left valign=bottom>";
            print("<p>" . T_('SORT_BY') . " <a href=" . $config['SITEURL'] . "/request?category=" . $_GET['category'] . "&filter=" . $_GET['filter'] . "&sort=votes>" . T_('VOTES') . "</a>,
                 <a href=" . $config['SITEURL'] . "/request?category=" . $_GET['category'] . "&filter=" . $_GET['filter'] . "&sort=request>Request Name</a>, or
                 <a href=" . $config['SITEURL'] . "/request?category=" . $_GET['category'] . "&filter=" . $_GET['filter'] . "&sort=added>" . T_('DATE_ADDED') . "</a>.</p>");
            print("<form method=get action=$config[SITEURL]/request>");
            ?>
             </td><td width=100% align=right valign=bottom>
             <select name="category">
              <option value="0"><?php print("" . T_('SHOW_ALL') . "\n");?></option>
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
            print("<input type=submit align=center value=" . T_('DISPLAY') . " style='height: 22px'>\n");
            print("</form></td></tr></table>");
            print("<form method=post action=$config[SITEURL]/request/takedelreq>");
            print("<div class='table-responsive'> <table class='table table-striped'><thead><tr>");
            print("<th>" . T_('REQUESTS') . "</th>
                   <th>" . T_('TYPE') . "</th>
                   <th>" . T_('DATE_ADDED') . "</th>
                   <th>" . T_('ADDED_BY') . "</th>
                   <th>" . T_('FILLED') . "</th>
                   <th>" . T_('FILLED_BY') . "</th>
                   <th>" . T_('VOTES') . "</th>
                   <th>Comm</th>
                   <th>" . T_('DEL') . "</th></tr></thead>");
            for ($i = 0; $i < $num; ++$i) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $privacylevel = $arr["privacy"];
                if ($arr["downloaded"] > 0) {
                    $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
                    $ratio = "<font color=" . get_ratio_color($ratio) . "><b>$ratio</b></font>";
                } else if ($arr["uploaded"] > 0) {
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
                    if (get_user_class() >= 5) {
                        $addedby = "<td class=table_col2 align=center><a href=$config[SITEURL]/users/profile?id=$arr[userid]><b>$arr[username] ($ratio)</b></a></td>";
                    } else {
                        $addedby = "<td class=table_col2 align=center><a href=$config[SITEURL]/users/profile?id=$arr[userid]><b>$arr[username] (----)</b></a></td>";
                    }
                } else {
                    $addedby = "<td class=table_col2 align=center><a href=$config[SITEURL]/users/profile?id=$arr[userid]><b>$arr[username] ($ratio)</b></a></td>";
                }
                $filled = $arr['filled'];
                if ($filled) {
                    $filled = "<a href=$filled><font color=green><b>Yes</b></font></a>";
                    $filledbydata = "<a href=$config[SITEURL]/users/profile?id=$arr[filledby]><b>$arr2[username]</b></a>";
                } else {
                    $filled = "<a href=$config[SITEURL]/request/reqdetails?id=$arr[id]><font color=red><b>No</b></font></a>";
                    $filledbydata = "<i>nobody</i>";
                }
                print("<tr><td class=table_col1 align=left><a href=$config[SITEURL]/request/reqdetails?id=$arr[id]><b>$arr[request]</b></a></td>" .
                    "<td class=table_col2 align=center>$arr[parent_cat]: $arr[cat]</td><td align=center
                      class=table_col1>$arr[added]</td>$addedby<td
                      class=table_col2>$filled</td>
                      <td class=table_col1>$filledbydata</td>
                      <td class=table_col2><a href=$config[SITEURL]/request/votesview?requestid=$arr[id]><b>$arr[hits]</b></a></td>
                      <td class=table_col1 align=center><a href=$config[SITEURL]/request/reqdetails?id=$arr[id]><b>" . $arr["comments"] . "");
                if ($_SESSION['id'] == $arr['userid'] || get_user_class() > 5) {
                    print("<td class=table_col1><input type=\"checkbox\" name=\"delreq[]\" value=\"" . $arr['id'] . "\" />&nbsp;<a href='$config[SITEURL]/request/takereqedit?id=$arr[id]'><img src='$config[SITEURL]/images/requests/edit.png' title=" . T_("EDIT") . " alt=" . T_("EDIT") . "></a></td>");
                } else {
                    print("<td class=table_col1>&nbsp;</td>");
                }
                print("</tr>\n");
            }
            print("</table></div>");
            print("<p align=right><input type=submit value=" . T_('DO_DELETE') . "></p>");
            print("</form>");
            echo $pagerbottom;

            end_frame();
            stdfoot();
        } else {
            autolink(TTURL . "/index.php", "Request are not available");
            stdhead();
            show_error_msg(T_("Request Off"), "Request are not available", 0);
            stdfoot();
            die;
        }
    }

    public function makereq()
    {
        dbconn();
        global $config;
        loggedinonly();
        stdhead("Requests Page");
        begin_frame("" . T_('MAKE_REQUEST') . "");
        print("<br>\n");
        if ($config["REQUESTSON"]) {
            print("<a href=$config[SITEURL]/request><button  class='btn btn-sm btn-success'>All Request</button></a>&nbsp;
                   <a href=$config[SITEURL]/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>");
            $where = "WHERE userid = " . $_SESSION["id"] . "";
            $res2 = DB::run("SELECT * FROM requests $where");
            $num2 = $res2->rowCount();
            ?>
            <center><big><b><font color=red>If this is abused, it will be for VIP only!</font></b></big>
            <table border=0 width=100% cellspacing=0 cellpadding=3>
            <tr><td><li><b>Before posting a request, please make sure to search the site first to make sure it's not already posted.</li>
            <li><u>1 request per day per member</u>. Any more than that will be deleted by a moderator.</li></b>
            </td></tr>
            <tr><td class=colhead align=left><?php print("" . T_('SEARCH') . " " . T_('TORRENT') . "");?></td></tr>
            <tr><td align=left><form method="get" action=$config[SITEURL]/torrents/search>
            <input type="text" name="<?php print("" . T_('SEARCH') . "\n");?>" size="40" value="<?php htmlspecialchars($searchstr)?>" />
            in
            <select name="cat">
            <option value="0">(all types)</option>
            <?php
            $cats = genrelist();
            $catdropdown = "";
            foreach ($cats as $cat) {
                $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
                if ($cat["id"] == (int) $_GET["cat"]) {
                    $catdropdown .= " selected=\"selected\"";
                }
                $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
            }
            $deadchkbox = "<input type=\"checkbox\" name=\"incldead\" value=\"1\"";
            if ($_GET["incldead"]) {
                $deadchkbox .= " checked=\"checked\"";
            }
            $deadchkbox .= " /> " . T_('INC_DEAD') . "\n";
            $catdropdown?>
            </select>
            <?php $deadchkbox?>
            <input type="submit" value="<?php print("" . T_('SEARCH') . "\n");?>"  />
            </form>
            </td></tr></table>
            <li><b>When possible, please provide a full scene release name.<br>
            You can check out <a href=pre.php>our pre database</a> (nobles+only) <br>
            or use public sites such as NFOrce or VCDQ for help with that.</b></li>
            <?php
            print("<form method=post action=$config[SITEURL]/request/confirmreq><a name=add id=add></a>\n");
            print("<CENTER><table border=0 width=600 cellspacing=0 cellpadding=3>\n");
            print("<tr><td class=colhead align=center><B>" . T_('MAKE_REQUEST') . "</B></a></td><tr>\n");
            print("<tr><td align=center><b>Title: </b><input type=text size=40 name=requesttitle>");
            ?>
            <select name="cat">
            <option value="0"><?php echo "(" . T_("ALL") . " " . T_("TYPES") . ")"; ?></option>
            <?php
            $cats = genrelist();
            $catdropdown = "";
            foreach ($cats as $cat) {
                $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
                if ($cat["id"] == $_GET["cat"]) {
                    $catdropdown .= " selected=\"selected\"";
                }
                $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
            }
            echo $catdropdown?>
            </select>
            <?php
            print("<tr><td align=center>Additional Information <b>(Optional - but be generous!</b>)<br><textarea name=descr rows=7
            cols=60></textarea>\n");
            print("<tr><td align=center><button  class='btn btn-sm btn-success'>" . T_('SUBMIT') . "</button>\n");
            print("</form>\n");
            print("</table></CENTER>\n");
        } else {
            echo "<b><font color=red>Sorry, requests are currently disabled.";
        }
        end_frame();
        stdfoot();
    }

    public function addvote()
    {
        dbconn();
        global $config;
        stdhead("Vote");
        begin_frame("" . T_('VOTES') . "");
        $requestid = (int) $_GET["id"];
        $userid = (int) $_SESSION["id"];
        $res = DB::run("SELECT * FROM addedrequests WHERE requestid=$requestid and userid = $userid");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $voted = $arr;
        if ($voted) {
            ?>
        <p>You've already voted for this request, only 1 vote for each request is allowed</p><p>Back to <a href=$config[SITEURL]/request><b>requests</b></a></p>
        <?php
        } else {
            DB::run("UPDATE requests SET hits = hits + 1 WHERE id=$requestid");
            DB::run("INSERT INTO addedrequests VALUES(0, $requestid, $userid)");
            print("<p>Successfully voted for request $requestid</p><p>Back to <a href=$config[SITEURL]/request><b>requests</b></a></p>");
        }
        end_frame();
        stdfoot();
    }

    public function votesview()
    {
        dbconn(false);
        global $config;
        loggedinonly();
        $requestid = (int) $_GET['requestid'];
        $res2 = DB::run("select count(addedrequests.id) from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid");
        $row = $res2->fetch(PDO::FETCH_ASSOC);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?");
        $res = DB::run("select users.id as userid,users.username, users.downloaded,users.uploaded, requests.id as requestid, requests.request from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid $limit");
        stdhead("Votes");
        $res2 = DB::run("select request from requests where id=$requestid");
        $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
        begin_frame("" . T_('VOTES') . ": <a href=$config[SITEURL]/request/reqdetails?id=$requestid>$arr2[request]</a>");
        print("<a href=$config[SITEURL]/request><button  class='btn btn-sm btn-success'>All Request</button></a>&nbsp;
        <a href=$config[SITEURL]/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>");
        print("<p><center><a href=$config[SITEURL]/request/addvote?id=$requestid><b>" . T_('VOTE_FOR_THIS') . " " . T_('REQUEST') . "</b></a></p>");
        if ($res->rowCount() == 0) {
            print("<p align=center><b>" . T_('NOTHING_FOUND') . "</b></p>\n");
        } else {
            print("<center><div class='table-responsive'> <table class='table table-striped' width='60%'><thead><tr>");
            print("<th>" . T_('USERNAME') . "</th><th>" . T_('UPLOADED') . "</td>
                   <th>" . T_('DOWNLOADED') . "</th><th>" . T_('RATIO') . "</th></tr></thead>");
            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
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
                $joindate = "$arr[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"])) . " ago)";
                $downloaded = mksize($arr["downloaded"]);
                if ($arr["enabled"] == 'no') {
                    $enabled = "<font color = red>No</font>";
                } else {
                    $enabled = "<font color = green>Yes</font>";
                }
                print("<tr><td class=table_col1><a href=$config[SITEURL]/users/profile?id=$arr[userid]><b>$arr[username]</b></a></td><td align=left class=table_col2>$uploaded</td><td align=left class=table_col1>$downloaded</td><td align=left class=table_col2>$ratio</td></tr>\n");
            }
            print("</table></center><BR><BR>\n");
        }
        end_frame();
        stdfoot();
    }

    public function confirmreq()
    {
        dbconn(true);
        global $config;
        $requesttitle = $_POST["requesttitle"];
        if (!$requesttitle) {
            show_error_msg("Error!", "You must enter a request!", 1);
        }
        $cat = $_POST["cat"];
        if ($cat == 0) {
            show_error_msg("Error!", "Category cannot be empty!", 1);
        }
        $descr = $_POST["descr"];
        DB::run("INSERT INTO requests (hits, userid, cat, request, descr, added) VALUES(?,?,?,?,?,?)", [1, $_SESSION['id'], $cat, $requesttitle, $descr, get_date_time()]);
        $id = DB::lastInsertId();
        DB::run("INSERT INTO addedrequests (requestid,userid) VALUES($id, $_SESSION[id])");
        DB::run("INSERT INTO shoutbox (user,message,date,userid) VALUES('System', '$_SESSION[username] has made a request for [url=" . $config['SITEURL'] . "/request/reqdetails?id=" . $id . "]" . $requesttitle . "[/url]', now(), '0')");
        write_log("$requesttitle was added to the Request section");
        header("Refresh: 0; url=$config[SITEURL]/request");
    }

    public function takereqedit()
    {
        dbconn(true);
        global $config;
        if (get_user_class() < 5) {
            show_error_msg("Error", "Access denied.", 1);
        }
        $id = (int) $_GET["id"];
        if (!is_valid_id($id)) {
            show_error_msg("Error", "You must select a category to put the request in!");
        }
        $descr = $_POST["desc"];
        $cat = $_POST["cat"];
        $filled = $_POST["filled"];
        $request = $_POST["request"];
        $filledby = $_POST["filledby"];
        if (!empty($_POST)) {
            if (!$filled) {
                DB::run("UPDATE requests SET cat=?, request=?, descr=?, filled =?, filled=? WHERE id = ?", [$cat, $request, $descr, 'yes', $filled, $id]);
            } else {
                DB::run("UPDATE requests SET cat=?, filledby =?, request=?, descr=?, filled =?  WHERE id =? ", [$cat, 0, $request, $descr, 'no', $id]);
            }
            header("Refresh: 0; url=$config[SITEURL]/request/reqdetails?id=$id");
        }
        $res = DB::run("SELECT * FROM requests WHERE id =$id");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        stdhead("Edit Request");
        begin_frame("Edit Request");
        print("<a href=$config[SITEURL]/request><button  class='btn btn-sm btn-success'>All Request</button></a>&nbsp;
        <a href=$config[SITEURL]/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>");
        ?>
        <div><center>
        <form name="form" action="takereqedit&id=<?php echo $arr['id']; ?>" method="post">
        <input type="hidden" name="filledby" value="<?php echo $arr['filledby']; ?>" />
        <label for="cat">Change Cat id</label>
        <input type="text" name="cat" value="<?php echo $arr['id']; ?>" id="cat"><br>
        <label for="request">Request Tilte</label>
        <input type="text" name="request" value="<?php echo $arr['request']; ?>" id="request"><br>
        <label for="descr">Description</label>
        <input type="text" name="descr" value="<?php echo $arr['descr']; ?>" id="descr"><br>
        <label for="filled">Url To Torrent</label>
        <input type="text" name="filled" value="<?php echo $arr['filled']; ?>" id="filled"><br>
        <input type="submit" value="Update">
        </form>
        </center></div>
        <?php
        end_frame();
        stdfoot();
    }

    public function takedelreq()
    {
        dbconn(true);
        global $config;
        stdhead("Delete");
        begin_frame("Delete");
        if (get_user_class($_SESSION) > 5) {
            if (empty($_POST["delreq"])) {
                print("<CENTER>You must select at least one request to delete.</CENTER>");
                end_frame();
                stdfoot();
                die;
            }
            $do = "DELETE FROM requests WHERE id IN (" . implode(", ", $_POST['delreq']) . ")";
            $do2 = "DELETE FROM addedrequests WHERE requestid IN (" . implode(", ", $_POST['delreq']) . ")";
            $res2 = DB::run($do2);
            $res = DB::run($do);
            print("<CENTER>Request Deleted OK</CENTER>");
            echo "<BR><BR>";
        } else {
            foreach ($_POST['delreq'] as $del_req) {
                $query = DB::run("SELECT * FROM requests WHERE userid=$_SESSION[id] AND id = $del_req");
                $num = $query->rowCount();
                if ($num > 0) {
                    $res2 = DB::run("DELETE FROM requests WHERE id IN ($del_req)");
                    $res = DB::run("DELETE FROM addedrequests WHERE requestid IN ($del_req)");
                    print("<CENTER>Request ID $del_req Deleted</CENTER>");
                } else {
                    print("<CENTER>No Permission to delete Request ID $del_req</CENTER>");
                }
            }
        }
        end_frame();
        stdfoot();
    }

    public function reqreset()
    {
        dbconn();
        global $config;
        loggedinonly();
        stdhead("Reset Request");
        begin_frame("Reset");
        $requestid = (int) $_GET["requestid"];
        $res = DB::run("SELECT userid, filledby FROM requests WHERE id =$requestid");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if (($_SESSION['id'] == $arr['userid']) || (get_user_class() >= 4) || ($_SESSION['id'] == $arr['filledby'])) {
            DB::run("UPDATE requests SET filled='', filledby=0 WHERE id =$requestid");
            print("Request $requestid successfully reset.");
        } else {
            print("Sorry, cannot reset a request when you are not the owner");
        }
        end_frame();
        stdfoot();
    }

    public function reqfilled()
    {
        dbconn();
        global $config;
        loggedinonly();
        stdhead("Fill Request");
        begin_frame("Request Filled");
        $filledurl = $_GET["filledurl"];
        $requestid = (int) $_GET["requestid"];
        $res = DB::run("SELECT users.username, requests.userid, requests.request FROM requests inner join users on requests.userid = users.id where requests.id = $requestid");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $res2 = DB::run("SELECT username FROM users where id =" . $_SESSION['id']);
        $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
        $msg = "Your request $requestid ";
        $msg2 = "Your Request Filled";
        DB::run("UPDATE requests SET filled = '$filledurl', filledby = $_SESSION[id] WHERE id = $requestid");
        DB::run("INSERT INTO messages (poster, sender, receiver, added, subject, msg) VALUES (?,?,?,?,?,?)", [0, 0, $arr['userid'], get_date_time(), $msg2, $msg]);
        print("<div align=left>Request $requestid was successfully filled with <a href=$filledurl>$filledurl</a>.  User <a href=$config[SITEURL]/users/profile?id=$arr[userid]><b>$arr[username]</b></a> automatically PMd.  <br>Filled that accidently? No worries, <a href=$config[SITEURL]/request/reqreset?requestid=$requestid>CLICK HERE</a> to mark the request as unfilled.  Do <b>NOT</b> follow this link unless you are sure there is a problem.<br></div>");
        print("Thank you for filling a request :)<br><a href=$config[SITEURL]/request>View More Requests</a>");
        end_frame();
        stdfoot();
    }

    public function reqdetails()
    {
        dbconn();
        global $config;
        loggedinonly();
        stdhead("Request Details");
        $id = (int) $_GET["id"];
        $res = DB::run("SELECT * FROM requests WHERE id = $id");
        if ($res->rowCount() != 1) {
            show_error_msg("ID NOT FOUND", "That request doesn't exist.", 1);
        }
        $num = $res->fetch(PDO::FETCH_ASSOC);
        $s = $num["request"];
        $filled = $num["filled"];
        $catid = $num["cat"];
        $catn = DB::run("SELECT parent_cat,name FROM categories WHERE id='$catid' ");
        $catname = $catn->fetch(PDO::FETCH_ASSOC);
        $pcat = $catname["parent_cat"];
        $ncat = $catname["name"];
        begin_frame("Request: $s");
        print("<a href=$config[SITEURL]/request><button  class='btn btn-sm btn-success'>All Request</button></a>&nbsp;
        <a href=$config[SITEURL]/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>");
        print("<center><table width=600 border=0 cellspacing=0 cellpadding=3>\n");
        print("<tr><td align=left><B>" . T_('REQUEST') . ": </B></td><td width=70% align=left>$num[request]</td></tr>");
        print("<tr><td align=left><B>Category: </B></td><td width=70% align=left>$pcat: $ncat</td></tr>");
        if ($num["descr"]) {
            print("<tr><td align=left><B>" . T_('COMMENTS') . ": </B></td><td width=70% align=left>$num[descr]</td></tr>");
        }
        print("<tr><td align=left><B>" . T_('DATE_ADDED') . ": </B></td><td width=70% align=left>$num[added]</td></tr>");
        $cres = DB::run("SELECT username FROM users WHERE id=$num[userid]");
        if ($cres->rowCount() == 1) {
            $carr = $cres->fetch(PDO::FETCH_ASSOC);
            $username = "$carr[username]";
            $comment = "$carr[descr]";
        }
        print("<tr><td align=left><B>Requested by: </B></td><td width=70% align=left>$username</td></tr>");
        if ($num["filled"] == null) {
            print("<tr><td align=left><B>" . T_('VOTE_FOR_THIS') . ": </B></td><td width=50% align=left><a href=$config[SITEURL]/request/addvote?id=$id><b>" . T_('VOTES') . "</b></a></tr></tr>");
            print("<form method=get action=$config[SITEURL]/request/reqfilled>");
            print("<tr><td align=left><B>To Fill This Request:</B> </td><td>Enter the <b>full</b> direct URL of the torrent i.e. http://infamoustracker.org/torrents-details.php?id=134 (just copy/paste from another window/tab) or modify the existing URL to have the correct ID number</td></tr>");
            print("</table>");
            print("<input type=text size=80 name=filledurl value=TYPE-DIRECT-URL-HERE>\n");
            print("<input type=hidden value=$id name=requestid>");
            print("<button  class='btn btn-sm btn-success'>Fill Request</button></form>");
            print("<p><hr></p><form method=get action=$config[SITEURL]/request/makereq#add>Or <button  class='btn btn-sm btn-success'>Add A New Request</button></form></center>");
        } else {
            print("<tr><td align=left><B>URL: </B></td><td width=50% align=left><a href=$filled target=_new>$filled</a></td></tr>");
            print("</table>");
        }
        end_frame();
        begin_frame("comments");
        $commcount = DB::run("SELECT COUNT(*) FROM comments WHERE req = $id")->fetchColumn();
        $commentbar = "<p align=center><a class=index href=$config[SITEURL]/request/reqcomment?action=add&amp;tid=$id>Add comment</a></p>\n";
        if ($commcount) {
            //list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "comments/req?id=$id&amp;");
            $commquery = "SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, username, title, class, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE req = $id ORDER BY comments.id";
            $commres = DB::run($commquery);
        } else {
            unset($commres);
        }
        if ($commcount) {
            //print($pagertop);
            print($commentbar);
            reqcommenttable($commres, 'req');
            //print($pagerbottom);
        } else {
            print($commentbar);
            print("<br /><b>" . T_("NOCOMMENTS") . "</b><br />\n");
        }
        end_frame();
        stdfoot();
    }

    public function reqcomment()
    {
        $action = $_GET["action"];
        dbconn(false);
        global $config;
        loggedinonly();
        require_once "helpers/bbcode_helper.php";
        if ($action == "add") {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $reqid = 0 + $_POST["tid"];
                if (!is_valid_id($reqid)) {
                    show_error_msg("Error", "Wrong ID $reqid.", 1);
                }
                $res = DB::run("SELECT request FROM requests WHERE id = $reqid");
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                if (!$arr) {
                    show_error_msg("Error", "No request with ID $reqid.", 1);
                }
                $text = trim($_POST["msg"]);
                if (!$text) {
                    show_error_msg("Error", "Don't leave any fields blank!", 1);
                }
                DB::run("INSERT INTO comments (user, req, added, text, ori_text) VALUES (" . $_SESSION["id"] . ",$reqid, '" . get_date_time() . "', " . sqlesc($text) . "," . sqlesc($text) . ")");
                $reqid = DB::lastInsertId();
                DB::run("UPDATE requests SET comments = comments + 1 WHERE id = $reqid");
                header("Refresh: 0; url=$config[SITEURL]/request/reqdetails?id=$reqid");
                exit();
            }
            $reqid = 0 + $_GET["tid"];
            if (!is_valid_id($reqid)) {
                show_error_msg("Error", "Wrong ID $reqid.", 1);
            }
            $res = DB::run("SELECT request FROM requests WHERE id = $reqid");
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            if (!$arr) {
                show_error_msg("Error", "Wrong ID $reqid.", 1);
            }
            stdhead("Add comment to \"" . $arr["request"] . "\"");
            begin_frame("Add a request comment");
            print("<a href=$config[SITEURL]/request><button  class='btn btn-sm btn-success'>All Request</button></a>&nbsp;
            <a href=$config[SITEURL]/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>");
            print("<b>Add comment to \"" . htmlspecialchars($arr["request"]) . "\"</b>\n");
            print("<p><form name=\"Form\" method=\"post\" action=\"$config[SITEURL]/request/reqcomment?action=add\">\n");
            print("<input type=\"hidden\" name=\"tid\" value=\"$reqid\"/>\n");
            print("" . textbbcode("Form", "msg") . "");
            print("<center><button  class='btn btn-sm btn-success'>Add</button></center></form>\n");
            $res = DB::run("SELECT comments.id, text, comments.added, username, users.id as user, users.avatar FROM comments LEFT JOIN users ON comments.user = users.id WHERE req = $reqid ORDER BY comments.id DESC LIMIT 5");
            $allrows = array();
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $row[] = $row;
            }
            if (count($allrows)) {
                print("<b>Last comments in reverse order.</b>\n");
                commenttable($row);
            }
            end_frame();
            stdfoot();
            die;
        } elseif ($action == "edit") {
            $commentid = 0 + $_GET["cid"];
            if (!is_valid_id($commentid)) {
                show_error_msg("Error", "Wrong ID $commentid.", 1);
            }
            $res = DB::run("SELECT c.*, o.request FROM comments AS c JOIN requests AS o ON c.req = o.id WHERE c.id=$commentid");
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            if (!$arr) {
                show_error_msg("Error", "Wrong ID $commentid.", 1);
            }
            if ($arr["user"] != $_SESSION["id"] && get_user_class($_SESSION) < 5) {
                show_error_msg("Error", "Access denied.", 1);
            }
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $text = $_POST["msg"];
                $returnto = $_POST["returnto"];
                if ($text == "") {
                    show_error_msg("Error", "Don't leave any fields blank!", 1);
                }
                $text = sqlesc($text);
                $editedat = sqlesc(get_date_time());
                DB::run("UPDATE comments SET text=$text, editedby=$_SESSION[id], editedat=$editedat WHERE id=$commentid");
                if ($returnto) {
                    header("Location: $returnto");
                } else {
                    header("Location: $config[SITEURL]/");
                }
                die;
            }
            stdhead("Edit comment for \"" . $arr["request"] . "\"");
            begin_frame("");
            print("<b>Edit comment for \"" . htmlspecialchars($arr["request"]) . "\"</b>\n");
            print("<form name=Form method=\"post\" action=\"$config[SITEURL]/request/reqcomment?action=edit&amp;cid=$commentid\">\n");
            print("<input type=\"hidden\" name=\"returnto\" value=\"" . $_SERVER["HTTP_REFERER"] . "\" />\n");
            print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" />\n");
            print("" . textbbcode("Form", "msg", $content = $arr["text"]) . "");
            print("<button class='btn btn-sm btn-success'>Edit></button></form>\n");
            end_frame();
            stdfoot();
            die;
        } elseif ($action == "delete") {
            if (get_user_class($_SESSION) < 5) {
                show_error_msg("Error", "Access denied.", 1);
            }
            $commentid = 0 + $_GET["cid"];
            if (!is_valid_id($commentid)) {
                show_error_msg("Error", "Invalid ID $commentid.", 1);
            }
            $sure = $_GET["sure"];
            if (!$sure) {
                $referer = $_SERVER["HTTP_REFERER"];
                show_error_msg("Delete comment", "You`re about to delete this comment. Click " . "<a href=?action=delete&cid=$commentid&sure=1" . ($referer ? "&returnto=" . urlencode($referer) : "") . ">here</a> if you're sure.", 1);
            }
            $res = DB::run("SELECT req FROM comments WHERE id=$commentid");
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            if ($arr) {
                $reqid = $arr["req"];
            }
            $stmt = DB::run("DELETE FROM comments WHERE id=$commentid");
            if ($stmt > 0) {
                DB::run("UPDATE requests SET comments = comments - 1 WHERE id = $reqid");
            }
            $returnto = (int) $_GET["returnto"];
            if ($returnto) {
                header("Location: $returnto");
            } else {
                header("Location: $config[SITEURL]/request");
            }
            die;
        } else {
            show_error_msg("Error", "Unknown action $action", 1);
        }
        die;
    }
}