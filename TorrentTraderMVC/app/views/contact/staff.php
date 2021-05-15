<?php
Style::begin('Staff PMs');
?>
<form method=post action="<?php echo URLROOT; ?>/contactstaff/takecontactanswered">
<table class='table table-striped table-bordered table-hover'><thead>
        <tr>
        <td class=colhead align=left>Subject</td>
        <td class=colhead align=left>Sender</td>
        <td class=colhead align=left>Added</td>
        <td class=colhead align=left>Answered</td>
        <td class=colhead align=center>Set Answered</td>
        <td class=colhead align=left>Del</td>
        </tr></thead><tbody>
  <?php while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
            if ($arr['answered']) {
                $res3 = DB::run("SELECT username FROM users WHERE id=$arr[answeredby]");
                $arr3 = $res3->fetch(PDO::FETCH_ASSOC);
                $answered = "<font color=green><b>Yes - <a href=".URLROOT."/users/read?id=$arr[answeredby]><b>" . Users::coloredname($arr3['username']) . "</b></a> (<a href=".URLROOT."/contactstaff/viewanswer?pmid=$arr[id]>View Answer</a>)</b></font>";
            } else {
                $answered = "<font color=red><b>No</b></font>";
            }
            $pmid = $arr["id"]; ?>
        <tr>
        <td><a href='<?php echo URLROOT; ?>/contactstaff/viewpm?pmid=<?php echo $pmid; ?>'><b><?php echo $arr['subject']; ?></b></td>
        <td><a href='<?php echo URLROOT; ?>/users/read?id=$arr[sender]'><b><?php echo Users::coloredname($arr['username']); ?></b></a></td>
        <td><?php echo $arr['added']; ?></td><td align=left><?php echo $answered; ?></td>
        <td><input type="checkbox" name="setanswered[]" value="<?php echo $arr['id']; ?>"></td>
        <td><a href='<?php echo URLROOT; ?>/contactstaff/deletestaffmessage?id=<?php echo $arr['id']; ?>''>Del</a></td>
        </tr></tbody>
   <?php } ?>
</table>
<div class="form-group">
     <button type="submit" class="btn btn-primary btn-block"><?php echo Lang::T("Confirm"); ?></button>
</div>
</form>
<?php
Style::end();