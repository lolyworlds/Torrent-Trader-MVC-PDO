<?php
Style::begin("The Dead Torrents");
echo "<div style='margin-top:10px' align='center'><font size='4'>List of dead torrents without being shared and sorted by date of sharing </font></div>";
echo "<hr class='barre'/><div style='margin-top:16px; margin-bottom:20px' align='center'><font size='4'>We have<font color='IndianRed'><b>$data[count]</b></font> DEAD" . ($data['count'] != 1 ? "s" : "") . " torrent" . ($data['count'] != 1 ? "s" : "") . "</font></div>";
if ($data['count'] > $data['perpage']) {
    print($data['pagertop']);
    print("<br />");
}
?>
<form id="myform" method='post' action='<?php echo URLROOT; ?>/peers/dead.php'>
<input type='hidden' name='do' value='delete' />
<div class='table-responsive'><table class='table table-striped'>
    <thead><tr>
		<th class="table_head" align="left"><?php echo Lang::T("TORRENT_NAME"); ?></th>
		<th class="table_head" align="left"><?php echo Lang::T("UPLOADER"); ?></th>
		<th class="table_head"><?php echo Lang::T("SIZE"); ?></th>
		<th class="table_head"><img src="<?php echo URLROOT; ?>/assets/images/seed.gif" border="0" title="Seeders"></th>
		<th class="table_head"><img src="<?php echo URLROOT; ?>/assets/images/leech.gif" border="0" title="Leechers"></th>
		<th class="table_head"><img src="<?php echo URLROOT; ?>/assets/images/check.png" border="0" title="Completed"></th>
		<th class="table_head" width="1%"><?php echo Lang::T("ADDED"); ?></th>
		<th class="table_head" width="1%"><?php echo Lang::T("LAST_ACTION"); ?></th>
<?php if ($_SESSION["class"] >= 6) {?>
		<th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
<?php }?>
	</tr></thead>
<?php
while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    if ($row["username"]) {
        $owner = "<a href='" . URLROOT . "/profile?id=" . $row["owner"] . "'><b>" . $row["username"] . "</b></a>";
    } else {
        $owner = Lang::T("UNKNOWN_USER");
    }
    ?>
    <tbody>
	<tr>
		<td class="table_col1"><a href="<?php echo URLROOT; ?>/torrents/read?id=<?php echo $row["id"]; ?>"><?php echo CutName(htmlspecialchars($row["name"]), 50) ?></a></td>
		<td class="table_col2"><?php echo $owner; ?></td>
		<td class="table_col2" align="center"><?php echo mksize($row["size"]); ?></td>
		<td class="table_col1" align="center"><font color="limegreen"><b><?php echo number_format($row["seeders"]); ?></b></font></td>
		<td class="table_col2" align="center"><font color="red"><b><?php echo number_format($row["leechers"]); ?></b></font></td>
		<td class="table_col1" align="center"><font color="#0080FF"><b><?php echo number_format($row["times_completed"]); ?></b></font></td>
		<td class="table_col2" align="center"><?php echo date("d.M.Y H:i", TimeDate::utc_to_tz_time($row["added"])) ?></td>
		<td class="table_col2" align="center"><?php echo date("d.M.Y H:i", TimeDate::utc_to_tz_time($row["last_action"])) ?></td>
<?php if ($_SESSION["class"] >= 6) {?>
		<td class='table_col2' align='center'><input type='checkbox' name='torrentids[]' value='<?php echo $row["id"]; ?>' /></td>
<?php }?>
    </tr>
<?php
}
?>
</tbody></table></div>
<?php if ($_SESSION["class"] >= 6) {?>
    <button type="submit" class="btn btn-sm btn-primary" value='Remove The Checks '>Remove The Checks</button>
<?php }?>
</form>
 <?php
if ($data['count'] > $data['perpage']) {
    print($data['pagerbottom']);
} else {
    print("<br />");
}
Style::end();