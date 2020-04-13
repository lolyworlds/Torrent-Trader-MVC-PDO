<?php

if ($action=="avatars"){
	stdhead("Avatar Log");
	navmenu();

	begin_frame("Avatar Log");

	$query = DB::run("SELECT count(*) FROM users WHERE enabled=? AND avatar !=?", ['yes', '']);
	$count = $query->fetch(PDO::FETCH_LAZY);
	$count = $count[0];

	list($pagertop, $pagerbottom, $limit) = pager(50, $count, '/admincp?action=avatars&amp;');
	echo ($pagertop);
	?>
	<table border="0" class="table_table" align="center">
	<tr>
	<th class="table_head"><?php echo T_("USER")?></th>
	<th class="table_head">Avatar</th>
	</tr><?php

	$query = "SELECT username, id, avatar FROM users WHERE enabled='yes' AND avatar !='' $limit";
	$res = DB::run($query);

	while($arr = $res->fetch(PDO::FETCH_ASSOC)){
			echo("<tr><td class='table_col1'><b><a href=\"/accountdetails?id=" . $arr['id'] . "\">" . class_user($arr['username']) . "</a></b></td><td class='table_col2'>");

			if (!$arr['avatar'])
				echo "<img width=\"80\" src='images/default_avatar.png' alt='' /></td></tr>";
			else
				echo "<img width=\"80\" src=\"".htmlspecialchars($arr["avatar"])."\" alt='' /></td></tr>";
	}
	?>
	</table>
	<?php
	echo ($pagerbottom);
	end_frame();
	stdfoot();
}