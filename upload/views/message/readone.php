<?php
       usermenu($CURUSER["id"]);
        include 'views/message/messagenavbar.php';
    ?><br>
    <div class='table'><table class='table table-striped'><thead>
    <tr><th width='150'><?php echo $lastposter; ?></th><th align='left'><small>Posted at <?php echo $arr['added']; ?> </small></th></tr></thead><tbody>
    <tr valign='top'><td width='20%' align='left'>
    <center><?php echo $button; ?></center></td>
    <td><br /><?php echo format_comment($arr['msg']); ?></td></tr>
    <tbody></table></div>

    <br><center><h1> History</h1></center><br>
    <?php
              foreach ($arr4 as $row) {
                  $arr3 = DB::run("SELECT username FROM users WHERE id=?", [$row["sender"]])->fetch();
    
                  $sender = "<a href='" . TTURL . "/users/profile?id=" . $row["sender"] . "'><b>" . class_user_colour($arr3["username"]) . "</b></a>";
                  if ($row["sender"] == 0) {
                    $sender = "<font class='error'><b>System</b></font>";
                  }
                  $added = utc_to_tz($row["added"]); ?>
    <div class='table'><table class='table table-striped'><thead>
    <tr><th width='150'><?php echo $sender; ?></th><th align='left'><small>Posted at <?php echo $added; ?> </small></th></tr></thead><tbody>
    <tr valign='top'><td width='20%' align='left'>
    <td><br /><?php echo format_comment($row['msg']); ?></td></tr>
    <tbody></table></div>
     <?php
              }