<div class="card">
        <div class="card-header">
            <?php echo T_("SIGNUP"); ?>
        </div>
        <div class="card-body">
<?php echo T_("COOKIES"); ?>

<form method="post" action="<?php echo TTURL; ?>/account/signup?takesignup=1">
	<?php if ($invite_row) {?>
	<input type="hidden" name="invite" value="<?php echo $_GET["invite"]; ?>" />
	<input type="hidden" name="secret" value="<?php echo htmlspecialchars($_GET["secret"]); ?>" />
	<?php }?>
	<table cellspacing="0" cellpadding="2" border="0">
			<tr>
				<td><?php echo T_("USERNAME"); ?>: <font class="required">*</font></td>
				<td><input type="text" size="40" name="wantusername" /></td>
			</tr>
			<tr>
				<td><?php echo T_("PASSWORD"); ?>: <font class="required">*</font></td>
				<td><input type="password" size="40" name="wantpassword" /></td>
			</tr>
			<tr>
				<td><?php echo T_("CONFIRM"); ?>: <font class="required">*</font></td>
				<td><input type="password" size="40" name="passagain" /></td>
			</tr>
			<?php if (!$invite_row) {?>
			<tr>
				<td><?php echo T_("EMAIL"); ?>: <font class="required">*</font></td>
				<td><input type="text" size="40" name="email" /></td>
			</tr>
			<?php }?>
			<tr>
				<td><?php echo T_("AGE"); ?>:</td>
				<td><input type="text" size="40" name="age" maxlength="3" /></td>
			</tr>
			<tr>
				<td><?php echo T_("COUNTRY"); ?>:</td>
				<td>
					<select name="country" size="1">
						<?php
$countries = "<option value=\"0\">---- " . T_("NONE_SELECTED") . " ----</option>\n";
$ct_r = DB::run("SELECT id,name,domain from countries ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

foreach ($ct_r as $ct_a) {
    $countries .= "<option value=\"$ct_a[id]\">$ct_a[name]</option>\n";
}
?>
						<?php echo $countries; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo T_("GENDER"); ?>:</td>
				<td>
					<input type="radio" name="gender" value="Male" /><?php echo T_("MALE"); ?>
					&nbsp;&nbsp;
					<input type="radio" name="gender" value="Female" /><?php echo T_("FEMALE"); ?>
				</td>
			</tr>
			<tr>
				<td><?php echo T_("PREF_BITTORRENT_CLIENT"); ?>:</td>
				<td><input type="text" size="40" name="client"  maxlength="20" /></td>
			</tr>
			<tr>
				<td align="center" colspan="2">
                <input type="submit" value="<?php echo T_("SIGNUP"); ?>" />
              </td>
			</tr>
	</table>
</form>
</div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: $config['default_theme']) . "/footer.php";?>