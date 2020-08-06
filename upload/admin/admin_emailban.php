<?php

if ($action=="emailbans"){
	stdhead(T_("EMAIL_BANS"));
	adminnavmenu();

	$remove = (int) $_GET['remove'];

	if (is_valid_id($remove)){
		DB::run("DELETE FROM email_bans WHERE id=$remove");
		write_log(sprintf(T_("EMAIL_BANS_REM"), $remove, $CURUSER["username"]));
	}

	if ($_GET["add"] == '1'){
		$mail_domain = trim($_POST["mail_domain"]);
		$comment = trim($_POST["comment"]);

		if (!$mail_domain || !$comment){
			show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA").".",0);
			stdfoot();
			die;
		}
		$mail_domain= $mail_domain;
		$comment = $comment;
		$added = get_date_time();

		$ins = DB::run("INSERT INTO email_bans (added, addedby, mail_domain, comment) VALUES(?,?,?,?)", [$added, $CURUSER['id'], $mail_domain, $comment]);
		write_log(sprintf(T_("EMAIL_BANS_ADD"), $mail_domain, $CURUSER["username"]));
		show_error_msg(T_("COMPLETE"), T_("EMAIL_BAN_ADDED"), 0);
		stdfoot();
		die;
	}

	begin_frame(T_("EMAILS_OR_DOMAINS_BANS"));
	print(T_("EMAIL_BANS_INFO") . "<br /><br /><br /><b>".T_("ADD_EMAIL_BANS")."</b>\n");
	print("<form method='post' action='admincp?action=emailbans&amp;add=1'>\n"); 
    print("<table border='0' cellspacing='0' cellpadding='5' align='center'>\n");
	print("<tr><td align='right'>".T_("EMAIL_ADDRESS") . T_("DOMAIN_BANS")."</td><td><input type='text' name='mail_domain' size='40' /></td></tr>\n");
	print("<tr><td align='right'>".T_("ADDCOMMENT")."</td><td><input type='text' name='comment' size='40' /></td></tr>\n");
	print("<tr><td colspan='2' align='center'><input type='submit' value='".T_("ADD_BAN")."' /></td></tr>\n");
	print("\n</table></form>\n<br />");
	//}

//	$row = DB::run("SELECT count(id) FROM email_bans")->fetch();
	$count = DB::run("SELECT count(id) FROM email_bans")->fetchColumn();
// $count = $row[0];
	$perpage = 40;list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, basename(__FILE__)."?action=emailbans&amp;");
	print("<br /><b>".T_("EMAIL_BANS")." ($count)</b>\n");

	if ($count == 0){
		print("<p align='center'><b>".T_("NOTHING_FOUND")."</b></p><br />\n");
	}else{
		echo $pagertop;
		print("<table border='0' cellspacing='0' cellpadding='5' width='90%' align='center' class='table_table'>\n");
		print("<tr><th class='table_head'>Added</th><th class='table_head'>Mail Address Or Domain</th><th class='table_head'>Banned By</th><th class='table_head'>Comment</th><th class='table_head'>Remove</th></tr>\n");
		$res = DB::run("SELECT * FROM email_bans ORDER BY added DESC $limit");

		while ($arr = $res->fetch(PDO::FETCH_LAZY)){
			$r2 = DB::run("SELECT username FROM users WHERE id=$arr[userid]");
			$a2 = $r2->fetch(PDO::FETCH_ASSOC);

			$r4 = DB::run("SELECT username,id FROM users WHERE id=$arr[addedby]");
			$a4 = $r4->fetch(PDO::FETCH_ASSOC);
			print("<tr><td class='table_col1'>".utc_to_tz($arr['added'])."</td><td align='left' class='table_col2'>$arr[mail_domain]</td><td align='left' class='table_col1'><a href='".TTURL."/users/profile?id=$a4[id]'>$a4[username]"."</a></td><td align='left' class='table_col2'>$arr[comment]</td><td class='table_col1'><a href='/admincp?action=emailbans&amp;remove=$arr[id]'>Remove</a></td></tr>\n");
		}

		print("</table>\n");

		echo $pagerbottom;
		echo "<br />";
	}
	end_frame();
	stdfoot();
}
