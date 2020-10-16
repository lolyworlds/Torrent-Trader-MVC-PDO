<?php
if (!$config["MEMBERSONLY"] || $_SESSION['loggedin'] == true) {
	$title = T_("BROWSE_TORRENTS");
    $blockId = "b-" . sha1($title);
    ?>
    
    <div class="card">
        <div class="card-header">
            <?php echo $title ?>
            <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
        </div>
        <div class="card-body slidingDiv<?php echo $blockId; ?>">
		<!-- content -->
		<?php
    $catsquery = $pdo->run("SELECT distinct parent_cat FROM categories ORDER BY parent_cat");?>
	<div class="list-group">
		<a href="<?php echo TTURL; ?>/torrents/browse" class="list-group-item"><i class="fa fa-folder-open"></i> <?php echo T_("SHOW_ALL"); ?></a>
	<?php while ($catsrow = $catsquery->fetch(PDO::FETCH_ASSOC)) {?>
		<a href="<?php echo TTURL; ?>/torrents/browse?parent_cat=<?php echo urlencode($catsrow["parent_cat"]); ?>" class="list-group-item"><i class="fa fa-folder-open"></i> <?php echo $catsrow["parent_cat"]; ?></a>
	<?php }?>
	</div>

		<!-- end content -->
		</div>
</div>
<br />
    <?php
}