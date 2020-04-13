<?php
require_once("backend/init.php");
require_once("backend/bbcode.php");
dbconn();

if ($site_config["MEMBERSONLY"]) {
    loggedinonly();
}
 

$id = (int)$_GET["id"];
$type = $_GET["type"];
$edit = (int)$_GET["edit"];
$delete = (int)$_GET["delete"];

if ($edit == 1 || $delete == 1 || $_GET["takecomment"] == 'yes') loggedinonly();

if (!isset($id) || !$id || ($type != "torrent" && $type != "news"))
	show_error_msg(T_("ERROR"), T_("ERROR"), 1);

if ($edit=='1'){
	$row = DB::run("SELECT user FROM comments WHERE id=?", [$id])->fetch();

    if (($type == "torrent" && $CURUSER["edit_torrents"] == "no" || $type == "news" && $CURUSER["edit_news"] == "no") && $CURUSER['id'] != $row['user'])   
		show_error_msg(T_("ERROR"),T_("ERR_YOU_CANT_DO_THIS"),1);

		$save = (int)$_GET["save"];

		if($save){
			$text = $_POST['text'];

			$result= DB::run("UPDATE comments SET text=? WHERE id=?", [$text, $id]);
			write_log(class_user($CURUSER['username'])." has edited comment: ID:$id");
			show_error_msg(T_("COMPLETE"), "Comment Edited OK",1);
		}

		stdhead("Edit Comment");

    	$arr = DB::run("SELECT * FROM comments WHERE id=?", [$id])->fetch();

		begin_frame(T_("EDITCOMMENT"));
		print("<center><b> ".T_("EDITCOMMENT")." </b><p>\n");
		print("<form method=\"post\" name=\"comment\" action=\"comments.php?type=$type&amp;edit=1&save=1&amp;id=$id\">\n");
		print textbbcode("comment","text", htmlspecialchars($arr["text"]));
		print("<p><input type=\"submit\"  value=\"Submit Changes\" /></p></form></center>\n");
		end_frame();
		stdfoot();
		die();
}

if ($delete=='1'){
	if ($CURUSER["delete_news"] == "no" && $type == "news" || $CURUSER["delete_torrents"] == "no" && $type == "torrent")  
		show_error_msg(T_("ERROR"),T_("ERR_YOU_CANT_DO_THIS"),1);

	if ($type == "torrent") {
		$res = DB::run("SELECT torrent FROM comments WHERE id=?", [$id]);
		$row = $res->fetch(PDO::FETCH_ASSOC);
		if ($row["torrent"] > 0) {
			DB::run("UPDATE torrents SET comments = comments - 1 WHERE id = $row[torrent]");
		}
	}

	DB::run("DELETE FROM comments WHERE id =?", [$id]);
	write_log(class_user($CURUSER['username'])." has deleted comment: ID: $id");
	show_error_msg(T_("COMPLETE"), "Comment deleted OK", 1);
}


stdhead(T_("COMMENTS"));


//take comment add
if ($_GET["takecomment"] == 'yes'){
	$body = $_POST['body'];
	
	if (!$body)
		show_error_msg(T_("ERROR"), T_("YOU_DID_NOT_ENTER_ANYTHING"), 1);

	if ($type =="torrent"){
        DB::run("UPDATE torrents SET comments = comments + 1 WHERE id = $id");
	}

    $ins = DB::run("INSERT INTO comments (user, ".$type.", added, text) VALUES (?, ?, ?, ?)",[$CURUSER["id"], $id, get_date_time(), $body]);

	if ($ins)
			show_error_msg(T_("COMPLETED"), "Your Comment was added successfully.", 0);
		else
			show_error_msg(T_("ERROR"), T_("UNABLE_TO_ADD_COMMENT"), 0);
}//end insert comment

//NEWS
if ($type =="news"){
	$res = DB::run("SELECT * FROM news WHERE id =?", [$id]);
	$row = $res->fetch(PDO::FETCH_LAZY);

	if (!$row){
		show_error_msg(T_("ERROR"), "News id invalid", 0);
		stdfoot();
	}

	begin_frame(T_("NEWS"));
	echo htmlspecialchars($row['title']) . "<br /><br />".format_comment($row['body'])."<br />";
	end_frame();
	
}

//TORRENT
if ($type =="torrent"){
	$res = DB::run("SELECT id, name FROM torrents WHERE id =?", [$id]);
	$row = $res->fetch(PDO::FETCH_LAZY);

	if (!$row){
		show_error_msg(T_("ERROR"), "News id invalid", 0);
		stdfoot();
	}

	echo "<center><b>".T_("COMMENTSFOR")."</b> <a href='torrents-details.php?id=".$row['id']."'>".htmlspecialchars($row['name'])."</a></center><br />";
	
}

begin_frame(T_("COMMENTS"));
	$commcount = DB::run("SELECT COUNT(*) FROM comments WHERE $type =?", [$id])->fetchColumn();

	if ($commcount) {
		list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "comments.php?id=$id&amp;type=$type&amp;");
        $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE $type = $id ORDER BY comments.id $limit");
	}else{
		unset($commres);
	}

	if ($commcount) {
		print($pagertop);
		commenttable($commres, $type);
		print($pagerbottom);
	}else {
		print("<br /><b>" .T_("NOCOMMENTS"). "</b><br />\n");
	}

	echo "<center>";
	echo "<form name=\"comment\" method=\"post\" action=\"comments.php?type=$type&amp;id=$id&amp;takecomment=yes\">";
	echo textbbcode("comment","body")."<br />";
	echo "<input type=\"submit\"  value=\"".T_("ADDCOMMENT")."\" />";
	echo "</form></center>";

	end_frame();

stdfoot();