<?php
if ($action=="messagespy"){                                    
	if ($do == "del") {
		if ($_POST["delall"])
			DB::run("DELETE FROM `messages`");
		else {
			if (!@count($_POST["del"])) show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
			$ids = array_map("intval", $_POST["del"]);
			$ids = implode(", ", $ids);
			DB::run("DELETE FROM `messages` WHERE `id` IN ($ids)");
		}
		autolink("admincp.php?action=messagespy", T_("CP_DELETED_ENTRIES")); 
		stdhead();
		show_error_msg(T_("SUCCESS"), T_("CP_DELETED_ENTRIES"), 0);
		stdfoot();
		die;
	}


	stdhead("Message Spy");
	navmenu();

	$row = DB::run("SELECT COUNT(*) FROM messages WHERE location in ('in', 'both')")->fetch(PDO::FETCH_LAZY);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=messagespy&amp;");

	begin_frame("Message Spy");

	echo $pagertop;

	$res = DB::run("SELECT * FROM messages WHERE location in ('in', 'both') ORDER BY id DESC $limit");

	print("<form id='messagespy' method='post' action='?action=messagespy&amp;do=del'><table border='0' cellspacing='0' cellpadding='3' align='center' class='table_table'>\n");

	print("<tr><th class='table_head' align='left'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th><th class='table_head' align='left'>Sender</th><th class='table_head' align='left'>Receiver</th><th class='table_head' align='left'>Text</th><th class='table_head' align='left'>Date</th></tr>\n");

	while ($arr = $res->fetch(PDO::FETCH_ASSOC)){
		$res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr["receiver"]]);

		if ($arr2 = $res2->fetch())
			$receiver = "<a href='account-details.php?id=" . $arr["receiver"] . "'><b>" . $arr2["username"] . "</b></a>";
		else
			$receiver = "<i>Deleted</i>";

		$arr3 = DB::run("SELECT username FROM users WHERE id=?", [$arr["sender"]])->fetch();

		$sender = "<a href='account-details.php?id=" . $arr["sender"] . "'><b>" . $arr3["username"] . "</b></a>";
		if( $arr["sender"] == 0 )
			$sender = "<font class='error'><b>System</b></font>";
		$msg = format_comment($arr["msg"]);

		$added = utc_to_tz($arr["added"]);

		print("<tr><td class='table_col2'><input type='checkbox' name='del[]' value='$arr[id]' /></td><td align='left' class='table_col1'>$sender</td><td align='left' class='table_col2'>$receiver</td><td align='left' class='table_col1'>$msg</td><td align='left' class='table_col2'>$added</td></tr>");
	}

	print("</table><br />");
	echo "<input type='submit' value='Delete Checked' /> <input type='submit' value='Delete All' name='delall' /></form>";


	print($pagerbottom);

	end_frame();
	stdfoot();
}