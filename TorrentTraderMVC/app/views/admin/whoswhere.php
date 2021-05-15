<table class='table table-striped table-bordered table-hover'><thead>
<tr>
    <th class="table_head">Username</th>
    <th class="table_head">Page</th>
    <th class="table_head">Accessed</th>
</tr></thead><tbody>
<?php while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)): ?>
<tr>
    <td class="table_col1" align="center"><a href="<?php echo URLROOT; ?>/users/profile?id=<?php echo $row["id"]; ?>"><b><?php echo Users::coloredname($row["username"]); ?></b></a></td>
    <td class="table_col2" align="center"><?php echo htmlspecialchars($row["page"]); ?></td>
    <td class="table_col1" align="center"><?php echo TimeDate::utc_to_tz($row["last_access"]); ?></td>
</tr>
<?php endwhile;?>
</tbody></table>