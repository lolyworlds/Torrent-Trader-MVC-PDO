<?php
if ($data['id'] != $_SESSION["id"]) {
    Style::begin("Invite Tree for [<a href=".URLROOT."/profile?id=$data[id]>" . $data['id'] . "</a>]");
} else {
    Style::begin("You have $data[invitees] invitees " . Users::coloredname($_SESSION["username"]) . "");
}
    ?><br />
    <div class='table-responsive'> <table class='table table-striped'><thead><tr>
	<th><b>Invited&nbsp;Members</b></th>
    <th><b>Class</b></th>
    <th><b>Registered</b></th>
	<th><b>Last&nbsp;access</b></th>
    <th><b>Downloaded</b></th>
	<th><b>Uploaded<b></th>
	<th><b>Ratio</b></th>
	<th><b>Warned</b></th>
	</tr></thead> <?php
for ($i = 1; $i <= $data['num']; $i++) {
    $arr = $data['res']->fetch(PDO::FETCH_ASSOC);
    if ($arr["invited_by"] != $_SESSION['id'] && $_SESSION["class"] < 5) {
        Session::flash('info', "<b>Access Denied</b>", URLROOT . "/profile?id=$data[id]");
    }
    if ($arr['added'] == '0000-00-00 00:00:00') {
        $arr['added'] = '---';
    }
    if ($arr['last_access'] == '0000-00-00 00:00:00') {
        $arr['last_access'] = '---';
    }
    if ($arr["downloaded"] != 0) {
        $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
    } else {
        $ratio = "---";
    }
    $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
    if ($arr["warned"] !== "yes") {
        $warned = "<font color=limegreen><b>No</b></font>";
    } else {
        $warned = "<font color=red><b>Yes</b></font>";
    }
    $class = get_user_class_name($arr["class"]);
    $added = substr($arr['added'], 0, 10);
    $last_access = substr($arr['last_access'], 0, 10);
    $downloaded = mksize($arr["downloaded"]);
    $uploaded = mksize($arr["uploaded"]);
    ?>
    <tbody><tr>
    <td><a href='<?php echo URLROOT; ?>/profile?id=$arr[id]'><b><?php echo Users::coloredname($arr['username']); ?></b></a></td>
	<td><?php echo $class; ?></td>
	<td><?php echo $added; ?></td>
	<td><?php echo $last_access; ?></td>
	<td><?php echo $downloaded; ?></font></td>
	<td><?php echo $uploaded; ?></font></td>
	<td><?php echo $ratio; ?></td>
	<td><?php echo $warned; ?></td>
    </tr></tbody>
    </table></div>
    <?php
}

if ($arr["invited_by"] != $_SESSION['id']) {
    print("<div style='margin-top:10px' align='center'>[<a href=" . URLROOT . "/profile?id=$data[id]><b>Go Back to User Profile</b></a>]</div>");
}
print("<br />"); //one small space here!
Style::end();