<?php
if ($_SESSION['loggedin'] == true) {

    $avatar = htmlspecialchars($_SESSION["avatar"]);
    if (!$avatar) {
        $avatar = URLROOT . "/assets/images/default_avatar.png";
    }

    $userdownloaded = mksize($_SESSION["downloaded"]);
    $useruploaded = mksize($_SESSION["uploaded"]);
    $privacylevel = Lang::T($_SESSION["privacy"]);
    $countslot = DB::run("SELECT DISTINCT torrent FROM peers WHERE userid =?  AND seeder=?", [$_SESSION['id'], 'yes']);
    $maxslotdownload = $countslot->rowCount();
    $slots = number_format($_SESSION["maxslots"]) . "/" . number_format($maxslotdownload);

    if ($_SESSION["uploaded"] > 0 && $_SESSION["downloaded"] == 0) {
        $userratio = '<span class="label label-success pull-right">Inf.</span>';
    } elseif ($_SESSION["downloaded"] > 0) {
        $userratio = '<span class="label label-info pull-right">' . number_format($_SESSION["uploaded"] / $_SESSION["downloaded"] , 2). '</span>';
    } else {
        $userratio = '<span class="label label-info pull-right">---</span>';
    }

    Block::begin(Users::coloredname($_SESSION["username"]));
    ?>

        <img class="embed-responsive" src="<?php echo $avatar; ?>" alt="Avatar"  />
	<ul class="list-group">
		<li class="list-group-item"><?php echo Lang::T("DOWNLOADED"); ?> : <span class="label label-danger pull-right"><?php echo $userdownloaded; ?></span></li>
		<li class="list-group-item"><?php echo Lang::T("UPLOADED"); ?>: <span class="label label-success pull-right"><?php echo $useruploaded; ?></span></li>
		<li class="list-group-item"><?php echo Lang::T("CLASS"); ?>: <div class="pull-right"><?php echo Lang::T($_SESSION["level"]); ?></div></li>
		<li class="list-group-item"><?php echo Lang::T("ACCOUNT_PRIVACY_LVL"); ?>: <div class="pull-right"><?php echo $privacylevel; ?></div></li>
		<li class="list-group-item"><?php echo Lang::T("Seed Bonus"); ?>: <a href="<?php echo URLROOT; ?>/bonus"><div class="pull-right"><?php echo $_SESSION['seedbonus']; ?></div></a></span></li>
		<li class="list-group-item"><?php echo Lang::T("RATIO"); ?>: <?php echo $userratio; ?></span></li>
		<li class="list-group-item"><?php echo Lang::T("Available Slots"); ?>: <div class="pull-right"><?php echo $slots; ?></div></span></li>
    </ul>
    <br />
	<div class="text-center">
	<a href='<?php echo URLROOT; ?>/profile?id=<?php echo $_SESSION["id"]; ?>'><button class="btn btn-primary"><?php echo Lang::T("ACCOUNT"); ?></button></a>
		<?php if ($_SESSION["control_panel"] == "yes") {?>
		<a href="<?php echo URLROOT; ?>/admincp" class="btn btn-warning"><?php echo Lang::T("STAFFCP"); ?></a>
		<?php }?>
	</div>
	<br />
        <!-- end content -->

    <?php block::end();
}