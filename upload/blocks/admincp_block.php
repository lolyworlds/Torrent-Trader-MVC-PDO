<?php
    
   if ($CURUSER["control_panel"] == "yes") 
   {
       begin_block("AdminCP");
       ?>
       
       <select name="admin" onchange="if(this.options[this.selectedIndex].value != -1){ window.location = this.options[this.selectedIndex].value; }">
       <option value="-1">Navigation</option>
       <option value="/admincp?action=usersearch">Advanced User Search</option>
       <option value="/admincp?action=avatars">Avatar Log</option>
       <option value="/admincp?action=backups">Backups</option>
       <option value="/admincp?action=ipbans">Banned Ip's</option>
       <option value="/admincp?action=bannedtorrents">Banned Torrents</option>
       <option value="/admincp?action=blocks&amp;do=view">Blocks</option>
       <option value="/admincp?action=cheats">Detect Possibe Cheats</option>
       <option value="/admincp?action=emailbans">E-mail Bans</option>
       <option value="/faqmanage">FAQ</option>
       <option value="/admincp?action=freetorrents">Freeleech Torrents</option>
       <option value="/admincp?action=lastcomm">Latest Comments</option>
       <option value="/admincp?action=masspm">Mass PM</option>
       <option value="/admincp?action=messagespy">Message Spy</option>
       <option value="/admincp?action=news&amp;do=view">News</option>
       <option value="/admincp?action=peers">Peers List</option>
       <option value="/admincp?action=polls&amp;do=view">Polls</option>
       <option value="/admincp?action=reports&amp;do=view">Reports System</option>
       <option value="/admincp?action=rules&amp;do=view">Rules</option>
       <option value="/admincp?action=sitelog">Site Log</option>
       <option value="teamscreate">Teams</option>
       <option value="/admincp?action=style">Theme Management</option>
       <option value="/admincp?action=categories&amp;do=view">Torrent Categories</option>
       <option value="/admincp?action=torrentlangs&amp;do=view">Torrent Languages</option>
       <option value="/admincp?action=torrentmanage">Torrents</option>
       <option value="/admincp?action=groups&amp;do=view">Usergroups View</option>
       <option value="/admincp?action=warned">Warned Users</option>
       <option value="/admincp?action=whoswhere">Who's Where</option>
       <option value="/admincp?action=censor">Word Censor</option>
       <option value="/admincp?action=forum">Forum Management</option>
       </select>
    
       <?php
       end_block();
   }   

?>