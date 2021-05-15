<?php
Style::begin(Lang::T("FORUM_MANAGEMENT"));
        $query = DB::run("SELECT * FROM forumcats ORDER BY sort, name");
        $allcat = $query->rowCount();
        $forumcat = array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $forumcat[] = $row;
        }

        echo "
        <div class='border border-warning'>
        <form action='".URLROOT."/adminforum' method='post'>
    <input type='hidden' name='sid' value='$sid' />
<input type='hidden' name='action' value='forum' />
<input type='hidden' name='do' value='add_this_forum' />
<div class='table-responsive'>
<table class='table'>
<tr>
<td class='table_col1'>" . Lang::T("CP_FORUM_NEW_NAME") . ":</td>
<td class='table_col2' align='right'><input type='text' name='new_forum_name' size='90' maxlength='30'  value='$new_forum_name' /></td>
</tr>
<tr>
<td class='table_col1'>" . Lang::T("CP_FORUM_SORT_ORDER") . ":</td>
<td class='table_col2' align='right'><input type='text' name='new_forum_sort' size='30' maxlength='10'  value='$new_forum_sort' /></td>
</tr>
<tr>
<td class='table_col1'>" . Lang::T("CP_FORUM_NEW_DESC") . ":</td>
<td class='table_col2' align='right'><textarea cols='50%' rows='5' name='new_desc'>$new_desc</textarea></td>
</tr>
<tr>
<td class='table_col1'>" . Lang::T("CP_FORUM_NEW_CAT") . ":</td>
<td class='table_col2' align='right'><select name='new_forum_cat'>";
        foreach ($forumcat as $row) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }

        ?>
</select>
</tr>
<tr>
<td class='table_col1'>Mininum Class Needed to Read:</td>
<td class='table_col2' align='right'><select name='minclassread'>
<option value='<?echo _USER; ?>'>User</option>
<option value='<?echo _POWERUSER; ?>'>Power User</option>
<option value='<?echo _VIP; ?>'>VIP</option>
<option value='<?echo _UPLOADER; ?>'>Uploader</option>
<option value='<?echo _MODERATOR; ?>'>Moderator</option>
<option value='<?echo _SUPERMODERATOR; ?>'>Super Moderator</option>
<option value='<?echo _ADMINISTRATOR; ?>'>Administrator</option>
</select></td>
</tr>
<tr>
<td class='table_col1'>Mininum Class Needed to Post:</td>
<td class='table_col2' align='right'><select name='minclasswrite'>
<option value='<?echo _USER; ?>'>User</option>
<option value='<?echo _POWERUSER; ?>'>Power User</option>
<option value='<?echo _VIP; ?>'>VIP</option>
<option value='<?echo _UPLOADER; ?>'>Uploader</option>
<option value='<?echo _MODERATOR; ?>'>Moderator</option>
<option value='<?echo _SUPERMODERATOR; ?>'>Super Moderator</option>
<option value='<?echo _ADMINISTRATOR; ?>'>Administrator</option>

<?php
echo "</select></td>
</tr>" .
        "<tr>
<td class='table_col1'>" . Lang::T("FORUM_ALLOW_GUEST_READ") . ":</td>
<td class='table_col2' align='right'><input type=\"radio\" name=\"guest_read\" value=\"yes\" checked='checked' />Yes, <input type=\"radio\" name=\"guest_read\" value=\"no\" />No</td></tr>" .
        "<tr>
<th class='table_head' colspan='2' align='center'>
<input type='submit' value='Add new forum' />
<input type='reset' value='" . Lang::T("RESET") . "' />
</th>
</tr>";

#if($error_ac != "") echo "<tr><td colspan='2' align='center' style='background:#eeeeee;border:2px red solid'><b>COULD  NOT ADD NEW forum:</b><br /><ul>$error_ac</ul></td></tr>\n";

        echo "</table>
</form></div></div><br>

