<?php

if ($action=="polls" && $do=="view"){
	$title = T_("POLLS_MANAGEMENT");
    require 'views/admin/header.php';
	adminnavmenu();
	begin_frame(T_("POLLS_MANAGEMENT"));

	echo "<center><a href='$site_config[SITEURL]/admincp?action=polls&amp;do=add'>Add New Poll</a>&nbsp;/&nbsp;";
	
	echo "<a href='$site_config[SITEURL]/admincp?action=polls&amp;do=results'>View Poll Results</a></center>";

	echo "<b>Polls</b> (Top poll is current)<br />";

	$query = DB::run("SELECT id,question,added FROM polls ORDER BY added DESC");
	echo "<div class='border border-warning'>";
	while($row = $query->fetch(PDO::FETCH_ASSOC)){
		echo "<a href='$site_config[SITEURL]/admincp?action=polls&amp;do=add&amp;subact=edit&amp;pollid=$row[id]'>".stripslashes($row["question"])."</a> - ".utc_to_tz($row['added'])." - <a href='$site_config[SITEURL]/admincp?action=polls&amp;do=delete&amp;id=$row[id]'>Delete</a><br />\n\n";
	}
    echo '</div>';
	end_frame();

	require 'views/admin/footer.php';
}


/////////////
if ($action=="polls" && $do=="results"){
	$title = T_("POLLS_MANAGEMENT");
    require 'views/admin/header.php';
	adminnavmenu();
	begin_frame("Results");
	echo "<table class=\"table_table\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"95%\">";
	echo '<tr>';
	echo '<th class="table_head">Username</th>';
	echo '<th class="table_head">Question</th>';
	echo '<th class="table_head">Voted</th>';
	echo '</tr>';

	$poll = DB::run("SELECT * FROM pollanswers ORDER BY pollid DESC");
	while ($res = $poll->fetch(PDO::FETCH_LAZY)) {
		$user = DB::run("SELECT username,id FROM users WHERE id =?", [$res['userid']])->fetch();
		$option = "option".$res["selection"];
		if ($res["selection"] < 255) {
			$vote = DB::run("SELECT ".$option." FROM polls WHERE id =?", [$res['pollid']])->fetch();
		} else {
			$vote["option255"] = "Blank vote";
		}
		$sond = DB::run("SELECT question FROM polls WHERE id =?", [$res['pollid']])->fetch();
		
		echo '<tr>';
		echo '<td class="table_col1" align="left"><b>';
		echo '<a href="'.TTURL.'/users/profile?id='.$user["id"].'">';
		echo '&nbsp;&nbsp;'.class_user_colour($user['username']);
		echo '</a>';
		echo '</b></td>';
		echo '<td class="table_col2" align="center">';
		echo '&nbsp;&nbsp;'.$sond['question'];
		echo '</td>';
		echo '<td class="table_col1" align="center">';
		echo $vote["$option"];
		echo '</td>';
		echo '</tr>';
	}

	echo '</table>';
	end_frame();
	require 'views/admin/footer.php';
}


if ($action=="polls" && $do=="delete"){
	$id = (int)$_GET["id"];
	
	if (!is_valid_id($id))
		show_error_msg(T_("ERROR"),sprintf(T_("CP_NEWS_INVAILD_ITEM_ID"), $newsid),1);

	DB::run("DELETE FROM polls WHERE id=?", [$id]);
	DB::run("DELETE FROM pollanswers WHERE  pollid=?", [$id]);
	
	autolink(TTURL."/admincp?action=polls&do=view", T_("Poll and answers deleted"));
}

