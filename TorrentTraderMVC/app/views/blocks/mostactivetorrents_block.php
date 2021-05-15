<?php
if ($_SESSION['loggedin'] == true) {
    $db = Database::instance();
    Block::begin(Lang::T("MOST_ACTIVE"));
    $where = "WHERE banned = 'no' AND visible = 'yes'";
    $TTCache = new Cache();
    $expires = 600; // Cache time in seconds
    if (($rows = $TTCache->Get("mostactivetorrents_block", $expires)) === false) {
        $res = $db->run("SELECT id, name, seeders, leechers FROM torrents $where ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT 10");

        $catsquery = $db->run("SELECT distinct parent_cat FROM categories ORDER BY parent_cat");
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
            <a href='<?php echo URLROOT; ?>/torrents/read?id=<?php echo $row["id"]; ?>' title='<?php echo htmlspecialchars($row["name"]); ?>'><?php echo $smallname; ?></a>
            </div>
            <div class="pull-left">
                <span class="label label-success"> S: <?php echo number_format($row['seeders']); ?></span>
                <span class="label label-warning"> L: <?php echo number_format($row['leechers']); ?></span>
            </div>
		<?php }

    } else {
        ?>
	<p><?php echo Lang::T("NOTHING_FOUND"); ?></p>
<?php } ?>
	<!-- end content -->

<?php block::end();
}