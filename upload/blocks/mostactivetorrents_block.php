<?php
if (!$config["MEMBERSONLY"] || $_SESSION['loggedin'] == true) {
    begin_block(T_("MOST_ACTIVE"));

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
            $smallname = htmlspecialchars(CutName($row["name"], $char1));?>

				<div class="pull-left"><a href='<?php echo TTURL; ?>/torrents/read?id=<?php echo $row["id"]; ?>' title='<?php echo htmlspecialchars($row["name"]); ?>'><?php echo $smallname; ?></a></div>
				<div class="pull-right"><span class="label label-success">S: <?php echo number_format($row["seeders"]); ?></span> <span class="label label-warning">L: <?php echo number_format($row["leechers"]); ?></span></div>
		<?php }

    } else {
        ?>
	<p><?php echo T_("NOTHING_FOUND"); ?></p>
<?php }
    end_block();
}
?>