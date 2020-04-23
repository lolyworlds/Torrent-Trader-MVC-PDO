<?php

if ($action=="bannedtorrents"){
	stdhead("Banned Torrents");
	adminnavmenu();
		
	$res2 = DB::run("SELECT COUNT(*) FROM torrents WHERE banned=?", ['yes']);
	$row = $res2->fetch(PDO::FETCH_LAZY);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/admincp?action=bannedtorrents&amp;");

	begin_frame("Banned ".T_("TORRENT_MANAGEMENT"));

	print("<center><form method='get' action='?'>\n");
	print("<input type='hidden' name='action' value='bannedtorrents' />\n");
	print(T_("SEARCH").": <input type='text' size='30' name='search' />\n");
	print("<input type='submit' value='Search' />\n");
	print("</form></center>\n");

	echo $pagertop;
	?>
	<table align="center" cellpadding="0" cellspacing="0" class="table_table" width="100%" border="0">
	<tr>
	<th class="table_head"><?php echo T_("NAME"); ?></th>
	<th class="table_head">Visible</th>
	<th class="table_head">Seeders</th>
	<th class="table_head">Leechers</th>
	<th class="table_head">External?</th>
	<th class="table_head">Edit?</th>
	</tr>
	<?php
	$resqq = DB::run("SELECT id, name, seeders, leechers, visible, banned, external FROM torrents WHERE banned=? ORDER BY name", ['yes']);
	while ($row = $resqq->fetch(PDO::FETCH_ASSOC)){
		$char1 = 35; //cut name length
		$smallname = CutName(htmlspecialchars($row["name"]), $char1);

		echo "<tr><td class='table_col1'>" . $smallname . "</td><td class='table_col2'>$row[visible]</td><td class='table_col1'>".number_format($row["seeders"])."</td><td class='table_col2'>".number_format($row["leechers"])."</td><td class='table_col1'>$row[external]</td><td class='table_col2'><a href=\"torrents/edit?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><font size='1' face='verdana'>EDIT</font></a></td></tr>\n";
	}

	echo "</table>\n";

	print($pagerbottom);

	end_frame();
	stdfoot();
}
