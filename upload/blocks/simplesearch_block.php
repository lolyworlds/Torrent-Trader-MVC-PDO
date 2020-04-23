<?php
if ($CURUSER){
begin_block(T_("SEARCH"));
?>
	<center>
	<form method="get" action="<?php echo $site_config[SITEURL] ?>/torrentssearch"><br />
	<input type="text" name="search" size="15" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" />
	<br /><br />
	<button type="submit" class="btn btn-primary btn-sm"><?php echo T_("SEARCH"); ?></button>
	</form>
	</center><br />
	<?php
end_block();
}
?>