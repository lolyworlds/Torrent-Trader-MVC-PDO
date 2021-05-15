<?php Style::begin(Lang::T("UPLOAD_RULES")); ?>
<b><?php echo stripslashes(UPLOADRULES); ?></b><br>
<?php Style::end(); ?>
<?php Style::begin($data['title']); ?>

<form name="upload" enctype="multipart/form-data" action="<?php echo URLROOT; ?>/upload/submit" method="post">
<input type="hidden" name="takeupload" value="yes" />
<table border="0" cellspacing="0" cellpadding="6" align="center">
<tr>
<td align='right' valign='top'><?php echo Lang::T("ANNOUNCE_URL"); ?>: </td><td align='left'>
<?php while (list($key, $value) = thisEach($data['announce_urls'])) { ?>
    <b><?php echo $value; ?></b><br />
<?php } ?>
<?php if (ALLOWEXTERNAL) { ?>
    <br /><b><?php echo Lang::T("THIS_SITE_ACCEPTS_EXTERNAL"); ?></b>
<?php }  ?>
</td></tr>

<tr><td align='right'><?php echo Lang::T("TORRENT_FILE"); ?>: </td>
<td align='left'> <input class="form-control" type='file' name='torrent' size='50' value='<?php echo $_FILES['torrent']['name']; ?>'></td></tr>

<tr><td align='right'><?php echo Lang::T("NFO"); ?>: </td>
<td align='left'> <input class="form-control" type='file' name='nfo' size='50' value='<?php echo $_FILES['nfo']['name']; ?>'><br></td></tr>

<tr><td align='right'><?php echo Lang::T("TORRENT_NAME"); ?>: </td>
<td align='left'><input class="form-control" type='text' name='name' size='60' value='<?php echo $_POST['name']; ?>' /><br /><?php echo Lang::T("THIS_WILL_BE_TAKEN_TORRENT"); ?></td></tr>

<?php if (IMDB1) { ?>
	<tr><td align='right'><a href="https://www.imdb.com/?ref_=nv_home" target='_blank'><img border='0' src='assets/images/imdb.png' width='50' height='50' title='Click here to go to Youtube'></a> </td>
	<td align='left'> <input class="form-control" type='text' name='imdb' size='60' value='<?php echo $_POST['imdb']; ?>' />Link from IMDB, example https://www.imdb.com/title/tt1799527/</td></tr>
<?php } ?>
<?php if (YOU_TUBE) { ?>
	<tr><td align=right><a href=\"http://www.youtube.com\" target='_blank'><img border='0' src='assets/images/youtube.png' width='50' height='50' title='Click here to go to Youtube'></a> </td>
	<td align=left><input class="form-control" type='text' name='tube' size='50' />&nbsp;<i><?php echo Lang::T("FORMAT"); ?>: </i> <span style='color:#FF0000'><b> https://www.youtube.com/watch?v=aYzVrjB-CWs</b></SPAN></td></tr>
<?php } ?>
	 <tr><td colspan='2' align='center'><?php echo Lang::T("MAX_FILE_SIZE"); ?>: <?php echo mksize(IMAGEMAXFILESIZE); ?>"&nbsp;
	 <?php echo Lang::T("ACCEPTED_FORMATS"); ?>: <?php echo implode(", ", array_unique(ALLOWEDIMAGETYPES)); ?><br /></td></tr>
	 <tr><td align='right'><?php echo Lang::T("IMAGE"); ?> 1:&nbsp;&nbsp;</td><td><input type='file' name='image0' size='50' /></td></tr>
	 <tr><td align='right'><?php echo Lang::T("IMAGE"); ?> 2:&nbsp;&nbsp;</td><td><input type='file' name='image1' size='50' /></td></tr>
<?php
$category = "<select name=\"type\">\n<option value=\"0\">" . Lang::T("CHOOSE_ONE") . "</option>\n";
$cats = genrelist();
foreach ($cats as $row) {
    $category .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";
}
$category .= "</select>\n";
print("<tr><td align='right'>" . Lang::T("CATEGORY") . ": </td><td align='left'>" . $category . "</td></tr>");

$language = "<select name=\"lang\">\n<option value=\"0\">" . Lang::T("UNKNOWN_NA") . "</option>\n";
$langs = langlist();
foreach ($langs as $row) {
    $language .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
}
$language .= "</select>\n";
print("<tr><td align='right'>" . Lang::T("LANGUAGE") . ": </td><td align='left'>" . $language . "</td></tr>");

if ($_SESSION["class"] > _VIP) {
    print("<tr><td align='right'>VIP: </td><td align=left><input type='checkbox' name='vip' " .
        (($row["vip"] == "yes") ? " checked='checked' " : "") . " value='yes'>Check this box if you want the torrent or only for VIP.</td></tr>");
}
if ($_SESSION["class"] > _VIP) {
    print("<tr><td align='right'>Freeleech: </td><td align=left><input type='checkbox' name='freeleech' " .
        (($row["free"] == 1) ? " checked='checked' " : "") . " value='yes'>Check this box if you want the torrent freeleech.</td></tr>");
}

if (ANONYMOUSUPLOAD && MEMBERSONLY) {?>
	<tr><td align="right"><?php echo Lang::T("UPLOAD_ANONY"); ?>: </td><td><?php printf("<input name='anonycheck' value='yes' type='radio' " . ($anonycheck ? " checked='checked'" : "") . " />" . Lang::T("YES") . " <input name='anonycheck' value='no' type='radio' " . (!$anonycheck ? " checked='checked'" : "") . " />" . Lang::T("NO") . "");?> &nbsp;<i><?php echo Lang::T("UPLOAD_ANONY_MSG"); ?></i>
	</td></tr>
<?php } ?>
<tr><td align='center' colspan='2'><?php echo Lang::T("DESCRIPTION"); ?></td></tr></table>
<?php
require_once APPROOT."/helpers/bbcode_helper.php";
print textbbcode("upload", "descr", "$descr");
?>
<br /><br /><br /><center><input type="submit" class="btn btn-sm btn-primary" value="<?php echo Lang::T("UPLOAD_TORRENT"); ?>" /><br />
<i><?php echo Lang::T("CLICK_ONCE_IMAGE"); ?></i>
</center>
</form>
<?php
Style::end();
?>