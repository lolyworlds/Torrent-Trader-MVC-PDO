    <center>
    This page displays all unconfirmed users excluding users which have been invited by current members. <?php echo number_format($data['count']); ?> members are pending;
    </center>
    <?php if ($data['count'] > 0): ?>
    <br />
    <form id="confirmreg" method="post" action="<?php echo URLROOT; ?>/adminconfirmusers?do=confirm">
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head">E-mail</th>
        <th class="table_head">Registered</th>
        <th class="table_head">IP</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = $data['res']->fetch(PDO::FETCH_LAZY)): ?>
    <tr>
        <td class="table_col1" align="center"><?php echo Users::coloredname($row["username"]); ?></td>
        <td class="table_col2" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col1" align="center"><?php echo TimeDate::utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile;?>
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
    if ($data['count'] > 25) {
        echo $data['pagerbottom'];
    }
    Style::end();