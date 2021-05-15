<?php
Style::begin(Lang::T("DUPLICATEIP"));
    ?>
        <center><?php echo Lang::T("DUPLICATEIPINFO"); ?></center>
        <br />
        <?php if ($num > 0): ?>
        <br />
        <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
        <tr>
        <th class="table_head"><?php echo Lang::T("USERNAME"); ?></th>
        <th class="table_head"><?php echo Lang::T("USERCLASS"); ?></th>
        <th class="table_head"><?php echo Lang::T("EMAIL"); ?></th>
        <th class="table_head"><?php echo Lang::T("IP"); ?></th>
        <th class="table_head"><?php echo Lang::T("ADDED"); ?></th>
        <th class="table_head"><?php echo Lang::T("COUNT"); ?></th>
        </tr>
        <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
        <td class="table_col1" align="center"><a href="<?php echo URLROOT; ?>/users/profile?id=<?php echo $row["id"]; ?>"><?php echo Users::coloredname($row["username"]); ?></a></td>
        <td class="table_col2" align="center"><?php echo get_user_class_name($row["class"]); ?></td>
        <td class="table_col1" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><?php echo TimeDate::utc_to_tz($row["added"]); ?></td>
        <td class="table_col1" align="center"><a href="<?php echo URLROOT; ?>/admincp?action=usersearch&amp;ip=<?php echo $row['ip']; ?>" target='_blank'><?php echo number_format($row['count']); ?></a></td>
        </tr>
        <?php endwhile;?>
        </table>
        <?php else: ?>
        <center><b><?php echo Lang::T("NOTHING_FOUND"); ?></b></center>
        <?php
    endif;
    Style::end();