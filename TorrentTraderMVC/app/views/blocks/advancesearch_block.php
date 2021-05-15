<?php
if ($_SESSION['loggedin']) {
    Block::begin(Lang::T("SEARCH"));
    ?>

        <!-- content -->
	<form method="get" action="<?php echo URLROOT; ?>/search">
		<input type="text" name="search" style="width: 95%" value="<?php echo htmlspecialchars($_GET["search"]); ?>" /><br />
		<select name="cat"  style="width: 95%" >
			<option value="0">(<?php echo Lang::T("ALL_TYPES"); ?>)</option>
			<?php
    $cats = genrelist();
    $catdropdown = "";
    foreach ($cats as $cat) {
        $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
        if ($cat["id"] == @$_GET["cat"]) {
            $catdropdown .= " selected=\"selected\"";
        }
        $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
    }
    ?>
			<?php echo $catdropdown; ?>
		</select><br />
		<select name="incldead" style="width: 95%" >
			<option value="0"><?php echo Lang::T("ACTIVE"); ?></option>
			<option value="1"><?php echo Lang::T("INCLUDE_DEAD"); ?></option>
			<option value="2"><?php echo Lang::T("ONLY_DEAD"); ?></option>
		</select><br />
		<?php if (ALLOWEXTERNAL) {?>
		<select name="inclexternal" style="width: 95%" >
			<option value="0"><?php echo Lang::T("LOCAL"); ?>/<?php echo Lang::T("EXTERNAL"); ?></option>
			<option value="1"><?php echo Lang::T("LOCAL_ONLY"); ?></option>
			<option value="2"><?php echo Lang::T("EXTERNAL_ONLY"); ?></option>
		</select><br />
		<?php }?>
		<button type="submit" class="btn btn-primary center-block" /><?php echo Lang::T("SEARCH"); ?></button>
	</form>
    <!-- end content -->

<?php block::end();
}