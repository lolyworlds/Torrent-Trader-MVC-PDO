<?php
if ($_SESSION['class'] > _MODERATOR) {
    ?>
<br>
<div class="border border-warning"> 
    <ul class="list-group">
    supermod
    </ul>
</div>
<?php
}
?>
<br>
<div class="border border-success">
    <ul class="list-group">
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminprivacy"><img src="<?php echo URLROOT; ?>/assets/images/admin/privacy_level.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Privacy Level</b></a></li>    
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminpendinginvite"><img src="<?php echo URLROOT; ?>/assets/images/admin/pending_invited_user.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Pending Invited Users</b></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminpolls"><img src="<?php echo URLROOT; ?>/assets/images/admin/polls.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("POLLS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminreports"><img src="<?php echo URLROOT; ?>/assets/images/admin/report_system.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("REPORTS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminrules/rulesview"><img src="<?php echo URLROOT; ?>/assets/images/admin/rules.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("RULES"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminseedbonus"><img src="<?php echo URLROOT; ?>/assets/images/admin/seedbonus.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Seed Bonus</b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminsitelog"><img src="<?php echo URLROOT; ?>/assets/images/admin/site_log.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("SITELOG"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminsimpleusersearch"><img src="<?php echo URLROOT; ?>/assets/images/admin/simple_user_search.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Simple User Search</b></a></li> 
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/contactstaff/staffbox"><img src="<?php echo URLROOT; ?>/assets/images/admin/staffmess.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Staff Messages</b></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/exceptions/admin"><img src="<?php echo URLROOT; ?>/assets/images/admin/sql_error.png" border="0" width="20" height="20" alt="" />&nbsp;<b>SQL Error</b></a></li> 
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminteams"><img src="<?php echo URLROOT; ?>/assets/images/admin/teams.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("TEAMS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admintheme"><img src="<?php echo URLROOT; ?>/assets/images/admin/themes.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("THEME_MANAGEMENT"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admincategories/categoriesview"><img src="<?php echo URLROOT; ?>/assets/images/admin/torrent_cats.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("TORRENT_CAT_VIEW"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admintorrentlang/torrentlangsview"><img src="<?php echo URLROOT; ?>/assets/images/admin/torrent_lang.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("TORRENT_LANG"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admintorrents"><img src="<?php echo URLROOT; ?>/assets/images/admin/torrents.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("TORRENTS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admingroups/groupsview"><img src="<?php echo URLROOT; ?>/assets/images/admin/user_groups.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("USER_GROUPS_VIEW"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminwarnedusers"><img src="<?php echo URLROOT; ?>/assets/images/admin/warned_user.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("WARNED_USERS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminwhoswhere"><img src="<?php echo URLROOT; ?>/assets/images/admin/whos_where.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("WHOS_WHERE"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admincensor"><img src="<?php echo URLROOT; ?>/assets/images/admin/word_censor.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("WORD_CENSOR"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admincleanshout"><img src="<?php echo URLROOT; ?>/assets/images/shoutclear.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("CLEAR_SHOUTBOX"); ?></b></a></li>
    </ul>
</div>