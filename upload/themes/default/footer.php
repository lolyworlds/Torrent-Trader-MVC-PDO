</div>
<!-- END MIDDLE COLUMN -->
<!-- START RIGHT COLUMN -->
    <?php if ($site_config["RIGHTNAV"]){ ?>
<div class="col-sm-2 sidenav">
    <?php rightblocks(); ?>
</div>
    <?php } ?>
<!-- END RIGHT COLUMN -->
</div>
</div>
<!-- START FOOTER COLUMN -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php"><b>
                        <font color='#fff'><?php if ($CURUSER){ echo T_("HOME");}?></font>
                    </b></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href='/rss'><b>
                        <font color='#fff'><?php if ($CURUSER){  echo T_("RSS_FEED");}?></font>
                    </b></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href='/rss?custom=1'><b>
                        <font color='#fff'><?php if ($CURUSER){  echo T_("FEED_INFO"); }?></font>
                    </b></a>
            </li>
        </ul>
        <span class="navbar-text">
            <?php echo '<a href="http://www.torrenttrader.xyz"><b><font color="#fff">Powered by TorrentTrader PDO/BOOTSTRAP&nbsp&nbsp</font></b></a>';?>
            <?php $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart'];
            printf(T_("PAGE_GENERATED_IN"), $totaltime);?>
        </span>
    </div>
</nav>
<!-- END FOOTER COLUMN -->
</body>

</html>
<?php ob_end_flush(); ?>