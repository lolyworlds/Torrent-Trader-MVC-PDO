<form method="post" action="<?php echo URLROOT; ?>/adminrules/rulesaddsect?save=1">
<table border="0" cellspacing="0" cellpadding="10" align="center">
<tr><td>Section Title:</td><td><input style="width: 400px;" type="text" name="title" /></td></tr>
<tr><td style="vertical-align: top;">Rules:</td><td><textarea cols="60" rows="15" name="text"></textarea><br />
<br />NOTE: Remember that BB can be used (NO HTML)</td></tr>
<tr><td colspan="2" align="center"><input type="radio" name='public' value="yes" checked="checked" />For everybody
<input type="radio" name='public' value="no" />&nbsp;Members Only - (Min User Class: 
<input type="text" name='class' value="0" size="1" />)</td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="Add" style="width: 60px;" /></td></tr>
</table></form>