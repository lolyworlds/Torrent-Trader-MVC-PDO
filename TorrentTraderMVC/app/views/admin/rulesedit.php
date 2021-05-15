<form method="post" action="<?php echo URLROOT; ?>/adminrules/rulesedit?save=1">
<table border="0" cellspacing="0" cellpadding="10" align="center">
<?php while ($res1 = $data['res']->fetch(PDO::FETCH_ASSOC)) { ?>

<tr><td>Section Title:</td><td>
<input style="width: 400px;" type="text" name="title" value="<?php echo $res1['title']; ?>" />
</td></tr>

<tr><td style="vertical-align: top;">Rules:</td><td>
<textarea cols="60" rows="15" name="text"><?php echo stripslashes($res1["text"]); ?></textarea><br />
NOTE: Remember that BB can be used (NO HTML)</td></tr>

<tr><td colspan="2" align="center">
<input type="radio" name='public' value="yes" <?php echo ($res1["public"] == "yes" ? "checked='checked'" : ""); ?>/>
For everybody :
<input type="radio" name='public' value="no" <?php echo ($res1["public"] == "no" ? "checked='checked'" : ""); ?> />
Members Only (Min User Class: 
<input type=\"text\" name='class' value="<?php echo $res1['class']; ?>" size="1" />)</td></tr>

<tr><td colspan="2" align="center"><input type="hidden" value="<?php echo $data['id']; ?>" name="id" />
<input type="submit" value="<?php echo Lang::T("SAVE"); ?>" style="width: 60px;" /></td></tr>
<?php } ?>
</table></form>