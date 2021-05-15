<?php
        Style::begin("Warned Users");
        ?>
    <center>
    This page displays all users which are enabled and have active warnings, they can be mass deleted or deleted per user. Please note that if you delete a warning which was for poor ratio then
    this is extending the time user has left to expire. <?php echo number_format($data['count']); ?> users are warned;
    </center>
    <br />
    <?php if ($data['count'] > 0): ?>
    <br />
    <form id="warned" method="post" action="<?php echo URLROOT; ?>/adminwarnedusers?do=delete">
    <table class='table table-striped table-bordered table-hover'><thead>
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head"><?php echo Lang::T("CLASS"); ?></th>
        <th class="table_head">Added</th>
        <th class="table_head">Last Access</th>
        <th class="table_head">Warnings</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr></thead><tbody>
    <?php while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="<?php echo URLROOT; ?>/users/profile?id=<?php echo $row["id"]; ?>"><?php echo Users::coloredname($row["username"]); ?></a></td>
        <td class="table_col2" align="center"><?php echo get_user_class_name($row["class"]); ?></td>
        <td class="table_col1" align="center"><?php echo TimeDate::utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo TimeDate::utc_to_tz($row["last_access"]); ?></td>
        <td class="table_col1" align="center"><a href="<?php echo URLROOT; ?>/users/profile?id=<?php echo $row["id"]; ?>#warnings"><?php echo number_format(get_row_count("warnings", "WHERE userid = '$row[id]' AND active = 'yes'")); ?></a></td>
        <td class="table_col2" align="center"><input type="checkbox" name="warned[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile;?>
    <tr>
        <td class="table_head" colspan="6" align="right">
        <input type="submit" value="Remove Checked" />
        <input type="submit" name="removeall" value="Remove All" />
        </td>
    </tr>
    </tbody></table>
    </form>
    <?php else: ?>
    <center><b>No Warned Users...</b></center>
    <?php
endif;
        if ($data['count'] > 25) {
            echo $data['pagerbottom'];
        }
        Style::end();