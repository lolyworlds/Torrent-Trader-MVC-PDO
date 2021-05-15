<div class="card">
<div class="card-header">
Report Torrent
</div>
<div class="card-body">

<b>Are you sure you would like to report torrent: ?</b><br /><a href='<?php echo URLROOT; ?>/torrents/read?id=$torrent'><b><?php echo $data['name']; ?></b></a><br />
<b>Reason</b> (required): 
<form method='post' action='<?php echo URLROOT; ?>/report/torrent'>
<input type='hidden' name='torrent' value='<?php echo $data['torrent']; ?>' />
<input class="form-control" type='text' size='100' name='reason' /><br>
<input class="btn btn-sm btn-primary" type='submit' value='Confirm' /></form>

</div>
</div>