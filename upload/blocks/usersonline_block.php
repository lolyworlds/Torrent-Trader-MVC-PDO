<?php
if ($_SESSION['loggedin'] == true) {
    $title = T_("ONLINE_USERS");
    $blockId = "b-" . sha1($title);
    $db = Database::instance();
    $TTCache = new Cache();
    $expires = 120; // Cache time in seconds 2 mins
    ?>
    <div class="card">
    <div class="card-header">
        <?php echo $title ?>
        <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
    </div>
    <div class="card-body slidingDiv<?php echo $blockId; ?>">
    <!-- content -->
    <?php
    if (($rows = $TTCache->Get("usersonline_block", $expires)) === false) {
        $res = $pdo->run("SELECT id, username FROM users WHERE enabled = 'yes' AND status = 'confirmed' AND privacy !='strong' AND UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(users.last_access) <= 900");

        $rows = array();
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }

        $TTCache->Set("usersonline_block", $rows, $expires);
    }

    if (!$rows) {?>
	<p class="text-center"><?php echo T_("NO_USERS_ONLINE"); ?></p>
    <?php } else {?>
	<?php for ($i = 0, $cnt = count($rows), $n = $cnt - 1; $i < $cnt; $i++) {
        $row = &$rows[$i];?>
        <a href='<?php echo TTURL; ?>/users/profile?id=<?php echo $row["id"]; ?>'><?php echo class_user_colour($row["username"]); ?></a><?php echo ($i < $n ? ", " : ""); ?>
	<?php
    }
    }
    ?>
    <!-- end content -->
    </div>
</div>
<br />
<?php
}