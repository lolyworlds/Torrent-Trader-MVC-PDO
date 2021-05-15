<?php
Style::begin(Lang::T("CP_CATEGORY_EDIT"));
        ?>
    <form action="<?php echo URLROOT; ?>/adminforum" method="post">
    <input type="hidden" name="do" value="save_editcat" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <table class='f-border a-form' align='center' width='80%' cellspacing='2' cellpadding='5'>
    <tr class='f-title'><td><?php echo Lang::T("CP_FORUM_NEW_NAME_CAT"); ?>:</td></tr>
    <tr><td align='center'><input type="text" name="changed_forumcat" class="option" size="35" value="<?php echo $r["name"]; ?>" /></td></tr>
    <tr><td align='center'<td><?php echo Lang::T("CP_FORUM_NEW_SORT_ORDER"); ?>:</td></tr>
    <tr><td align='center'><input type="text" name="changed_sortcat" class="option" size="35" value="<?php echo $r["sort"]; ?>" /></td></tr>
    <tr><td align='center'><input type="submit" class="button" value="Change" /></td></tr>
    </table>
    </form>
    <?php
      Style::end();