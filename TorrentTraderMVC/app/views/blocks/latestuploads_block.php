<?php
if ($_SESSION['loggedin'] == true) {
    $db = Database::instance();
    Block::begin(Lang::T("LATEST_TORRENTS"));
    $expire = 900; // time in seconds
    $TTCache = new Cache();
    if (($latestuploadsrecords = $TTCache->Get("latestuploadsblock", $expire)) === false) {
        $latestuploadsquery = $db->run("SELECT id, name, size, seeders, leechers FROM torrents WHERE banned='no' AND visible = 'yes' ORDER BY id DESC LIMIT 5");

        $latestuploadsrecords = array();
        while ($latestuploadsrecord = $latestuploadsquery->fetch(PDO::FETCH_ASSOC)) {
            $latestuploadsrecords[] = $latestuploadsrecord;
        }

        $TTCache->Set("latestuploadsblock", $latestuploadsrecords, $expire);
    }

    if ($latestuploadsrecords) {
        foreach ($latestuploadsrecords as $row) {
            $char1 = 40; //cut length
            $smallname = htmlspecialchars(substr($row['name'], 0, 30)) . "..."; ?>
			<div class="pull-left"><a href="<?php echo URLROOT; ?>/torrents/read?id=<?php echo $row["id"]; ?>" title="<?php echo htmlspecialchars($row["name"]); ?>"><?php echo $smallname; ?></a></div>
			<div class="pull-right"><?php echo Lang::T("SIZE"); ?>: <span class="label label-success"><?php echo mksize($row["size"]); ?></span></div>
		<?php }
    } else {?>
		<p calss="text-center"><?php echo Lang::T("NOTHING_FOUND"); ?></p>
	<?php } ?>
	<!-- end content -->

<?php block::end();
}