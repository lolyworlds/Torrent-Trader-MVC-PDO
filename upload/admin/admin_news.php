<?php

/////////////////////// NEWS ///////////////////////
if ($action=="news" && $do=="view"){
	stdhead(T_("NEWS_MANAGEMENT"));
	navmenu();

	begin_frame(T_("NEWS"));
	echo "<center><a href='admincp.php?action=news&amp;do=add'><b>".T_("CP_NEWS_ADD_ITEM")."</b></a></center><br />";

	$res = SQL_Query_exec("SELECT * FROM news ORDER BY added DESC");
	if (mysqli_num_rows($res) > 0){
		
		while ($arr = mysqli_fetch_assoc($res)) {
			$newsid = $arr["id"];
			$body = format_comment($arr["body"]);
			$title = $arr["title"];
			$userid = $arr["userid"];
			$added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

			$res2 = SQL_Query_exec("SELECT username FROM users WHERE id = $userid");
			$arr2 = mysqli_fetch_assoc($res2);
			
			$postername = $arr2["username"];
			
			if ($postername == "")
				$by = "Unknown";
			else
				$by = "<a href='account-details.php?id=$userid'><b>$postername</b></a>";
			
			print("<table border='0' cellspacing='0' cellpadding='0'><tr><td>");
			print("$added&nbsp;---&nbsp;by&nbsp;$by");
			print(" - [<a href='?action=news&amp;do=edit&amp;newsid=$newsid'><b>".T_("EDIT")."</b></a>]");
			print(" - [<a href='?action=news&amp;do=delete&amp;newsid=$newsid'><b>".T_("DEL")."</b></a>]");
			print("</td></tr>\n");

			print("<tr valign='top'><td><b>$title</b><br />$body</td></tr></table><br />\n");
		}

	}else{
	 echo "No News Posted";
	}

	end_frame();
	stdfoot();
}

if ($action=="news" && $do=="takeadd"){
	$body = $_POST["body"];
	
	if (!$body)
		show_error_msg(T_("ERROR"),T_("ERR_NEWS_ITEM_CAN_NOT_BE_EMPTY"),1); 

	$title = $_POST['title'];

	if (!$title)
		show_error_msg(T_("ERROR"),T_("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"),1);
	
	$added = $_POST["added"];

	if (!$added)
		$added = sqlesc(get_date_time());

	SQL_Query_exec("INSERT INTO news (userid, added, body, title) VALUES (".

	$CURUSER['id'] . ", $added, " . sqlesc($body) . ", " . sqlesc($title) . ")");

	if (mysqli_affected_rows($GLOBALS["DBconnector"]) == 1)
		show_error_msg(T_("COMPLETED"),T_("CP_NEWS_ITEM_ADDED_SUCCESS"),1);
	else
		show_error_msg(T_("ERROR"),T_("CP_NEWS_UNABLE_TO_ADD"),1);
}

if ($action=="news" && $do=="add"){
	stdhead(T_("NEWS_MANAGEMENT"));
	navmenu();

	begin_frame(T_("CP_NEWS_ADD"));
	print("<center><form method='post' action='admincp.php' name='news'>\n");
	print("<input type='hidden' name='action' value='news' />\n");
	print("<input type='hidden' name='do' value='takeadd' />\n");

	print("<b>".T_("CP_NEWS_TITLE").":</b> <input type='text' name='title' /><br />\n");

	echo "<br />".textbbcode("news","body")."<br />";

	print("<br /><br /><input type='submit' value='".T_("SUBMIT")."' />\n");

	print("</form><br /><br /></center>\n");
	end_frame();
	stdfoot();
}

if ($action=="news" && $do=="edit"){
	stdhead(T_("NEWS_MANAGEMENT"));
	navmenu();

	$newsid = (int)$_GET["newsid"];
	
	if (!is_valid_id($newsid))
		show_error_msg(T_("ERROR"),sprintf(T_("CP_NEWS_INVAILD_ITEM_ID"), $newsid),1);
                                                                                            
	$res = SQL_Query_exec("SELECT * FROM news WHERE id=$newsid");

	if (mysqli_num_rows($res) != 1)
		show_error_msg(T_("ERROR"), sprintf(T_("CP_NEWS_NO_ITEM_WITH_ID"), $newsid),1);

	$arr = mysqli_fetch_assoc($res);

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  		$body = $_POST['body'];

		if ($body == "")
    		show_error_msg(T_("ERROR"), T_("FORUMS_BODY_CANNOT_BE_EMPTY"),1);

		$title = $_POST['title'];

		if ($title == "")
			show_error_msg(T_("ERROR"), T_("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"),1);

		$body = sqlesc($body);

		$editedat = sqlesc(get_date_time());

		SQL_Query_exec("UPDATE news SET body=$body, title='$title' WHERE id=$newsid");

		$returnto = $_POST['returnto'];

		if ($returnto != "")
			header("Location: $returnto");
		else
			autolink("admincp.php?action=news&do=view", T_("CP_NEWS_ITEM_WAS_EDITED_SUCCESS")); 
	} else {
		$returnto = htmlspecialchars($_GET['returnto']);
		begin_frame(T_("CP_NEWS_EDIT"));
		print("<form method='post' action='?action=news&amp;do=edit&amp;newsid=$newsid' name='news'>\n");
		print("<center>");
		print("<input type='hidden' name='returnto' value='$returnto' />\n");
		print("<b>".T_("CP_NEWS_TITLE").": </b><input type='text' name='title' value=\"".$arr['title']."\" /><br /><br />\n");
		echo "<br />".textbbcode("news","body",$arr["body"])."<br />";
		print("<br /><input type='submit' value='Okay' />\n");
		print("</center>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="news" && $do=="delete"){

	$newsid = (int)$_GET["newsid"];
	
	if (!is_valid_id($newsid))
		show_error_msg(T_("ERROR"),sprintf(T_("CP_NEWS_INVAILD_ITEM_ID"), $newsid),1);

	SQL_Query_exec("DELETE FROM news WHERE id=$newsid");
    SQL_Query_exec("DELETE FROM comments WHERE news = $newsid");
	
	show_error_msg(T_("COMPLETED"),T_("CP_NEWS_ITEM_DEL_SUCCESS"),1);
}