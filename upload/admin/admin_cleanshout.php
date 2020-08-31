<?php
if ($action == "clearshout") {
    $title = T_("CLEAR_SHOUTBOX");
    require 'views/admin/header.php';
    adminnavmenu();
    begin_frame("" . T_("CLEAR_SHOUTBOX") . "");?>
    <br>
    <font size ='3'><center><?php echo T_("CLEAR_SHOUTBOX_TEXT"); ?></center></font>
    <br>
    <form enctype="multipart/form-data" method="post" action="admincp/admin_cleanshout.php?do=delete">
    <input type="hidden" name="action" value="clearshout" />
    <input type="hidden" name="do" value="delete" />
    <table class="f-border" cellspacing="0" cellpadding="5" width="100%" align="center">
    <tr><td colspan="2" align="center"><input type="submit" value="<?php echo T_("CLEAR_SHOUTBOX"); ?>" /></td></tr>
    </table></form>
    <?php
    end_frame();

    if ($do == "delete") {
        DB::run("TRUNCATE TABLE `shoutbox`");
        write_log("Shoutbox cleared by $_SESSION[username]");
        $msg_shout = "[color=#ff0000]" . T_("SHOUTBOX_CLEARED_MESSAGE") . "[/color]";
        DB::run("INSERT INTO shoutbox (userid, date, user, message) VALUES(?,?,?,?)", [0, get_date_time(), 'System', $msg_shout]);
        autolink(TTURL . "/admincp", "<b><font color='#ff0000'>Shoutbox Cleared....</font></b>");
    }
    stdfoot();
}