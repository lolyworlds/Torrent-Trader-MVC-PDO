<?php
if (($config["INVITEONLY"] || $config["ENABLEINVITES"]) && $_SESSION['loggedin'] == true) {
    $invites = $_SESSION["invites"];
    $title = T_("INVITES");
    $blockId = "b-" . sha1($title);
    ?>
    <div class="card">
        <div class="card-header">
            <?php echo $title ?>
            <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
        </div>
        <div class="card-body slidingDiv<?php echo $blockId; ?>">
        <!-- content -->

	<table border="0" width="100%">
	<tr>
        <td align="center">
        <?php printf(P_("YOU_HAVE_INVITES", $invites), $invites);?>
        </td>
    </tr>
	<?php if ($invites > 0) {?>
	<tr>
        <td align="center">
        <a href="<?php echo $config['SITEURL'] ?>/invite"><?php echo T_("SEND_AN_INVITE"); ?></a>
        </td>
    </tr>
	<?php }?>
	<?php if ($_SESSION["invitees"] > 0) {?>
    <tr>
        <td align="center">
        <a href="<?php echo $config['SITEURL'] ?>/invite/invitetree"><?php echo T_("Invite Tree"); ?></a>
        </td>
    </tr>
    <?php }?>
    </table>

       <!-- end content -->
       </div>
</div>
<br />
	<?php
}