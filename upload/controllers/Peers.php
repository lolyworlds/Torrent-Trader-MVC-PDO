<?php
  class Peers extends Controller
  {
      public function __construct()
      {
          // $this->userModel = $this->model('User');
      }

      public function index(){}

      // sharing on account details
      public function seeding()
      {
        dbconn();
		global $config;
		loggedinonly();
		stdhead("User CP");
		$id = (int)$_GET["id"];
		if (!is_valid_id($id))
		  show_error_msg(T_("NO_SHOW_DETAILS"), "Bad ID.",1);
		$user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
		if(!$user)
			show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID")." $id.",1);
		//add invites check here
		if ($_SESSION["view_users"] == "no" && $_SESSION["id"] != $id)
			 show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);	 
		if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $_SESSION["edit_users"] == "no")
			show_error_msg(T_("ERROR"), T_("NO_ACCESS_ACCOUNT_DISABLED"), 1);
		//Layout		
        begin_frame(sprintf(T_("USER_DETAILS_FOR"), class_user_colour($user["username"])));
        usermenu($id);
        if ($user["privacy"] != "strong" || ($_SESSION["control_panel"] == "yes") || ($_SESSION["id"] == $user["id"])) {
		
			$res = DB::run("SELECT torrent, uploaded, downloaded FROM peers WHERE userid =? AND seeder =?", [$id, 'yes']);
			if ($res->rowCount() > 0)
			  $seeding = peerstable($res);
		
			$res = DB::run("SELECT torrent, uploaded, downloaded FROM peers WHERE userid =? AND seeder =?", [$id, 'no']);
			if ($res->rowCount() > 0)
		
			  $leeching = peerstable($res);
		
			if ($seeding)
				print("<br><b>" .T_("CURRENTLY_SEEDING"). ":</b><br />$seeding<br /><br />");
		
			if ($leeching)
				print("<br><b>" .T_("CURRENTLY_LEECHING"). ":</b><br />$leeching<br /><br />");
		
			if (!$leeching && !$seeding)
				print("<br><b>".T_("NO_ACTIVE_TRANSFERS")."</b><br />");
		
		}
			echo "</div>"; // start id1
			
			echo "<div id=id3  style=display:none>"; // start id1
			//page numbers
			$page = (int) $_GET["page"];
			$perpage = 25;
			$where = "";
			if ($_SESSION['control_panel'] != "yes")
				$where = "AND anon='no'";
			$count = DB::run("SELECT COUNT(*) FROM torrents WHERE owner='$id' $where")->fetchColumn();
		
			unset($where);
			$orderby = "ORDER BY id DESC";
			//get sql info
			if ($count) {
				list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/accountdetails?id=$id&amp;");
				$res = DB::run("SELECT torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, torrents.anon, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.announce FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE owner = $id $orderby $limit");
			}else{
				unset($res);
			}
		
			if ($count) {
				print($pagertop);
				torrenttable($res);
				print($pagerbottom);
			}else {
				print("<b>".T_("UPLOADED_TORRENTS_ERROR")."</b><br />");
			}

		
		end_frame();
		stdfoot();
      }

      // sharing on account details
      public function uploaded()
      {
        dbconn();
		global $config;
		loggedinonly();
		stdhead("User CP");
		$id = (int)$_GET["id"];
		if (!is_valid_id($id))
		  show_error_msg(T_("NO_SHOW_DETAILS"), "Bad ID.",1);
		$user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
		if(!$user)
			show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID")." $id.",1);
		//add invites check here
		if ($_SESSION["view_users"] == "no" && $_SESSION["id"] != $id)
			 show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);	 
		if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $_SESSION["edit_users"] == "no")
			show_error_msg(T_("ERROR"), T_("NO_ACCESS_ACCOUNT_DISABLED"), 1);
		//Layout		
		begin_frame(sprintf(T_("USER_DETAILS_FOR"), class_user_colour($user["username"])));
		usermenu($id);
        $page = (int) $_GET["page"];
        $perpage = 25;
        $where = "";
        if ($_SESSION['control_panel'] != "yes")
            $where = "AND anon='no'";
        $count = DB::run("SELECT COUNT(*) FROM torrents WHERE owner='$id' $where")->fetchColumn();
    
        unset($where);
        $orderby = "ORDER BY id DESC";
        //get sql info
        if ($count) {
            list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/accountdetails?id=$id&amp;");
            $res = DB::run("SELECT torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, torrents.anon, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.announce FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE owner = $id $orderby $limit");
        }else{
            unset($res);
        }
    
        if ($count) {
            print($pagertop);
            torrenttable($res);
            print($pagerbottom);
        }else {
            print("<br><br><center><b>".T_("UPLOADED_TORRENTS_ERROR")."</b></center><br />");
        }
    

		end_frame();
		stdfoot();
      }

      // sharing on torrent details
      public function peerlist()
      {
        dbconn();
		global $config;
        $id = (int) $_GET["id"];

        if (!is_valid_id($id)) {
            show_error_msg(T_("ERROR"), T_("THATS_NOT_A_VALID_ID"), 1);
        }
        //check permissions
        if ($config["MEMBERSONLY"]) {
            loggedinonly();
        }
        if ($_SESSION["view_torrents"] == "no") {
            show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
        }
        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $char1 = 50; //cut length
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);

        stdhead(T_("DETAILS_FOR_TORRENT") . " \"" . $row["name"] . "\"");
        begin_frame(T_("TORRENT_DETAILS_FOR") . " \"" . $shortname . "\"");


	   if ($row["external"] != 'yes') {
		echo "<b>" . T_("PEERS_LIST") . ":</b><br />";
		$query = DB::run("SELECT * FROM peers WHERE torrent = $id ORDER BY seeder DESC");

		$result = $query->rowCount();
		if ($result == 0) {
			echo T_("NO_ACTIVE_PEERS") . "\n";
		} else {
			?>

<table class='table table-striped table-bordered table-hover'><thead>
				<tr>
					<th class="table_head"><?php echo T_("PORT"); ?></th>
					<th class="table_head"><?php echo T_("UPLOADED"); ?></th>
					<th class="table_head"><?php echo T_("DOWNLOADED"); ?></th>
					<th class="table_head"><?php echo T_("RATIO"); ?></th>
					<th class="table_head"><?php echo T_("_LEFT_"); ?></th>
					<th class="table_head"><?php echo T_("FINISHED_SHORT") . "%"; ?></th>
					<th class="table_head"><?php echo T_("SEED"); ?></th>
					<th class="table_head"><?php echo T_("CONNECTED_SHORT"); ?></th>
					<th class="table_head"><?php echo T_("CLIENT"); ?></th>
					<th class="table_head"><?php echo T_("USER_SHORT"); ?></th>
				</tr></thead><tbody>

				<?php
while ($row1 = $query->fetch(PDO::FETCH_ASSOC)) {

				if ($row1["downloaded"] > 0) {
					$ratio = $row1["uploaded"] / $row1["downloaded"];
					$ratio = number_format($ratio, 3);
				} else {
					$ratio = "---";
				}

				$percentcomp = sprintf("%.2f", 100 * (1 - ($row1["to_go"] / $row["size"])));

				if ($config["MEMBERSONLY"]) {
					$res = DB::run("SELECT id, username, privacy FROM users WHERE id=" . $row1["userid"] . "");
					$arr = $res->fetch(PDO::FETCH_LAZY);

					$arr["username"] = "<a href='$config[SITEURL]/users/profile?id=$arr[id]'>" . class_user_colour($arr['username']) . "</a>";
				}

				# With $config["MEMBERSONLY"] off this will be shown.
				if (!$arr["username"]) {
					$arr["username"] = "Unknown User";
				}

				if ($arr["privacy"] != "strong" || ($_SESSION["control_panel"] == "yes")) {
					print("<tr><td class='table_col2'>" . $row1["port"] . "</td><td class='table_col1'>" . mksize($row1["uploaded"]) . "</td><td class='table_col2'>" . mksize($row1["downloaded"]) . "</td><td class='table_col1'>" . $ratio . "</td><td class='table_col2'>" . mksize($row1["to_go"]) . "</td><td class='table_col1'>" . $percentcomp . "%</td><td class='table_col2'>$row1[seeder]</td><td class='table_col1'>$row1[connectable]</td><td class='table_col2'>" . htmlspecialchars($row1["client"]) . "</td><td class='table_col1'>$arr[username]</td></tr>");
				} else {
					print("<tr><td class='table_col2'>" . $row1["port"] . "</td><td class='table_col1'>" . mksize($row1["uploaded"]) . "</td><td class='table_col2'>" . mksize($row1["downloaded"]) . "</td><td class='table_col1'>" . $ratio . "</td><td class='table_col2'>" . mksize($row1["to_go"]) . "</td><td class='table_col1'>" . $percentcomp . "%</td><td class='table_col2'>$row1[seeder]</td><td class='table_col1'>$row1[connectable]</td><td class='table_col2'>" . htmlspecialchars($row1["client"]) . "</td><td class='table_col1'>Private</td></tr>");
				}

			}
			echo "</tbody></table>";
		}
	}

		
		end_frame();
		stdfoot();
      }
