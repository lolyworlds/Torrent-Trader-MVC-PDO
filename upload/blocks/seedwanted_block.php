<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	begin_block(T_("SEEDERS_WANTED"));

	$external = "external = 'no'";
	// Uncomment below to include external torrents
	$external = 1;

	$expires = 600; // Cache time in seconds
	if (($rows = $TTCache->Get("seedwanted_block", $expires)) === false) {
		$res = DB::run("SELECT id, name, seeders, leechers FROM torrents WHERE seeders = ? AND leechers > ? AND banned = ? AND ? ORDER BY leechers DESC LIMIT 5", [0, 0, 'no', $external]);
		$rows = array();

		while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
			$rows[] = $row;
		}

		$TTCache->Set("seedwanted_block", $rows, $expires);
	}


	if (!$rows) {
		echo "<br />".T_("NOTHING_FOUND")."<br />";
	} else {
		echo "<div id='sNeeded' class='bMenu'><ul>\n";
		foreach ($rows as $row) { 
			$char1 = 18; //cut length 
			$smallname = htmlspecialchars(CutName($row["name"], $char1));
			echo "<li><a href='torrents-details.php?id=$row[id]' title='".htmlspecialchars($row["name"])."'>$smallname</a><br /> - [".T_("LEECHERS").": " . number_format($row["leechers"]) . "]</li>\n";
		}
	echo "</ul></div>\n";
	}
	end_block();
}
?>