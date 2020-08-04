<?php       
        print("<br /><br /><form method='post' name='bbform' enctype='multipart/form-data' action='edit?action=doedit'>");
        print("<input type=\"hidden\" name=\"id\" value=\"$id\" />\n");
        
        print("<table class='table_table' cellspacing='0' cellpadding='4' width='586' align='center'>\n");
        echo "<tr><td class='table_col1' align='right' width='60'><b>".T_("NAME").": </b></td><td class='table_col2' ><input type=\"text\" name=\"name\" value=\"" . htmlspecialchars($row["name"]) . "\" size=\"60\" /></td></tr>";
        echo "<tr><td class='table_col1'  align='right'><b>".T_("IMAGE").": </b></td><td class='table_col2'><b>".T_("IMAGE")." 1:</b>&nbsp;&nbsp;<input type='radio' name='img1action' value='keep' checked='checked' />".T_("KEEP_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img1action' value='delete' />".T_("DELETE_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img1action' value='update' />".T_("UPDATE_IMAGE")."<br /><input type='file' name='image0' size='60' /> <br /><br /> <b>".T_("IMAGE")." 2:</b>&nbsp;&nbsp;<input type='radio' name='img2action' value='keep' checked='checked' />".T_("KEEP_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img2action' value='delete' />".T_("DELETE_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img2action' value='update' />".T_("UPDATE_IMAGE")."<br /><input type='file' name='image1' size='60' /></td></tr>";
        echo "<tr><td class='table_col1'  align='right'><b>".T_("NFO").": </b><br /></td><td class='table_col2' ><input type='radio' name='nfoaction' value='keep' checked='checked' />Keep NFO &nbsp; <input type='radio' name='nfoaction' value='update' />Update NFO:";
        if ($row["nfo"] == "yes"){
            echo "&nbsp;&nbsp;<a href='$site_config[SITEURL]/nfo/view?id=".$row["id"]."' target='_blank'>[".T_("VIEW_CURRENT_NFO")."]</a>";
        } else{
            echo "&nbsp;&nbsp;<font color='#ff0000'>".T_("NO_NFO_UPLOADED")."</font>";
        }
        echo "<br /><input type='file' name='nfofile' size='60' /></td></tr>";
        
        echo "<tr><td class='table_col1' align='right'><b>".T_("CATEGORIES").": </b></td><td class='table_col2'>".$catdropdown."</td></tr>";
        
        echo "<tr><td class='table_col1' align='right'><b>".T_("LANG").": </b></td><td class='table_col2'>".$langdropdown."</td></tr>";
        
        if ($CURUSER["edit_torrents"] == "yes")
            echo "<tr><td class='table_col1' align='right'><b>".T_("BANNED").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"banned\"" . (($row["banned"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /> ".T_("BANNED")."?<br /></td></tr>";
        echo "<tr><td class='table_col1' align='right'><b>".T_("VISIBLE").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"visible\"" . (($row["visible"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /> " .T_("VISIBLEONMAIN"). "<br /></td></tr>";
        
        if ($row["external"] != "yes" && $CURUSER["edit_torrents"] == "yes"){
            echo "<tr><td class='table_col1' align='right'><b>".T_("FREE_LEECH").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"freeleech\"" . (($row["freeleech"] == "1") ? " checked=\"checked\"" : "" ) . " value=\"1\" />".T_("FREE_LEECH_MSG")."<br /></td></tr>";
        }
        
        if ($site_config['ANONYMOUSUPLOAD']) {
            echo "<tr><td class='table_col1' align='right'><b>".T_("ANONYMOUS_UPLOAD").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"anon\"" . (($row["anon"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" />(".T_("ANONYMOUS_UPLOAD_MSG").")<br /></td></tr>";
        }
        print ("<tr><td class='table_head' align='center' colspan='2'><b>" .T_("DESCRIPTION"). ":</b></td></tr></table>");
        require_once("helpers/bbcode_helper.php");
        print textbbcode("bbform","descr", htmlspecialchars($row["descr"]));
        
            
        print("<br /><center><input type=\"submit\" value='".T_("SUBMIT")."' /> <input type='reset' value='".T_("UNDO")."' /></center>\n");
        print("</form>\n");