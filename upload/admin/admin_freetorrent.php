<?php

if ($action=="freetorrents"){
    
    /*
    * Todo:
    *  Optimize Query show freeleech ONLY!
    */
    
	stdhead("Free Leech ".T_("TORRENT_MANAGEMENT"));
	navmenu();

	$search = trim($_GET['search']);

	if ($search != '' ){
		$whereand = "AND name LIKE '%$search%";
	}

	$res2 = DB::run("SELECT COUNT(*) FROM torrents WHERE freeleech='1' $whereand");
	$row = $res2->fetch(PDO::FETCH_LAZY);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=freetorrents&amp;");

	begin_frame(T_("TORRENTS_FREE_LEECH"));

	print("<center><form method='get' action='?'>\n");
	print("<input type='hidden' name='action' value='freetorrents' />\n");
	print(T_("SEARCH").": <input type='text' size='30' name='search' />\n");
	print("<input type='submit' value='Search' />\n");
	print("</form></center>\n");

	echo $pagertop;
	?>
	<table align="center" cellpadding="0" cellspacing="0" class="table_table" width="100%" border="0">
	<tr> 
        <th class="table_head"><?php echo T_("NAME"); ?></th>
        <th class="table_head"><?php echo T_("VISIBLE"); ?></th>
        <th class="table_head"><?php echo T_("BANNED"); ?></th>
        <th class="table_head"><?php echo T_("SEEDERS"); ?></th>
        <th class="table_head"><?php echo T_("LEECHERS"); ?></th>
        <th class="table_head"><?php echo T_("EDIT"); ?></th>
	</tr>
	<?php
	$rqq = "SELECT id, name, seeders, leechers, visible, banned FROM torrents WHERE freeleech='1' $whereand ORDER BY name $limit";
	$resqq = DB::run($rqq);

	while ($row = $resqq->fetch(PDO::FETCH_LAZY)){
		
		$char1 = 35; //cut name length 
		$smallname = CutName(htmlspecialchars($row["name"]), $char1);

		echo "<tr><td class='table_col1'>" . $smallname . "</td><td class='table_col2'>$row[visible]</td><td class='table_col1'>$row[banned]</td><td class='table_col2'>".number_format($row["seeders"])."</td><td class='table_col1'>".number_format($row["leechers"])."</td><td class='table_col2'><a href=\"torrents-edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><font size='1' face='verdana'>EDIT</font></a></td></tr>\n";
	}

	echo "</table>\n";

	print($pagerbottom);

	end_frame();
	stdfoot();
}