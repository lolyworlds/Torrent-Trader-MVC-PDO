<div class="card">
    <div class="card-header">
        <?php echo Lang::T("LOGIN"); ?>
    </div>
    <div class="card-body">
        <?php if (MEMBERSONLY) {?>
        <center><b><?php echo Lang::T("MEMBERS_ONLY"); ?></b>
        <?php }?>
        <div class="row justify-content-center">
        <form method="post" action="<?php echo URLROOT; ?>/login/submit" autocomplete="off">
        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
        <div class="form-group">
            <label for="username"><?php echo Lang::T("USERNAME"); ?></label>
            <input id="username" type="text" class="form-control" name="username" minlength="3" maxlength="25" required autofocus>
        </div>
        <div class="form-group">
	        <label for="password"><?php echo Lang::T("PASSWORD"); ?></label>
	        <input id="password" type="password" class="form-control" name="password" minlength="6" maxlength="16" required data-eye>
	    </div>
        <div class="form-group">
            <?php (new Captcha)->html(); ?>
            <button type="submit" class="btn btn-primary btn-block"><?php echo Lang::T("LOGIN"); ?></button>
            <center><i><?php echo Lang::T("COOKIES"); ?></i></center>
		</div>
        <div class="margin-top20 text-center">
            <a href="<?php echo URLROOT; ?>/signup"><?php echo Lang::T("SIGNUP"); ?></a> | 
            <a href="<?php echo URLROOT; ?>/recover"><?php echo Lang::T("RECOVER_ACCOUNT"); ?></a>
		</div>
        </form>
        </div>
        </center>
    </div>
</div>