<?php
if (!$config["MEMBERSONLY"] || $_SESSION['loggedin'] == true) {
    $title = T_("LATEST_TORRENTS");
    $blockId = "b-" . sha1($title);
    $pdo = Database::instance();
    ?>
    
    <div class="card">
        <div class="card-header">
            <?php echo $title ?>
            <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
        </div>
        <div class="card-body slidingDiv<?php echo $blockId; ?>">
        <!-- content -->
        <?php
    $expire = 900; // time in seconds
    $TTCache = new Cache();
    if (($latestuploadsrecords = $TTCache->Get("latestuploadsblock", $expire)) === false) {
        $latestuploadsquery = $pdo->run("SELECT id, name, size, seeders, leechers FROM torrents WHERE banned='no' AND visible = 'yes' ORDER BY id DESC LIMIT 5");

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
			<div class="pull-left"><a href="<?php echo TTURL; ?>/torrents-details.php?id=<?php echo $row["id"]; ?>" title="<?php echo htmlspecialchars($row["name"]); ?>"><?php echo $smallname; ?></a></div>
			<div class="pull-right"><?php echo T_("SIZE"); ?>: <span class="label label-success"><?php echo mksize($row["size"]); ?></span></div>
		<?php }
    } else {?>
		<p calss="text-center"><?php echo T_("NOTHING_FOUND"); ?></p>
	<?php } ?>
	<!-- end content -->
    </div>
</div>
<br />
<?php
}