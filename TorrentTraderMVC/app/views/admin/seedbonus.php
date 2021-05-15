<center>
This page displays all available options trade which users can exchange for seedbonus <?php echo number_format($data['count']); ?>
</center>
<center>
<a href='<?php echo  URLROOT; ?>/adminseedbonus/change?id=null'>Add</a> a new option?
</center>
<?php if ($data['count'] > 0): ?>
<form id="seedbonus" method="post" action="<?php echo URLROOT; ?>/adminseedbonus">
<input type="hidden" name="do" value="del" />
<div class='table-responsive'> <table class='table table-striped'><thead><tr>
    <th>Title</th>
    <th>Description</th>
    <th>Points</th>
    <th>Value</th>
    <th>Type</th>
    <th>Edit</th>
    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
</tr></thead>
<?php while ($row = $data['res']->fetch(PDO::FETCH_LAZY)):
    $row->value = ($row->type == "traffic") ? mksize($row->value) : (int) $row->value;
    ?>
    <tbody><tr>
    <td><?php echo htmlspecialchars($row->title); ?></td>
    <td><?php echo htmlspecialchars($row->descr); ?></td>
    <td><?php echo $row->cost; ?></td>
    <td><?php echo $row->value; ?></td>
    <td><?php echo $row->type; ?></td>
    <td><a href="<?php echo URLROOT; ?>/adminseedbonus/change?id=<?php echo $row->id; ?>">Edit</a></td>
    <td><input type="checkbox" name="ids[]" value="<?php echo $row->id; ?>" /></td>
    </tr></tbody>
    <?php endwhile;?>
  </table>
<ul>
    <li><input type="submit" value="Remove Selected" /></li>
    </ul>
</form>
</div>
<?php
endif;
if ($data['count'] > 25) {
    echo $data['pagerbottom'];
}