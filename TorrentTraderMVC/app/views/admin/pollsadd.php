<?php if ($_GET["subact"] == "edit") {
            $poll = $data['res']->fetch(PDO::FETCH_LAZY);
        } ?>
<form method="post" action="<?php echo URLROOT; ?>/adminpolls/pollssave">
<table border="0" cellspacing="0" class="table_table" align="center">
<tr><td class="table_col1">Question <font class="error">*</font></td><td class="table_col2" align="left"><input name="question" size="60" maxlength="255" value="<?php echo $poll['question']; ?>" /></td></tr>
<tr><td class="table_col1">Option 1 <font class="error">*</font></td><td class="table_col2" align="left"><input name="option0" size="60" maxlength="40" value="<?php echo $poll['option0']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 2 <font class="error">*</font></td><td class="table_col2" align="left"><input name="option1" size="60" maxlength="40" value="<?php echo $poll['option1']; ?>" /><br /></td></tr>
 <tr><td class="table_col1">Option 3</td><td class="table_col2" align="left"><input name="option2" size="60" maxlength="40" value="<?php echo $poll['option2']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 4</td><td class="table_col2" align="left"><input name="option3" size="60" maxlength="40" value="<?php echo $poll['option3']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 5</td><td class="table_col2" align="left"><input name="option4" size="60" maxlength="40" value="<?php echo $poll['option4']; ?>" /><br /></td></tr>
 <tr><td class="table_col1">Option 6</td><td class="table_col2" align="left"><input name="option5" size="60" maxlength="40" value="<?php echo $poll['option5']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 7</td><td class="table_col2" align="left"><input name="option6" size="60" maxlength="40" value="<?php echo $poll['option6']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 8</td><td class="table_col2" align="left"><input name="option7" size="60" maxlength="40" value="<?php echo $poll['option7']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 9</td><td class="table_col2" align="left"><input name="option8" size="60" maxlength="40" value="<?php echo $poll['option8']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 10</td><td class="table_col2" align="left"><input name="option9" size="60" maxlength="40" value="<?php echo $poll['option9']; ?>" /><br /></td></tr>
 <tr><td class="table_col1">Option 11</td><td class="table_col2" align="left"><input name="option0" size="60" maxlength="40" value="<?php echo $poll['option0']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 12</td><td class="table_col2" align="left"><input name="option1" size="60" maxlength="40" value="<?php echo $poll['option1']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 13</td><td class="table_col2" align="left"><input name="option2" size="60" maxlength="40" value="<?php echo $poll['option2']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 14</td><td class="table_col2" align="left"><input name="option3" size="60" maxlength="40" value="<?php echo $poll['option3']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 15</td><td class="table_col2" align="left"><input name="option4" size="60" maxlength="40" value="<?php echo $poll['option4']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 16</td><td class="table_col2" align="left"><input name="option5" size="60" maxlength="40" value="<?php echo $poll['option5']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 17</td><td class="table_col2" align="left"><input name="option6" size="60" maxlength="40" value="<?php echo $poll['option6']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 18</td><td class="table_col2" align="left"><input name="option7" size="60" maxlength="40" value="<?php echo $poll['option7']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 19</td><td class="table_col2" align="left"><input name="option8" size="60" maxlength="40" value="<?php echo $poll['option8']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Sort</td><td class="table_col2">
<input type="radio" name="sort" value="yes" <?php echo $poll["sort"] != "no" ? " checked='checked'" : "" ?> />Yes
<input type="radio" name="sort" value="no" <?php echo $poll["sort"] == "no" ? " checked='checked'" : "" ?> /> No
</td></tr>
<tr><td class="table_head" colspan="2" align="center"><input type="submit" value="<?php echo $data['pollid'] ? "Edit poll" : "Create poll"; ?>" /></td></tr>
</table>
<p><font class="error">*</font> required</p>
<input type="hidden" name="pollid" value="<?php echo $poll["id"] ?>" />
<input type="hidden" name="subact" value="<?php echo $data['pollid'] ? 'edit' : 'create' ?>" />
</form>