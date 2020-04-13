<?php
////////// categories /////////////////////
if ($action=="categories" && $do=="view"){
	stdhead(T_("Categories Management"));
	navmenu();

	begin_frame(T_("TORRENT_CATEGORIES"));
	echo "<center><a href='admincp.php?action=categories&amp;do=add'><b>Add New Category</b></a></center><br />";

	print("<i>Please note that if no image is specified, the category name will be displayed</i><br /><br />");

	echo("<center><table width='95%' class='table_table'>");
	echo("<tr><th width='10' class='table_head'>Sort</th><th class='table_head'>Parent Cat</th><th class='table_head'>Sub Cat</th><th class='table_head'>Image</th><th width='30' class='table_head'></th></tr>");
	$query = "SELECT * FROM categories ORDER BY parent_cat ASC, sort_index ASC";
	$sql = SQL_Query_exec($query);
	while ($row = mysqli_fetch_assoc($sql)) {
		$id = $row['id'];
		$name = $row['name'];
		$priority = $row['sort_index'];
		$parent = $row['parent_cat'];

		print("<tr><td class='table_col1'>$priority</td><td class='table_col2'>$parent</td><td class='table_col1'>$name</td><td class='table_col2' align='center'>");
		if (isset($row["image"]) && $row["image"] != "")
			print("<img border=\"0\" src=\"" . $site_config['SITEURL'] . "/images/categories/" . $row["image"] . "\" alt=\"" . $row["name"] . "\" />");
		else
			print("-");	
		print("</td><td class='table_col1'><a href='admincp.php?action=categories&amp;do=edit&amp;id=$id'>[EDIT]</a> <a href='admincp.php?action=categories&amp;do=delete&amp;id=$id'>[DELETE]</a></td></tr>");
	}
	echo("</table></center>");
	end_frame();
	stdfoot();
}


if ($action=="categories" && $do=="edit"){
	stdhead(T_("Categories Management"));
	navmenu();

	$id = (int)$_GET["id"];
	
	if (!is_valid_id($id))
		show_error_msg(T_("ERROR"),T_("INVALID_ID"),1);

	$res = SQL_Query_exec("SELECT * FROM categories WHERE id=$id");

	if (mysqli_num_rows($res) != 1)
		show_error_msg(T_("ERROR"), "No category with ID $id.",1);

	$arr = mysqli_fetch_assoc($res);

	if ($_GET["save"] == '1'){
  		$parent_cat = $_POST['parent_cat'];
		if ($parent_cat == "")
    		show_error_msg(T_("ERROR"), "Parent Cat cannot be empty!",1);

		$name = $_POST['name'];
		if ($name == "")
			show_error_msg(T_("ERROR"), "Sub cat cannot be empty!",1);

		$sort_index = $_POST['sort_index'];
		$image = $_POST['image'];

		$parent_cat = sqlesc($parent_cat);
		$name = sqlesc($name);
		$sort_index = sqlesc($sort_index);
		$image = sqlesc($image);

		SQL_Query_exec("UPDATE categories SET parent_cat=$parent_cat, name=$name, sort_index=$sort_index, image=$image WHERE id=$id");

		show_error_msg(T_("COMPLETED"),"category was edited successfully.",0);

	} else {
		begin_frame(T_("CP_CATEGORY_EDIT"));
		print("<form method='post' action='?action=categories&amp;do=edit&amp;id=$id&amp;save=1'>\n");
		print("<center><table border='0' cellspacing='0' cellpadding='5'>\n");
		print("<tr><td align='left'><b>Parent Category: </b><input type='text' name='parent_cat' value=\"".$arr['parent_cat']."\" /> All Subcats with EXACTLY the same parent cat are grouped</td></tr>\n");
		print("<tr><td align='left'><b>Sub Category: </b><input type='text' name='name' value=\"".$arr['name']."\" /></td></tr>\n");
		print("<tr><td align='left'><b>Sort: </b><input type='text' name='sort_index' value=\"".$arr['sort_index']."\" /></td></tr>\n");
		print("<tr><td align='left'><b>Image: </b><input type='text' name='image' value=\"".$arr['image']."\" /> single filename</td></tr>\n");
		print("<tr><td align='center'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");
		print("</table></center>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="categories" && $do=="delete"){
	stdhead(T_("Categories Management"));
	navmenu();

	$id = (int)$_GET["id"];

	if ($_GET["sure"] == '1'){

		if (!is_valid_id($id))
			show_error_msg(T_("ERROR"),sprintf(T_("CP_NEWS_INVAILD_ITEM_ID"), $newsid),1);

		$newcatid = (int) $_POST["newcat"];

		SQL_Query_exec("UPDATE torrents SET category=$newcatid WHERE category=$id"); //move torrents to a new cat

		SQL_Query_exec("DELETE FROM categories WHERE id=$id"); //delete old cat
		
		show_error_msg(T_("COMPLETED"),"Category Deleted OK",1);

	}else{
		begin_frame(T_("CATEGORY_DEL"));
		print("<form method='post' action='?action=categories&amp;do=delete&amp;id=$id&amp;sure=1'>\n");
		print("<center><table border='0' cellspacing='0' cellpadding='5'>\n");
		print("<tr><td align='left'><b>Category ID to move all Torrents To: </b><input type='text' name='newcat' /> (Cat ID)</td></tr>\n");
		print("<tr><td align='center'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");
		print("</table></center>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="categories" && $do=="takeadd"){
  		$name = $_POST['name'];
		if ($name == "")
    		show_error_msg(T_("ERROR"), "Sub Cat cannot be empty!",1);

		$parent_cat = $_POST['parent_cat'];
		if ($parent_cat == "")
			show_error_msg(T_("ERROR"), "Parent Cat cannot be empty!",1);

		$sort_index = $_POST['sort_index'];
		$image = $_POST['image'];

		$parent_cat = sqlesc($parent_cat);
		$name = sqlesc($name);
		$sort_index = sqlesc($sort_index);
		$image = sqlesc($image);

	SQL_Query_exec("INSERT INTO categories (name, parent_cat, sort_index, image) VALUES ($name, $parent_cat, $sort_index, $image)");

	if (mysqli_affected_rows($GLOBALS["DBconnector"]) == 1)
		show_error_msg(T_("COMPLETED"),"Category was added successfully.",1);
	else
		show_error_msg(T_("ERROR"),"Unable to add category",1);
}

if ($action=="categories" && $do=="add"){
	stdhead(T_("CATEGORY_MANAGEMENT"));
	navmenu();

	begin_frame(T_("CATEGORY_ADD"));
	print("<center><form method='post' action='admincp.php'>\n");
	print("<input type='hidden' name='action' value='categories' />\n");
	print("<input type='hidden' name='do' value='takeadd' />\n");
                       
	print("<table border='0' cellspacing='0' cellpadding='5'>\n");

	print("<tr><td align='left'><b>Parent Category:</b> <input type='text' name='parent_cat' /></td></tr>\n");
	print("<tr><td align='left'><b>Sub Category:</b> <input type='text' name='name' /></td></tr>\n");
	print("<tr><td align='left'><b>Sort:</b> <input type='text' name='sort_index' /></td></tr>\n");
	print("<tr><td align='left'><b>Image:</b> <input type='text' name='image' /></td></tr>\n");

	print("<tr><td colspan='2'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");

	print("</table></form></center>\n");
	end_frame();
	stdfoot();
}

