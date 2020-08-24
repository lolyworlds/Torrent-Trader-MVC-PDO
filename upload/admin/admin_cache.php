<?php
if ($action == "prune") {
    $title = T_("_BLC_MAN_");
	require 'views/admin/header.php';
    adminnavmenu();
    begin_frame('Purge');

# Prune Block Cache.
    $TTCache = new Cache();
    $TTCache->Delete("blocks_left");
    $TTCache->Delete("blocks_middle");
    $TTCache->Delete("blocks_right");
    $TTCache->Delete("latestuploadsblock");
    $TTCache->Delete("mostactivetorrents_block");
    $TTCache->Delete("newestmember_block");
    $TTCache->Delete("seedwanted_block");
    $TTCache->Delete("usersonline_block");
    echo 'Purge Cache Successful';
    end_frame();
    require 'views/admin/footer.php';
}