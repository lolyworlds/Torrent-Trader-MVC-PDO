<?php
if ($CURUSER) {
	begin_block(T_("NAVIGATION")); 
?>
<div class="list-group">
	<a href='<?php echo TTURL; ?>/index' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("HOME"); ?></a>
<?php
if ($CURUSER["view_torrents"]=="yes" || !$site_config["MEMBERSONLY"]) { ?>
	<a href='<?php echo TTURL; ?>/torrents/browse' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("BROWSE_TORRENTS"); ?></a>
	<a href='<?php echo TTURL; ?>/torrents/today' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("TODAYS_TORRENTS"); ?></a>
	<a href='<?php echo TTURL; ?>/torrents/search' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("SEARCH"); ?></a>
	<a href='<?php echo TTURL; ?>/torrents/needseed' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("TORRENT_NEED_SEED"); ?></a>
<?php }
    if ($CURUSER["edit_torrents"]=="yes") { ?>
	<a href='<?php echo TTURL; ?>/torrents/import' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("MASS_TORRENT_IMPORT"); ?></a>
<?php }
    if ($CURUSER && $CURUSER["view_users"]=="yes") { ?>
	<a href='<?php echo TTURL; ?>/teams/index' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("TEAMS"); ?></a>
	<a href='<?php echo TTURL; ?>/users' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("MEMBERS"); ?></a>
<?php } ?>
	<a href='<?php echo TTURL; ?>/rules' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("SITE_RULES"); ?></a>
	<a href='<?php echo TTURL; ?>/faq' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("FAQ"); ?></a>
<?php if ($CURUSER && $CURUSER["view_users"]=="yes") { ?>
	<a href='<?php echo TTURL; ?>/group/staff' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("STAFF"); ?></a>
<?php } ?>
</div>
<?php
end_block();
}
?>