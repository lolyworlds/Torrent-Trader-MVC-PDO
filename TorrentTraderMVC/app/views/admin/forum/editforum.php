<?php
Style::begin(Lang::T("FORUM_MANAGEMENT_EDIT"));
?>
<div class="jumbotron">
          <form action="<?php echo URLROOT; ?>/adminforum" method="post">
          <input type="hidden" name="do" value="save_edit" />
          <input type="hidden" name="id" value="<?php echo $id; ?>" />
          <table class='f-border a-form' align='center' width='80%' cellspacing='2' cellpadding='5'>
          <tr class='f-form'>
<td class='table_col1'>New Name for Forum:</td>
<td class='table_col2' align='right'><input type="text" name="changed_forum" class="option" size="35" value="<?php echo $r["name"]; ?>" /></td>
          </tr><tr class='f-form'>
<td class='table_col1'>Sort Order:</td>
<td class='table_col2' align='right'><input type="text" name="changed_sort" class="option" size="35" value="<?php echo $r["sort"]; ?>" /></td>
          </tr><tr class='f-form'>
<td class='table_col1'>Description:</td>
<td class='table_col2' align='right'><textarea cols='50' rows='5' name='changed_forum_desc'><?php echo $r["description"]; ?></textarea></td>
          </tr><tr class='f-form'>
<td class='table_col1'>New Category:</td>
<td class='table_col2' align='right'><select name='changed_forum_cat'>
    <?php
$query = DB::query("SELECT * FROM forumcats ORDER BY sort, name");
        while ($row = $query->fetch()) {
            echo "<option value={$row['id']}>{$row['name']}</option>";
        }
        ?>
</select></td>
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
</select></td>
</tr>
<tr>
<td class='table_col1'>Allow Guest Read:</td>
	<td align='right'><input type="radio" name="guest_read" value="yes" <?php echo $r["guest_read"] == "yes" ? "checked='checked'" : "" ?> />Yes,
	           <input type="radio" name="guest_read" value="no" <?php echo $r["guest_read"] != "yes" ? "checked='checked'" : "" ?> />No</td></tr>

<tr>
<th class='table_head' colspan='2' align='center'>
<input type="submit" class="button" value="Change" />
</th>
</tr>
</table>
</form></div>
    <?php
Style::end();