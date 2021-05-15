<?php
            Style::begin("" . Lang::T('MAKE_REQUEST') . "");
            print("<br>\n");
            print("<a href=".URLROOT."/request><button  class='btn btn-sm btn-success'>All Request</button></a>&nbsp;
                   <a href=".URLROOT."/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>");

            ?>
            <center><big><b><font color=red>If this is abused, it will be for VIP only!</font></b></big>
            <table border=0 width=100% cellspacing=0 cellpadding=3>
            <tr><td><li><b>Before posting a request, please make sure to search the site first to make sure it's not already posted.</li>
            <li><u>1 request per day per member</u>. Any more than that will be deleted by a moderator.</li></b>
            </td></tr>
            <tr><td class=colhead align=left><?php print("" . Lang::T('SEARCH') . " " . Lang::T('TORRENT') . "");?></td></tr>
            <tr><td align=left><form method="get" action=<?php echo URLROOT ?>/search>
            <input type="text" name="<?php print("" . Lang::T('SEARCH') . "\n");?>" size="40" value="<?php htmlspecialchars($searchstr)?>" />
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
            $deadchkbox .= " /> " . Lang::T('INC_DEAD') . "\n";
            $catdropdown?>
            </select>
            <?php $deadchkbox?>
            <input type="submit" value="<?php print("" . Lang::T('SEARCH') . "\n");?>"  />
            </form>
            </td></tr></table>
            <li><b>When possible, please provide a full scene release name.<br>
            You can check out <a href=pre.php>our pre database</a> (nobles+only) <br>
            or use public sites such as NFOrce or VCDQ for help with that.</b></li>
            <?php
            print("<form method=post action=".URLROOT."/request/confirmreq><a name=add id=add></a>\n");
            print("<CENTER><table border=0 width=600 cellspacing=0 cellpadding=3>\n");
            print("<tr><td class=colhead align=center><B>" . Lang::T('MAKE_REQUEST') . "</B></a></td><tr>\n");
            print("<tr><td align=center><b>Title: </b><input type=text size=40 name=requesttitle>");
            ?>
            <select name="cat">
            <option value="0"><?php echo "(" . Lang::T("ALL") . " " . Lang::T("TYPES") . ")"; ?></option>
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
            print("<tr><td align=center><button  class='btn btn-sm btn-success'>" . Lang::T('SUBMIT') . "</button>\n");
            print("</form>\n");
            print("</table></CENTER>\n");
            
        Style::end();