<?php
Style::begin("Contact us");
?>
<p>Send message to Staff</p>
<form method=post name=message action='<?php echo URLROOT; ?>/contactstaff'>
<div class="form-group">

<div class="form-group">
	<label for="name">Subject: </label>
	<input id="name" type="text" class="form-control" name="sub" minlength="3" maxlength="75" required autofocus>
</div>
<div class="form-group">
    <label for="msg">Message: </label>
    <textarea class="form-control" id="msg" name="msg" rows="3"></textarea>
</div>
<div class="form-group">
	<button type="submit" class="btn btn-primary btn-block"><?php echo Lang::T("Submit"); ?></button>
</div>

</div>
</form>
<?php
Style::end();