<div class="card">
<div class="card-header">
Report Comment
</div>
<div class="card-body">

<b>Are you sure you would like to report Comment: ?</b><br /><br /><b><?php echo format_comment($data["text"]); ?></b><br />
<p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them</p>
<b>Reason</b> (required): <form method='post' action='<?php echo URLROOT; ?>/report/comment'>
<input class="form-control" type='hidden' name='comment' value='<?php echo $data['comment']; ?>' /><br>
<input class="btn btn-sm btn-primary" type='text' size='100' name='reason' /><input type='submit'  value='Confirm' /></form>

</div>
</div>