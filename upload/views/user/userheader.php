<a href='<?php echo TTURL; ?>/users/profile?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Profile</button></a>&nbsp;
<a href='<?php echo TTURL; ?>/users/details?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Edit</button></a>&nbsp;
<a href='<?php echo TTURL; ?>/users/email?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Email</button></a>&nbsp;
<a href='<?php echo TTURL; ?>/users/preferences?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Preferences</button></a>&nbsp;
<a href='<?php echo TTURL; ?>/users/other?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Other</button></a>&nbsp;
<a href='<?php echo TTURL; ?>/peers/uploaded?id=<?php echo $user["id"]; ?>'><button type="button" class="btn btn-sm btn-primary">Uploaded</button></a>
<?php if ($CURUSER['class'] > $site_config['Vip']) { ?>
<a href='<?php echo TTURL; ?>/warning?id=<?php echo $user["id"]; ?>'><button type="button" class="btn btn-sm btn-primary">Warn</button></a>
<a href='<?php echo TTURL; ?>/users/admin?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-success">Admin</button></a>&nbsp;
<?php } ?>