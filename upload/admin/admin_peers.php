<?php


if ($action=="peers"){
	stdhead("Peers List");
	navmenu();

	begin_frame("Peers List");

	$count1 = number_format(get_row_count("peers"));

	print("<center>We have $count1 peers</center><br />");

	$res4 = SQL_Query_exec("SELECT COUNT(*) FROM peers $limit");
	$row4 = mysqli_fetch_array($res4);

	$count = $row4[0];
	$peersperpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($peersperpage, $count, "admincp.php?action=peers&amp;");

	print("$pagertop");

	$sql = "SELECT * FROM peers ORDER BY started DESC $limit";
	$result = SQL_Query_exec($sql);

	if( mysqli_num_rows($result) != 0 ) {
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

		while($row = mysqli_fetch_assoc($result)) {
			if ($site_config['MEMBERSONLY']) {
				$sql1 = "SELECT id, username FROM users WHERE id = $row[userid]";
				$result1 = SQL_Query_exec($sql1);
				$row1 = mysqli_fetch_assoc($result1);
			}

			if ($row1['username'])
				print'<tr><td class="table_col1"><a href="account-details.php?id=' . $row['userid'] . '">' . $row1['username'] . '</a></td>';
			else
				print'<tr><td class="table_col1">'.$row["ip"].'</td>';

			$sql2 = "SELECT id, name FROM torrents WHERE id = $row[torrent]";
			$result2 = SQL_Query_exec($sql2);

			while ($row2 = mysqli_fetch_assoc($result2)) {

                $smallname = CutName(htmlspecialchars($row2["name"]), 40);
                
				print'<td class="table_col2"><a href="torrents-details.php?id=' . $row['torrent'] . '">' . $smallname . '</a></td>';
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

	stdfoot();
}