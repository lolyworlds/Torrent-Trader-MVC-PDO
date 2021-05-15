<?php
require APPROOT . '/views/admin/header.php';
?><br>
<div class="card">
    <div class="card-header">
        <?php echo Lang::T("Team Edit"); ?>
    </div>
    <div class="card-body">
        <form name='smolf3d' method='get' action='<?php echo URLROOT ?>/adminteams/edit'>
        <input type='hidden' name='id' value='<?php echo $data['editid']; ?>' />
        <input type='hidden' name='edited' value='1' />
        <table cellspacing='0' cellpadding='5' width='50%'>
        <tr><td><?php echo Lang::T("TEAM_NAME"); ?>: </td><td align='right'><input class="form-control" type='text' size='50' name='team_name' value='<?php echo $data['name']; ?>' /></td></tr>
        <tr><td><?php echo Lang::T("TEAM_LOGO_URL"); ?>: </td><td align='right'><input class="form-control" type='text' size='50' name='team_image' value='<?php echo $data['image']; ?>' /></td></tr>
        <tr><td><?php echo Lang::T("TEAM_OWNER_NAME"); ?>: </td><td align='right'><input class="form-control" type='text' size='50' name='team_owner' value='<?php echo $data['owner']; ?>' /></td></tr>
        <tr><td valign='top'><?php echo Lang::T("DESCRIPTION"); ?>: </td><td align='right'><textarea class="form-control" name='team_info' cols='35' rows='5'><?php echo $data['info']; ?></textarea><br />(BBCode is allowed)</td></tr>
        <tr><td></td><td><div align='right'><button type='submit' class='btn btn-sm btn-primary'>Update</button></div></td></tr>
        </table></form>
    </div>
</div>
<?php
require APPROOT . '/views/admin/footer.php';
?>