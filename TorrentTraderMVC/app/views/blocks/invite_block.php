<?php
if ((INVITEONLY || ENABLEINVITES) && $_SESSION['loggedin'] == true) {
    $invites = $_SESSION["invites"];
    Block::begin(Lang::T("INVITES"));
    ?>

	<table border="0" width="100%">
	<tr>
        <td align="center">
        <?php printf(Lang::N("YOU_HAVE_INVITES", $invites), $invites);?>
        </td>
    </tr>
	<?php if ($invites > 0) {?>
	<tr>
        <td align="center">
        <a href="<?php echo URLROOT ?>/invite"><?php echo Lang::T("SEND_AN_INVITE"); ?></a>
        </td>
    </tr>
	<?php }?>
	<?php if ($_SESSION["invitees"] > 0) {?>
    <tr>
        <td align="center">
        <a href="<?php echo URLROOT ?>/invite/invitetree"><?php echo Lang::T("Invite Tree"); ?></a>
        </td>
    </tr>
    <?php }?>
    </table>

       <!-- end content -->

	<?php block::end();
}