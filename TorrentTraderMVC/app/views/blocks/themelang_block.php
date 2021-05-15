<?php
if ($_SESSION['loggedin'] == true) {
	$db = Database::instance();
    Block::begin(Lang::T("THEME") . " / " . Lang::T("LANGUAGE"));
    $stylesheets = '';
    $languages = '';
    $ss_r = $db->run("SELECT * from stylesheets");
    $ss_sa = array();

    while ($ss_a = $ss_r->fetch(PDO::FETCH_ASSOC)) {
        $ss_id = $ss_a["uri"];
        $ss_name = $ss_a["name"];
        $ss_sa[$ss_name] = $ss_id;
    }

    ksort($ss_sa);
    reset($ss_sa);

    while (list($ss_name, $ss_id) = thisEach($ss_sa)) {
        if ($ss_id == $_SESSION["stylesheet"]) {
            $ss = " selected='selected'";
        } else {
            $ss = "";
        }

        $stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
    }

    $lang_r = $db->run("SELECT * from languages");
    $lang_sa = array();

    while ($lang_a = $lang_r->fetch(PDO::FETCH_ASSOC)) {
        $lang_id = $lang_a["name"];
        $lang_name = $lang_a["name"];
        $lang_sa[$lang_name] = $lang_id;
    }

    ksort($lang_sa);
    reset($lang_sa);

    while (list($lang_name, $lang_id) = thisEach($lang_sa)) {
        if ($lang_id == $_SESSION["language"]) {
            $lang = " selected='selected'";
        } else {
            $lang = "";
        }

        $languages .= "<option value='$lang_id'$lang>$lang_name</option>\n";
    }

    ?>

 <form method="post" action="<?php echo URLROOT; ?>/stylesheet" class="form-horizontal">
 	<div class="form-group">
		<label><?php echo Lang::T("THEME"); ?></label>
		<select name="stylesheet" style="width: 95%" ><?php echo $stylesheets; ?></select>
  	</div>
	<div class="form-group">
		<label><?php echo Lang::T("LANGUAGE"); ?></label>
		<select name="language" style="width: 95%" ><?php echo $languages; ?></select></td>
  	</div>
	<button type="submit" class="btn btn-primary center-block" value="" /><?php echo Lang::T("APPLY"); ?></button>
  </form>

<?php
    block::end();
}