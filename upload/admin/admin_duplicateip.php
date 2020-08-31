<?php
if ($action == "duplicateips") {
    $res = DB::run("SELECT ip FROM users GROUP BY ip HAVING count(*) > 1");
    $num = $res->rowCount();

    list($pagertop, $pagerbottom, $limit) = pager(25, $num, 'admincp.php?action=duplicateips&amp;');

    $res = DB::run("SELECT id, username, class, email, ip, added, last_access, COUNT(*) as count FROM users GROUP BY ip HAVING count(*) > 1 ORDER BY id ASC $limit");

    $title = T_("DUPLICATEIP");
    require 'views/admin/header.php';
    begin_frame(T_("DUPLICATEIP"));
    ?>

        <center><?php echo T_("DUPLICATEIPINFO"); ?></center>

        <br />

        <?php if ($num > 0): ?>
        <br />
        <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
        <tr>
        <th class="table_head"><?php echo T_("USERNAME"); ?></th>
        <th class="table_head"><?php echo T_("USERCLASS"); ?></th>
        <th class="table_head"><?php echo T_("EMAIL"); ?></th>
        <th class="table_head"><?php echo T_("IP"); ?></th>
        <th class="table_head"><?php echo T_("ADDED"); ?></th>
        <th class="table_head"><?php echo T_("COUNT"); ?></th>
        </tr>
        <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>

        <td class="table_col1" align="center"><a href="<?php echo TTURL; ?>/users/profile?id=<?php echo $row["id"]; ?>"><?php echo class_user_colour($row["username"]); ?></a></td>
        <td class="table_col2" align="center"><?php echo get_user_class_name($row["class"]); ?></td>
        <td class="table_col1" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col1" align="center"><a href="<?php echo TTURL; ?>/admincp?action=usersearch&amp;ip=<?php echo $row['ip']; ?>" target='_blank'><?php echo number_format($row['count']); ?></a></td>
        </tr>
        <?php endwhile;?>
        </table>
        <?php else: ?>
        <center><b><?php echo T_("NOTHING_FOUND"); ?></b></center>
        <?php
endif;

    //   if ($num > 25) echo $pagerbottom;

    end_frame();
    stdfoot();
}