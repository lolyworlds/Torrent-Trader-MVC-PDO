<?php
//USERS ONLINE
if ($CURUSER){
begin_block(T_("NEWEST_MEMBERS"));

$expire = 600; // time in seconds
if (($rows = $TTCache->Get("newestmember_block", $expire)) === false) {
	$res = DB::run("SELECT id, username FROM users WHERE enabled =?  AND status=? AND privacy !=?  ORDER BY id DESC LIMIT 5", ['yes', 'confirmed', 'strong']);
	$rows = array();

	while ($row = $res->fetch(PDO::FETCH_ASSOC))
		$rows[] = $row;

	$TTCache->Set("newestmember_block", $rows, $expire);
}

if (!$rows) {
	echo T_("NOTHING_FOUND");
} else {
		echo "<div id='nMember' class='bMenu'><ul>\n";
	foreach ($rows as $row) {
		echo "<li><a href='account-details.php?id=$row[id]'>" . class_user($row["username"]) . "</a></li>\n";
	}
		echo "</ul></div>\n";
}

end_block();
}
?>