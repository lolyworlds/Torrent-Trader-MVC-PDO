<?php
if ($_SESSION['class'] > _SUPERMODERATOR) {
    ?>
<br>
<div class="border border-danger"> 
    <ul class="list-group">
    admin only bits
    </ul>
</div>
<?php
}
?>
<br>
<div class="border border-success">
    <ul class="list-group">
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminadduser"><img src="<?php echo URLROOT; ?>/assets/images/admin/adduser.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("Add User"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminadvancedsearch"><img src="<?php echo URLROOT; ?>/assets/images/admin/user_search.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("ADVANCED_USER_SEARCH"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminavatar"><img src="<?php echo URLROOT; ?>/assets/images/admin/avatar_log.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("AVATAR_LOG"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminbackup"><img src="<?php echo URLROOT; ?>/assets/images/admin/db_backup.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("BACKUPS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminipban"><img src="<?php echo URLROOT; ?>/assets/images/admin/ip_block.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("BANNED_IPS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminbantorrent"><img src="<?php echo URLROOT; ?>/assets/images/admin/banned_torrents.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("BANNED_TORRENTS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminblocks"><img src="<?php echo URLROOT; ?>/assets/images/admin/blocks.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("BLOCKS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/client"><img src="<?php echo URLROOT; ?>/assets/images/admin/client.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Client Ban</b></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admincheats"><img src="<?php echo URLROOT; ?>/assets/images/admin/cheats.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("Detect Cheats"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminduplicateip"><img src="<?php echo URLROOT; ?>/assets/images/admin/double-ip.ico" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("DUPLICATEIP"); ?></b></a></li> 
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminemailban"><img src="<?php echo URLROOT; ?>/assets/images/admin/mail_bans.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("EMAIL_BANS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/faq/manage"><img src="<?php echo URLROOT; ?>/assets/images/admin/faq.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("FAQ"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminforum"><img src="<?php echo URLROOT; ?>/assets/images/admin/forums.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("FORUM_MANAGEMENT"); ?></b></a></li>
	<li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminfreetorrent"><img src="<?php echo URLROOT; ?>/assets/images/admin/free_leech.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Freeleech Torrents</b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminhitnrun"><img src="<?php echo URLROOT; ?>/assets/images/hitnrun.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Hit & Runs</b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admincomments"><img src="assets/images/admin/comments.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("LATEST_COMMENTS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminmasspm"><img src="<?php echo URLROOT; ?>/assets/images/admin/mass_pm.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("MASS_PM"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminmessagespy"><img src="<?php echo URLROOT; ?>/assets/images/admin/message_spy.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("MESSAGE_SPY"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminnews/newsview"><img src="<?php echo URLROOT; ?>/assets/images/admin/news.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("NEWS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminpeers"><img src="<?php echo URLROOT; ?>/assets/images/admin/peer_list.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("PEERS_LIST"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admininviteusers"><img src="<?php echo URLROOT; ?>/assets/images/admin/invited_user.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Invited Users</b></a></li>   
    </ul>
</div>