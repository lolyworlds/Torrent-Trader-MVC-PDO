<?php

if ($CURUSER){
begin_block(T_("NAVIGATION"));
echo "<div id='navigate' class='bMenu'><ul>";
echo "<li><a href='$site_config[SITEURL]/index.php'>".T_("HOME")."</a></li>";

if ($CURUSER["view_torrents"]=="yes" || !$site_config["MEMBERSONLY"])
{ 
echo "<li><a href='$site_config[SITEURL]/torrents/browse'>".T_("BROWSE_TORRENTS")."</a></li>";
echo "<li><a href='$site_config[SITEURL]/torrents/today'>".T_("TODAYS_TORRENTS")."</a></li>";
echo "<li><a href='$site_config[SITEURL]/torrentssearch'>".T_("SEARCH")."</a></li>";
echo "<li><a href='$site_config[SITEURL]/torrents/needseed'>".T_("TORRENT_NEED_SEED")."</a></li>";
}
if ($CURUSER["edit_torrents"]=="yes")
{
echo "<li><a href='$site_config[SITEURL]/torrents/import'>".T_("MASS_TORRENT_IMPORT")."</a></li>";
}
if ($CURUSER && $CURUSER["view_users"]=="yes")
{
echo "<li><a href='$site_config[SITEURL]/teams/index'>".T_("TEAMS")."</a></li>";
echo "<li><a href='$site_config[SITEURL]/users'>".T_("MEMBERS")."</a></li>";
}
echo "<li><a href='$site_config[SITEURL]/rules'>".T_("SITE_RULES")."</a></li>";
echo "<li><a href='$site_config[SITEURL]/faq'>".T_("FAQ")."</a></li>";
if ($CURUSER && $CURUSER["view_users"]=="yes")
{
echo "<li><a href='$site_config[SITEURL]/group/staff'>".T_("STAFF")."</a></li>";
}
echo "</ul></div>";
end_block();
}
?>