if ($action=="polls" && $do=="add"){
	$title = T_("POLLS_MANAGEMENT");
    require 'views/admin/header.php';
	adminnavmenu();

	$pollid = (int)$_GET["pollid"];

	if ($_GET["subact"] == "edit"){
		$res = DB::run("SELECT * FROM polls WHERE id =?", [$pollid]);
		$poll = $res->fetch(PDO::FETCH_LAZY);
	}
                                
	begin_frame("Polls");
	?>                                                
    <form method="post" action="<?php echo TTURL; ?>/admincp?action=polls&amp;do=save"> 
	<table border="0" cellspacing="0" class="table_table" align="center">
    <tr><td class="table_col1">Question <font class="error">*</font></td><td class="table_col2" align="left"><input name="question" size="60" maxlength="255" value="<?php echo $poll['question']; ?>" /></td></tr>
    <tr><td class="table_col1">Option 1 <font class="error">*</font></td><td class="table_col2" align="left"><input name="option0" size="60" maxlength="40" value="<?php echo $poll['option0']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 2 <font class="error">*</font></td><td class="table_col2" align="left"><input name="option1" size="60" maxlength="40" value="<?php echo $poll['option1']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 3</td><td class="table_col2" align="left"><input name="option2" size="60" maxlength="40" value="<?php echo $poll['option2']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 4</td><td class="table_col2" align="left"><input name="option3" size="60" maxlength="40" value="<?php echo $poll['option3']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 5</td><td class="table_col2" align="left"><input name="option4" size="60" maxlength="40" value="<?php echo $poll['option4']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 6</td><td class="table_col2" align="left"><input name="option5" size="60" maxlength="40" value="<?php echo $poll['option5']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 7</td><td class="table_col2" align="left"><input name="option6" size="60" maxlength="40" value="<?php echo $poll['option6']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 8</td><td class="table_col2" align="left"><input name="option7" size="60" maxlength="40" value="<?php echo $poll['option7']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 9</td><td class="table_col2" align="left"><input name="option8" size="60" maxlength="40" value="<?php echo $poll['option8']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 10</td><td class="table_col2" align="left"><input name="option9" size="60" maxlength="40" value="<?php echo $poll['option9']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Sort</td><td class="table_col2">
    <input type="radio" name="sort" value="yes" <?php echo $poll["sort"] != "no" ? " checked='checked'" : "" ?> />Yes
    <input type="radio" name="sort" value="no" <?php echo $poll["sort"] == "no" ? " checked='checked'" : "" ?> /> No
    </td></tr>
    <tr><td class="table_head" colspan="2" align="center"><input type="submit" value="<?php echo $pollid ? "Edit poll": "Create poll"; ?>" /></td></tr>
    </table>
    <p><font class="error">*</font> required</p>
    <input type="hidden" name="pollid" value="<?php echo $poll["id"]?>" />
    <input type="hidden" name="subact" value="<?php echo $pollid?'edit':'create'?>" />
    </form>
	<?php
	end_frame();
	require 'views/admin/footer.php';
}

if ($action=="polls" && $do=="save"){

	$subact = $_POST["subact"];
	$pollid = (int)$_POST["pollid"];

	$question = $_POST["question"];
	$option0 = $_POST["option0"];
	$option1 = $_POST["option1"];
	$option2 = $_POST["option2"];
	$option3 = $_POST["option3"];
	$option4 = $_POST["option4"];
	$option5 = $_POST["option5"];
	$option6 = $_POST["option6"];
	$option7 = $_POST["option7"];
	$option8 = $_POST["option8"];
	$option9 = $_POST["option9"];
	$option10 = $_POST["option10"];
	$sort = (int)$_POST["sort"];

	if (!$question || !$option0 || !$option1)
		show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA")."!", 1);

	if ($subact == "edit"){

		if (!is_valid_id($pollid))
			show_error_msg(T_("ERROR"),T_("INVALID_ID"),1);

		DB::run("UPDATE polls SET " .
		"question = ?, " .
		"option0 = ?, " .
		"option1 = ?, " .
		"option2 = ?, " .
		"option3 = ?, " .
		"option4 = ?, " .
		"option5 = ?, " .
		"option6 = ?, " .
		"option7 = ?, " .
		"option8 = ?, " .
		"option9 = ?, " .
		"option10 =?, " .
		"sort =? " .
	"WHERE id = $pollid", [$question, $option0, $option1, $option2, $option3, $option4, $option5,
	$option6, $option7, $option8, $option9, $option10, $sort]);
	}else{
		DB::run("INSERT INTO polls (added,question,option0,option1,option2,option3,option4,option5,
		option6,option7,option8,option9,sort)
		VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", [get_date_time(), $question, $option0, $option1,
		 $option2, $option3, $option4, $option5,
	$option6, $option7, $option8, $option9, $sort]);
	}

	autolink(TTURL."/admincp?action=polls&do=view", T_("COMPLETE"));
}