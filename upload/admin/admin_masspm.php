<?php
if ($action=="masspm"){
	stdhead("Mass Private Message");
	navmenu();

    # Tidy Up...
    

	//send pm
	if ($_GET["send"] == '1'){

		$sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);

		$dt = sqlesc(get_date_time());
		$msg = $_POST['msg'];
        $subject = $_POST["subject"];

		if (!$msg)
			show_error_msg(T_("ERROR"),"Please Enter Something!",1);

		$updateset = array_map("intval", $_POST['clases']);

		$query = SQL_Query_exec("SELECT id FROM users WHERE class IN (".implode(",", $updateset).") AND enabled = 'yes' AND status = 'confirmed'");
		while($dat=mysqli_fetch_assoc($query)){
			SQL_Query_exec("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES ($sender_id, $dat[id], '" . get_date_time() . "', " . sqlesc($msg) .", ".sqlesc($subject).")");
		}

		write_log("A Mass PM was sent by ($CURUSER[username])");
		show_error_msg(T_("COMPLETE"), "Mass PM Sent",1);
		die;
	}

	begin_frame("Mass Private Message");
    
    print("<form name='masspm' method='post' action='admincp.php?action=masspm&amp;send=1'>\n"); 
	print("<table border='0' cellspacing='0' cellpadding='5' align='center' width='90%'>\n");
	

	$query = "SELECT group_id, level FROM groups";
	$res = SQL_Query_exec($query);

    echo "<tr><td><b>Send to:</b></td></tr>";
	while ($row = mysqli_fetch_array($res)){

		echo "<tr><td><input type='checkbox' name='clases[]' value='$row[group_id]' /> $row[level]<br /></td></tr>\n";
	}
                           
	?>   
    <tr>
    <td><b>Subject:</b><br /><input type="text" name="subject" size="30" /></td>
    </tr>
	<tr>
	<td><br /><b>Message: </b><br /> <?php print textbbcode("masspm", "msg"); ?></td>
	</tr>
    
	<tr>
	<td><b><?php echo T_("SENDER");?></b>
	<?php echo $CURUSER['username']?> <input name="sender" type="radio" value="self" checked="checked" />
	System <input name="sender" type="radio" value="system" /></td>
	</tr>

	<tr>
	<td><input type="submit" value="Send" /></td>
	</tr>
	</table></form>
	<?php
	end_frame();
	stdfoot();
}