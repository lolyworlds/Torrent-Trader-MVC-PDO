<?php
        usermenu($CURUSER["id"]);
        include 'views/message/messagenavbar.php';

    ?>
    <div class='table'><table class='table table-striped'><thead>
    <tr><th width='150'><?php echo $lastposter; ?></th><th align='left'><small>Posted at <?php echo $arr['added']; ?> </small></th></tr></thead><tbody>
    <tr valign='top'><td width='20%' align='left'>
    <center><?php echo $button; ?></center></td>
    <td><br /><?php echo format_comment($arr['msg']); ?></td></tr>
    <tbody></table></div>