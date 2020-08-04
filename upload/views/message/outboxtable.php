<?php
            include 'views/message/usernavbar.php';
            include 'views/message/messagenavbar.php';
            ?>
            <form id='messagespy' method='post' action='messages&amp;do=del'>
            
                   
            <div class='table-responsive'><table class='table table-striped'>
                   <thead>
                   <tr>
                   <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
                   <th>Read</th>
                   <th>Receiver</th>
                   <th>Subject</th>
                   <th>Date</th></tr></thead>
<?php
while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {

    if ($arr["receiver"] == $CURUSER['id']) 
    $receiver = "Yourself";
elseif (is_valid_id($arr["receiver"]))
{
    $res2 = DB::run("SELECT username FROM users WHERE `id` = $arr[receiver]");
    $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
    $receiver = "<a href=\"users/profile?id=$arr[receiver]\">".($arr2["username"] ? class_user_colour($arr2["username"]) : "[Deleted]")."</a>";
}
else $receiver = T_("SYSTEM");

            $subject = "<a href='" . TTURL . "/messages/read?outbox&amp;id=" . $arr["id"] . "'><b>" . format_comment($arr["subject"]) . "</b></a>";
            //$subject = "<a href=\"javascript:read($arr[id]);\"><img src=\"".$site_config["SITEURL"]."/images/plus.gif\" id=\"img_$arr[id]\" class=\"read\" border=\"0\" alt='' /></a>&nbsp;<a href=\"javascript:read($arr[id]);\">$subject</a>";

            $added = utc_to_tz($arr["added"]);

            if ($arr["unread"] == "yes") {
                $unread = "<img src='" . TTURL . "/views/themes/default/forums/folder_new.png' alt='read' width='25' height='25'>";
            } else {
                $unread = "<img src='" . TTURL . "/views/themes/default/forums/folder.png' alt='unread' width='25' height='25'>";
            }

            ?>
        <tbody><tr>
        <td><input type='checkbox' name='del[]' value='<?php echo $arr['id']; ?>' /></td>
        <td><?php echo $unread; ?></td>
        <td><?php echo $receiver; ?></td>
        <td><?php echo $subject; ?></td>
        <td><?php echo $added; ?></td></tr>
        <?php
}?>

    <tbody></table></div>
    <?php echo '<div style="float: left;">read&nbsp;<img src="'.$site_config["SITEURL"].'/views/themes/default/forums/folder.png" alt="read" width="20" height="20">&nbsp;unread&nbsp;<img src="'.$site_config["SITEURL"].'/views/themes/default/forums/folder_new.png" alt="unread" width="20" height="20"></div>'; ?>
    <center><input type='submit' value='Delete Checked' /></center></form>