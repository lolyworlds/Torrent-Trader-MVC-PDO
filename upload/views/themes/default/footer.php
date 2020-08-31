</div>
<!-- END MIDDLE COLUMN -->
<!-- START RIGHT COLUMN -->
    <?php if ($config["RIGHTNAV"]) {?>
<div class="col-sm-2">
    <?php rightblocks();?>
</div>
    <?php }?>
<!-- END RIGHT COLUMN -->
</div>
</div>
<!-- END MAIN -->

<!-- Footer -->
<footer>
  <hr />
  <ul class="list-unstyled text-center">
        <li><?php printf(T_("POWERED_BY_TT"), $config["ttversion"]);?></li>
        <li><?php $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart'];?></li>
        <li><?php printf(T_("PAGE_GENERATED_IN"), $totaltime);?></li>
        <li><a href="https://www.torrenttrader.xyz" target="_blank">www.torrenttrader.xyz</a> -|- <a href='<?php echo TTURL; ?>/rss'><i class="fa fa-rss-square"></i> <?php echo T_("RSS_FEED"); ?></a> - <a href='<?php echo TTURL; ?>/rss/custom=1'><?php echo T_("FEED_INFO"); ?></a></li>
        <li>Bootstrap 4.3.1 -|- jQuery 3.4.1</li>
		<li>Update By: M-jay 2020</li>
      </ul>
</footer>
<!-- Footer -->

</body>

</html>
<?php ob_end_flush();?>