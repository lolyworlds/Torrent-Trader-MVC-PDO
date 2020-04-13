<?php
/////////////////////// GROUPS MANAGEMENT ///////////////////////
if ($action=="groups" && $do=="view"){
	stdhead(T_("GROUPS_MANAGEMENT"));
	navmenu();

	begin_frame(T_("USER_GROUPS"));
	
    print("<center><a href='admincp.php?action=groups&amp;do=add'>".T_("GROUPS_ADD_NEW")."</a></center>\n");

	print("<br /><br />\n<table width=\"100%\" align=\"center\" border=\"0\" class=\"table_table\">\n");
	print("<tr>\n");
	print("<th class='table_head'>".T_("NAME")."</th>\n");
	print("<th class='table_head'>".T_("TORRENTS")."<br />".T_("GROUPS_VIEW_EDIT_DEL")."</th>\n");
	print("<th class='table_head'>".T_("MEMBERS")."<br />".T_("GROUPS_VIEW_EDIT_DEL")."</th>\n");
	print("<th class='table_head'>".T_("NEWS")."<br />".T_("GROUPS_VIEW_EDIT_DEL")."</th>\n");
	print("<th class='table_head'>".T_("FORUM")."<br />".T_("GROUPS_VIEW_EDIT_DEL")."</th>\n");
	print("<th class='table_head'>".T_("UPLOAD")."</th>\n");
	print("<th class='table_head'>".T_("DOWNLOAD")."</th>\n");
	print("<th class='table_head'>".T_("CP_VIEW")."</th>\n");
    print("<th class='table_head'>".T_("CP_STAFF_PAGE")."</th>");
    print("<th class='table_head'>".T_("CP_STAFF_PUBLIC")."</th>");
    print("<th class='table_head'>".T_("CP_STAFF_SORT")."</th>");
	print("<th class='table_head'>".T_("DEL")."</th>\n");
	print("</tr>\n");

	$getlevel=SQL_Query_exec("SELECT * from groups ORDER BY group_id");
	while ($level=mysqli_fetch_assoc($getlevel)) {
		 print("<tr>\n");
		 print("<td class='table_col1'><a href='admincp.php?action=groups&amp;do=edit&amp;group_id=".$level["group_id"]."'>".htmlspecialchars($level["level"])."</a></td>\n");
		 print("<td class='table_col2'>".$level["view_torrents"]."/".$level["edit_torrents"]."/".$level["delete_torrents"]."</td>\n");
		 print("<td class='table_col1'>".$level["view_users"]."/".$level["edit_users"]."/".$level["delete_users"]."</td>\n");
		 print("<td class='table_col2'>".$level["view_news"]."/".$level["edit_news"]."/".$level["delete_news"]."</td>\n");
		 print("<td class='table_col1'>".$level["view_forum"]."/".$level["edit_forum"]."/".$level["delete_forum"]."</td>\n");
		 print("<td class='table_col2'>".$level["can_upload"]."</td>\n");
		 print("<td class='table_col1'>".$level["can_download"]."</td>\n");
		 print("<td class='table_col2'>".$level["control_panel"]."</td>\n");
         print("<td class='table_col1'>".$level["staff_page"]."</td>\n");
         print("<td class='table_col2'>".$level["staff_public"]."</td>\n");  
         print("<td class='table_col1'>".$level["staff_sort"]."</td>\n");  
		 print("<td class='table_col1'><a href='admincp.php?action=groups&amp;do=delete&amp;group_id=".$level["group_id"]."'>Del</a></td>\n");

		 print("</tr>\n");
	}

	print("</table><br /><br />");
	end_frame();
	stdfoot();
}

