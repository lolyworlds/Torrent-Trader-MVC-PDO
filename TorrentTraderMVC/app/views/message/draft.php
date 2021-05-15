<div class="card">
<div class="card-header">
    <?php echo $data['pagename']; ?>
</div>
    <div class="card-body">
    <?php include APPROOT.'/views/message/messagenavbar.php'; ?>
    <form id='messagespy' method='post' action='<?php echo URLROOT; ?>/messages/draft?do=del'>
    <div class='table-responsive'><table class='table table-striped'>
        <thead>
        <tr>
        <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
        <th>Receiver</th>
        <th>Subject</th>
        <th>Date</th></tr></thead>
<?php
foreach ($data['res'] as $arr) {
    $res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr["receiver"]]);
    if ($arr2 = $res2->fetch()) {
        $receiver = "<a href='" . URLROOT . "/profile?id=" . $arr["receiver"] . "'><b>" . Users::coloredname($arr2["username"]) . "</b></a>";
    } else {
        $receiver = "<i>Deleted</i>";
    }
    $subject = "<a href='" . URLROOT . "/messages/read?draft&amp;id=" . $arr["id"] . "'><b>" . format_comment($arr["subject"]) . "</b></a>";
    //$subject = "<a href=\"javascript:read($arr[id]);\"><img src=\"".URLROOT."/assets/images/plus.gif\" id=\"img_$arr[id]\" class=\"read\" border=\"0\" alt='' /></a>&nbsp;<a href=\"javascript:read($arr[id]);\">$subject</a>";
    $added = TimeDate::utc_to_tz($arr["added"]);

    ?>
        <tbody><tr>
        <td><input type='checkbox' name='del[]' value='<?php echo $arr['id']; ?>' /></td>
        <td><?php echo $receiver; ?></td>
        <td><?php echo $subject; ?></td>
        <td><?php echo $added; ?></td></tr>
        <?php
}?>
<?php echo $data['pagerbottom']; ?>
    <tbody></table></div>
    <center><button type="submit" class="btn btn-primary" value='Delete Checked' />Delete Checked</button></center>
    </div>
</div><br />