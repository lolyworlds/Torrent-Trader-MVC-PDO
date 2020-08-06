<?php
if ($action == "prune") {
    stdhead(T_("_BLC_MAN_"));
    adminnavmenu();
    begin_frame(T_("BLC_VIEW"));

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
    echo 'success';
    end_frame();
    stdfoot();
}