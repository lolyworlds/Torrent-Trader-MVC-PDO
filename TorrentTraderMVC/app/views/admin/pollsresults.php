<table class=\"table_table\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"95%\">
<tr>
<th class="table_head">Username</th>
<th class="table_head">Question</th>
<th class="table_head">Voted</th>
</tr>
<?php while ($res = $data['poll']->fetch(PDO::FETCH_LAZY)) {
        $user = DB::run("SELECT username,id FROM users WHERE id =?", [$res['userid']])->fetch();
        $option = "option" . $res["selection"];
        if ($res["selection"] < 255) {
            $vote = DB::run("SELECT " . $option . " FROM polls WHERE id =?", [$res['pollid']])->fetch();
        } else {
            $vote["option255"] = "Blank vote";
        }
        $sond = DB::run("SELECT question FROM polls WHERE id =?", [$res['pollid']])->fetch();
?>
<tr>
<td class="table_col1" align="left"><b>
<a href="<?php echo URLROOT; ?>/profile?id=<?php echo $user["id"]; ?>">
&nbsp;&nbsp;<?php echo Users::coloredname($user['username']); ?>
</a>
</b></td>
<td class="table_col2" align="center">
&nbsp;&nbsp;<?php echo $sond['question']; ?>
</td>
<td class="table_col1" align="center">
<?php echo $vote["$option"]; ?>
</td>
</tr>
<?php  } ?>
</table>