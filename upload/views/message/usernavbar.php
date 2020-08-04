    <a href='<?php echo TTURL; ?>/users/profile?id=<?php echo $CURUSER['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Profile</button></a>
    <?php if ($CURUSER["id"] == $CURUSER['id'] OR $CURUSER["class"] > $site_config['Uploader']) { ?>
    <a href='<?php echo TTURL; ?>/users/details?id=<?php echo $CURUSER['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Edit</button></a>
    <?php } ?>
    <?php if ($CURUSER["id"] == $CURUSER['id'] ) { ?>
    <a href='<?php echo TTURL; ?>/users/changepw?id=<?php echo $CURUSER['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Password</button></a>
    <a href='<?php echo TTURL; ?>/users/email?id=<?php echo $CURUSER['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Email</button></a>
    <a href='<?php echo TTURL; ?>/messages'><button type="button" class="btn btn-sm btn-primary">Messages</button></a>
    <a href='<?php echo TTURL; ?>/bonus'><button type="button" class="btn btn-sm btn-primary">Seed Bonus</button></a>
    <?php } ?>
    <?php if ($CURUSER["view_torrents"]) { ?>
    <a href='<?php echo TTURL; ?>/peers/seeding?id=<?php echo $CURUSER['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Seeding</button></a>
    <?php } ?>
    <?php if ($CURUSER["class"] > $site_config['Uploader']) { ?>
    <a href='<?php echo TTURL; ?>/users/admin?id=<?php echo $CURUSER['id']; ?>'><button type="button" class="btn btn-sm btn-success">Admin</button></a>
    <?php } 