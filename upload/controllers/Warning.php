<?php
  class Warning extends Controller
  {
      public function __construct()
      {
          // $this->userModel = $this->model('User');
      }

      // warn on account details
      public function index()
      {
		  
        dbconn();
		global $site_config, $CURUSER;
		loggedinonly();
		if ($action == 'addwarning'){
			$userid = (int)$_POST["userid"];
			$reason = $_POST["reason"];
			$expiry = (int)$_POST["expiry"];
			$type = $_POST["type"];
		
			if (!$this->valid->id($userid))
				show_error_msg(T_("EDITING_FAILED"), T_("INVALID_USERID"),1);
		
			if (!$reason || !$expiry || !$type){
				show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA").".", 1);
			}
		
			$timenow = get_date_time();
		
			$expiretime = get_date_time(gmtime() + (86400 * $expiry));
		
			$ret = DB::run("INSERT INTO warnings (userid, reason, added, expiry, warnedby, type) VALUES ('$userid','$reason','$timenow','$expiretime','".$CURUSER['id']."','$type')");
		
			$ret = DB::run("UPDATE users SET warned=? WHERE id=?",['yes',$userid]);
		
			$msg = sqlesc("You have been warned by " . $CURUSER["username"] . " - Reason: ".$reason." - Expiry: ".$expiretime."");
			$added = sqlesc(get_date_time());
			DB::run("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $userid, $msg, $added)");
		
			write_log($CURUSER['username']." has added a warning for user: <a href='$site_config[SITEURL]/users/profile?id=$userid'>$userid</a>");
			header("Location: ".TTURL."/users/profile?id=$userid");
			die;
		}
		
		
		if ($action == "deleteaccount"){
			
			if ($CURUSER["delete_users"] != "yes")//only allow admins to delete users
				show_error_msg(T_("ERROR"), T_("TASK_ADMIN"),1);
		
			$userid = (int)$_POST["userid"];
			$username = sqlesc($_POST["username"]);
			$delreason = sqlesc($_POST["delreason"]);
		
			if (!$this->valid->id($userid))
				show_error_msg(T_("FAILED"), T_("INVALID_USERID"),1);
		
			if ($CURUSER["id"] == $userid) 
				show_error_msg(T_("ERROR"), "You cannot delete yourself.", 1);
				
			if (!$delreason){
				show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA"), 1);
			}
		
			deleteaccount($userid);
		
			write_log($CURUSER['username']." has deleted account: $username");
		
			show_error_msg(T_("COMPLETED"), T_("USER_DELETE"), 1);
			die;
		}
		stdhead("User CP");
		
		$id = (int)$_GET["id"];
		
		if (!is_valid_id($id))
		  show_error_msg(T_("NO_SHOW_DETAILS"), "Bad ID.",1);
		
		$user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
		if(!$user)
			show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID")." $id.",1);
		
		//add invites check here
		if ($CURUSER["view_users"] == "no" && $CURUSER["id"] != $id)
			 show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);
			 
		if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $CURUSER["edit_users"] == "no")
			show_error_msg(T_("ERROR"), T_("NO_ACCESS_ACCOUNT_DISABLED"), 1);

		//Layout		
		begin_frame(sprintf(T_("USER_DETAILS_FOR"), class_user_colour($user["username"])));

			if($CURUSER["edit_users"]=="yes"){
				?>
		<a href='<?php echo TTURL; ?>/users/profile?id=<?php echo $user["id"]; ?>'><button type="button" class="btn btn-sm btn-primary">Details</button></a>
		<a href='<?php echo TTURL; ?>/peers/seeding?id=<?php echo $user["id"]; ?>'><button type="button" class="btn btn-sm btn-primary">Seeding</button></a>
		<a href='<?php echo TTURL; ?>/peers/uploaded?id=<?php echo $user["id"]; ?>'><button type="button" class="btn btn-sm btn-primary">Uploaded</button></a>		
		<?php if($CURUSER["edit_users"]=="yes"){ ?>
		<a href='<?php echo TTURL; ?>/users/details?id=<?php echo $user["id"]; ?>'><button type="button" class="btn btn-sm btn-primary">Edit</button></a>
		<a href='<?php echo TTURL; ?>/warning?id=<?php echo $user["id"]; ?>'><button type="button" class="btn btn-sm btn-primary">Warn</button></a>
		<?php } ?>
		<?php
		
			print '<a name="warnings"></a>';
			
			$res = DB::run("SELECT * FROM warnings WHERE userid=? ORDER BY id DESC", [$id]);
		
			if ($res->rowCount() > 0){
				?>
				<b>Warnings:</b><br />
				<table border="1" cellpadding="3" cellspacing="0" width="80%" align="center" class="table_table">
				<tr>
					<th class="table_head">Added</th>
					<th class="table_head"><?php echo T_("EXPIRE"); ?></th>
					<th class="table_head"><?php echo T_("REASON"); ?></th>
					<th class="table_head"><?php echo T_("WARNED_BY"); ?></th>
					<th class="table_head"><?php echo T_("TYPE"); ?></th>      
				</tr>
				<?php
		
				while ($arr = $res->fetch(PDO::FETCH_ASSOC)){
					if ($arr["warnedby"] == 0) {
						$wusername = T_("SYSTEM");
					} else {
						$res2 = DB::run("SELECT id,username FROM users WHERE id =?", [$arr['warnedby']]);
						$arr2 = $res2->fetch();
						$wusername = class_user_colour($arr2["username"]);
					}
					$arr['added'] = utc_to_tz($arr['added']);
					$arr['expiry'] = utc_to_tz($arr['expiry']);
		
					$addeddate = substr($arr['added'], 0, strpos($arr['added'], " "));
					$expirydate = substr($arr['expiry'], 0, strpos($arr['expiry'], " "));
					print("<tr><td class='table_col1' align='center'>$addeddate</td><td class='table_col2' align='center'>$expirydate</td><td class='table_col1'>".format_comment($arr['reason'])."</td><td class='table_col2' align='center'><a href='$site_config[SITEURL]/accountdetails?id=".$arr2['id']."'>".$wusername."</a></td><td class='table_col1' align='center'>".$arr['type']."</td></tr>\n");
				 }
		
				echo "</table>\n";
			}else{
				echo T_("NO_WARNINGS");
			}
		
		
			print("<form method='post' action='$site_config[SITEURL]/adminmodtasks'>\n");
			print("<input type='hidden' name='action' value='addwarning' />\n");
			print("<input type='hidden' name='userid' value='$id' />\n");
			echo "<br /><br /><center><table border='0'><tr><td align='right'><b>".T_("REASON").":</b> </td><td align='left'><textarea cols='40' rows='5' name='reason'></textarea></td></tr>";
			echo "<tr><td align='right'><b>".T_("EXPIRE").":</b> </td><td align='left'><input type='text' size='4' name='expiry' />(days)</td></tr>";
			echo "<tr><td align='right'><b>".T_("TYPE").":</b> </td><td align='left'><input type='text' size='10' name='type' /></td></tr>";
			echo "<tr><td colspan='2' align='center'><button type='submit' class='btn btn-sm btn-success'><b>" .T_("ADD_WARNING"). "</b></button></td></tr></table></center></form>";
		
			if($CURUSER["delete_users"] == "yes"){
				print("<hr /><center><form method='post' action='$site_config[SITEURL]/adminmodtasks'>\n");
				print("<input type='hidden' name='action' value='deleteaccount' />\n");
				print("<input type='hidden' name='userid' value='$id' />\n");
				print("<input type='hidden' name='username' value='".$user["username"]."' />\n");
				echo "<b>".T_("REASON").":</b><input type='text' size='30' name='delreason' />";
                echo "<button type='submit' class='btn btn-sm btn-danger'><b>" .T_("DELETE_ACCOUNT"). "</b></button></form></center>";
                echo "<a href='users/profile?id=$id'><button type='submit' class='btn btn-sm'><b>Back To Account</b></button></a></center>";
			}
		
		}
		
		end_frame();
		stdfoot();
      }
  }