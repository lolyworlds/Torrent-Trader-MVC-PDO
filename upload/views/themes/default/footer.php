</div>
<!-- END MIDDLE COLUMN -->
<!-- START RIGHT COLUMN -->
    <?php if ($site_config["RIGHTNAV"]){ ?>
<div class="col-sm-2">
    <?php rightblocks(); ?>
</div>
    <?php } ?>
<!-- END RIGHT COLUMN -->
</div>
</div>
<!-- END MAIN -->

<!-- Footer -->
<footer>
  <hr />
  <ul class="list-unstyled text-center">
        <li><?php printf (T_("POWERED_BY_TT"), $site_config["ttversion"]); ?></li>
        <li><?php $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart']; ?></li>
        <li><?php printf(T_("PAGE_GENERATED_IN"), $totaltime); ?></li>
        <li><a href="https://www.torrenttrader.xyz" target="_blank">www.torrenttrader.xyz</a> -|- <a href='<?php echo TTURL; ?>/rss'><i class="fa fa-rss-square"></i> <?php echo T_("RSS_FEED"); ?></a> - <a href='<?php echo TTURL; ?>/rss.phpcustom=1'><?php echo T_("FEED_INFO"); ?></a></li>
        <li>Bootstrap 4.3.1 -|- jQuery 3.4.1</li>
		<li>Update By: M-jay ©2020</li>
      </ul>
</footer>
<!-- Footer -->

  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script>
  <!-- TT old JS -->
  <script src="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/js/java_klappe.js"></script>
  <!-- Jquery 3.4 core JavaScript -->
  <script src="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/js/jquery-3.3.1.min"></script>
  <!-- Popper JavaScript -->
  <script src="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/js/popper.min"></script>
  <!-- Bootstrap 4 core JavaScript -->
  <script src="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/js/bootstrap-4.3.1"></script>


</body>

</html>
<?php ob_end_flush(); ?>