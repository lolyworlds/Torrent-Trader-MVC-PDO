<?php
if ($_SESSION['loggedin'] == true) {
    $_GET['search'] = $_GET['search'] ?? '';
    Block::begin(Lang::T("SEARCH"));
    ?>
        <!-- content -->
	<form method="get" action="<?php echo URLROOT; ?>/search" class="form-inline">
		<div class="input-group">
			<input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
			<span class="input-group-btn">
				<button type="submit" class="btn btn-primary"/><?php echo Lang::T("SEARCH"); ?></button>
			</span>
		</div>
	</form>
	<?php
    block::end();
}
?>