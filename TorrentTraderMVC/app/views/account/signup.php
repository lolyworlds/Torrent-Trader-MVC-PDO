<?php
Style::begin(Lang::T("SIGNUP"));
?>
<div class="row justify-content-center">
<form method="post" action="<?php echo URLROOT; ?>/signup/submit">
<?php if ($data['invite']) {?>
	<input type="hidden" name="invite" value="<?php echo $_GET["invite"]; ?>" />
	<input type="hidden" name="secret" value="<?php echo htmlspecialchars($_GET["secret"]); ?>" />
<?php }?>
<div class="form-group">
    <?php echo Lang::T("COOKIES"); ?><br>
<div class="form-group">
	<label for="wantusername"><?php echo Lang::T("USERNAME"); ?>:</label>
	<input id="wantusername" type="text" class="form-control" name="wantusername" minlength="3" maxlength="25" required autofocus>
</div>
<div class="form-group">
	<label for="wantpassword"><?php echo Lang::T("PASSWORD"); ?>:</label>
	<input id="wantpassword" type="password" class="form-control" name="wantpassword" minlength="6" maxlength="25" required autofocus>
</div>
<div class="form-group">
	<label for="passagain"><?php echo Lang::T("CONFIRM"); ?>:</label>
	<input id="passagain" type="password" class="form-control" name="passagain" minlength="6" maxlength="25" required autofocus>
</div>

<?php if (!$data['invite']) { ?>
	<div class="form-group">
	    <label for="email"><?php echo Lang::T("EMAIL"); ?>:</label>
	    <input id="email" type="text" class="form-control" name="email" minlength="3" maxlength="25" required autofocus>
    </div>
<?php } ?>

<div class="form-group">
	<label for="age"><?php echo Lang::T("AGE"); ?>:</label>
	<input id="age" type="text" class="form-control" name="age" minlength="3" maxlength="25" required autofocus>
</div>
<div class="form-group">
	<label for="country"><?php echo Lang::T("COUNTRY"); ?>:</label>
	<select name="country" size="1">
		<?php Countries::echoCountry(); ?>
	</select>
</div>

<label for="name"><?php echo Lang::T("GENDER"); ?>:</label>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="gender" id="Male" value="Male">
  <label class="form-check-label" for="gender">Male</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="gender" id="Female" value="Female">
  <label class="form-check-label" for="gender">Female</label>
</div>

<div class="form-group">
	<label for="client"><?php echo Lang::T("PREF_BITTORRENT_CLIENT"); ?>:</label>
	<input id="client" type="text" class="form-control" name="client" minlength="3" maxlength="25" required autofocus>
</div>
<div class="form-group no-margin">
	<button type="submit" class="btn btn-primary btn-block"><?php echo Lang::T("SIGNUP"); ?></button>
</div>
</div>
</form>
</div>
<?php
Style::end();
?>