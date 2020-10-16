<?php
if (!$config["MEMBERSONLY"] || $_SESSION['loggedin'] == true) {
    $title = T_("MOST_ACTIVE");
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
    $where = "WHERE banned = 'no' AND visible = 'yes'";
    //uncomment the following line to exclude external torrents
    //$where = "WHERE external !='yes' AND banned ='no' AND visible = 'yes'"
    $TTCache = new Cache();
    $expires = 600; // Cache time in seconds
    if (($rows = $TTCache->Get("mostactivetorrents_block", $expires)) === false) {
        $res = $pdo->run("SELECT id, name, seeders, leechers FROM torrents $where ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT 10");

        $catsquery = $pdo->run("SELECT distinct parent_cat FROM categories ORDER BY parent_cat");
        $rows = array();
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }

        $TTCache->Set("mostactivetorrents_block", $rows, $expires);
    }

    if ($rows) {
        foreach ($rows as $row) {
            $char1 = 40; //cut length
            $smallname = htmlspecialchars(substr($row["name"], 0, 30)) . "..."; ?>
            <div class="pull-left">
            <a href='<?php echo TTURL; ?>/torrents/read?id=<?php echo $row["id"]; ?>' title='<?php echo htmlspecialchars($row["name"]); ?>'><?php echo $smallname; ?></a>
            </div>
            <div class="pull-left">
                <span class="label label-success"> S: <?php echo number_format($row['seeders']); ?></span>
                <span class="label label-warning"> L: <?php echo number_format($row['leechers']); ?></span>
            </div>
		<?php }

    } else {
        ?>
	<p><?php echo T_("NOTHING_FOUND"); ?></p>
<?php } ?>
	<!-- end content -->
	</div>
</div>
<br />
<?php
}
?>