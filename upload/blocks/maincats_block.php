<?php
if (!$config["MEMBERSONLY"] || $_SESSION['loggedin']) {
begin_block(T_("BROWSE_TORRENTS"));
$catsquery = $pdo->run("SELECT distinct parent_cat FROM categories ORDER BY parent_cat"); ?>
	<div class="list-group">
		<a href="torrents.php" class="list-group-item"><i class="fa fa-folder-open"></i> <?php echo T_("SHOW_ALL"); ?></a>
	<?php while($catsrow = $catsquery->fetch(PDO::FETCH_ASSOC)){ ?>
		<a href="<?php echo TTURL; ?>/torrents/browse?parent_cat=<?php echo urlencode($catsrow["parent_cat"]); ?>" class="list-group-item"><i class="fa fa-folder-open"></i> <?php echo $catsrow["parent_cat"]; ?></a>
	<?php } ?>
	</div>
<?php
end_block();
}
?>
