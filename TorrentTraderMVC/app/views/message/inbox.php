<?php
Style::begin(Lang::T($data['pagename']));
?>
    <?php include APPROOT.'/views/message/messagenavbar.php'; ?>
    <form id='messages' method='post' action='<?php echo URLROOT; ?>/messages/inbox?do=del'>
    <div class='table-responsive'><table class='table table-striped'>
    <thead><tr>
    <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
    <th>Read</th>
    <th>Sender</th>
    <th>Receiver</th>
    <th>Subject</th>
    <th>Date</th></tr></thead>
<?php
while ($arr = $data['mainsql']->fetch(PDO::FETCH_ASSOC)) {
    $msgdetails = Helper::msginboxdetails($arr); ?>
        <tbody><tr>
        <td><input type='checkbox' name='del[]' value='<?php echo $arr['id']; ?>' /></td>
        <td><?php echo $msgdetails['3']; ?></td>
        <td><?php echo $msgdetails['0']; ?></td>
        <td><?php echo $msgdetails['1']; ?></td>
        <td><?php echo $msgdetails['2']; ?></td>
        <td><?php echo $msgdetails['4']; ?></td></tr>
<?php }?>
    <tbody></table></div>
<?php echo '<div style="float: left;">read&nbsp;<img src="' . URLROOT . '/assets/images/forum/folder.png" alt="read" width="20" height="20">&nbsp;unread&nbsp;<img src="' . URLROOT . '/assets/images/forum/folder_new.png" alt="unread" width="20" height="20"></div>'; ?>
<center><button type="submit" class="btn btn-primary" value='Delete Checked' />Delete Checked</button>  <button type="submit" class="btn btn-primary" value='Read Checked' name='read' />Read Checked</button></center>
</form>
<?php echo $data['pagerbottom'];
Style::end();
