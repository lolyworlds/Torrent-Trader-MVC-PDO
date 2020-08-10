<?php

if ($action=="peers"){
	$title = T_("Peers List");
    require 'views/admin/header.php';
	adminnavmenu();

	begin_frame("Peers List");

	$count1 = number_format(get_row_count("peers"));

	print("<center>We have $count1 peers</center><br />");

	$count = DB::run("SELECT COUNT(*) FROM peers $limit")->fetchColumn();
	$peersperpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($peersperpage, $count, "/admincp?action=peers&amp;");

	print("$pagertop");

	$result = DB::run("SELECT * FROM peers ORDER BY started DESC $limit");

	if($result->rowCount() != 0 ) {
		print'<center><table width="100%" border="0" cellspacing="0" cellpadding="3" class="table_table">';
		print'<tr>';
		print'<th class="table_head">User</th>';
		print'<th class="table_head">Torrent</th>';
		print'<th class="table_head">IP</th>';
		print'<th class="table_head">Port</th>';
		print'<th class="table_head">Upl.</th>';
		print'<th class="table_head">Downl.</th>';
		print'<th class="table_head">Peer-ID</th>';
		print'<th class="table_head">Conn.</th>';
		print'<th class="table_head">Seeding</th>';
		print'<th class="table_head">Started</th>';
		print'<th class="table_head">Last<br />Action</th>';
		print'</tr>';

		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($site_config['MEMBERSONLY']) {
				$sql1 = "SELECT id, username FROM users WHERE id = $row[userid]";
				$row1 = DB::run($sql1)->fetch();
			}

			if ($row1['username'])
				print'<tr><td class="table_col1"><a href="'.TTURL.'/users/profile?id=' . $row['userid'] . '">' . class_user_colour($row1['username']) . '</a></td>';
			else
				print'<tr><td class="table_col1">'.$row["ip"].'</td>';

			$sql2 = "SELECT id, name FROM torrents WHERE id = $row[torrent]";
			$result2 = DB::run($sql2);

			while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                $smallname = CutName(htmlspecialchars($row2["name"]), 40);
				print'<td class="table_col2"><a href="torrents/read?id=' . $row['torrent'] . '">' . $smallname . '</a></td>';
				print'<td align="center" class="table_col1">' . $row['ip'] . '</td>';
				print'<td align="center" class="table_col2">' . $row['port'] . '</td>';

				if ($row['uploaded'] < $row['downloaded'])
					print'<td align="center" class="table_col1"><font class="error">' . mksize($row['uploaded']) . '</font></td>';
				else
					if ($row['uploaded'] == '0')
						print'<td align="center" class="table_col1">' . mksize($row['uploaded']) . '</td>';
					else
						print'<td align="center" class="table_col1"><font color="green">' . mksize($row['uploaded']) . '</font></td>';
				print'<td align="center" class="table_col2">' . mksize($row['downloaded']) . '</td>';
				print'<td align="center" class="table_col1">' . substr($row["peer_id"], 0, 8) . '</td>';
				if ($row['connectable'] == 'yes')
					print'<td align="center" class="table_col2"><font color="green">' . $row['connectable'] . '</font></td>';
				else
					print'<td align="center" class="table_col2"><font class="error">' . $row['connectable'] . '</font></td>';
				if ($row['seeder'] == 'yes')
					print'<td align="center" class="table_col1"><font color="green">' . $row['seeder'] . '</font></td>';
				else
					print'<td align="center" class="table_col1"><font class="error">' . $row['seeder'] . '</font></td>';
				print'<td align="center" class="table_col2">' . utc_to_tz($row['started']) . '</td>';
				print'<td align="center" class="table_col1">' . utc_to_tz($row['last_action']) . '</td>';
				print'</tr>';
			}
		}
		print'</table>';
		print("$pagerbottom</center>");
	}else{
		print'<center><b>No Peers</b></center><br />';
	}
	end_frame();
	require 'views/admin/footer.php';
}