if ($action=="groups" && $do=="edit"){
	$group_id=intval($_GET["group_id"]);
	$rlevel=SQL_Query_exec("SELECT * FROM groups WHERE group_id=$group_id");
	if (!$rlevel)
		show_error_msg(T_("ERROR"),T_("CP_NO_GROUP_ID_FOUND"),1);

	$level=mysqli_fetch_assoc($rlevel);

	stdhead(T_("GROUPS_MANAGEMENT"));
	navmenu();


	begin_frame(T_("CP_EDIT_GROUP"));
	?>
	<form action="admincp.php?action=groups&amp;do=update&amp;group_id=<?php echo $level["group_id"]; ?>" name="level" method="post">
	<table width="100%" align="center">
	<tr><td>Name:</td><td><input type="text" name="gname" value="<?php echo $level["level"];?>" size="40" /></td></tr>
	<tr><td>View Torrents:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vtorrent" value="yes" <?php if ($level["view_torrents"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vtorrent" value="no" <?php if ($level["view_torrents"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Edit Torrents:</td><td>  <?php echo T_("YES");?> <input type="radio" name="etorrent" value="yes" <?php if ($level["edit_torrents"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="etorrent" value="no" <?php if ($level["edit_torrents"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Delete Torrents:</td><td>  <?php echo T_("YES");?> <input type="radio" name="dtorrent" value="yes" <?php if ($level["delete_torrents"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="dtorrent" value="no" <?php if ($level["delete_torrents"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>View Users:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vuser" value="yes" <?php if ($level["view_users"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vuser" value="no" <?php if ($level["view_users"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Edit Users:</td><td>  <?php echo T_("YES");?> <input type="radio" name="euser" value="yes" <?php if ($level["edit_users"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="euser" value="no" <?php if ($level["edit_users"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Delete Users:</td><td>  <?php echo T_("YES");?> <input type="radio" name="duser" value="yes" <?php if ($level["delete_users"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="duser" value="no" <?php if ($level["delete_users"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>View News:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vnews" value="yes" <?php if ($level["view_news"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vnews" value="no" <?php if ($level["view_news"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Edit News:</td><td>  <?php echo T_("YES");?> <input type="radio" name="enews" value="yes" <?php if ($level["edit_news"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="enews" value="no" <?php if ($level["edit_news"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Delete News:</td><td> <?php echo T_("YES");?> <input type="radio" name="dnews" value="yes" <?php if ($level["delete_news"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="dnews" value="no" <?php if ($level["delete_news"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>View Forums:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vforum" value="yes" <?php if ($level["view_forum"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vforum" value="no" <?php if ($level["view_forum"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Edit In Forums:</td><td>  <?php echo T_("YES");?> <input type="radio" name="eforum" value="yes" <?php if ($level["edit_forum"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="eforum" value="no" <?php if ($level["edit_forum"]=="no") echo "checked = 'checked'" ?> /></td></tr>
	<tr><td>Delete In Forums:</td><td>  <?php echo T_("YES");?> <input type="radio" name="dforum" value="yes" <?php if ($level["delete_forum"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="dforum" value="no" <?php if ($level["delete_forum"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Can Upload:</td><td>  <?php echo T_("YES");?> <input type="radio" name="upload" value="yes" <?php if ($level["can_upload"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="upload" value="no" <?php if ($level["can_upload"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Can Download:</td><td>  <?php echo T_("YES");?> <input type="radio" name="down" value="yes" <?php if ($level["can_download"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="down" value="no" <?php if ($level["can_download"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Can View CP:</td><td>  <?php echo T_("YES");?> <input type="radio" name="admincp" value="yes" <?php if ($level["control_panel"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="admincp" value="no" <?php if ($level["control_panel"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Staff Page:</td><td>  <?php echo T_("YES");?> <input type="radio" name="staffpage" value="yes" <?php if ($level["staff_page"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="staffpage" value="no" <?php if ($level["staff_page"]=="no") echo "checked = 'checked'"; ?> /></td></tr> 
    <tr><td>Staff Public:</td><td>  <?php echo T_("YES");?> <input type="radio" name="staffpublic" value="yes" <?php if ($level["staff_public"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="staffpublic" value="no" <?php if ($level["staff_public"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
    <tr><td>Staff Sort:</td><td><input type='text' name='sort' size='3' value='<?php echo $level["staff_sort"]; ?>' /></td></tr>
    <?php
	print("\n<tr><td align=\"center\" ><input type=\"submit\" name=\"write\" value=\"Confirm\" /></td></tr>");
	print("</table></form><br /><br />");
	end_frame();
	stdfoot();
}

if ($action=="groups" && $do=="update"){
		stdhead(T_("GROUPS_MANAGEMENT"));
		navmenu();

		begin_frame(T_("_BTN_UPDT_"));

     $update = array();
     $update[] = "level = " . sqlesc($_POST["gname"]);
     $update[] = "view_torrents = " . sqlesc($_POST["vtorrent"]);
     $update[] = "edit_torrents = " . sqlesc($_POST["etorrent"]);
     $update[] = "delete_torrents = " . sqlesc($_POST["dtorrent"]);
     $update[] = "view_users = " . sqlesc($_POST["vuser"]);
     $update[] = "edit_users = " . sqlesc($_POST["euser"]);
     $update[] = "delete_users = " . sqlesc($_POST["duser"]);
     $update[] = "view_news = " . sqlesc($_POST["vnews"]);
     $update[] = "edit_news = " . sqlesc($_POST["enews"]);
     $update[] = "delete_news = " . sqlesc($_POST["dnews"]);
     $update[] = "view_forum = " . sqlesc($_POST["vforum"]);
     $update[] = "edit_forum = " . sqlesc($_POST["eforum"]);
     $update[] = "delete_forum = " . sqlesc($_POST["dforum"]);
     $update[] = "can_upload = " . sqlesc($_POST["upload"]);
     $update[] = "can_download = " . sqlesc($_POST["down"]);
     $update[] = "control_panel = " . sqlesc($_POST["admincp"]);
     $update[] = "staff_page = " . sqlesc($_POST["staffpage"]);
     $update[] = "staff_public = " . sqlesc($_POST["staffpublic"]);
     $update[] = "staff_sort = " . intval($_POST['sort']);
     $strupdate = implode(",", $update);

     $group_id=intval($_GET["group_id"]);
     SQL_Query_exec("UPDATE groups SET $strupdate WHERE group_id=$group_id");
                 
		echo "<br /><center><b>".T_("CP_UPDATED_OK")."</b></center><br />";
		end_frame();
		stdfoot();	
}

if ($action=="groups" && $do=="delete"){
		//Needs to be secured!!!!
		$group_id=intval($_GET["group_id"]);
		if (($group_id=="1") || ($group_id=="7"))
			show_error_msg(T_("ERROR"),T_("CP_YOU_CANT_DEL_THIS_GRP"),1);
 
		SQL_Query_exec("DELETE FROM groups WHERE group_id=$group_id");
        show_error_msg(T_("_DEL_"), T_("CP_DEL_OK"), 1);
}


if ($action=="groups" && $do=="add") {
	stdhead(T_("GROUPS_MANAGEMENT"));

	navmenu();

	begin_frame(T_("GROUPS_ADD_NEW"));
	?>
	<form action="admincp.php?action=groups&amp;do=addnew" name="level" method="post">
	<table width="100%" align="center">
	<tr><td>Group Name:</td><td><input type="text" name="gname" value="" size="40" /></td></tr>
	<tr><td>Copy Settings From: </td><td><select name="getlevel" size="1">
	<?php
	$rlevel=SQL_Query_exec("SELECT DISTINCT group_id, level FROM groups ORDER BY group_id");

	while($level=mysqli_fetch_assoc($rlevel)) {
		print("\n<option value='".$level["group_id"]."'>".htmlspecialchars($level["level"])."</option>");
	}
	print("\n</select></td></tr>");
	print("\n<tr><td align=\"center\" ><input type=\"submit\" name=\"confirm\" value=\"Confirm\" /></td></tr>");
	print("</table></form><br /><br />");
	end_frame();
	stdfoot();	
}

if ($action=="groups" && $do=="addnew") {
	
	stdhead(T_("GROUPS_MANAGEMENT"));

	navmenu();

	begin_frame(T_("GROUPS_ADD_NEW"));

	$group_id=intval($_POST["getlevel"]);

	$rlevel=SQL_Query_exec("SELECT * FROM groups WHERE group_id=$group_id");
	$level=mysqli_fetch_assoc($rlevel);
	if (!$level)
	   show_error_msg(T_("ERROR"),T_("CP_INVALID_ID"),1);

	$update = array();
	$update[] = "level = " . sqlesc($level["level"]);
	$update[] = "view_torrents = " . sqlesc($level["view_torrents"]);
	$update[] = "edit_torrents = " . sqlesc($level["edit_torrents"]);
	$update[] = "delete_torrents = " . sqlesc($level["delete_torrents"]);
	$update[] = "view_users = " . sqlesc($level["view_users"]);
	$update[] = "edit_users = " . sqlesc($level["edit_users"]);
	$update[] = "delete_users = " . sqlesc($level["delete_users"]);
	$update[] = "view_news = " . sqlesc($level["view_news"]);
	$update[] = "edit_news = " . sqlesc($level["edit_news"]);
	$update[] = "delete_news = " . sqlesc($level["delete_news"]);
	$update[] = "view_forum = " . sqlesc($level["view_forum"]);
	$update[] = "edit_forum = " . sqlesc($level["edit_forum"]);
	$update[] = "delete_forum = " . sqlesc($level["delete_forum"]);
	$update[] = "can_upload = " . sqlesc($level["can_upload"]);
	$update[] = "can_download = " . sqlesc($level["can_download"]);
	$update[] = "control_panel = " . sqlesc($level["control_panel"]);
    $update[] = "staff_page = " . sqlesc($level["staff_page"]);
    $update[] = "staff_public = " . sqlesc($level["staff_public"]);
    $update[] = "staff_sort = " . intval($level["staff_sort"]);
	$strupdate = implode(",", $update);
	SQL_Query_exec("INSERT INTO groups SET $strupdate");

	echo "<br /><center><b>Added OK</b></center><br />";
	end_frame();
	stdfoot();	
}