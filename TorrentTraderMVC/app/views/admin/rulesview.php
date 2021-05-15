<center><a href='<?php echo URLROOT; ?>/adminrules/rulesaddsect'>Add New Rules Section</a></center>
<?php while ($arr = $data['res']->fetch(PDO::FETCH_LAZY)) { ?>
<div class="border border-warning">
<table width='100%' cellspacing='0' class='table_table'><tr>
<th class='table_head'><?php echo  $arr["title"]; ?></th>
</tr><tr><td class='table_col1'>
<form method='post' action='<?php echo URLROOT; ?>/adminrules/rulesedit'>
<?php echo format_comment($arr["text"]); ?>
</td></tr><tr><td class='table_head' align='center'><input type='hidden' value=<?php echo $arr['id']; ?> name='id' />
<input type='submit' value='Edit' /></form>
</td>
</tr></table>
</div><br>
<?php } ?>