<b>" . Lang::T("FORUM_CURRENT") . ":</b><br>
<div class='table-responsive'>
<table class='table'><thead>";

        echo "<tr><th class='table_head' width='60'><font size='2'><b>" . Lang::T("ID") . "</b></font></th><th class='table_head' width='120'>" . Lang::T("NAME") . "</th><th class='table_head' width='250'>DESC</th><th class='table_head' width='45'>" . Lang::T("SORT") . "</th><th class='table_head' width='45'>CATEGORY</th><th class='table_head' width='18'>" . Lang::T("EDIT") . "</th><th class='table_head' width='18'>" . Lang::T("DEL") . "</th></tr></thead><tbody>\n";
        $query = DB::run("SELECT * FROM forum_forums ORDER BY sort, name");
        $allforums = $query->rowCount();
        if ($allforums == 0) {
            echo "<tr><td class='table_col1' colspan='7' align='center'>No Forums found</td></tr>\n";
        } else {
            while ($row = $query->fetch()) {
                foreach ($forumcat as $cat) {
                    if ($cat['id'] == $row['category']) {
                        $category = $cat['name'];
                    }
                }

                echo "<tr><td class='table_col1' width='60' align='center'><font size='2'><b>ID($row[id])</b></font></td><td class='table_col2' width='120'> $row[name]</td><td class='table_col1'  width='250'>$row[description]</td><td class='table_col2' width='45' align='center'>$row[sort]</td><td class='table_col1' width='45'>$category</td>\n";
                echo "<td class='table_col2' width='18' align='center'><a href='".URLROOT."/adminforum?do=edit_forum&amp;id=$row[id]'>[" . Lang::T("EDIT") . "]</a></td>\n";
                echo "<td class='table_col1' width='18' align='center'><a href='".URLROOT."/adminforum&?do=del_forum&amp;id=$row[id]'><img src='".URLROOT."/assets/images/delete.png' alt='" . Lang::T("FORUM_DELETE_CATEGORY") . "' width='17' height='17' border='0' /></a></td></tr>\n";
            }
        }
        echo "</tbody></table></div>
<b>" . Lang::T("FORUM_CURRENT_CATS") . ":</b><div class='table-responsive'>
<table class='table'><thead>
<tr><th class='table_head' width='60'><font size='2'><b>" . Lang::T("ID") . "</b></font></th><th class='table_head' width='120'>" . Lang::T("NAME") . "</th><th class='table_head' width='18'>" . Lang::T("SORT") . "</th><th class='table_head' width='18'>" . Lang::T("EDIT") . "</th><th class='table_head' width='18'>" . Lang::T("DEL") . "</th></tr></thead><tbody>\n";

        if ($allcat == 0) {
            echo "<tr class='table_col1'><td class='f-border' colspan='7' align='center'>" . Lang::T("FORUM_NO_CAT_FOUND") . "</td></tr>\n";
        } else {
            foreach ($forumcat as $row) {
                echo "<tr><td class='table_col1' width='60'><font size='2'><b>ID($row[id])</b></font></td><td class='table_col2' width='120'> $row[name]</td><td class='table_col1' width='18'>$row[sort]</td>\n";
                echo "<td class='table_col2' width='18'><a href='".URLROOT."/adminforum?do=edit_forumcat&amp;id=$row[id]'>[" . Lang::T("EDIT") . "]</a></td>\n";
                echo "<td class='table_col1' width='18'><a href='".URLROOT."/adminforum?do=del_forumcat&amp;id=$row[id]'><img src='".URLROOT."/assets/images/delete.png' alt='" . Lang::T("FORUM_DELETE_CATEGORY") . "' width='17' height='17' border='0' /></a></td></tr>\n";
            }
        }
        echo "</tbody></table></div>\n";

        echo "<div class='border border-warning'>
<form action='".URLROOT."/adminforum' method='post'>
<input type='hidden' name='do' value='add_this_forumcat' />
<div class='table-responsive'>
  <table class='table'>
<tr>
<td class='table_col1'>" . Lang::T("FORUM_NAME_OF_NEW_CAT") . ":</td>
<td class='table_col2' align='right' class='f-form'><input type='text' name='new_forumcat_name' size='60' maxlength='30'  value='$new_forumcat_name' /></td>
</tr>
<tr>
<td class='table_col1'>" . Lang::T("FORUM_CAT_SORT_ORDER") . ":</td>
<td class='table_col2' align='right'><input type='text' name='new_forumcat_sort' size='20' maxlength='10'  value='$new_forumcat_sort' /></td>
</tr>

<tr>
<th class='table_head' colspan='2' align='center'>
<input type='submit' value='" . Lang::T("FORUM_ADD_NEW_CAT") . "' />
<input type='reset' value='" . Lang::T("RESET") . "' />
</th>
</tr>
</table></div>
</form></div>";
        Style::end();