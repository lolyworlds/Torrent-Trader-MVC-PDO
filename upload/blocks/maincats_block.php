<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
begin_block(T_("BROWSE_TORRENTS"));
	$catsquery = $pdo->run("SELECT distinct parent_cat FROM categories ORDER BY parent_cat");
	echo "<div id='maincats' class='bMenu'><ul>\n";
	echo "<li><a href='$site_config[SITEURL]/torrents/browse'>".T_("SHOW_ALL")."</a></li>\n";
	while($catsrow = $catsquery->fetch(PDO::FETCH_ASSOC)){
		echo "<li><a href='$site_config[SITEURL]/torrents/browse?parent_cat=".urlencode($catsrow['parent_cat'])."'>$catsrow[parent_cat]</a></li>\n";
	}
	echo "</ul></div>\n";

end_block();
}
?>
