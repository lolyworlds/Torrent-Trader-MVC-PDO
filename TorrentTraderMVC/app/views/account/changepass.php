<?php
Style::header(Lang::T("Change Password"));
Style::begin(Lang::T("Change Password"));
?>
<div class="form-group">
    <form method="post" action="<?php echo URLROOT; ?>/account/changepw?id=<?php echo $data['id']; ?>">
    <input type="hidden" name="do" value="newpassword" />
    <div class="form-group">
	    <label for="name"><?php echo Lang::T("NEW_PASSWORD"); ?>:</label>
        <input id="name" type="password" class="form-control" name="chpassword" minlength="3" maxlength="25" required autofocus>
    </div>
    <div class="form-group">
	    <label for="name"><?php echo Lang::T("REPEAT"); ?>:</label>
        <input id="name" type="password" class="form-control" name="passagain" minlength="3" maxlength="25" required autofocus>
    </div>
    <div class="form-group">
	    <button type="submit" class="btn btn-primary"><?php echo Lang::T("Submit"); ?></button>
    </div>
    </form>
</div>
 <?php
Style::end();
Style::footer();