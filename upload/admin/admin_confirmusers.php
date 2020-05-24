<?php
#======================================================================#
#    Manual Conf Reg - Updated by djhowarth (29-10-2011)
#======================================================================#
if ($action == "confirmreg")
{
    if ($do == "confirm") 
    {
        if ($_POST["confirmall"])
            DB::run("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0'");
        else
        {
            if (!@count($_POST["users"])) show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1); 
            $ids = array_map("intval", $_POST["users"]);
            $ids = implode(", ", $ids);
            DB::run("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0' AND `id` IN ($ids)");
        }
        
        autolink(TTURL."/admincp?action=confirmreg", "Entries Confirmed");
    }
    
    $count = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
    
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, '/admincp?action=confirmreg&amp;'); 
    
    $res = DB::run("SELECT `id`, `username`, `email`, `added`, `ip` FROM `users` WHERE `status` = 'pending' AND `invited_by` = '0' ORDER BY `added` DESC $limit");

    stdhead("Manual Registration Confirm");
    adminnavmenu();
    
    begin_frame("Manual Registration Confirm");
    ?>
    
    <center>
    This page displays all unconfirmed users excluding users which have been invited by current members. <?php echo number_format($count); ?> members are pending;
    </center>

    <?php if ($count > 0): ?>
    <br />
    <form id="confirmreg" method="post" action="<?php echo TTURL; ?>/admincp?action=confirmreg&amp;do=confirm">
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head">E-mail</th>
        <th class="table_head">Registered</th>
        <th class="table_head">IP</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = $res->fetch(PDO::FETCH_LAZY)): ?>
    <tr>
        <td class="table_col1" align="center"><?php echo class_user($row["username"]); ?></td>
        <td class="table_col2" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td colspan="5" align="right">
        <input type="submit" value="Confirm Checked" />
        <input type="submit" name="confirmall" value="Confirm All" />
        </td>
    </tr>
    </table>         
    </form>
    <?php 
    endif;
    
    if ($count > 25) echo $pagerbottom;
    
    end_frame();
    stdfoot(); 
}