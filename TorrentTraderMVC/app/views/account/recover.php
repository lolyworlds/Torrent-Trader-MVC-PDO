<?php
Style::begin(Lang::T("RECOVER_ACCOUNT"));
?>
<div class="row justify-content-center">
<form method="post" action="<?php echo URLROOT; ?>/recover/submit">
<?php echo Lang::T("USE_FORM_FOR_ACCOUNT_DETAILS"); ?>
<div class="form-group">
<div class="form-group">
	<label for="name"><?php echo Lang::T("EMAIL_ADDRESS"); ?>:</label>
	<input id="name" type="text" class="form-control" name="email" minlength="3" maxlength="25" required autofocus>
</div>
<div class="form-group">
    <?php (new Captcha)->html(); ?>
	<button type="submit" class="btn btn-primary btn-block"><?php echo Lang::T("Submit"); ?></button>
</div>
</div>
</form>
</div>
<?php
Style::end();