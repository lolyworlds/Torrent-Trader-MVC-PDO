<div class="card">
<div class="card-header">
    Templates
</div>
    <div class="card-body">
<?php include APPROOT.'/views/message/messagenavbar.php'; ?>
    <form id='messagespy' method='post' action='<?php echo URLROOT; ?>/messages/templates?do=del'>
    <div class='table-responsive'><table class='table table-striped'>
    <thead>
    <tr>
    <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
    <th>Subject</th>
    <th>Date</th></tr></thead>
<?php
while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    $res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr["receiver"]]);
    $subject = "<a href='" . URLROOT . "/messages/read?templates&amp;id=" . $arr["id"] . "'><b>" . format_comment($arr["subject"]) . "</b></a>";
    $added = TimeDate::utc_to_tz($arr["added"]);
    ?>
        <tbody><tr>
        <td><input type='checkbox' name='del[]' value='<?php echo $arr['id']; ?>' /></td>
        <td><?php echo $subject; ?></td>
        <td><?php echo $added; ?></td></tr>
        <?php
}?>
    <tbody></table></div>
    <center><a href='<?php echo URLROOT; ?>/messages/create'>
    <button type='button' class='btn btn-sm btn-success'><b>Make New Template</b></button></a>
    <button type="submit" class="btn btn-sm btn-primary" value='Delete Checked' />Delete Checked</button></center>
    </form>
            </div>
</div><br />