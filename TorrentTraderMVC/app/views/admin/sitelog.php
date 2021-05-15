<form method='get' action='?'><center>
<input type='hidden' name='action' value='sitelog' />
<?php echo Lang::T("SEARCH"); ?>: <input type='text' size='30' name='search' />
<input type='submit' value='Search' />
</center></form><br>
<form id='sitelog' action='<?php echo URLROOT; ?>/adminsitelog' method='post'>
<table class='table table-striped table-bordered table-hover'><thead>
<tr>
    <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id)" /></th>
    <th class="table_head">Date</th>
    <th class="table_head">Time</th>
    <th class="table_head">Event</th>
</tr></thead<tbody>
<?php while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
            $arr['added'] = TimeDate::utc_to_tz($arr['added']);
            $date = substr($arr['added'], 0, strpos($arr['added'], " "));
            $time = substr($arr['added'], strpos($arr['added'], " ") + 1);
?>
<tr><td class='table_col2' align='center'>
<input type='checkbox' name='del[]' value='<?php echo $arr['id']; ?>' /></td>
<td class='table_col1' align='center'><?php echo $date; ?></td>
<td class='table_col2' align='center'><?php echo $time; ?></td>
<td class='table_col1' align='left'><?php echo stripslashes($arr["txt"]); ?></td>
<?php }
echo '</tbody></table>';
echo "<input type='submit' value='Delete Checked' /> <input type='submit' value='Delete All' name='delall' /></form>";
print($data['pagerbottom']);