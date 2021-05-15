<div class="card">
        <div class="card-header">
            Read
        </div>
        <div class="card-body">

<?php include APPROOT.'/views/message/messagenavbar.php'; ?>
<center><b><?php echo $data['subject']; ?></b></center><br>
<div class='table'><table class='table table-striped'><thead>
    <tr><th width='150'><?php echo $data['lastposter']; ?></th><th align='left'><small>Posted at <?php echo $data['added']; ?> </small></th></tr></thead><tbody>
    <tr valign='top'><td width='20%' align='left'>
    <center><?php echo $data['button']; ?></center></td>
    <td><br /><?php echo format_comment($data['msg']); ?></td></tr>
    <tbody></table></div>

    <br><center> History </center><br>
    <?php
    foreach ($data['arr4'] as $row) {
    $arr3 = DB::run("SELECT username FROM users WHERE id=?", [$row["sender"]])->fetch();
    $sender = "<a href='" . URLROOT . "/profile?id=" . $row["sender"] . "'><b>" . Users::coloredname($arr3["username"]) . "</b></a>";
    if ($row["sender"] == 0) {
        $sender = "<font class='error'><b>System</b></font>";
    }
    $added = TimeDate::utc_to_tz($row["added"]);?>
    <div class='table'><table class='table table-striped'><thead>
    <tr><th width='150'><?php echo $sender; ?></th><th align='left'><small>Posted at <?php echo $added; ?> </small></th></tr></thead><tbody>
    <tr valign='top'><td width='20%' align='left'>
    <td><br /><?php echo format_comment($row['msg']); ?></td></tr>
    <tbody></table></div>
     <?php
} ?>
</div>
</div><br />