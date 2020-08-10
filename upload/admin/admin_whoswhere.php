<?php

if ($action == "whoswhere")
{
    $title = "Where are members";
    require 'views/admin/header.php';
    adminnavmenu();
    
    $res = DB::run("SELECT `id`, `username`, `page`, `last_access` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `page` != '' ORDER BY `last_access` DESC LIMIT 100");
    begin_frame("Last 100 Page Views");
    ?>
    
    <table class='table table-striped table-bordered table-hover'><thead>
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head">Page</th>
        <th class="table_head">Accessed</th>
    </tr></thead><tbody>
    <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="<?php echo TTURL; ?>/users/profile?id=<?php echo $row["id"]; ?>"><b><?php echo class_user_colour($row["username"]); ?></b></a></td>
        <td class="table_col2" align="center"><?php echo htmlspecialchars($row["page"]); ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["last_access"]); ?></td>
    </tr>
    <?php endwhile; ?>
    </tbody></table>
    
    <?php 
    end_frame();
    require 'views/admin/footer.php'; 
}
