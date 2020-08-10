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
		autolink(TTURL."/admincp?action=messagespy", T_("CP_DELETED_ENTRIES")); 
	$title = T_("Message Spy");
    require 'views/admin/header.php';
		show_error_msg(T_("SUCCESS"), T_("CP_DELETED_ENTRIES"), 0);
		require 'views/admin/footer.php';
		die;
	}

	$title = T_("Message Spy");
    require 'views/admin/header.php';
	adminnavmenu();

	$row = DB::run("SELECT COUNT(*) FROM messages WHERE location in ('in', 'both')")->fetch(PDO::FETCH_LAZY);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/admincp?action=messagespy&amp;");

	begin_frame("Message Spy");

	echo $pagertop;

	$res = DB::run("SELECT * FROM messages WHERE location in ('in', 'both') ORDER BY id DESC $limit");

	print("<form id='messagespy' method='post' action='?action=messagespy&amp;do=del'><table class='table table-striped table-bordered table-hover'><thead>\n");

	print("<tr><th class='table_head' align='left'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th><th class='table_head' align='left'>Sender</th><th class='table_head' align='left'>Receiver</th><th class='table_head' align='left'>Text</th><th class='table_head' align='left'>Date</th></tr></thead><tbody>\n");

	while ($arr = $res->fetch(PDO::FETCH_ASSOC)){
		$res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr["receiver"]]);

		if ($arr2 = $res2->fetch())
			$receiver = "<a href='".TTURL."/users/profile?id=" . $arr["receiver"] . "'><b>" . class_user_colour($arr2["username"]) . "</b></a>";
		else
			$receiver = "<i>Deleted</i>";

		$arr3 = DB::run("SELECT username FROM users WHERE id=?", [$arr["sender"]])->fetch();

		$sender = "<a href='".TTURL."/users/profile?id=" . $arr["sender"] . "'><b>" . class_user_colour($arr3["username"]) . "</b></a>";
		if( $arr["sender"] == 0 )
			$sender = "<font class='error'><b>System</b></font>";
		$msg = format_comment($arr["msg"]);

		$added = utc_to_tz($arr["added"]);

		print("<tr><td class='table_col2'><input type='checkbox' name='del[]' value='$arr[id]' /></td><td align='left' class='table_col1'>$sender</td><td align='left' class='table_col2'>$receiver</td><td align='left' class='table_col1'>$msg</td><td align='left' class='table_col2'>$added</td></tr>");
	}

	print("</tbody></table><br />");
	echo "<center><input type='submit' value='Delete Checked' /> <input type='submit' value='Delete All' name='delall' /></center></form>";


	print($pagerbottom);

	end_frame();
	require 'views/admin/footer.php';
}