<?php
Style::begin("Friends");
usermenu($data['userid']);
echo '<br><br>';
Style::begin("Friend lists for " . Users::coloredname($data['username']) . "");
?>
<div class="row">
<?php
if ($data['friend']->rowCount() == 0) {
?>&nbsp;&nbsp;
<div><center><b>Friend list is empty!</b></center></div>
<?php
} else {
    while ($friend = $data['friend']->fetch(PDO::FETCH_ASSOC)) {
        $avatar = htmlspecialchars($friend["avatar"]);
        if (!$avatar) {
            $avatar = "".URLROOT."/assets/images/default_avatar.png";
        }
?>
<div class="col-md-4 mt-3">
<?php
echo "<img width=80px src=\"$avatar\">&nbsp;<a href=" . URLROOT . "/profile?id=" . $friend['id'] . "><b>" . Users::coloredname($friend['name']) . "</b></a> &nbsp;
<a href=" . URLROOT . "/messages/create?id=" . $friend['id'] . "><img src=" . URLROOT . "/assets/images/button_pm.gif title=Send&nbsp;PM border=0></a>&nbsp;
<a href=" . URLROOT . "friends/delete?id=$data[userid]&type=friend&targetid=" . $friend['id'] . "><img src=" . URLROOT . "/assets/images/delete.png title=Remove border=0></a>
<div style='margin-top:10px; margin-bottom:2px'>Last seen: " . date("<\\b>d.M.Y<\\/\\b> H:i", TimeDate::utc_to_tz_time($friend['last_access'])) . "</div>
[<b>" . TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($friend['last_access'])) . " ago</b>]";
?>
</div>
<?php
    }
}
?>
</div>
<?php
Style::end();
Style::begin("Unfriended lists for " . Users::coloredname($data['username']) . "");
?>
<div class="row">
<?php
if ($data['enemy']->rowCount() == 0) {
?>&nbsp;&nbsp;
<div><center><b>Unfriended list is empty!</b></center></div>
<?php
} else {
    while ($enemy = $data['enemy']->fetch(PDO::FETCH_ASSOC)) {
        $avatar = htmlspecialchars($enemy["avatar"]);
        if (!$avatar) {
            $avatar = "".URLROOT."/assets/images/default_avatar.png";
        }
?>
<div class="col-md-4">
<?php
echo "<img width=80px src=\"$avatar\">&nbsp;<a href=" . URLROOT . "/profile?id=" . $enemy['id'] . "><b>" . Users::coloredname($enemy['name']) . "</b></a> &nbsp;
<a href=" . URLROOT . "/messages/create?id=" . $enemy['id'] . "><img src=" . URLROOT . "/assets/images/button_pm.gif title=Send&nbsp;PM border=0></a>&nbsp;
<a href=" . URLROOT . "friends/delete?id=$data[userid]&type=friend&targetid=" . $enemy['id'] . "><img src=" . URLROOT . "/assets/images/delete.png title=Remove border=0></a>
<div style='margin-top:10px; margin-bottom:2px'>Last seen: " . date("<\\b>d.M.Y<\\/\\b> H:i", TimeDate::utc_to_tz_time($enemy['last_access'])) . "</div>
[<b>" . TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($enemy['last_access'])) . " ago</b>]";
?>
</div>
<?php
    }
}
?>
</div>
<?php
Style::end();
Style::end();