<?php
if ($CURUSER) {
begin_block($CURUSER["username"]);

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
		$userratio = '<span class="label label-info pull-right">---</span>'; ?>
	<center><img src="<?php echo $avatar;?>" alt=""></center>
	<ul class="list-group">
		<li class="list-group-item"><?php echo T_("DOWNLOADED");?> : <span class="label label-danger pull-right"><?php echo $userdownloaded;?></span></li>
		<li class="list-group-item"><?php echo T_("UPLOADED");?>: <span class="label label-success pull-right"><?php echo $useruploaded;?></span></li>
		<li class="list-group-item"><?php echo T_("CLASS");?>: <div class="pull-right"><?php echo T_($CURUSER["level"]);?></div></li>
		<li class="list-group-item"><?php echo T_("ACCOUNT_PRIVACY_LVL");?>: <div class="pull-right"><?php echo $privacylevel;?></div></li>
		<li class="list-group-item"><?php echo T_("SEEDBONUS");?>: <a href="<?php echo TTURL; ?>/bonus"><?php echo $CURUSER['seedbonus'];?></a></span></li>
		<li class="list-group-item"><?php echo T_("RATIO");?>: <?php echo $userratio;?></span></li>
	</ul>
	<div class="text-center">
		<a href="<?php echo TTURL; ?>/usercp" class="btn btn-primary"><?php echo T_("ACCOUNT"); ?></a>
		<?php if ($CURUSER["control_panel"]=="yes") { ?>
		<a href="<?php echo TTURL; ?>/admincp" class="btn btn-warning"><?php echo T_("STAFFCP");?></a>
		<?php } ?>
	</div>

<?php 
end_block();
} ?>