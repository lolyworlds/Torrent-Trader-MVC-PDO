    <nav class="navbar navbar-expand-lg">
        <!-- Start Infobar -->
        <div class='infobar'>
            <?php
                if (!$CURUSER){
                    echo "[<a href=".$site_config['SITEURL']."/account/login\><font color='#fff'>".T_("LOGIN")."</font></a>]<b> ";
                }else{
                    print (T_("HI")." ".class_user($CURUSER["username"]).""); 
                    // call controller/method
					echo " [<a href='$site_config[SITEURL]/account/logout'><font color='#fff'>".T_("LOGOUT")."</font></a>] ";
                    if ($CURUSER["control_panel"]=="yes") {
                        print("[<a href='$site_config[SITEURL]/admincp'><font color='#fff'>".T_("STAFFCP")."</font></a>] ");
                    }

                    // check for new pm's
                    global $pdo;
                    $arr = $pdo->run("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " and unread='yes' AND location IN ('in','both')")->fetch();
                    // offset fix
					$unreadmail = $arr[0];
                    if ($unreadmail){
                        print("[<a href='$site_config[SITEURL]/mailbox?inbox'><b><font color='#fff'>$unreadmail</font> ".P_("NEWPM", $unreadmail)."</b></a>]");  
                    }else{
                        print("[<a href='$site_config[SITEURL]/mailbox'><font color='#fff'>".T_("YOUR_MESSAGES")."</font></a>] ");
                    }   
                    //end check for pm's
                }
                ?>
        </div>
        <!-- End Infobar -->
        </div>
    </nav>
    <!-- END HEADER -->
    <!-- START NAVIGATION -->
    <?php if ($CURUSER){ ?>
    <nav class="navbar navbar-expand-lg">
  <a class="navbar-brand" href="<?php echo $site_config["SITEURL"]; ?>/index.php"><img src='<?php echo $site_config["SITEURL"]; ?>/views/themes/<?php echo $THEME; ?>/images/logo.gif'></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="<?php echo $site_config["SITEURL"]; ?>/index.php"><?php echo T_("HOME");?><span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $site_config["SITEURL"]; ?>/forums"><?php echo T_("FORUMS");?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $site_config["SITEURL"]; ?>/intro"><?php echo T_("pdo_help");?></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Torrents
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
			<a class="dropdown-item" href="<?php echo $site_config["SITEURL"]; ?>/torrents/browse"><?php echo T_("BROWSE_TORRENTS");?></a> 
			<a class="dropdown-item" href="<?php echo $site_config["SITEURL"]; ?>/torrentssearch"><?php echo T_("SEARCH_TORRENTS");?></a>
			<a class="dropdown-item" href="<?php echo $site_config["SITEURL"]; ?>/torrents/today"><?php echo T_("TODAYS_TORRENTS");?></a>
        </div>
      </li>
    </ul>
  </div>
</nav>
    <?php } ?>
    <!-- END NAVIGATION -->