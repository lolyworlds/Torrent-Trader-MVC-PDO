<?php
if (($site_config["INVITEONLY"] || $site_config["ENABLEINVITES"]) && $CURUSER) {
	$invites = $CURUSER["invites"];
	begin_block(T_("INVITES"));
	?>
	<table border="0" width="100%">
	<tr>
        <td align="center">
        <?php printf(P_("YOU_HAVE_INVITES", $invites), $invites); ?>
        </td>
    </tr>
	<?php if ($invites > 0 ){?>
	<tr>
        <td align="center">
        <a href="<?php echo $site_config['SITEURL'] ?>/invite"><?php echo T_("SEND_AN_INVITE"); ?></a>
        </td>
    </tr>
	<?php } ?>
	</table>
	<?php
	end_block();
}
?>