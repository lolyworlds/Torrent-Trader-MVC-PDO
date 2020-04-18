<?php

#====================================#
#		Theme Management		#
#====================================#

if ($action == "style") {
	if ($do == "add") {
		stdhead();
		adminnavmenu();
		if ($_POST) {
			if (empty($_POST['name']))
				$error .= T_("THEME_NAME_WAS_EMPTY")."<br />";
			if (empty($_POST['uri']))
				$error .= T_("THEME_FOLDER_NAME_WAS_EMPTY");
			if ($error)
				show_error_msg(T_("ERROR"), T_("THEME_NOT_ADDED_REASON")." $error", 1);
			if ($qry = DB::run("INSERT INTO stylesheets (name, uri) VALUES (?, ?)", [$_POST["name"], $_POST["uri"]]))
				show_error_msg(T_("SUCCESS"), "Theme '".htmlspecialchars($_POST["name"])."' added.", 0);
			elseif ($qry->errorCode() == 1062)
				show_error_msg(T_("FAILED"), T_("THEME_ALREADY_EXISTS"), 0);
			else
				show_error_msg(T_("FAILED"), T_("THEME_NOT_ADDED_DB_ERROR")." ".$qry->errorInfo(), 0);
		}
		begin_frame(T_("THEME_ADD"));
		?>
        <form action='/admincp' method='post'>
		<input type='hidden' name='action' value='style' />
        <input type='hidden' name='do' value='add' />
        <table align='center' width='400' cellspacing='0' class='table_table'>
		<tr>
		<td class='table_col1'><?php echo T_("THEME_NAME_OF_NEW")?>:</td>
		<td class='table_col2' align='right'><input type='text' name='name' size='30' maxlength='30' value='<?php echo $name; ?>' /></td>
		</tr>
		<tr>
		<td class='table_col1'><?php echo T_("THEME_FOLDER_NAME_CASE_SENSITIVE")?>:</td>
		<td class='table_col2' align='right'><input type='text' name='uri' size='30' maxlength='30' value='<?php echo $uri; ?>' /></td>
		</tr>
		<tr>
		<td colspan='2' align='center' class='table_head'>
		<input type='submit' value='Add new theme' />
		<input type='reset' value='<?php echo T_("RESET")?>' />
		</td>
		</tr>
		</table>
        </form>
		<br /><?php echo T_("THEME_PLEASE_NOTE_ALL_THEMES_MUST"); ?>
		<?php
		end_frame();
		stdfoot();
   } elseif ($do == "del") {

        if (!@count($_POST["ids"])) show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
        $ids = array_map("intval", $_POST["ids"]);
        $ids = implode(', ', $ids);
        DB::run("DELETE FROM `stylesheets` WHERE `id` IN ($ids)");
        DB::run("UPDATE `users` SET `stylesheet` = ".$site_config["default_theme"]." WHERE stylesheet NOT IN (SELECT id FROM stylesheets)");
        autolink("/admincp?action=style", T_("THEME_SUCCESS_THEME_DELETED"));
                                 
	}elseif ($do == "add2") {

		$add = $_POST["add"];
		$a = 0;
		foreach ($add as $theme) {
			if ($theme['add'] != 1) { $a++; continue; }
			$ins = DB::run("INSERT INTO stylesheets (name, uri) VALUES(?, ?)", [$theme['name'], $theme['uri']]);
			if (!$ins) {
				if ($ins->errorCode()  == 1062)
					$error .= htmlspecialchars($theme['name'])." - ".T_("THEME_ALREADY_EXISTS").".<br />";
				else
					$error .= htmlspecialchars($theme['name']).": ".T_("THEME_DATEBASE_ERROR")." ".$ins->errorInfo()." (".$ins->errorCode().")<br />";
			}else
				$added .= htmlspecialchars($theme['name'])."<br />";
		}
        
        if ($a == count($add))
            autolink("/admincp?action=style", T_("THEME_NOTHING_SELECTED"));

		if ($added)
            autolink("/admincp?action=style", sprintf(T_("THEME_THE_FOLLOWING_THEMES_WAS_ADDED"), $added));   
		if ($error)
			show_error_msg(T_("FAILED"), sprintf(T_("THEME_THE_FOLLOWING_THEMES_WAS_NOT_ADDED"), $error), 1);
        
	}else{
		stdhead(T_("THEME_MANAGEMENT"));
		adminnavmenu();
		begin_frame(T_("THEME_MANAGEMENT"));
		$res = DB::run("SELECT * FROM stylesheets");
		echo "<center><a href='/admincp?action=style&amp;do=add'>".T_("THEME_ADD")."</a><!-- - <b>".T_("THEME_CLICK_A_THEME_TO_EDIT")."</b>--></center><br />";
		echo T_("THEME_CURRENT").":<form id='deltheme' method='post' action='/admincp?action=style&amp;do=del'><table width='60%' class='table_table' align='center'>".
			"<tr><th class='table_head'>ID</th><th class='table_head'>".T_("NAME")."</th><th class='table_head'>".T_("THEME_FOLDER_NAME")."</th><th width='5%' class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th></tr>";
		while ($row=$res->fetch(PDO::FETCH_ASSOC)) {
			if (!is_dir("views/themes/$row[uri]"))
				$row['uri'] .= " <b>- ".T_("THEME_DIR_DONT_EXIST")."</b>";
			echo "<tr><td class='table_col1' align='center'>$row[id]</td><td class='table_col2' align='center'>$row[name]</td><td class='table_col1' align='center'>$row[uri]</td><td class='table_col2' align='center'><input name='ids[]' type='checkbox' value='$row[id]' /></td></tr>";
		}
		echo "<tr><td colspan='4' align='right'><input type='submit' value='".T_("SELECTED_DELETE")."' /></td></tr></table></form>";
		
		echo "<p>".T_("THEME_IN_THEMES_BUT_NOT_IN_DB")."</p><form id='addtheme' action='/admincp?action=style&amp;do=add2' method='post'><table width='60%' class='table_table' align='center'>".
			"<tr><th class='table_head'>".T_("NAME")."</th><th class='table_head'>".T_("THEME_FOLDER_NAME")."</th><th width='5%' class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th></tr>";
		$dh = opendir("views/themes/");
		$i=0;
		while (($file = readdir($dh)) !== false) {
			if ($file == "." || $file == ".." || !is_dir("views/themes/$file"))
				continue;
			if (is_file("views/themes/$file/header.php")) {
					$res = DB::run("SELECT id FROM stylesheets WHERE uri = '$file' ");
					if ($res->rowCount() == 0) {
						echo "<tr><td class='table_col1' align='center'><input type='text' name='add[$i][name]' value='$file' /></td><td class='table_col2' align='center'>$file<input type='hidden' name='add[$i][uri]' value='$file' /></td><td class='table_col1' align='center'><input type='checkbox' name='add[$i][add]' value='1' /></td></tr>";
						$i++;
					}
				}
		}
		if (!$i) echo "<tr><td class='table_col1' align='center' colspan='3'>".T_("THEME_NOTHING_TO_SHOW")."</td></tr>";
		echo "</table><p align='center'>".($i?"<input type='submit' value='".T_("SELECTED_ADD")."' />":"")."</p></form>";
		end_frame();
		stdfoot();
	}
}
