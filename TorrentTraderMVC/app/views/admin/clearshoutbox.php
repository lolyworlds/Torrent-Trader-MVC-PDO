<?php
Style::begin("" . Lang::T("CLEAR_SHOUTBOX") . "");?>
    <br>
    <font size ='3'><center><?php echo Lang::T("CLEAR_SHOUTBOX_TEXT"); ?></center></font>
    <br>
    <form enctype="multipart/form-data" method="post" action="<?php echo URLROOT; ?>/admincleanshout?do=delete">
    <input type="hidden" name="action" value="clearshout" />
    <input type="hidden" name="do" value="delete" />
    <table class="f-border" cellspacing="0" cellpadding="5" width="100%" align="center">
    <tr><td colspan="2" align="center"><input type="submit" value="<?php echo Lang::T("CLEAR_SHOUTBOX"); ?>" /></td></tr>
    </table></form>
    <?php
    Style::end();