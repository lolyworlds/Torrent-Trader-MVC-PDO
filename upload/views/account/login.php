<div class="card">
        <div class="card-header">
            <?php echo T_("LOGIN"); ?>
        </div>
        <div class="card-body">
<?php if ($config["MEMBERSONLY"]) { ?>
<center><b><?php echo T_("MEMBERS_ONLY"); ?></b></center>
<?php } ?>
<form method="post" action="<?php echo TTURL; ?>/account/login">
  <table border="0" cellpadding="3" align="center">
		<tr><td align="center"><b><?php echo T_("USERNAME"); ?>:</b> <input type="text" size="40" name="username" /></td></tr>
		<tr><td align="center"><b><?php echo T_("PASSWORD"); ?>:</b> <input type="password" size="40" name="password" /></td></tr>
		<tr><td align="center"><input type="checkbox" name="rememberme" value="rememberme" />&nbsp;Remember Me</td></tr>
		<tr><td colspan="2" align="center"><button type='submit' class='btn btn-sm btn-primary'><?php echo T_("LOGIN"); ?></button><br /><br /><i><?php echo T_("COOKIES"); ?></i></td></tr>
	</table>

</form>
<p align="center"><a href="<?php echo TTURL; ?>/account/signup"><?php echo T_("SIGNUP"); ?></a> | <a href="<?php echo TTURL; ?>/account/recover"><?php echo T_("RECOVER_ACCOUNT"); ?></a></p>
</div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php"; ?>