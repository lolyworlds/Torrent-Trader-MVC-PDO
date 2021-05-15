<?php
if ($_SESSION['loggedin'] == true) {
    Block::begin(Lang::T("NAVIGATION"));
    ?>

    <div class="list-group">
	<a href='<?php echo URLROOT; ?>/index' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("HOME"); ?></a>
	<a href='<?php echo URLROOT; ?>/topten' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("Top 10"); ?></a>
    <?php
if ($_SESSION["view_torrents"] == "yes" || !MEMBERSONLY) {?>
	<a href='<?php echo URLROOT; ?>/search/browse' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("BROWSE_TORRENTS"); ?></a>
	<a href='<?php echo URLROOT; ?>/search/today' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("TODAYS_TORRENTS"); ?></a>
	<a href='<?php echo URLROOT; ?>/search' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("SEARCH"); ?></a>
	<a href='<?php echo URLROOT; ?>/seed/needseed' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("TORRENT_NEED_SEED"); ?></a>
<?php }
    if ($_SESSION["edit_torrents"] == "yes") {?>
	<a href='<?php echo URLROOT; ?>/import' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("MASS_TORRENT_IMPORT"); ?></a>
<?php }
    if ($_SESSION['loggedin'] == true && $_SESSION["view_users"] == "yes") {?>
	<a href='<?php echo URLROOT; ?>/teams/index' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("TEAMS"); ?></a>
	<a href='<?php echo URLROOT; ?>/members' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("MEMBERS"); ?></a>
<?php }?>
	<a href='<?php echo URLROOT; ?>/rules' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("SITE_RULES"); ?></a>
	<a href='<?php echo URLROOT; ?>/faq' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("FAQ"); ?></a>
<?php if ($_SESSION['loggedin'] == true && $_SESSION["view_users"] == "yes") {?>
	<a href='<?php echo URLROOT; ?>/staff' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo Lang::T("STAFF"); ?></a>
<?php }?>
    </div>
	<!-- end content -->

<?php block::end();
}