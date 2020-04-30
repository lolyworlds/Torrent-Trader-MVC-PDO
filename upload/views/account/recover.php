<form method="post" action="<?php echo TTURL; ?>/account/recover">
<table border="0" cellspacing="0" cellpadding="5">
    <tr>
        <td>
            <b><?php echo T_("NEW_PASSWORD"); ?></b>:
        </td>
        <td>
            <input type="hidden" name="secret" value="<?php echo $_GET['secret']; ?>" />
            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
            <input type="password" size="40" name="password" />
        </td>
    </tr>
    <tr>
        <td>
            <b><?php echo T_("REPEAT"); ?></b>:
        </td>
        <td>
            <input type="password" size="40" name="password1" />
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><input type="submit" value="<?php echo T_("SUBMIT"); ?>" /></td>
    </tr>
</table>
</form>