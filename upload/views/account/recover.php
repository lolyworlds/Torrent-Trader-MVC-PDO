<?php echo T_("USE_FORM_FOR_ACCOUNT_DETAILS"); ?>
<form method="post" action="<?php echo TTURL; ?>/account/recover?take=1">
    <table border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td><b><?php echo T_("EMAIL_ADDRESS"); ?>:</b></td>
            <td><input type="text" size="40" name="email" />&nbsp;<input type="submit" value="<?php echo T_("SUBMIT");?>" /></td>
        </tr>
    </table>
</form> 

</div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php"; ?>