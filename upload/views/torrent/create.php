	<form name="upload" enctype="multipart/form-data" action="<?php echo TTURL; ?>/torrents/create" method="post">
	<input type="hidden" name="takeupload" value="yes" />
	<table border="0" cellspacing="0" cellpadding="6" align="center">
	<?php
	print ("<tr><td align='right' valign='top'>" . T_("ANNOUNCE_URL") . ": </td><td align='left'>");
	
	while (list($key,$value) = thisEach($announce_urls)) {
		echo "<b>$value</b><br />";
	}
	
	if ($site_config["ALLOWEXTERNAL"]){
		echo "<br /><b>".T_("THIS_SITE_ACCEPTS_EXTERNAL")."</b>";
	}
	
	print ("</td></tr>");
	print ("<tr><td align='right'>" . T_("TORRENT_FILE") . ": </td><td align='left'> <input type='file' name='torrent' size='50' value='" . $_FILES['torrent']['name'] . "' />\n</td></tr>");
	print ("<tr><td align='right'>" .T_("NFO"). ": </td><td align='left'> <input type='file' name='nfo' size='50' value='" . $_FILES['nfo']['name'] . "' /><br />\n</td></tr>");
	print ("<tr><td align='right'>" . T_("TORRENT_NAME") . ": </td><td align='left'><input type='text' name='name' size='60' value='" . $_POST['name'] . "' /><br />".T_("THIS_WILL_BE_TAKEN_TORRENT")." \n</td></tr>");
	
	print ("<TR><td align=right>".T_("VIDEOTUBE").": </td><td align=left><input type='text' name='tube' size='50' />&nbsp;<a href=\"http://www.youtube.com\" target='_blank'><img border='0' src='$site_config[SITEURL]/images/youtube.png' width='50' height='50' title='Click here to go to Youtube'></a><br/><i>".T_("FORMAT").": </i> <span style='color:#FF0000'><b> http://www.youtube.com/watch?v=Jc9KR3tOP</b></SPAN></td></tr>");
	
	print ("<tr><td colspan='2' align='center'>".T_("MAX_FILE_SIZE").": ".mksize($site_config['image_max_filesize'])."<br />".T_("ACCEPTED_FORMATS").": ".implode(", ", array_unique($site_config["allowed_image_types"]))."<br /></td></tr><tr><td align='right'>".T_("IMAGE")." 1:&nbsp;&nbsp;</td><td><input type='file' name='image0' size='50' /></td></tr><tr><td align='right'>".T_("IMAGE")." 2:&nbsp;&nbsp;</td><td><input type='file' name='image1' size='50' /></td></tr>");
	
	$category = "<select name=\"type\">\n<option value=\"0\">" . T_("CHOOSE_ONE") . "</option>\n";
	
	$cats = genrelist();
	foreach ($cats as $row)
		$category .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";
	
	$category .= "</select>\n";
	print ("<tr><td align='right'>" . T_("CATEGORY") . ": </td><td align='left'>".$category."</td></tr>");
	
	
	$language = "<select name=\"lang\">\n<option value=\"0\">".T_("UNKNOWN_NA")."</option>\n";
	
	$langs = langlist();
	foreach ($langs as $row)
		$language .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
	
	$language .= "</select>\n";
	print ("<tr><td align='right'>".T_("LANGUAGE").": </td><td align='left'>".$language."</td></tr>");
	
	if ($site_config['ANONYMOUSUPLOAD'] && $site_config["MEMBERSONLY"] ){ ?>
		<tr><td align="right"><?php echo T_("UPLOAD_ANONY");?>: </td><td><?php printf("<input name='anonycheck' value='yes' type='radio' " . ($anonycheck ? " checked='checked'" : "") . " />".T_("YES")." <input name='anonycheck' value='no' type='radio' " . (!$anonycheck ? " checked='checked'" : "") . " />".T_("NO").""); ?> &nbsp;<i><?php echo T_("UPLOAD_ANONY_MSG");?></i>
		</td></tr>
		<?php
	}
	
	print ("<tr><td align='center' colspan='2'>" . T_("DESCRIPTION") . "</td></tr></table>");
	//$descr = '';
	require_once("helpers/bbcode_helper.php");
	print textbbcode("upload","descr","$descr");
	?>
	
	<br /><br /><br /><center><input type="submit" value="<?php echo T_("UPLOAD_TORRENT"); ?>" /><br />
	<i><?php echo T_("CLICK_ONCE_IMAGE");?></i>
	</center>
	</form>