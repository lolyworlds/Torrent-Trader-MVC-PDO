<?php
if ($CURUSER) {
    begin_block(T_("NEWEST_MEMBERS"));

    $expire = 600; // time in seconds
    if (($rows = $TTCache->Get("newestmember_block", $expire)) === false) {
        $res = $pdo->run("SELECT id, username FROM users WHERE enabled =?  AND status=? AND privacy !=?  ORDER BY id DESC LIMIT 5", ['yes', 'confirmed', 'strong']);
        $rows = array();

        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }

        $TTCache->Set("newestmember_block", $rows, $expire);
    }

    if (!$rows) {?>
	<p class="text-center"><?php echo T_("NOTHING_FOUND");?></p>
<?php } else { ?>
		<div class="list-group">
	<?php foreach ($rows as $row) { ?>
			<a href='<?php echo TTURL; ?>/accountdetails?id=<?php echo $row["id"];?>' class="list-group-item"><?php echo $row["username"];?></a>
	<?php } ?>
		</div>
<?php }

    end_block();
}
?>