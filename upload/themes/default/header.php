<!doctype html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <!-- Theme css -->
    <link rel="shortcut icon" href="<?php echo $site_config["SITEURL"]; ?>/themes/default/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="<?php echo $site_config["SITEURL"]; ?>/themes/default/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $site_config["SITEURL"]; ?>/themes/default/theme.css" />
    <!-- JS -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo $site_config["SITEURL"]; ?>/backend/java_klappe.js"></script>
    <!-- Style  -->
</head>

<body>
    <!-- START HEADER -->
    <nav class="navbar navbar-expand-lg navbar-dark primary-color">
        <a class="navbar-brand" href='index.php' title='Home' target='_self'><img
                src='<?php echo $site_config["SITEURL"]; ?>/themes/default/images/logo.gif' alt='logo' width='212'
                height='46' /></a>
        <!-- Start Infobar -->
        <div class='infobar'>
            <?php
                if (!$CURUSER){
                    echo "[<a href=\"account-login.php\"><font color='#fff'>".T_("LOGIN")."</font></a>]<b> ";
                }else{
                    print (T_("LOGGED_IN_AS").": ".$CURUSER["username"].""); 
                    echo " [<a href=\"account-logout.php\"><font color='#fff'>".T_("LOGOUT")."</font></a>] ";
                    if ($CURUSER["control_panel"]=="yes") {
                        print("[<a href='admincp.php'><font color='#fff'>".T_("STAFFCP")."</font></a>] ");
                    }

                    // check for new pm's
                    $arr = DB::run("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " and unread='yes' AND location IN ('in','both')")->fetch();
                    $unreadmail = $arr[0];
                    if ($unreadmail){
                        print("[<a href='mailbox.php?inbox'><b><font color='#fff'>$unreadmail</font> ".P_("NEWPM", $unreadmail)."</b></a>]");  
                    }else{
                        print("[<a href='mailbox.php'><font color='#fff'>".T_("YOUR_MESSAGES")."</font></a>] ");
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
                <li><a href="forums.php"><?php echo T_("FORUMS");?></a></li>
                <li><a href="torrents-upload.php"><?php echo T_("UPLOAD_TORRENT");?></a></li>
                <li><a href="torrents.php"><?php echo T_("BROWSE_TORRENTS");?></a></li>
                <li><a href="torrents-today.php"><?php echo T_("TODAYS_TORRENTS");?></a></li>
                <li><a href="torrents-search.php"><?php echo T_("SEARCH_TORRENTS");?></a></li>
            </ul>
        </div>
    </div>
    <?php } ?>
    <!-- END NAVIGATION -->
    <!-- Start Content -->
    <div class="container-fluid">
        <div class="row content">
            <!-- START left COLUMN -->
            <div class="col-sm-2 sidenav">
                <?php if ($site_config["LEFTNAV"]){ ?>

                <?php leftblocks();?>
            </div>
            <?php } ?>
            <!-- END LEFT COLUMN -->
            <!-- START MIDDLE COLUMN -->
            <div class="col-sm-8 middlebit">