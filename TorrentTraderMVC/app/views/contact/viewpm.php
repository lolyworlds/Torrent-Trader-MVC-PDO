<?php
Style::begin("Messages to staff");
?>
<table class='table table-striped table-bordered table-hover'><thead><tr><td>     
From <b><?php echo $data['sender']; ?></b> at <?php echo $data["added"]; ?> (<?php $data['elapsed']; ?> ago) GMT <br><br>
<b>Subject: <?php echo $data['subject']; ?></b><br>
<b>Answered:</b> <?php echo $data['answered']; ?>&nbsp;&nbsp;<?php echo $data['setanswered']; ?><br><br>
<?php echo format_comment($data["msg"]); ?><br><br>
<?php
print(($data["sender1"] ? "<a href=" . URLROOT . "/contactstaff/answermessage?receiver=" . $data["sender1"] . "&answeringto=$data[iidee]><b>Reply</b></a>" : "<font class=gray><b>Reply</b></font>") . " | <a href=" . URLROOT . "/contactstaff/deletestaffmessage?id=" . $data["id"] . "><b>Delete</b></a></td>");
?>
</td></tr></table>
<?php
Style::end();