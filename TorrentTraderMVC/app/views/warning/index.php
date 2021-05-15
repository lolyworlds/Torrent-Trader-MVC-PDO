<div class="card">
<div class="card-header">
    <?php echo $data['title']; ?>
</div>
<div class="card-body">
<?php if ($_SESSION["edit_users"] == "yes") {?>
    <?php usermenu($data['id']);?>
    <?php if ($data['res']->rowCount() > 0) {?>
		<br><center><b>Warnings:</b></center><br>
        <div class='table-responsive'><table class='table table-striped'>
        <thead><tr>
		<th class="table_head">Added</th>
		<th class="table_head"><?php echo Lang::T("EXPIRE"); ?></th>
		<th class="table_head"><?php echo Lang::T("REASON"); ?></th>
		<th class="table_head"><?php echo Lang::T("WARNED_BY"); ?></th>
		<th class="table_head"><?php echo Lang::T("TYPE"); ?></th>
		</tr></thead>
        <?php
    while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
        if ($arr["warnedby"] == 0) {
            $wusername = Lang::T("SYSTEM");
        } else {
        $res2 = DB::run("SELECT id,username FROM users WHERE id =?", [$arr['warnedby']]);
        $arr2 = $res2->fetch();
        $wusername = Helper::userColour($arr2["username"]);
        }
        $arr['added'] = TimeDate::utc_to_tz($arr['added']);
        $arr['expiry'] = TimeDate::utc_to_tz($arr['expiry']);
        $addeddate = substr($arr['added'], 0, strpos($arr['added'], " "));
        $expirydate = substr($arr['expiry'], 0, strpos($arr['expiry'], " "));
        print("<tbody><tr><td class='table_col1' align='center'>$addeddate</td><td class='table_col2' align='center'>$expirydate</td><td class='table_col1'>" . format_comment($arr['reason']) . "</td><td class='table_col2' align='center'><a href='" . URLROOT . "/accountdetails?id=" . $arr2['id'] . "'>" . $wusername . "</a></td><td class='table_col1' align='center'>" . $arr['type'] . "</td></tr></tbody>\n");
    }
    echo "</table></div>\n";
} else {
    echo '<br><center><b>' . Lang::T("NO_WARNINGS") . '</b><center><br>';
}
?>
    <br><form method='post' action='<?php echo URLROOT; ?>/warning/submit'>
    <input type='hidden' name='userid' value='<?php echo $data['id']; ?>'>
    <center><table>
    <tr><td><b><?php echo Lang::T("REASON"); ?>:</b> </td><td><textarea class="form-control" cols='40' rows='5' name='reason'></textarea></td></tr>
    <tr><td><b><?php echo Lang::T("EXPIRE"); ?>:</b> </td><td><input class="form-control" type='text' size='4' name='expiry' />(days)</td></tr>
    <tr><td><b><?php echo Lang::T("TYPE"); ?>:</b> </td><td><input class="form-control" type='text' size='10' name='type' /></td></tr>
    </table></center>
    <br><center><button type='submit' class='btn btn-sm btn-success'><b><?php echo Lang::T("ADD_WARNING"); ?></b></button></center><br>
    </form>
    
<?php if ($_SESSION["delete_users"] == "yes") {?>
    <center><form method='post' action='<?php echo URLROOT; ?>/warning/deleteaccount'>
    <input type='hidden' name='userid' value='<?php echo $data['id']; ?>' />
    <input type='hidden' name='username' value='<?php echo $data["username"]; ?>' />
    <br><b><?php echo Lang::T("REASON"); ?>:</b><input class="form-control" type='text' size='30' name='delreason' /><br>
    <button type='submit' class='btn btn-sm btn-danger'><b><?php echo Lang::T("DELETE_ACCOUNT"); ?></b></button>
     </form></center><br>
    <a href='<?php echo URLROOT; ?>/profile?id=<?php echo $data['id']; ?>'><button type='submit' class='btn btn-sm'><b>Back To Account</b></button></a></center>
<?php }
} ?>
</div>
</div>