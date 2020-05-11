<?php
if (($site_config["INVITEONLY"] || $site_config["ENABLEINVITES"]) && $CURUSER) {
	$invites = $CURUSER["invites"];
	begin_block(T_("INVITES"));
	?>
    <p class="text-center"><?php printf(P_("YOU_HAVE_INVITES", $invites), $invites); ?></p>
	<?php if ($invites > 0 ){?>
    <p class="text-center"><a href="<?php echo TTURL; ?>/invite"><?php echo T_("SEND_AN_INVITE"); ?></a></p>
	<?php }
	end_block();
}
?>