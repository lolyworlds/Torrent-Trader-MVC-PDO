<?php
if ($_SESSION['loggedin'] === true && $_SESSION["control_panel"] == "yes") {
    $title = T_("AdminCP");
    $blockId = "b-" . sha1($title);
    ?>
    <div class="card">
        <div class="card-header">
            <?php echo $title ?>
            <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
        </div>
        <div class="card-body slidingDiv<?php echo $blockId; ?>">
        <!-- content -->

       <select name="admin" style="width: 95%" onchange="if(this.options[this.selectedIndex].value != -1){ window.location = this.options[this.selectedIndex].value; }">
       <option value="-1">Navigation</option>
       <option value="<?php echo TTURL; ?>/admincp?action=usersearch">Advanced User Search</option>
       <option value="<?php echo TTURL; ?>/admincp?action=avatars">Avatar Log</option>
       <option value="<?php echo TTURL; ?>/admincp?action=backups">Backups</option>
       <option value="<?php echo TTURL; ?>/admincp?action=ipbans">Banned Ip's</option>
       <option value="<?php echo TTURL; ?>/admincp?action=bannedtorrents">Banned Torrents</option>
       <option value="<?php echo TTURL; ?>/admincp?action=blocks&amp;do=view">Blocks</option>
       <option value="<?php echo TTURL; ?>/admincp?action=cheats">Detect Possibe Cheats</option>
       <option value="<?php echo TTURL; ?>/admincp?action=emailbans">E-mail Bans</option>
       <option value="<?php echo TTURL; ?>/admincp/faq/manage">FAQ</option>
       <option value="<?php echo TTURL; ?>/admincp?action=freetorrents">Freeleech Torrents</option>
       <option value="<?php echo TTURL; ?>/admincp?action=lastcomm">Latest Comments</option>
       <option value="<?php echo TTURL; ?>/admincp?action=masspm">Mass PM</option>
       <option value="<?php echo TTURL; ?>/admincp?action=messagespy">Message Spy</option>
       <option value="<?php echo TTURL; ?>/admincp?action=news&amp;do=view">News</option>
       <option value="<?php echo TTURL; ?>/admincp?action=peers">Peers List</option>
       <option value="<?php echo TTURL; ?>/admincp?action=polls&amp;do=view">Polls</option>
       <option value="<?php echo TTURL; ?>/admincp?action=reports&amp;do=view">Reports System</option>
       <option value="<?php echo TTURL; ?>/admincp?action=rules&amp;do=view">Rules</option>
       <option value="<?php echo TTURL; ?>/admincp?action=sitelog">Site Log</option>
       <option value="<?php echo TTURL; ?>/teams/create">Teams</option>
       <option value="<?php echo TTURL; ?>/admincp?action=style">Theme Management</option>
       <option value="<?php echo TTURL; ?>/admincp?action=categories&amp;do=view">Torrent Categories</option>
       <option value="<?php echo TTURL; ?>/admincp?action=torrentlangs&amp;do=view">Torrent Languages</option>
       <option value="<?php echo TTURL; ?>/admincp?action=torrentmanage">Torrents</option>
       <option value="<?php echo TTURL; ?>/admincp?action=groups&amp;do=view">Usergroups View</option>
       <option value="<?php echo TTURL; ?>/admincp?action=warned">Warned Users</option>
       <option value="<?php echo TTURL; ?>/admincp?action=whoswhere">Who's Where</option>
       <option value="<?php echo TTURL; ?>/admincp?action=censor">Word Censor</option>
       <option value="<?php echo TTURL; ?>/admincp?action=forum">Forum Management</option>
       </select>
       
    <!-- end content -->
    </div>
</div>
<br />
<?php
}