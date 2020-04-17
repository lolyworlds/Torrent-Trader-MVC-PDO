    <nav class="navbar navbar-expand-lg navbar-dark primary-color">
        <a class="navbar-brand" href='index.php' title='Home' target='_self'><img
                src='<?php echo $site_config["SITEURL"]; ?>/views/themes/default/images/logo.gif' alt='logo' width='212'
                height='46' /></a>
        <!-- Start Infobar -->
        <div class='infobar'>
            <?php
                if (!$CURUSER){
                    echo "[<a href=\"/accountlogin\"><font color='#fff'>".T_("LOGIN")."</font></a>]<b> ";
                }else{
                    print (T_("LOGGED_IN_AS").": ".class_user($CURUSER["username"]).""); 
                    // call controller/method
					echo " [<a href='/accountlogin/logout'><font color='#fff'>".T_("LOGOUT")."</font></a>] ";
                    if ($CURUSER["control_panel"]=="yes") {
                        print("[<a href='/admincp'><font color='#fff'>".T_("STAFFCP")."</font></a>] ");
                    }

                    // check for new pm's
                    $arr = DB::run("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " and unread='yes' AND location IN ('in','both')")->fetch();
                    $unreadmail = $arr[0];
                    if ($unreadmail){
                        print("[<a href='mailbox?inbox'><b><font color='#fff'>$unreadmail</font> ".P_("NEWPM", $unreadmail)."</b></a>]");  
                    }else{
                        print("[<a href='mailbox'><font color='#fff'>".T_("YOUR_MESSAGES")."</font></a>] ");
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
    <div class='navigation'>
        <div class='menu'>
            <ul id='nav-one' class='dropmenu'>
                <li><a href="index.php"><?php echo T_("HOME");?></a></li>
                <li><a href="forums"><?php echo T_("FORUMS");?></a></li>
                <li><a href="torrentsupload"><?php echo T_("UPLOAD_TORRENT");?></a></li>
                <li><a href="torrentsmain"><?php echo T_("BROWSE_TORRENTS");?></a></li>
                <li><a href="torrentstoday"><?php echo T_("TODAYS_TORRENTS");?></a></li>
                <li><a href="torrentssearch"><?php echo T_("SEARCH_TORRENTS");?></a></li>
            </ul>
        </div>
    </div>
    <?php } ?>
    <!-- END NAVIGATION -->