<?php
if ($_SESSION['loggedin'] == true) {
    $_GET['search'] = $_GET['search'] ?? '';
    $title = T_("SEARCH");
    $blockId = "b-" . sha1($title);
    ?>
    <div class="card">
        <div class="card-header">
            <?php echo $title ?>
            <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
        </div>
        <div class="card-body slidingDiv<?php echo $blockId; ?>">
        <!-- content -->
	<form method="get" action="<?php echo TTURL; ?>/torrents/search" class="form-inline">
		<div class="input-group">
			<input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
			<span class="input-group-btn">
				<button type="submit" class="btn btn-primary"/><?php echo T_("SEARCH"); ?></button>
			</span>
		</div>
	</form>
	<?php
    end_block();
}
?>