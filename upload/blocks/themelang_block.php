<?php
if ($CURUSER){
	begin_block(T_("THEME")." / ".T_("LANGUAGE"));
$stylesheets= '';
$languages = '';
	$ss_r = $pdo->run("SELECT * from stylesheets");
	$ss_sa = array();

	while ($ss_a = $ss_r->fetch(PDO::FETCH_ASSOC)){
		$ss_id = $ss_a["id"];
		$ss_name = $ss_a["name"];
		$ss_sa[$ss_name] = $ss_id;
	}

	ksort($ss_sa);
	reset($ss_sa);
    
	while (list($ss_name, $ss_id) = thisEach($ss_sa)){
		if ($ss_id == $CURUSER["stylesheet"]) $ss = " selected='selected'"; else $ss = "";
		$stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
	}

	$lang_r = $pdo->run("SELECT * from languages");
	$lang_sa = array();

	while ($lang_a = $lang_r->fetch(PDO::FETCH_ASSOC)){
		$lang_id = $lang_a["id"];
		$lang_name = $lang_a["name"];
		$lang_sa[$lang_name] = $lang_id;
	}

	ksort($lang_sa);
	reset($lang_sa);

	while (list($lang_name, $lang_id) = thisEach($lang_sa)){
		if ($lang_id == $CURUSER["language"]) $lang = " selected='selected'"; else $lang = "";
		$languages .= "<option value='$lang_id'$lang>$lang_name</option>\n";
	}

?>
 
 <form method="post" action="<?php echo TTURL; ?>/stylesheet" class="form-horizontal">
 	<div class="form-group">
		<label class="col-sm-4 control-label"><?php echo T_("THEME"); ?></label>
		<div class="col-sm-8">
			<select name="stylesheet" class="form-control"><?php echo $stylesheets; ?></select>
  		</div>
  	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?php echo T_("LANGUAGE"); ?></label>
		<div class="col-sm-8">
			<select name="language" class="form-control"><?php echo $languages; ?></select></td>
  		</div>
  	</div>
	<button type="submit" class="btn btn-primary center-block" value="" /><?php echo T_("APPLY"); ?></button>
  </form>  
<?php
end_block();
}
?>