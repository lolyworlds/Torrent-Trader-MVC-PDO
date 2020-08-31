<?php
usermenu($_SESSION["id"]);
include 'views/message/messagenavbar.php';
?>
            <form id='messagespy' method='post' action='messages&amp;do=del'>


            <div class='table-responsive'><table class='table table-striped'>
                   <thead>
                   <tr>
                   <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
                   <th>Receiver</th>
                   <th>Subject</th>
                   <th>Date</th></tr></thead>
<?php
foreach ($res as $arr) {

    $res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr["receiver"]]);

    if ($arr2 = $res2->fetch()) {
        $receiver = "<a href='" . TTURL . "/users/profile?id=" . $arr["receiver"] . "'><b>" . class_user_colour($arr2["username"]) . "</b></a>";
    } else {
        $receiver = "<i>Deleted</i>";
    }

    $subject = "<a href='" . TTURL . "/messages/read?draft&amp;id=" . $arr["id"] . "'><b>" . format_comment($arr["subject"]) . "</b></a>";
    //$subject = "<a href=\"javascript:read($arr[id]);\"><img src=\"".$config["SITEURL"]."/images/plus.gif\" id=\"img_$arr[id]\" class=\"read\" border=\"0\" alt='' /></a>&nbsp;<a href=\"javascript:read($arr[id]);\">$subject</a>";

    $added = utc_to_tz($arr["added"]);

    ?>
        <tbody><tr>
        <td><input type='checkbox' name='del[]' value='<?php echo $arr['id']; ?>' /></td>
        <td><?php echo $receiver; ?></td>
        <td><?php echo $subject; ?></td>
        <td><?php echo $added; ?></td></tr>
        <?php
}?>

    <tbody></table></div>
    <center><input type='submit' value='Delete Checked' /></center></form>