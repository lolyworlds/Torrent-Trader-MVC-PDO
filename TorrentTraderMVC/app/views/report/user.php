<div class="card">
<div class="card-header">
Report User
</div>
<div class="card-body">

<b>Are you sure you would like to report user ?</b><br /><a href='<?php echo URLROOT; ?>/profile?id=<?php echo $data['user']; ?>'><b><?php echo Users::coloredname($data['username']); ?></b></a><br />
<p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them</p>
<b>Reason</b> (required): 
<form method='post' action='<?php echo URLROOT; ?>/report/user'>
<input type='hidden' name='user' value='<?php echo $data['user']; ?>' />
<input class="form-control" type='text' size='100' name='reason' /><br>
<input class="btn btn-sm btn-primary" type='submit' value='Confirm' /></form>

</div>
</div>