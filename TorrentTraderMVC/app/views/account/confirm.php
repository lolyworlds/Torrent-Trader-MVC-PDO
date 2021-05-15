<?php
Style::begin(Lang::T("RECOVER_ACCOUNT"));
?>
<div class="row justify-content-center">
<form method="post" action="<?php echo URLROOT; ?>/recover/ok">
<div class="form-group">
<div class="form-group">
	<label for="name"><?php echo Lang::T("NEW_PASSWORD"); ?>:</label>
    <input type="hidden" name="secret" value="<?php echo $_GET['secret']; ?>" />
    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
    <input id="name" type="text" class="form-control" name="password" minlength="3" maxlength="25" required autofocus>
</div>
<div class="form-group">
	<label for="name"><?php echo Lang::T("REPEAT"); ?>:</label>
    <input id="name" type="text" class="form-control" name="password1" minlength="3" maxlength="25" required autofocus>
</div>
<div class="form-group">
	<button type="submit" class="btn btn-primary"><?php echo Lang::T("Submit"); ?></button>
</div>

</div>
</form>
</div>
<?php
Style::end();