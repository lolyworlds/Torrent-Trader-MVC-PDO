<?php

if ($action == "whoswhere")
{
    stdhead("Where are members");
    navmenu();
    
    $res = DB::run("SELECT `id`, `username`, `page`, `last_access` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `page` != '' ORDER BY `last_access` DESC LIMIT 100");
    begin_frame("Last 100 Page Views");
    ?>
    
    <table border="0" cellpadding="4" cellspacing="3" width="80%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head">Page</th>
        <th class="table_head">Accessed</th>
    </tr>
    <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="/accountdetails?id=<?php echo $row["id"]; ?>"><b><?php echo class_user($row["username"]); ?></b></a></td>
        <td class="table_col2" align="center"><?php echo htmlspecialchars($row["page"]); ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["last_access"]); ?></td>
    </tr>
    <?php endwhile; ?>
    </table>
    
    <?php 
    end_frame();
    stdfoot(); 
}
