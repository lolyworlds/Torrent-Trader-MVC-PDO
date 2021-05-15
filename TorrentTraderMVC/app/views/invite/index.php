<?php
Style::begin(Lang::T("INVITE"));
?>
<div class="form-group">
<form method="post" action="<?php echo URLROOT; ?>/invite/submit?take=1">
<div class="form-group">
	<label for="email"><?php echo Lang::T("EMAIL_ADDRESS"); ?>:</label>
	<input id="email" type="text" class="form-control" name="email" minlength="3" maxlength="25" required autofocus>
</div>
<?php echo Lang::T("EMAIL_ADDRESS_VALID_MSG"); ?><br><br>
<div class="form-group">
	<label for="mess"><?php echo Lang::T("MESSAGE"); ?>:</label><br>
	<textarea name="mess" rows="10" cols="50"></textarea>
</div>
<div class="form-group">
	<button type="submit" class="btn btn-primary btn-block"><?php echo Lang::T("SEND_AN_INVITE"); ?></button>
</div>
</form>
</div>
<?php
Style::end();