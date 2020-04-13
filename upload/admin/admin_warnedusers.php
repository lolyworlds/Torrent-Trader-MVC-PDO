<?php

#======================================================================#
# Warned Users - Updated by djhowarth (11-12-2011)
#======================================================================#
if ($action == "warned")
{
    if ($do == "delete") 
    {
        if ($_POST["removeall"])
        {
            $res = DB::run("SELECT `id` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `warned` = 'yes'");
            while ($row = $res->fetch(PDO::FETCH_LAZY))
            {
                DB::run("DELETE FROM `warnings` WHERE `active` = 'yes' AND `userid` = '$row[id]'");
                DB::run("UPDATE `users` SET `warned` = 'no' WHERE `id` = '$row[id]'");
            }
        }
        else
        {
            if (!@count($_POST['warned'])) show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
            $ids = array_map("intval", $_POST["warned"]);
            $ids = implode(", ", $ids);
                
            DB::run("DELETE FROM `warnings` WHERE `active` = 'yes' AND `userid` IN ($ids)");
            DB::run("UPDATE `users` SET `warned` = 'no' WHERE `id` IN ($ids)");
        }
        
        
        autolink("/admincp?action=warned", "Entries Confirmed");
    }
    
    $count = get_row_count("users", "WHERE enabled = 'yes' AND status = 'confirmed' AND warned = 'yes'");
    
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, '/admincp?action=warned&amp;');
    
    $res = DB::run("SELECT `id`, `username`, `class`, `added`, `last_access` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `warned` = 'yes' ORDER BY `added` DESC $limit");

    stdhead("Warned Users");
    navmenu();
    
    begin_frame("Warned Users");
    ?>
    
    <center>
    This page displays all users which are enabled and have active warnings, they can be mass deleted or deleted per user. Please note that if you delete a warning which was for poor ratio then
    this is extending the time user has left to expire. <?php echo number_format($count); ?> users are warned;
    </center>

    <br />
    <?php if ($count > 0): ?>
    <br />
    <form id="warned" method="post" action="/admincp?action=warned&amp;do=delete">
    <table cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head"><?php echo T_("CLASS");?></th>   
        <th class="table_head">Added</th>  
        <th class="table_head">Last Access</th>
        <th class="table_head">Warnings</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="/accountdetails?id=<?php echo $row["id"]; ?>"><?php echo class_user($row["username"]); ?></a></td>
        <td class="table_col2" align="center"><?php echo get_user_class_name($row["class"]); ?></td>  
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo utc_to_tz($row["last_access"]); ?></td>
        <td class="table_col1" align="center"><a href="/accountdetails?id=<?php echo $row["id"]; ?>#warnings"><?php echo number_format(get_row_count("warnings", "WHERE userid = '$row[id]' AND active = 'yes'")); ?></a></td>
        <td class="table_col2" align="center"><input type="checkbox" name="warned[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td class="table_head" colspan="6" align="right">
        <input type="submit" value="Remove Checked" />
        <input type="submit" name="removeall" value="Remove All" />
        </td>
    </tr>
    </table>         
    </form>
    <?php else: ?>
    <center><b>No Warned Users...</b></center>
    <?php
    endif;
    
    if ($count > 25) echo $pagerbottom;

    end_frame();
    stdfoot(); 
}