/////////////////////////////
////////////////////////////
///////////////////////////


      // popout
      public function seeding1()
      {
        dbconn();
		global $config;
		loggedinonly();

		$id = (int)$_GET["id"];
		
		if (!is_valid_id($id))
		  show_error_msg("Can't show details", "Bad ID.",1);
		  
		$userid = (int)$_GET["id"];
		
		$userid = $_GET['id'];
		$action = $_GET['action'];
		
		if (!$userid)
			$userid = $_SESSION['id'];
		
		if (!is_valid_id($userid))
			show_error_msg("Error", "Invalid ID $userid.");
		
		if ($userid != $_SESSION["id"])
			show_error_msg("Error", "Not allowed to view others activity here.");
		
		$user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
		if(!$user)
		show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID")." $id.",1);
		
		$res = DB::run("SELECT torrent,uploaded,downloaded FROM peers LEFT JOIN torrents ON torrent = torrents.id WHERE userid='$id' AND seeder='yes'");
		//$res = DB::run("SELECT torrent,uploaded,downloaded FROM peers LEFT JOIN torrents ON torrent = torrents.id WHERE userid='$id' AND seeder='yes' AND (torrents.team = 0 OR torrents.team = ".$_SESSION['team']. " OR ".$_SESSION['class']." = 7)");
		if ($res->rowCount() > 0)
		$seeding = peerstable($res);

		$res = DB::run("SELECT torrent,uploaded,downloaded FROM peers LEFT JOIN torrents ON torrent = torrents.id WHERE userid='$id' AND seeder='no'");		
		//$res = DB::run("SELECT torrent,uploaded,downloaded FROM peers LEFT JOIN torrents ON torrent = torrents.id WHERE userid='$id' AND seeder='no' AND (torrents.team = 0 OR torrents.team = ".$_SESSION['team']. " OR ".$_SESSION['class']." = 7)");
		if ($res->rowCount() > 0)
		$leeching = peerstable($res);
		
			if ($seeding)
				print("$seeding");
					
			if (!$seeding)
				print("<B>Currently not seeding<BR><BR><a href=\"javascript:self.close()\">close window</a><BR>");
		
      }

      // popout
      public function leeching()
      {
        dbconn();
		global $config;
		loggedinonly();

		$id = (int)$_GET["id"];
		
		if (!is_valid_id($id))
		  show_error_msg("Can't show details", "Bad ID.",1);
		  
		$userid = (int)$_GET["id"];
		
		$userid = $_GET['id'];
		$action = $_GET['action'];
		
		if (!$userid)
			$userid = $_SESSION['id'];
		
		if (!is_valid_id($userid))
			show_error_msg("Error", "Invalid ID $userid.");
		
		if ($userid != $_SESSION["id"])
			show_error_msg("Error", "Not allowed to view others activity here.");
		
		$user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
		if(!$user)
		show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID")." $id.",1);
		
		$res = DB::run("SELECT torrent,uploaded,downloaded FROM peers LEFT JOIN torrents ON torrent = torrents.id WHERE userid='$id' AND seeder='yes'");
		//$res = DB::run("SELECT torrent,uploaded,downloaded FROM peers LEFT JOIN torrents ON torrent = torrents.id WHERE userid='$id' AND seeder='yes' AND (torrents.team = 0 OR torrents.team = ".$_SESSION['team']. " OR ".$_SESSION['class']." = 7)");
		if ($res->rowCount() > 0)
		$seeding = peerstable($res);
		
		$res = DB::run("SELECT torrent,uploaded,downloaded FROM peers LEFT JOIN torrents ON torrent = torrents.id WHERE userid='$id' AND seeder='no'");
		//$res = DB::run("SELECT torrent,uploaded,downloaded FROM peers LEFT JOIN torrents ON torrent = torrents.id WHERE userid='$id' AND seeder='no' AND (torrents.team = 0 OR torrents.team = ".$_SESSION['team']. " OR ".$_SESSION['class']." = 7)");
		if ($res->rowCount() > 0)
		$leeching = peerstable($res);
		
			if ($leeching)
				print("$leeching");
				
			if (!$leeching)
				print("<B>Not currently leeching!<BR><br><a href=\"javascript:self.close()\">close window</a><BR>\n");
		
	  }
	  
	        // popout
			public function dead()
			{
			  dbconn();
			  global $config;
			  loggedinonly();
	  
			  if ($_SESSION["control_panel"] != "yes") { show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1); }

			  $page = (int) $_GET["page"];
			  $perpage = 50;
		  
			  $res2 = DB::run("SELECT COUNT(*) FROM torrents WHERE banned = 'no' AND seeders < 1");
			  $row2 = mysqli_fetch_array($res2);
			  $count = $row2[0];
		  
			  list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "$config[SITEURL]/peers/dead&amp;");
		  
			  $res = DB::run("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size, torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, torrents.last_action, users.username FROM torrents LEFT JOIN users ON torrents.owner = users.id WHERE torrents.banned = 'no' AND torrents.seeders < 1 ORDER BY torrents.added DESC $limit");
			
			  if ($_POST["do"] == "delete") {
				  if (!@count($_POST["torrentids"]))
					  show_error_msg("Error", "<div style='margin-top:10px; margin-bottom:10px' align='center'>You must select at least one torrent.  &nbsp; [<a href='$config[SITEURL]/peers/dead'><b>Return </b></a>]</div>", 1);
				  foreach ($_POST["torrentids"] as $id) {
					  deletetorrent(intval($id));
					  write_log("<a href=$config[SITEURL]/users/profile?id=$_SESSION[id]><b>$_SESSION[username]</b></a>deleted the torrent ID : [<b>$id</b>]of the page: <i><b>Dead Torrents </b></i>");
				  }
				  autolink(TTURL."/peers/dead", "The selected torrent has been successfully deleted .");
			  }
			 
			  stdhead("Dead Torrents");
		  
			  if ($count < 1) show_error_msg("".T_("NOTHING_FOUND")."", "<div style='margin-top:10px; margin-bottom:10px' align='center'><font size='2'><i>...No Dead Torrents !</i></font></div>", 1);
		  
			  begin_frame("The Dead Torrents");
		  
			  echo "<div style='margin-top:10px' align='center'><font size='4'>List of dead torrents without being shared and sorted by date of sharing </font></div>";
		  
			  echo "<hr class='barre'/><div style='margin-top:16px; margin-bottom:20px' align='center'><font size='4'>We have<font color='IndianRed'><b>$count</b></font> DEAD".($count != 1 ? "s" : "")." torrent".($count != 1 ? "s" : "")."</font></div>";
		  
			  If ($count > $perpage) { print($pagertop);
			  print("<br />");
			  }
			
			  ?>
			  <form id="myform" method='post' action='<?php echo $config['SITEURL']; ?>/peers/dead.php'>
			  <input type='hidden' name='do' value='delete' />
			  <table cellpadding="5" cellspacing="0" class="table_table" align="center" width="98%">
				  <tr>
					  <th class="table_head" align="left"><?php echo T_("TORRENT_NAME"); ?></th>
					  <th class="table_head" align="left"><?php echo T_("UPLOADER"); ?></th>
					  <th class="table_head"><?php echo T_("SIZE"); ?></th>
					  <th class="table_head"><img src="<?php echo $config['SITEURL']; ?>/images/seed.gif" border="0" title="Seeders"></th>
					  <th class="table_head"><img src="<?php echo $config['SITEURL']; ?>/images/leech.gif" border="0" title="Leechers"></th>
					  <th class="table_head"><img src="<?php echo $config['SITEURL']; ?>/images/ready.png" border="0" title="Completed"></th>
					  <th class="table_head" width="1%"><?php echo T_("ADDED"); ?></th>
					  <th class="table_head" width="1%"><?php echo T_("LAST_ACTION"); ?></th>
				  <?php if (get_user_class() >= 6) { ?>
					  <th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
				  <?php } ?>
				  </tr>  
		  
				  <?php
			  while ($row = $res->fetch(PDO::FETCH_ASSOC))
			  {
				  if ($row["username"]) $owner = "<a href='$config[SITEURL]/users/profile?id=".$row["owner"]."'><b>".$row["username"]."</b></a>";
				  else $owner = T_("UNKNOWN_USER");
				  ?> 
		  
				  <tr>
					  <td class="table_col1"><a href="<?php echo $config['SITEURL']; ?>/torrents/read?id=<?php echo $row["id"]; ?>"><?php echo CutName(htmlspecialchars($row["name"]), 50) ?></a></td>
					  <td class="table_col2"><?php echo $owner; ?></td>
					  <td class="table_col2" align="center"><?php echo mksize($row["size"]); ?></td>
					  <td class="table_col1" align="center"><font color="limegreen"><b><?php echo number_format($row["seeders"]); ?></b></font></td>
					  <td class="table_col2" align="center"><font color="red"><b><?php echo number_format($row["leechers"]); ?></b></font></td>
					  <td class="table_col1" align="center"><font color="#0080FF"><b><?php echo number_format($row["times_completed"]); ?></b></font></td>
					  <td class="table_col2" align="center"><?php echo date("d.M.Y H:i", utc_to_tz_time($row["added"])) ?></td>
					  <td class="table_col2" align="center"><?php echo date("d.M.Y H:i", utc_to_tz_time($row["last_action"])) ?></td>
				  <?php if (get_user_class() >= 6) { ?>
					  <td class='table_col2' align='center'><input type='checkbox' name='torrentids[]' value='<?php echo $row["id"]; ?>' /></td>
				  <?php } ?>
				  </tr>     
		  
			  <?php
			  }
			  ?>
			  </table>
			  <?php if (get_user_class() >= 6) { ?>
				  <div style='margin-top:3px' align='right'><input type='submit' value='Remove The Checks ' />&nbsp;</div>
			  <?php } ?>
			  </form>
			  <?php
			  If ($count > $perpage) { print($pagerbottom); } else { print("<br />"); }
		  
			  end_frame();
			  stdfoot();

			}
  }