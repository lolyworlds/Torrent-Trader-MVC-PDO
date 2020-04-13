<?php
// Forum management 
if ($action == "forum") {

    $error_ac == "";
    if ($_POST["do"] == "add_this_forum") {
        
        $new_forum_name = $_POST["new_forum_name"];
        $new_desc = $_POST["new_desc"];
        $new_forum_sort = (int) $_POST["new_forum_sort"];
        $new_forum_cat  = (int) $_POST["new_forum_cat"];
        $minclassread = (int)  $_POST["minclassread"];
        $minclasswrite = (int) $_POST["minclasswrite"];
        $guest_read = sqlesc($_POST["guest_read"]);
        
        if ($new_forum_name == "") $error_ac .= "<li>".T_("CP_FORUM_NAME_WAS_EMPTY")."</li>\n";
        if ($new_desc == "") $error_ac .= "<li>".T_("CP_FORUM_DESC_WAS_EMPTY")."</li>\n";
        if ($new_forum_sort == "") $error_ac .= "<li>".T_("CP_FORUM_SORT_ORDER_WAS_EMPTY")."</li>\n";
        if ($new_forum_cat == "") $error_ac .= "<li>".T_("CP_FORUM_CATAGORY_WAS_EMPTY")."</li>\n";

        if ($error_ac == "") {
            $res = SQL_Query_exec("INSERT INTO forum_forums (`name`, `description`, `sort`, `category`, `minclassread`, `minclasswrite`, `guest_read`) VALUES (".sqlesc($new_forum_name).", ".sqlesc($new_desc).", ".sqlesc($new_forum_sort).", '$new_forum_cat', '$minclassread', '$minclasswrite', $guest_read)");
            if ($res)
                autolink("admincp.php?action=forum", T_("CP_FORUM_NEW_ADDED_TO_DB"));
            else
                echo "<h4>".T_("CP_COULD_NOT_SAVE_TO_DB")."</h4>";
        } 
        else
            autolink("admincp.php?action=forum", $error_ac);
    }

    if ($_POST["do"] == "add_this_forumcat") {
        
        $new_forumcat_name = $_POST["new_forumcat_name"];
        $new_forumcat_sort = $_POST["new_forumcat_sort"];
        
        if ($new_forumcat_name == "") $error_ac .= "<li>".T_("CP_FORUM_CAT_NAME_WAS_EMPTY")."</li>\n";
        if ($new_forumcat_sort == "") $error_ac .= "<li>".T_("CP_FORUM_CAT_SORT_WAS_EMPTY")."</li>\n";

        if ($error_ac == "") {
            $res = SQL_Query_exec("INSERT INTO forumcats (`name`, `sort`) VALUES (".sqlesc($new_forumcat_name).", '".intval($new_forumcat_sort)."')");
            if ($res)
                autolink("admincp.php?action=forum", "Thank you, new forum cat added to db ...");
            else
                echo "<h4>".T_("CP_COULD_NOT_SAVE_TO_DB")."</h4>";
        } 
        else
            autolink("admincp.php?action=forum", $error_ac);
    }

    if ($_POST["do"] == "save_edit") {
        
        $id = (int) $_POST["id"];
        $changed_sort = (int) $_POST["changed_sort"];
        $changed_forum = sqlesc($_POST["changed_forum"]);
        $changed_forum_desc = sqlesc($_POST["changed_forum_desc"]);
        $changed_forum_cat = (int) $_POST["changed_forum_cat"];
        $minclasswrite = (int) $_POST["minclasswrite"];
        $minclassread  = (int) $_POST["minclassread"];
        $guest_read = sqlesc($_POST["guest_read"]);
        
        SQL_Query_exec("UPDATE forum_forums SET sort = '$changed_sort', name = $changed_forum, description = $changed_forum_desc, category = '$changed_forum_cat', minclassread='$minclassread', minclasswrite='$minclasswrite', guest_read=$guest_read WHERE id='$id'");
        autolink("admincp.php?action=forum", "<center><b>".T_("CP_UPDATE_COMPLETED")."</b></center>");
    }

    if ($_POST["do"] == "save_editcat") {
        
        $id = (int) $_POST["id"];
        $changed_sortcat = (int) $_POST["changed_sortcat"];
        
        SQL_Query_exec("UPDATE forumcats SET sort = '$changed_sortcat', name = ".sqlesc($_POST["changed_forumcat"])." WHERE id='$id'");
        autolink("admincp.php?action=forum", "<center><b>".T_("CP_UPDATE_COMPLETED")."</b></center>");
    }

    if ($_POST["do"] == "delete_forum" && is_valid_id($_POST["id"])) 
    {
        SQL_Query_exec("DELETE FROM forum_forums WHERE id = $_POST[id]");
        SQL_Query_exec("DELETE FROM forum_topics WHERE forumid = $_POST[id]");
        SQL_Query_exec("DELETE FROM forum_posts WHERE topicid = $_POST[id]");
        SQL_Query_exec("DELETE FROM forum_readposts WHERE topicid = $_POST[id]");
        autolink("admincp.php?action=forum", T_("CP_FORUM_DELETED"));
    }
    
    if ($_POST["do"] == "delete_forumcat" && is_valid_id($_POST["id"])) 
    {
        SQL_Query_exec("DELETE FROM forumcats WHERE id = $_POST[id]");
        
        $res = SQL_Query_exec("SELECT id FROM forum_forums WHERE category = $_POST[id]");
        
        while ( $row = mysqli_fetch_assoc($res) )
        {
            $res2 = SQL_Query_exec("SELECT id FROM forum_topics WHERE forumid = $row[id]");
            
            while ( $arr = mysqli_fetch_assoc($res2) )
            {
                SQL_Query_exec("DELETE FROM forum_posts WHERE topicid = $arr[id]");
                SQL_Query_exec("DELETE FROM forum_readposts WHERE topicid = $arr[id]");
            }
            
            SQL_Query_exec("DELETE FROM forum_topics WHERE forumid = $row[id]");  
            SQL_Query_exec("DELETE FROM forum_forums WHERE id = $row[id]");  
        }
        
        autolink("admincp.php?action=forum", T_("CP_FORUM_CAT_DELETED"));
    }
    
    stdhead(T_("FORUM_MANAGEMENT"));
    
    $groupsres = SQL_Query_exec("SELECT group_id, level FROM groups ORDER BY group_id ASC");
    while ($groupsrow = mysqli_fetch_row($groupsres))
        $groups[$groupsrow[0]] = $groupsrow[1];

    if ($_GET["do"] == "edit_forum") {
        
        $id = (int) $_GET["id"];
        
        $q = SQL_Query_exec("SELECT * FROM forum_forums WHERE id = '$id'");
        $r = mysqli_fetch_array($q);
        
        if (!$r)
             autolink("admincp.php?action=forum", T_("FORUM_INVALID"));
        
        begin_frame(T_("FORUM_MANAGEMENT_EDIT"));   
    ?>
          <form action="admincp.php?action=forum" method="post">
          <input type="hidden" name="do" value="save_edit" />
          <input type="hidden" name="id" value="<?php echo $id; ?>" />
          <table class='f-border a-form' align='center' width='80%' cellspacing='2' cellpadding='5'>
          <tr class='f-form'>
          <td>New Name for Forum:</td>
          <td align='right'><input type="text" name="changed_forum" class="option" size="35" value="<?php echo $r["name"]; ?>" /></td>
          </tr><tr class='f-form'>
          <td><?php echo T_("CP_FORUM_NEW_SORT_ORDER");?>:</td>
          <td align='right'><input type="text" name="changed_sort" class="option" size="35" value="<?php echo $r["sort"]; ?>" /></td>
          </tr><tr class='f-form'>
          <td>Description:</td>
          <td align='right'><textarea cols='50' rows='5' name='changed_forum_desc'><?php echo $r["description"]; ?></textarea></td>
          </tr><tr class='f-form'>
          <td>New Category:</td>
          <td align='right'><select name='changed_forum_cat'>
    <?php
    $query = SQL_Query_exec("SELECT * FROM forumcats ORDER BY sort, name");
    while ($row = mysqli_fetch_array($query))
        echo "<option value='{$row['id']}'>{$row['name']}</option>";

    echo "</select></td></tr>
    <tr class='f-form'><td>".T_("FORUM_MIN_CLASS_READ").":</td>
    <td align='right'><select name='minclassread'>";

    foreach ($groups as $id => $level) {
        $s = $r["minclassread"] == $id ? " selected='selected'" : "";
        echo "<option value='$id' $s>$level</option>";
    }

    echo "</select></td></tr><tr class='f-form'><td>".T_("FORUM_MIN_CLASS_POST").":</td>
    <td align='right'><select name='minclasswrite'>";

    foreach ($groups as $id => $level) {
        $s = $r["minclasswrite"] == $id ? " selected='selected'" : "";
        echo "<option value='$id' $s>$level</option>";
    }
    ?>
    </select></td></tr><tr class='f-form'>
    <td><td><?php echo T_("FORUM_ALLOW_GUEST_READ");?>:</td><td align='right'><input type="radio" name="guest_read" value="yes" <?php echo $r["guest_read"] == "yes" ? "checked='checked'" : ""?> />Yes, <input type="radio" name="guest_read" value="no" <?php echo $r["guest_read"] != "yes" ? "checked='checked'" : ""?> />No</td></tr>
    <tr class='f-form'><td><input type="submit" class="button" value="Change" /></td></tr>
    </table>
    </form>
    <?php
        end_frame();
        stdfoot();
    }

if ($_GET["do"] == "del_forum") {
    
    $id = (int) $_GET["id"];
    
    $t = SQL_Query_exec("SELECT * FROM forum_forums WHERE id = '$id'");
    $v = mysqli_fetch_array($t);
    
    if (!$v)
         autolink("admincp.php?action=forum", T_("FORUM_INVALID"));
    
    begin_frame(T_("CONFIRM")); 
?>
    <form class='a-form' action="admincp.php?action=forum" method="post">
    <input type="hidden" name="do" value="delete_forum" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <?php echo T_("CP_FORUM_REALY_DEL");?> <?php echo "<b>$v[name] with ID$v[id] ???</b>"; ?> <?php echo T_("CP_FORUM_THIS_WILL_REALLY_DEL");?>.
    <input type="submit" name="delcat" class="button" value="Delete" />
    </form>
<?php
          end_frame();
          stdfoot();
}

if ($_GET["do"] == "del_forumcat") {
    
    $id = (int) $_GET["id"];

    $t = SQL_Query_exec("SELECT * FROM forumcats WHERE id = '$id'");
    $v = mysqli_fetch_array($t);
    
    if (!$v)
         autolink("admincp.php?action=forum", T_("FORUM_INVALID_CAT"));
    
    begin_frame(T_("CONFIRM")); 
?>
  <form class='a-form' action="admincp.php?action=forum" method="post">
  <input type="hidden" name="do" value="delete_forumcat" />
  <input type="hidden" name="id" value="<?php echo $id; ?>" />
      <?php echo T_("CP_FORUM_REALY_DEL_CAT");?><?php echo "<b>$v[name] with ID$v[id] ???</b>"; ?> <?php echo T_("CP_FORUM_THIS_WILL_REALLY_DEL");?>.
      <input type="submit" name="delcat" class="button" value="Delete" />
      </form>
<?php
          end_frame();
          stdfoot();
}

if ($_GET["do"] == "edit_forumcat") {
    
    $id = (int) $_GET["id"];

    $q = SQL_Query_exec("SELECT * FROM forumcats WHERE id = '$id'");
    $r = mysqli_fetch_array($q);
    
    if (!$r)
         autolink("admincp.php?action=forum", T_("FORUM_INVALID_CAT")); 
         
    begin_frame(T_("CP_CATEGORY_EDIT"));
    ?>
    <form action="admincp.php?action=forum" method="post">
    <input type="hidden" name="do" value="save_editcat" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <table class='f-border a-form' align='center' width='80%' cellspacing='2' cellpadding='5'>
    <tr class='f-title'><td><?php echo T_("CP_FORUM_NEW_NAME_CAT");?>:</td></tr>
    <tr><td align='center'><input type="text" name="changed_forumcat" class="option" size="35" value="<?php echo $r["name"]; ?>" /></td></tr>
    <tr><td align='center'<td><?php echo T_("CP_FORUM_NEW_SORT_ORDER");?>:</td></tr>
    <tr><td align='center'><input type="text" name="changed_sortcat" class="option" size="35" value="<?php echo $r["sort"]; ?>" /></td></tr>
    <tr><td align='center'><input type="submit" class="button" value="Change" /></td></tr>
    </table>
    </form>
    <?php
    end_frame();
    stdfoot();
}
    
    if (!$do) {
        navmenu();
        begin_frame(T_("FORUM_MANAGEMENT"));
        $query = SQL_Query_exec("SELECT * FROM forumcats ORDER BY sort, name");
        $allcat = mysqli_num_rows($query);
        $forumcat = array();
        while ($row = mysqli_fetch_array($query))
            $forumcat[] = $row;

        echo "
    <form action='admincp.php' method='post'>   
    <input type='hidden' name='sid' value='$sid' />
<input type='hidden' name='action' value='forum' />
<input type='hidden' name='do' value='add_this_forum' />
<table class='table_table' align='center' width='80%' cellspacing='2' cellpadding='5'>
<tr>
<td class='table_col1'>".T_("CP_FORUM_NEW_NAME").":</td>
<td class='table_col2' align='right'><input type='text' name='new_forum_name' size='90' maxlength='30'  value='$new_forum_name' /></td>
</tr>
<tr>
<td class='table_col1'>".T_("CP_FORUM_SORT_ORDER").":</td>
<td class='table_col2' align='right'><input type='text' name='new_forum_sort' size='30' maxlength='10'  value='$new_forum_sort' /></td>
</tr>
<tr>
<td class='table_col1'>".T_("CP_FORUM_NEW_DESC").":</td>
<td class='table_col2' align='right'><textarea cols='50' rows='5' name='new_desc'>$new_desc</textarea></td>
</tr>
<tr>
<td class='table_col1'>".T_("CP_FORUM_NEW_CAT").":</td>
<td class='table_col2' align='right'><select name='new_forum_cat'>";
foreach ($forumcat as $row)
    echo "<option value='{$row['id']}'>{$row['name']}</option>";

echo "</select></td>
</tr>
<tr>
<td class='table_col1'>".T_("FORUM_MIN_CLASS_READ").":</td>
<td class='table_col2' align='right'><select name='minclassread'>";

foreach ($groups as $id => $level) {
    $s = $r["minclassread"] == $id ? " selected='selected'" : "";
    echo "<option value='$id' $s>$level</option>";
}

echo "</select></td></tr>
<tr>
<td class='table_col1'>".T_("FORUM_MIN_CLASS_POST").":</td>
<td class='table_col2' align='right'><select name='minclasswrite'>";

foreach ($groups as $id => $level) {
    $s = $r["minclasswrite"] == $id ? " selected='selected'" : "";
    echo "<option value='$id' $s>$level</option>";
}

echo "</select></td>
</tr>".
"<tr>
<td class='table_col1'>".T_("FORUM_ALLOW_GUEST_READ").":</td>
<td class='table_col2' align='right'><input type=\"radio\" name=\"guest_read\" value=\"yes\" checked='checked' />Yes, <input type=\"radio\" name=\"guest_read\" value=\"no\" />No</td></tr>".
"<tr>
<th class='table_head' colspan='2' align='center'>
<input type='submit' value='Add new forum' />
<input type='reset' value='".T_("RESET")."' />
</th>
</tr>";

#if($error_ac != "") echo "<tr><td colspan='2' align='center' style='background:#eeeeee;border:2px red solid'><b>COULD  NOT ADD NEW forum:</b><br /><ul>$error_ac</ul></td></tr>\n";

echo "</table>
</form>

<b>".T_("FORUM_CURRENT").":</b>
<table class='table_table' align='center' width='80%' cellspacing='0' cellpadding='4'>";

echo "<tr><th class='table_head' width='60'><font size='2'><b>".T_("ID")."</b></font></th><th class='table_head' width='120'>".T_("NAME")."</th><th class='table_head' width='250'>DESC</th><th class='table_head' width='45'>".T_("SORT")."</th><th class='table_head' width='45'>CATEGORY</th><th class='table_head' width='18'>".T_("EDIT")."</th><th class='table_head' width='18'>".T_("DEL")."</th></tr>\n";
$query = SQL_Query_exec("SELECT * FROM forum_forums ORDER BY sort, name");
$allforums = mysqli_num_rows($query);
if ($allforums == 0) {
    echo "<tr><td class='table_col1' colspan='7' align='center'>No Forums found</td></tr>\n";
} else {
    while($row = mysqli_fetch_array($query)) {
        foreach ($forumcat as $cat)
            if ($cat['id'] == $row['category'])
                $category = $cat['name'];
            
            echo "<tr><td class='table_col1' width='60' align='center'><font size='2'><b>ID($row[id])</b></font></td><td class='table_col2' width='120'> $row[name]</td><td class='table_col1'  width='250'>$row[description]</td><td class='table_col2' width='45' align='center'>$row[sort]</td><td class='table_col1' width='45'>$category</td>\n";
            echo "<td class='table_col2' width='18' align='center'><a href='admincp.php?action=forum&amp;do=edit_forum&amp;id=$row[id]'>[".T_("EDIT")."]</a></td>\n";
            echo "<td class='table_col1' width='18' align='center'><a href='admincp.php?action=forum&amp;do=del_forum&amp;id=$row[id]'><img src='images/delete.gif' alt='".T_("FORUM_DELETE_CATEGORY")."' width='17' height='17' border='0' /></a></td></tr>\n";
    }
}
echo "</table>
<br /><b>".T_("FORUM_CURRENT_CATS").":</b><table class='table_table' align='center' width='80%' cellspacing='0' cellpadding='4'>
<tr><th class='table_head' width='60'><font size='2'><b>".T_("ID")."</b></font></th><th class='table_head' width='120'>".T_("NAME")."</th><th class='table_head' width='18'>".T_("SORT")."</th><th class='table_head' width='18'>".T_("EDIT")."</th><th class='table_head' width='18'>".T_("DEL")."</th></tr>\n";

if ($allcat == 0) {
    echo "<tr class='table_col1'><td class='f-border' colspan='7' align='center'>".T_("FORUM_NO_CAT_FOUND")."</td></tr>\n"; 
} else {
    foreach ($forumcat as $row) {
        echo "<tr><td class='table_col1' width='60'><font size='2'><b>ID($row[id])</b></font></td><td class='table_col2' width='120'> $row[name]</td><td class='table_col1' width='18'>$row[sort]</td>\n";
        echo "<td class='table_col2' width='18'><a href='admincp.php?action=forum&amp;do=edit_forumcat&amp;id=$row[id]'>[".T_("EDIT")."]</a></td>\n";
        echo "<td class='table_col1' width='18'><a href='admincp.php?action=forum&amp;do=del_forumcat&amp;id=$row[id]'><img src='images/delete.gif' alt='".T_("FORUM_DELETE_CATEGORY")."' width='17' height='17' border='0' /></a></td></tr>\n";
    }
}
echo "</table>\n";

echo "<br />
<form action='admincp.php?action=forum' method='post'>
<input type='hidden' name='do' value='add_this_forumcat' /> 
<table class='table_table' align='center' width='80%' cellspacing='2' cellpadding='5'>
<tr>
<td class='table_col1'>".T_("FORUM_NAME_OF_NEW_CAT").":</td>
<td class='table_col2' align='right' class='f-form'><input type='text' name='new_forumcat_name' size='60' maxlength='30'  value='$new_forumcat_name' /></td>
</tr>
<tr>
<td class='table_col1'>".T_("FORUM_CAT_SORT_ORDER").":</td>
<td class='table_col2' align='right'><input type='text' name='new_forumcat_sort' size='20' maxlength='10'  value='$new_forumcat_sort' /></td>
</tr>

<tr>
<th class='table_head' colspan='2' align='center'>
<input type='submit' value='".T_("FORUM_ADD_NEW_CAT")."' />
<input type='reset' value='".T_("RESET")."' />
</th>
</tr>
</table>
</form>";
end_frame();
stdfoot();
    } // End New Forum


} // End Forum management 