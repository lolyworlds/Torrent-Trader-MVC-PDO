   <a href='<?php echo URLROOT; ?>/profile?id=<?php echo $_SESSION['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Profile</button></a>
    <?php if ($_SESSION["id"] == $data['id'] or $_SESSION["class"] > _UPLOADER) {?>
    <a href='<?php echo URLROOT; ?>/profile/edit?id=<?php echo $_SESSION['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Edit</button></a>&nbsp;
    <?php }?>
    <?php if ($_SESSION["id"] == $_SESSION['id']) {?>
    <a href='<?php echo URLROOT; ?>/account/changepw?id=<?php echo $_SESSION['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Password</button></a>
    <a href='<?php echo URLROOT; ?>/account/email?id=<?php echo $_SESSION['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Email</button></a>
    <a href='<?php echo URLROOT; ?>/messages'><button type="button" class="btn btn-sm btn-primary">Messages</button></a>
    <a href='<?php echo URLROOT; ?>/bonus'><button type="button" class="btn btn-sm btn-primary">Seed Bonus</button></a>
    <?php }?>
    <?php if ($_SESSION["view_users"]) {?>
    <a href='<?php echo URLROOT; ?>/friends?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Your Friends</button></a>
    <?php }?>
    <?php if ($_SESSION["view_torrents"]) {?>
    <a href='<?php echo URLROOT; ?>/peers/seeding?id=<?php echo $_SESSION['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Seeding</button></a>
    <a href='<?php echo URLROOT; ?>/peers/uploaded?id=<?php echo $_SESSION['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Uploaded</button></a>
    <?php }?>
    <?php if ($_SESSION["class"] > _UPLOADER) {?>
    <a href='<?php echo URLROOT; ?>/warning?id=<?php echo $_SESSION['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Warn</button></a>
    <a href='<?php echo URLROOT; ?>/profile/admin?id=<?php echo $_SESSION['id']; ?>'><button type="button" class="btn btn-sm btn-success">Admin</button></a>
	<?php } ?>

<div>
     <br><center>
     <a href="<?php echo URLROOT; ?>/messages"><b><?php echo Lang::T("Over View"); ?></b></a>&nbsp;|&nbsp;
    <a href="<?php echo URLROOT; ?>/messages/inbox"><b><?php echo Lang::T("INBOX"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo URLROOT; ?>/messages/outbox"><b><?php echo Lang::T("OUTBOX"); ?></b></a>&nbsp;|&nbsp;

    <a href="<?php echo URLROOT; ?>/messages/draft"><b><?php echo Lang::T("DRAFT"); ?></b></a>&nbsp;|&nbsp;
	<a href="<?php echo URLROOT; ?>/messages/templates"><b><?php echo Lang::T("TEMPLATES"); ?></b></a>&nbsp;|&nbsp;

	<a href="<?php echo URLROOT; ?>/messages/create"><b><?php echo Lang::T("COMPOSE"); ?></b></a>
        </center><br>
    </div>