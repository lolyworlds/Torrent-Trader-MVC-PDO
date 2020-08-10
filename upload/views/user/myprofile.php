<?php
			usermenu();
			if ($user["privacy"] != "strong" || ($CURUSER["control_panel"] == "yes") || ($CURUSER["id"] == $user["id"])) {
			?>
			<table align="center" border="0" cellpadding="6" cellspacing="1" width="100%">
			<tr>
				<td width="50%"><b><?php echo T_("PROFILE"); ?></b></td>
				<td width="50%"><b><?php echo T_("ADDITIONAL_INFO"); ?></b></td>
			</tr>
		
			<tr valign="top">
				<td align="left">
                <?php echo T_("USERNAME"); ?>: <?php echo class_user_colour($user["username"])?><br />
                <?php echo T_("EMAIL"); ?>: <?php echo $user["email"]; ?><br />
	            <?php echo T_("PASSKEY"); ?>: <?php echo $user["passkey"]; ?><br />
                <?php echo T_("IP"); ?>: <?php echo $user["ip"]; ?><br />
				<?php echo T_("USERCLASS"); ?>: <?php echo get_user_class_name($user["class"])?><br />
				<?php echo T_("TITLE"); ?>: <i><?php echo format_comment($user["title"])?></i><br />
				<?php echo T_("JOINED"); ?>: <?php echo htmlspecialchars(utc_to_tz($user["added"]))?><br />
				<?php echo T_("LAST_VISIT"); ?>: <?php echo htmlspecialchars(utc_to_tz($user["last_access"]))?><br />
				<?php echo T_("LAST_SEEN"); ?>: <?php echo htmlspecialchars($user["page"]);?><br />
				</td>
		
				<td align="left">
                <?php echo T_("AGE"); ?>: <?php echo htmlspecialchars($user["age"])?><br />
                <?php echo T_("GENDER"); ?>: <?php echo T_($user["gender"]); ?><br />
				<?php echo T_("CLIENT"); ?>: <?php echo htmlspecialchars($user["client"])?><br />
				<?php echo T_("COUNTRY"); ?>: <?php echo $country?><br />
				<?php echo T_("DONATED"); ?>  <?php echo $site_config['currency_symbol']; ?><?php echo number_format($user["donated"], 2); ?><br /> 
				<?php echo T_("WARNINGS"); ?>: <?php echo htmlspecialchars($user["warned"])?><br />
				<?php if ($CURUSER["edit_users"] == "yes"){ echo T_("ACCOUNT_PRIVACY_LVL").": <b>".T_($user["privacy"])."</b><br />"; }?>
				<?php echo T_("SIGNATURE"); ?>:</b> <?php echo format_comment($usersignature); ?><br />
				</td>
			</tr>
		
			<tr>
				<td width="50%"><b><?php echo T_("STATISTICS"); ?></b></td>
                <td width="50%"><b><?php echo T_("OTHER"); ?></b></td>
			</tr>
		
			<tr valign="top">
				<td align="left">
				<?php echo T_("UPLOADED"); ?>: <?php echo mksize($user["uploaded"]); ?><br />
				<?php echo T_("DOWNLOADED"); ?>: <?php echo mksize($user["downloaded"]); ?><br />
				<?php echo T_("RATIO"); ?>: <?php echo $ratio; ?><br />
				<?php echo T_("AVG_DAILY_DL"); ?>: <?php echo mksize($user["downloaded"] / (DateDiff($user["added"], time()) / 86400)); ?><br />
				<?php echo T_("AVG_DAILY_UL"); ?>: <?php echo mksize($user["uploaded"] / (DateDiff($user["added"], time()) / 86400)); ?><br />
				<?php echo T_("TORRENTS_POSTED"); ?>: <?php echo number_format($numtorrents); ?><br />
				<?php echo T_("COMMENTS_POSTED"); ?>: <?php echo number_format($numcomments); ?><br />
				Forum Posts: <?php echo number_format($numforumposts); ?><br />   
				</td>
		
				<td align="left">
                
				<img src="<?php echo $avatar; ?>" alt="" title="<?php echo class_user_colour($user["username"]); ?>" height="80" width="80" /><br />
				<a href="<?php echo $site_config['SITEURL'] ?>/messages/create?&amp;id=<?php echo $user["id"]?>"><button type='button' class='btn btn-sm btn-success'><?php echo T_("SEND_PM") ?></button></a><br />
				<!-- <a href=#>View Forum Posts</a><br />
				<a href=#>View Comments</a><br /> -->
				<a href="<?php echo $site_config['SITEURL'] ?>/report/user?user=<?php echo $user["id"]?>"><button type='button' class='btn btn-sm btn-danger'><?php echo T_("REPORT_MEMBER") ?></button></a><br />
			<?php if ($CURUSER["edit_users"]=="yes") { ?>
		  <div style="margin-bottom:3px"><a href="<?php echo $site_config['SITEURL']; ?>/snatched?uid=<?php echo $user["id"]?>"><button type='button' class='btn btn-sm btn-warning'><?php echo T_("SNATCHLIST") ?></button></a></div>
		<?php } ?>
				</td>
			</tr>
			<?php if ($CURUSER["edit_users"] == "yes") { ?>
			<tr>
				<td width="50%"><b><?php echo T_("STAFF_ONLY_INFO"); ?></b></td>
			</tr>
		
			<tr valign="top">
				<td align="left">
					<?php
						if ($user["invited_by"]) {
							$invited = $user['invited_by'];
							$row = DB::run("SELECT username FROM users WHERE id=?", [$invited])->fetch();
							echo "<b>".T_("INVITED_BY").":</b> <a href=\"$site_config[SITEURL]/users/profile?id=$user[invited_by]\">".class_user_colour($row['username'])."</a><br />";
						}
						echo "<b>".T_("INVITES").":</b> ".number_format($user["invites"])."<br />";
						$invitees = array_reverse(explode(" ", $user["invitees"]));
						$rows = array();
						foreach ($invitees as $invitee) {
							$res = DB::run("SELECT id, username FROM users WHERE id=? and status=?", [$invitee, 'confirmed']);
							if ($row = $res->fetch()) {
								$rows[] = "<a href=\"$site_config[SITEURL]/users/profile?id=$row[id]\">".class_user_colour($row['username'])."</a>";
							}
						}
						if ($rows)
							echo "<b>".T_("INVITEES").":</b> ".implode(", ", $rows)."<br />";
					?>
				</td>
			</tr>
			<?php
			}
			//team
			$res = DB::run("SELECT name,image FROM teams WHERE id=? LIMIT 1", [$user['team']]);
			if ($res->rowCount() == 1) {
				$arr = $res->fetch();
				echo "<tr><td colspan='2' align='left'><b>Team Member Of:</b><br />";
				echo"<img src='".htmlspecialchars($arr["image"])."' alt='' /><br />".sqlesc($arr["name"])."<br /><br /><a href='$site_config[SITEURL]/teams'>[View ".T_("TEAMS")."]</a></td></tr>"; 
			}
			?>
		
			</table>
		
			<?php
		}else{
			echo sprintf(T_("REPORT_MEMBER_MSG"), $user["id"]);
		}