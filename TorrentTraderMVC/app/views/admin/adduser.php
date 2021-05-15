<?php
Style::adminheader("Add User");
Style::adminnavmenu();
Style::begin("Add User");
?>
    <center><b>Add user</b></center>
    <div class='row justify-content-md-center'>
        <div class='col-md-8'>
    <form method=get action=?>
    <input type=hidden name=action value=users>
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
    <div class="form-group">
        <label for="search">Search Users:</label>
        <input id="name" type="text" class="form-control" name="search" minlength="3" maxlength="25" required autofocus>
        <input type=submit value='Search'class="btn btn-primary" >
	</div>
    </form>
    <form method=post action='<?php echo URLROOT; ?>/adminadduser/addeduserok'>
    <div class="form-group">
	    <label for="name">Username</label>
	    <input id="name" type="text" class="form-control" name="username" minlength="3" maxlength="25" required autofocus>
	</div>
    <div class="form-group">
	    <label for="name">Password</label>
	    <input id="name" type="password" class="form-control" name="password" minlength="3" maxlength="25" required autofocus>
	</div>
    <div class="form-group">
	    <label for="name">Re-type password </label>
	    <input id="name" type="password" class="form-control" name="password2" minlength="3" maxlength="25" required autofocus>
	</div>
    <div class="form-group">
	    <label for="name">E-mail</label>
	    <input id="name" type="text" class="form-control" name="email" minlength="3" maxlength="25" required autofocus>
	</div>
    <div class="form-group">
    <div class="form-group no-margin">
		<button type="submit" class="btn btn-primary  btn-sm">Okay</button>
	</div>
    </form>
    </div>
    </div>
    </div>
<?php
Style::end();
Style::adminfooter();