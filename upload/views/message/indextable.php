<?php
usermenu($_SESSION["id"]);
include 'views/message/messagenavbar.php';
?>
            <form id='messages' method='post' action='<?php echo TTURL; ?>/messages/inbox?do=del'>


            <div class='table-responsive'><table class='table table-striped'>
                   <thead>
                   <tr>
                   <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
                   <th>Read</th>
                   <th>Sender</th>
                   <th>Receiver</th>
                   <th>Subject</th>
                   <th>Date</th></tr></thead>
<?php
while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {

    if ($arr["sender"] == $_SESSION['id']) {
        $sender = "Yourself";
    } elseif (is_valid_id($arr["sender"])) {
        $res2 = DB::run("SELECT username FROM users WHERE `id` = $arr[sender]");
        $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
        $sender = "<a href=\"/users/profile?id=$arr[sender]\">" . ($arr2["username"] ? class_user_colour($arr2["username"]) : "[Deleted]") . "</a>";
    } else {
        $sender = T_("SYSTEM");
    }

//    $sender = $arr['sendername'];

    if ($arr["receiver"] == $_SESSION['id']) {
        $receiver = "Yourself";
    } elseif (is_valid_id($arr["receiver"])) {
        $res2 = DB::run("SELECT username FROM users WHERE `id` = $arr[receiver]");
        $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
        $receiver = "<a href=\"/users/profile?id=$arr[receiver]\">" . ($arr2["username"] ? class_user_colour($arr2["username"]) : "[Deleted]") . "</a>";
    } else {
        $receiver = T_("SYSTEM");
    }

    $subject = "<a href='" . TTURL . "/messages/read?inbox&amp;id=" . $arr["id"] . "'><b>" . format_comment($arr["subject"]) . "</b></a>";
    $added = utc_to_tz($arr["added"]);

    if ($arr["unread"] == "yes") {
        $unread = "<img src='" . TTURL . "/themes/default/forums/folder_new.png' alt='read' width='25' height='25'>";
    } else {
        $unread = "<img src='" . TTURL . "/themes/default/forums/folder.png' alt='unread' width='25' height='25'>";
    }

    ?>
        <tbody><tr>
        <td><input type='checkbox' name='del[]' value='<?php echo $arr['id']; ?>' /></td>
        <td><?php echo $unread; ?></td>
        <td><?php echo $sender; ?></td>
        <td><?php echo $receiver; ?></td>
        <td><?php echo $subject; ?></td>
        <td><?php echo $added; ?></td></tr>
        <?php
}?>

    <tbody></table></div>
    <?php echo '<div style="float: left;">read&nbsp;<img src="' . $config["SITEURL"] . '/themes/' . ($_SESSION['stylesheet'] ?: $config['default_theme']) . '/forums/folder.png" alt="read" width="20" height="20">&nbsp;unread&nbsp;<img src="' . $config["SITEURL"] . '/themes/' . ($_SESSION['stylesheet'] ?: $config['default_theme']) . '/forums/folder_new.png" alt="unread" width="20" height="20"></div>'; ?>
    <center><input type='submit' value='Delete Checked' /> <input type='submit' value='Read Checked' name='read' /></center></form>