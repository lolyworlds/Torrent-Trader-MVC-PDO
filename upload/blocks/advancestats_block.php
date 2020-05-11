<?php
$date_time = get_date_time(gmtime()-(3600*24)); // the 24hrs is the hours you want listed
$registered = number_format(get_row_count("users"));
$ncomments = number_format(get_row_count("comments"));
$nmessages = number_format(get_row_count("messages"));
$ntor = number_format(get_row_count("torrents"));
$totaltoday = number_format(get_row_count("users", "WHERE users.last_access>='$date_time'"));
$regtoday = number_format(get_row_count("users", "WHERE users.added>='$date_time'"));
$todaytor = number_format(get_row_count("torrents", "WHERE torrents.added>='$date_time'"));
$guests = number_format(getguests());
$seeders = get_row_count("peers", "WHERE seeder='yes'");
$leechers = get_row_count("peers", "WHERE seeder='no'");
$members = number_format(get_row_count("users", "WHERE UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(users.last_access) < 900"));
$totalonline = $members + $guests;

$result = $pdo->run("SELECT SUM(downloaded) AS totaldl FROM users");
while ($row = $result->fetch(PDO::FETCH_LAZY)) {
	$totaldownloaded = $row["totaldl"]; 
} 

$result = $pdo->run("SELECT SUM(uploaded) AS totalul FROM users");
while ($row =  $result->fetch(PDO::FETCH_LAZY)) {
	$totaluploaded      = $row["totalul"]; 
}
$localpeers = $leechers+$seeders;
if($CURUSER["edit_users"]=="yes") {
begin_block(T_("STATS"));
?>

<ul class="list-unstyled">
	<p><strong><?php echo T_("TORRENTS");?></strong></p>
	<li><i class="fa fa-folder-open-o"></i> <?php echo T_("TRACKING");?>: <strong><?php echo $ntor;?> <?php echo P_("TORRENT", $ntor);?></strong></li>
	<li><i class="fa fa-calendar-o"></i> <?php echo T_("NEW_TODAY");?>: <strong><?php echo $todaytor ;?></strong></li>
	<li><i class="fa fa-refresh"></i> <?php echo T_("SEEDERS");?>: <strong><?php echo number_format($seeders);?></strong></li>
	<li><i class="fa fa-arrow-circle-down"></i> <?php echo T_("LEECHERS");?>: <strong><?php echo number_format($leechers);?></strong></li>
	<li><i class="fa fa-arrow-circle-up"></i> <?php echo T_("PEERS");?>: <strong><?php echo number_format($localpeers);?></strong></li>
	<li><i class="fa fa-download"></i> <?php echo T_("DOWNLOADED");?>: <strong><span class="label label-danger"><?php echo mksize($totaldownloaded);?></span></strong></li>
	<li><i class="fa fa-upload"></i> <?php echo T_("UPLOADED");?>: <strong><span class="label label-success"><?php echo mksize($totaluploaded);?></span></strong></li>
	<hr />
	<p><strong><?php echo T_("MEMBERS");?></strong></p>
	<li><?php echo T_("WE_HAVE");?>: <strong><?php echo $registered;?> <?php echo P_("MEMBER", $registered);?></strong></li>
	<li><?php echo T_("NEW_TODAY");?>: <strong><?php echo $regtoday;?></strong></li>
	<li><?php echo T_("VISITORS_TODAY");?>: <strong><?php echo $totaltoday;?></strong></li>
	<hr />
	<p><strong><?php echo T_("ONLINE");?></strong></p>
	<li><?php echo T_("TOTAL_ONLINE");?>: <strong><?php echo $totalonline;?></strong></li>
	<li><?php echo T_("MEMBERS");?>: <strong><?php echo $members;?></strong></li>
	<li><?php echo T_("GUESTS_ONLINE");?>: <strong><?php echo $guests;?></strong></li>
	<li><?php echo T_("COMMENTS_POSTED");?>: <strong><?php echo $ncomments;?></strong></li>
	<li><?php echo T_("MESSAGES_SENT");?>: <strong><?php echo $nmessages;?></strong></li>
</ul>

<?php
end_block();
}
if($CURUSER["edit_users"]=="no") {
begin_block(T_("STATS"));
?>

<ul class="list-unstyled">
	<p><strong><?php echo T_("TORRENTS");?></strong></p>
	<li><i class="fa fa-folder-open-o"></i> <?php echo T_("TRACKING");?>: <strong><?php echo $ntor;?> <?php echo P_("TORRENT", $ntor);?></strong></li>
	<li><i class="fa fa-calendar-o"></i> <?php echo T_("NEW_TODAY");?>: <strong><?php echo $todaytor ;?></strong></li>
</ul>

<?php
end_block();
}
?>