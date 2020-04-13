<?php
#======================================================================#
#    View Pending Invited Users - Created by djhowarth (18-11-2011) 
#======================================================================#
if ($action == "pendinginvite")
{
    if ($do == "del") 
    {
        if (!@count($_POST["users"])) show_error_msg(T_("ERROR"), "Nothing Selected.", 1);

        $ids = array_map("intval", $_POST["users"]);
        $ids = implode(", ", $ids);
        
        $res = DB::run("SELECT u.id, u.invited_by, i.invitees FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' AND u.id IN ($ids)");
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {    
             # We remove the invitee from the inviter and give them back there invite.
             $invitees = str_replace("$row[id] ", "", $row["invitees"]);
             DB::run("UPDATE `users` SET `invites` = `invites` + 1, `invitees` = '$invitees' WHERE `id` = '$row[invited_by]'");
             DB::run("DELETE FROM `users` WHERE `id` = '$row[id]'");
        }

        autolink("admincp.php?action=pendinginvite", "Entries Deleted");
    }
    
  //  $count = get_row_count("users", "WHERE status = 'pending' AND invited_by != '0'");
    $count = DB::run("SELECT COUNT(*) FROM users WHERE status = 'pending' AND invited_by != '0'")->fetchColumn();

    list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'admincp.php?action=pendinginvite&amp;');  
                                                                     
    $res = DB::run("SELECT u.id, u.username, u.email, u.added, u.invited_by, i.username as inviter FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' ORDER BY u.added DESC $limit");
    
    stdhead("Invited Pending Users");
    navmenu();
    
    begin_frame("Invited Pending Users");
    ?>
    
    <center>
    This page displays all invited users which have been sent invites but haven't yet activated there account. By deleting a user the inviter will recieve their invite back and any data associated with the invitee will be deleted. <?php echo number_format($count); ?> members are pending;
    </center>

    <?php  if ($count > 0): ?>
    <br />
    <form id="pendinginvite" method="post" action="admincp.php?action=pendinginvite">
    <input type="hidden" name="do" value="del" />
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head">E-mail</th>
        <th class="table_head">Invited</th>
        <th class="table_head">Invited By</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td class="table_col1" align="center"><?php echo $row["username"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><a href="account-details.php?id=<?php echo $row["invited_by"]; ?>"><?php echo $row["inviter"]; ?></a></td>
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td colspan="5" align="right">
        <input type="submit" value="Delete Checked" />
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
