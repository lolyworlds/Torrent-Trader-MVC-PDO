<?php

if ($CURUSER){
begin_block(T_("NAVIGATION"));
echo "<div id='navigate' class='bMenu'><ul>";
echo "<li><a href='index.php'>".T_("HOME")."</a></li>";

if ($CURUSER["view_torrents"]=="yes" || !$site_config["MEMBERSONLY"])
{ 
echo "<li><a href='torrentsmain'>".T_("BROWSE_TORRENTS")."</a></li>";
echo "<li><a href='torrentstoday'>".T_("TODAYS_TORRENTS")."</a></li>";
echo "<li><a href='torrentssearch'>".T_("SEARCH")."</a></li>";
echo "<li><a href='torrentsneedseed'>".T_("TORRENT_NEED_SEED")."</a></li>";
}
if ($CURUSER["edit_torrents"]=="yes")
{
echo "<li><a href='torrentsimport'>".T_("MASS_TORRENT_IMPORT")."</a></li>";
}
if ($CURUSER && $CURUSER["view_users"]=="yes")
{
echo "<li><a href='teamsview'>".T_("TEAMS")."</a></li>";
echo "<li><a href='/memberlist'>".T_("MEMBERS")."</a></li>";
}
echo "<li><a href='/rules'>".T_("SITE_RULES")."</a></li>";
echo "<li><a href='/faq'>".T_("FAQ")."</a></li>";
if ($CURUSER && $CURUSER["view_users"]=="yes")
{
echo "<li><a href='/staff'>".T_("STAFF")."</a></li>";
}
echo "</ul></div>";
end_block();
}
?>