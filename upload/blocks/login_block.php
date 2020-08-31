<?php
if ($_SESSION['loggedin'] == true) {
    begin_block(class_user_colour($_SESSION["username"]));

    $avatar = htmlspecialchars($_SESSION["avatar"]);
    if (!$avatar) {
        $avatar = $config["SITEURL"] . "/images/default_avatar.png";
    }

    $userdownloaded = mksize($_SESSION["downloaded"]);
    $useruploaded = mksize($_SESSION["uploaded"]);
    $privacylevel = T_($_SESSION["privacy"]);
    $countslot = DB::run("SELECT DISTINCT torrent FROM peers WHERE userid =?  AND seeder=?", [$_SESSION['id'], 'yes']);
    $maxslotdownload = $countslot->rowCount();
    $slots = number_format($_SESSION["maxslots"]) . "/" . number_format($maxslotdownload);

    if ($_SESSION["uploaded"] > 0 && $_SESSION["downloaded"] == 0) {
        $userratio = '<span class="label label-success pull-right">Inf.</span>';
    } elseif ($_SESSION["downloaded"] > 0) {
        $userratio = '<span class="label label-info pull-right">' . number_format($_SESSION["uploaded"] / $_SESSION["downloaded"] . '</span>', 2);
    } else {
        $userratio = '<span class="label label-info pull-right">---</span>';
    }
    ?>
	<center><img src="<?php echo $avatar; ?>" alt=""></center>
	<ul class="list-group">
		<li class="list-group-item"><?php echo T_("DOWNLOADED"); ?> : <span class="label label-danger pull-right"><?php echo $userdownloaded; ?></span></li>
		<li class="list-group-item"><?php echo T_("UPLOADED"); ?>: <span class="label label-success pull-right"><?php echo $useruploaded; ?></span></li>
		<li class="list-group-item"><?php echo T_("CLASS"); ?>: <div class="pull-right"><?php echo T_($_SESSION["level"]); ?></div></li>
		<li class="list-group-item"><?php echo T_("ACCOUNT_PRIVACY_LVL"); ?>: <div class="pull-right"><?php echo $privacylevel; ?></div></li>
		<li class="list-group-item"><?php echo T_("SEEDBONUS"); ?>: <a href="<?php echo TTURL; ?>/bonus"><?php echo $_SESSION['seedbonus']; ?></a></span></li>
		<li class="list-group-item"><?php echo T_("RATIO"); ?>: <?php echo $userratio; ?></span></li>
		<li class="list-group-item"><?php echo T_("SLOTS_USED"); ?>: <?php echo $slots; ?></span></li>
	</ul>
	<div class="text-center">
	<a href='<?php echo TTURL; ?>/users/profile?id=<?php echo $_SESSION["id"]; ?>'><button class="btn btn-primary"><?php echo T_("ACCOUNT"); ?></button></a>
		<?php if ($_SESSION["control_panel"] == "yes") {?>
		<a href="<?php echo TTURL; ?>/admincp" class="btn btn-warning"><?php echo T_("STAFFCP"); ?></a>
		<?php }?>
	</div>
	<?php
    end_block();
}