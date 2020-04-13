<?php
require_once("backend/init.php");
dbconn();
loggedinonly();

if ($CURUSER["view_users"]=="no")
    show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);
    
$search = trim($_GET['search']);
$class = (int) $_GET['class'];
$letter = trim($_GET['letter']);

if (!$class)
	unset($class);

$q = $query = null;
if ($search) {
	$query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
	if ($search) {
		$q = "search=" . htmlspecialchars($search);
	}
} elseif ($letter) {
	if (strlen($letter) > 1)
		unset($letter);
	if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
		unset($letter);
	} else {
		$query = "username LIKE '$letter%' AND status='confirmed'";
	}
	$q = "letter=$letter";
}

if (!$query) {
	$query = "status='confirmed'";
}

if ($class) {
	$query .= " AND class=$class";
	$q .= ($q ? "&amp;" : "") . "class=$class";
}

stdhead(T_("USERS"));
begin_frame(T_("USERS"));

print("<center><br /><form method='get' action='memberlist.php'>\n");
print(T_("SEARCH").": <input type='text' size='30' name='search' />\n");
print("<select name='class'>\n");
print("<option value='-'>(any class)</option>\n");
$res = DB::run("SELECT group_id, level FROM groups");
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
	print("<option value='$row[group_id]'" . ($class && $class == $row['group_id'] ? " selected='selected'" : "") . ">".htmlspecialchars($row['level'])."</option>\n");
}
print("</select>\n");
print("<button type='submit' class='btn btn-primary btn-sm'>".T_("APPLY")."</button>");
print("</form></center>\n");

print("<p align='center'>\n");

print("<a href='memberlist.php'><b>".T_("ALL")."</b></a> - \n");
foreach (range("a", "z") as $l) {
	$L = strtoupper($l);
	if ($l == $letter)
		print("<b>$L</b>\n");
	else
		print("<a href='memberlist.php?letter=$l'><b>$L</b></a>\n");
}

print("</p>\n");

$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 5; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`users` ORDER BY `id` ASC"; // Change `users` & 'id' according to your table name.
$results = DB::run("SELECT users.*, groups.level FROM users INNER JOIN groups ON groups.group_id=users.class WHERE $query ORDER BY username LIMIT {$startpoint} , {$per_page}");

if ($results->rowCount()) {
 
 // call function for table
print("<br />");

print("<div class='table-responsive'> <table class='table table-striped'><thead><tr><thead><tr>
<th>" . T_("USERNAME") . "</th>
<th>" . T_("REGISTERED") . "</th>
<th>" . T_("LAST_ACCESS") . "</th>
<th>" . T_("CLASS") . "</th>
<th>" . T_("COUNTRY") . "</th>
</tr></thead>");

while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
	
		$cres = DB::run("SELECT name,flagpic FROM countries WHERE id=?", [$row['country']]);

		if ($carr = $cres->fetch(PDO::FETCH_ASSOC)) {
			$country = "<td><img src='$site_config[SITEURL]/images/countries/$carr[flagpic]' title='".htmlspecialchars($carr['name'])."' alt='".htmlspecialchars($carr['name'])."' /></td>";
		} else {
			$country = "<td><img src='$site_config[SITEURL]/images/countries/unknown.gif' alt='Unknown' /></td>";
		}

print("<tbody><tr>
<td><a href='account-details.php?id=$row[id]'><b>".class_user($row['username'])."</b></a>" .($row["donated"] > 0 ? "<img src='$site_config[SITEURL]/images/star.png' border='0' alt='Donated' />" : "")."</td>"."
<td>".utc_to_tz($row["added"])."</td>
<td>".utc_to_tz($row["last_access"])."</td>". "
<td>".T_($row["level"])."</td>$country</tr></tbody>");
}

print("</table></div>");
}

 else {
     echo "No records are found.";
}

// displaying paginaiton function
echo pagination($statement,$per_page,$page,$url='?');

end_frame();
stdfoot();
