<nav class="navbar navbar-expand-lg navbarone">
        <!-- Start Infobar -->

            <?php
                if (!$CURUSER){
                    echo "&nbsp&nbsp<a href=".$site_config['SITEURL']."/account/login\><font color='#fff'>".T_("LOGIN")."</font></a>&nbsp//&nbsp<a href=".$site_config['SITEURL']."/faq\><font color='#fff'>".T_("FAQ")."</font></a>&nbsp//&nbsp<a href=".$site_config['SITEURL']."/rules\><font color='#fff'>".T_("RULES")."</font></a><b>";
                }else{
					
					$avatar = htmlspecialchars($CURUSER["avatar"]);
					if (!$avatar)
					$avatar = $site_config["SITEURL"]."/images/default_avatar.png";
	
					$userdownloaded = mksize($CURUSER["downloaded"]);
					$useruploaded = mksize($CURUSER["uploaded"]);
					$privacylevel = T_($CURUSER["privacy"]);
					
					if ($CURUSER["uploaded"] > 0 && $CURUSER["downloaded"] == 0)
						$userratio = '<span class="label label-success pull-right">Inf.</span>';
					elseif ($CURUSER["downloaded"] > 0)
						$userratio = '<span class="label label-info pull-right">'.number_format($CURUSER["uploaded"] / $CURUSER["downloaded"].'</span>', 2);
					else
						$userratio = '<span class="label label-info pull-right">---</span>';
					
                    print (T_("HI")." &nbsp<a href='$site_config[SITEURL]/users/profile?id=$CURUSER[id]'>".class_user_colour($CURUSER["username"])."</a>"); 
                    // call controller/method
					echo " &nbsp//&nbsp<a href='$site_config[SITEURL]/account/logout'><font color='#fff'>".T_("LOGOUT")."</font></a>";
                    if ($CURUSER["control_panel"]=="yes") {
                        print(" &nbsp//&nbsp<a href='$site_config[SITEURL]/admincp'><font color='#fff'>".T_("STAFFCP")."</font></a>");
                    }

                    // check for new pm's
                    global $pdo;
                    $arr = DB::run("SELECT * FROM messages WHERE receiver=" . $CURUSER["id"] . " and unread='yes' AND location IN ('in','both')")->fetchAll(); ;
                    $unreadmail = count($arr);
                    if ($unreadmail !== 0){
                        print(" &nbsp//&nbsp<a href='$site_config[SITEURL]/messages/inbox'><b><font color='#fff'>$unreadmail</font> ".P_("NEWPM", $unreadmail)."</b></a>");  
                    }else{
                        print(" &nbsp//&nbsp<a href='$site_config[SITEURL]/messages'><font color='#fff'>".T_("YOUR_MESSAGES")."</font></a>");
                    }   
                    //end check for pm's
					
          print ("! &nbsp//&nbsp
          <img src='$site_config[SITEURL]/images/seed.gif' border='none' height='20' width='20' alt='Downloaded' title='Downloaded'><font color='#FFCC66'><b>$userdownloaded</b></font>&nbsp;
          <img src='$site_config[SITEURL]/images/up.gif' border='none' height='20' width='20' alt='Uploaded' title='Uploaded'> <font color='#33CCCC'><b>$useruploaded</b></font> 
          <img src='$site_config[SITEURL]/images/button_online.png' border='none' height='20' width='20' alt='Ratio' title='Ratio'> (<b><font color='#FFF'>$userratio</font></b>)&nbsp
          //&nbsp;Bonus points:&nbsp;<a href='" .$site_config["SITEURL"]. "/bonus' title='Seed Bonus Points'><font color=#00cc00>$CURUSER[seedbonus]</font></a>&nbsp
          //&nbsp;Donated: <a href='" .$site_config["SITEURL"]. "/donate' title='Donated Amount'><font color=#ffff00>&nbsp;$CURUSER[donated]</font>&nbsp;");
//////connectable yes or know////////
if ($CURUSER["view_torrents"]=="yes") {
  $activeseed = get_row_count("peers", "WHERE userid = '$CURUSER[id]' AND seeder = 'yes'");
  $activeleech = get_row_count("peers", "WHERE userid = '$CURUSER[id]' AND seeder = 'no'");
  $stmt = DB::run("SELECT connectable FROM peers WHERE userid=" . $CURUSER["id"] . " LIMIT 1");
  $connect = $stmt->fetchColumn();
  if($connect == 'yes') {
  $connectable = "<b><font face=\"Verdana\" style=\"font-size: 10px\" color=\"#00FF00\">YES</font></b>";
  } elseif($connect == 'no') {
  $connectable = "<b><font face=\"Verdana\" style=\"font-size: 10px\" color=\"FF0000\">NO</font></b>";
  } else {
      $connectable ="<b><font face=\"Verdana\" style=\"font-size: 10px\" color=\"99CCFF\">Check Settings</font></b>";
 }    
}

print("&nbsp;<font color=#fff>(<i>Seeding:</i></font> <a href=\"javascript:popout(0)\"onclick=\"window.open('" .$site_config["SITEURL"]. "/peers/seeding1?id=" . $CURUSER["id"] . "','Seeding','width=350,height=350,scrollbars=yes')\"><font color='#ffff00'><b>".$activeseed."</b></font></a>&nbsp;");
print("<font color=#fff><i>Leeching:</i> </font><a href=\"javascript:popout(0)\"onclick=\"window.open('" .$site_config["SITEURL"]. "/peers/leeching?id=" . $CURUSER["id"] . "','Seeding','width=350,height=350,scrollbars=yes')\"><font color='#ffff00'><b>".$activeleech."</b></font></a>&nbsp;");
print("<font color=#fff><i>Connected:</i></font> ".$connectable.")");
//////connectable yes or know end of mod////////					
					
                }
                ?>

        <!-- End Infobar -->
        </div>
    </nav>
    <!-- END HEADER -->
    <!-- START NAVIGATION -->
    <nav class="navbar navbar-expand-lg">
  <a class="navbar-brand" href="<?php echo TTURL; ?>/index.php"><img src='<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/images/logo.gif' height='50px'></a>
      <?php if ($CURUSER){ ?>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
	  <li class="nav-item dropdown">
	  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Your Home</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
		    <a class="dropdown-item" href="<?php echo TTURL; ?>/users/profile?id=<?php echo $CURUSER["id"]; ?>"><?php echo T_("PROFILE");?></a>
			<a class="dropdown-item" href="<?php echo TTURL; ?>/messages/inbox"><?php echo T_("YOUR_MESSAGES");?></a>
            <a class="dropdown-item" href="<?php echo TTURL; ?>/peers/seeding?id=<?php echo $CURUSER['id']; ?>"><?php echo T_("YOUR_TORRENTS");?></a>
            <a class="dropdown-item" href="<?php echo TTURL; ?>/users/friends"><?php echo T_("FRIENDS");?></a>
            <a class="dropdown-item" href="<?php echo TTURL; ?>/bonus"><?php echo T_("SEEDING_BONUS");?></a> <!-- Check the link! -->
            <a class="dropdown-item" href="<?php echo TTURL; ?>/invite"><?php echo T_("INVITES");?></a> <!-- Check the link! -->
		</div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Torrents</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
			<a class="dropdown-item" href="<?php echo TTURL; ?>/torrents/browse"><?php echo T_("BROWSE_TORRENTS");?></a>
			<a class="dropdown-item" href="<?php echo TTURL; ?>/torrents/create"><?php echo T_("UPLOAD_TORRENT");?></a>
			<a class="dropdown-item" href="<?php echo TTURL; ?>/torrents/search"><?php echo T_("SEARCH_TORRENTS");?></a>
			<a class="dropdown-item" href="<?php echo TTURL; ?>/torrents/request"><?php echo T_("MAKE_REQUEST");?></a>
			<a class="dropdown-item" href="<?php echo TTURL; ?>/torrents/today"><?php echo T_("TODAYS_TORRENTS");?></a>
			<a class="dropdown-item" href="<?php echo TTURL; ?>/torrents/needseed"><?php echo T_("TORRENT_NEED_SEED");?></a>
        </div>
      </li>
	  <li class="nav-item dropdown">
	  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo T_("FORUMS");?></a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
		    <a class="dropdown-item" href="<?php echo TTURL; ?>/forums"><?php echo T_("FORUMS");?></a> 
		    <a class="dropdown-item" href="<?php echo TTURL; ?>/forums/viewunread"><?php echo T_("FORUM_NEW_POSTS");?></a> 
		    <a class="dropdown-item" href="<?php echo TTURL; ?>/forums/search"><?php echo T_("SEARCH");?></a> 
		    <a class="dropdown-item" href="<?php echo TTURL; ?>/faq"><?php echo T_("FORUM_FAQ");?></a>
		</div>
      </li>	
	  <li class="nav-item dropdown">
	  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Help</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
		    <a class="dropdown-item" href="<?php echo TTURL; ?>/intro"><?php echo T_("PDO HELP");?></a>
			<a class="dropdown-item" href="<?php echo TTURL; ?>/users/profile?id=<?php echo $CURUSER['id']; ?>"><?php echo T_("ACCOUNT_DETAILS");?></a> 
			<a class="dropdown-item" href="<?php echo TTURL; ?>/users/details?id=<?php echo $CURUSER['id']; ?>"><?php echo T_("UserCP");?></a> 
			<a class="dropdown-item" href="<?php echo TTURL; ?>/messages/inbox"><?php echo T_("YOUR_MESSAGES");?></a>
			<a class="dropdown-item" href="<?php echo TTURL; ?>/torrents/create"><?php echo T_("UPLOAD_TORRENT");?></a>
		</div>
      </li>	
	  <li class="nav-item dropdown">
	  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Contact Us</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
		    <a class="dropdown-item" href="<?php echo TTURL; ?>/group/staff">Contact Mods</a> 
		    <a class="dropdown-item" href="<?php echo TTURL; ?>/group/staff"><?php echo T_("STAFF_CONTACTS");?></a> 
		    <a class="dropdown-item" href="#"><?php echo T_("TWITTER");?></a>
		    <a class="dropdown-item" href="#"><?php echo T_("FACEBOOK");?></a>
		</div>
      </li>
	<?php if ($CURUSER["control_panel"]=="yes") { ?>
	  <li class="nav-item dropdown">
	  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
			<a href="<?php echo TTURL; ?>/admincp" class="btn btn-warning"><?php echo T_("STAFFCP");?></a>
			<a class="dropdown-item" href="<?php echo TTURL; ?>/users/profile?id=<?php echo $CURUSER['id']; ?>"><?php echo T_("ACCOUNT_DETAILS");?></a> 
			<a class="dropdown-item" href="<?php echo TTURL; ?>/users/details?id=<?php echo $CURUSER['id']; ?>"><?php echo T_("YOUR_PROFILE");?></a> 
			<a class="dropdown-item" href="<?php echo TTURL; ?>/messages/inbox"><?php echo T_("YOUR_MESSAGES");?></a>
			<a class="dropdown-item" href="<?php echo TTURL; ?>/torrents/create"><?php echo T_("UPLOAD_TORRENT");?></a>
		</div>
      </li>
	<?php } ?>	  
	  <li>
		<div class="search-container">
			<form method="get" action="<?php echo TTURL; ?>/torrents/search" class="form-inline">
				<div class="input-group-sm">
					<input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
					<span class="input-group-btn">
						<button type="submit" class="btn btn-secondary btn-sm"/><?php echo T_("SEARCH"); ?></button>
					</span>
				</div>
			</form>
		</div>
      </li>
    </ul>
  </div>
    <?php } ?>
</nav>
<!-- <br> -->

    <!-- END NAVIGATION -->