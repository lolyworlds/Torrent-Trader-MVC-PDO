<?php
if ($_SESSION['loggedin']) {
	begin_block(T_("SEARCH"));
?>
	<form method="get" action="<?php echo TTURL; ?>/torrents/search">
		<input type="text" name="search" style="width: 95%" value="<?php echo htmlspecialchars($_GET["search"]); ?>" /><br />
		<select name="cat"  style="width: 95%" >
			<option value="0">(<?php echo T_("ALL_TYPES"); ?>)</option>
			<?php
			$cats = genrelist();
			$catdropdown = "";
			foreach ($cats as $cat) {
				$catdropdown .= "<option value=\"" . $cat["id"] . "\"";
				if ($cat["id"] == @$_GET["cat"])
					$catdropdown .= " selected=\"selected\"";
				$catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
			}
			?>
			<?php echo $catdropdown; ?>
		</select><br />
		<select name="incldead" style="width: 95%" >
			<option value="0"><?php echo T_("ACTIVE"); ?></option>
			<option value="1"><?php echo T_("INCLUDE_DEAD"); ?></option>
			<option value="2"><?php echo T_("ONLY_DEAD"); ?></option>
		</select><br />
		<?php if ($config["ALLOWEXTERNAL"]){?>
		<select name="inclexternal" style="width: 95%" >
			<option value="0"><?php echo T_("LOCAL"); ?>/<?php echo T_("EXTERNAL"); ?></option>
			<option value="1"><?php echo T_("LOCAL_ONLY"); ?></option>
			<option value="2"><?php echo T_("EXTERNAL_ONLY"); ?></option>
		</select><br />
		<?php } ?>
		<button type="submit" class="btn btn-primary center-block" /><?php echo T_("SEARCH"); ?></button>
	</form>
	<?php
	end_block();
}
?>