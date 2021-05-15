<?php
Style::begin(Lang::T("GROUPS_ADD_NEW"));
    ?>
	<form action="<?php echo URLROOT; ?>/admingroups/groupsaddnew" name="level" method="post">
	<table width="100%" align="center">
	<tr><td>Group Name:</td><td><input type="text" name="gname" value="" size="40" /></td></tr>
	<tr><td>Group colour:</td><td align="left"><input type="text" name="gcolor" value="" size="10" /></td></tr>
	<tr><td>Copy Settings From: </td><td><select name="getlevel" size="1">
	<?php
    while ($level = $data['rlevel']->fetch(PDO::FETCH_ASSOC)) {
        print("\n<option value='" . $level["group_id"] . "'>" . htmlspecialchars($level["level"]) . "</option>");
    }
    print("\n</select></td></tr>");
    print("\n<tr><td align=\"center\" ><input type=\"submit\" name=\"confirm\" value=\"Confirm\" /></td></tr>");
    print("</table></form><br /><br />");
    Style::end();