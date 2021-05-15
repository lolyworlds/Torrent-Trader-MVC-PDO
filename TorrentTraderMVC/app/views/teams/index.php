
        <div class="card">
        <div class="card-header">
        Teams View
        </div>
        <div class="card-body">
        <center>Please <a href="<?php echo URLROOT ?>/staff">contact</a>
        a member of staff if you would like a new team creating</center><br>

 <?php  while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)): ?>

        <div class='table-responsive'><table class='table table-striped'>
        <thead><tr>
        <th></th>
        <th>Owner: <?php echo ($row["username"]) ? '<a href="'.URLROOT.'/profile?id=' . $row["owner"] . '">' . Users::coloredname($row["username"]) . '</a>' : "Unknown User"; ?> - Added: <?php echo TimeDate::utc_to_tz($row["added"]); ?></th>
        </tr></thead>
        <tbody><tr>
        <td><img src="<?php echo htmlspecialchars($row["image"]); ?>" border="0" alt="<?php echo htmlspecialchars($row["name"]); ?>" title="<?php echo htmlspecialchars($row["name"]); ?>" /></td>
        <td><b>Name:</b><?php echo htmlspecialchars($row["name"]); ?><br /><b>Info:</b> <?php echo format_comment($row["info"]); ?></td>
        </tr>
        <tr>
        <td class="table_col1" colspan="2">
        <b>Members:</b>
        <?php foreach (explode(',', $row['members']) as $member): $member = explode(" ", $member);?>
	    <a href="<?php echo URLROOT ?>/profile?id=<?php echo $member[0]; ?>"><?php echo htmlspecialchars($member[1]); ?></a>,
	    <?php endforeach;?>
        </td>
        </tr><tbody>
        </table></div>
		<br />
		<?php endwhile; ?>
        </div>
        </div>