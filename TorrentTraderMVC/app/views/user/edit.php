<?php
Style::begin($data['title']);
usermenu($data['id']);
foreach ($data['selectuser'] as $selectedid):
$acceptpms = $selectedid['acceptpms'] == "yes";
?>

<div class="jumbotron">
<form action="<?php echo URLROOT; ?>/profile/submit?id=<?php echo $data['id']; ?>" method="post">

<div class="row">
    <div class="col">
             
 <?php 
$acceptpms = $selectedid['acceptpms'] == "yes";
print("<b>" . Lang::T("ACCEPT_PMS") . ":</b> <br>&nbsp;<input type='radio' name='acceptpms'" . ($acceptpms ? " checked='checked'" : "") .
    " value='yes' /><b>" . Lang::T("FROM_ALL") . "</b> <input type='radio' name='acceptpms'" .
    ($acceptpms ? "" : " checked='checked'") . " value='no' /><b>" . Lang::T("FROM_STAFF_ONLY") . "</b><br /><i>" . Lang::T("ACCEPTPM_WHICH_USERS") . "</i><br>");
    print("<br><b>" . Lang::T("ACCOUNT_PRIVACY_LVL") . ":</b> <br>&nbsp;" . priv("normal", "<b>" . Lang::T("NORMAL") . "</b>") . " " . priv("low", "<b>" . Lang::T("LOW") . "</b>") . " " . priv("strong", "<b>" . Lang::T("STRONG") . "</b>") . "<br /><i>" . Lang::T("ACCOUNT_PRIVACY_LVL_MSG") . "</i>");
print("<br><br><b>" . Lang::T("EMAIL_NOTIFICATION") . ":</b><br>&nbsp;<input type='checkbox' name='pmnotif' " . (strpos($selectedid['notifs'], "[pm]") !== false ? " checked='checked'" : "") .
    " value='yes' /><b>" . Lang::T("PM_NOTIFY_ME") . "</b><br /><i>" . Lang::T("EMAIL_WHEN_PM") . "</i><br>");

print("<br><b>" . Lang::T("THEME") . ":</b><br>&nbsp;<select name='stylesheet'>$data[stylesheets]</select><br>");
print("<br><b>" . Lang::T("PREFERRED_CLIENT") . ":</b><br>&nbsp;<input type='text' size='20' maxlength='20' name='client' value=\"" . htmlspecialchars($selectedid['client']) . "\" /><br>");
print("<br><b>" . Lang::T("AGE") . ":</b><br><input type='text' size='3' maxlength='2' name='age' value=\"" . htmlspecialchars($selectedid['age']) . "\" /><br>");
print("<br><b>" . Lang::T("GENDER") . ":</b><br> &nbsp;<select size='1' name='gender'>\n$data[gender]\n</select><br>");
print("<br><b>" . Lang::T("COUNTRY") . ":</b><br> &nbsp;<select name='country'>\n$data[countries]\n</select><br><br>");
print("<br><b>" . Lang::T("TEAM") . ":</b><br>&nbsp;<select name='teams'>\n$data[teams]\n</select><br>");
?>

    </div>
    <div class="col">

<?php
print("<b>" . Lang::T("AVATAR_LINK") . ":</b> &nbsp;<input type='text' class='form-control' name='avatar' size='50' value=\"" . htmlspecialchars($selectedid["avatar"]) .
"\" />\n<a href=" . URLROOT . "/account/avatar?id=".$data['id']."><b>Or upload file (90px x 90px)</b></a><br />");
print("<br><b>" . Lang::T("CUSTOM_TITLE") . ":</b><br> &nbsp;<input type='text' class='form-control' name='title' size='50' value=\"" . strip_tags($selectedid["title"]) .
"\" /><br />\n <i>" . Lang::T("HTML_NOT_ALLOWED") . "</i>");
print("<br><b>" . Lang::T("SIGNATURE") . ":</b><br> &nbsp;<textarea name='signature'  class='form-control' >" . htmlspecialchars($selectedid["signature"]) .
"</textarea><br />\n <i><br>" . sprintf(Lang::T("MAX_CHARS"), 150) . ", " . Lang::T("HTML_NOT_ALLOWED") . "</i>");
?>
<br><br><b><?php echo Lang::T("RESET_PASSKEY"); ?>:</b><br> &nbsp;<input type='checkbox' name='resetpasskey' value='1' />&nbsp;<i><?php echo Lang::T("RESET_PASSKEY_MSG"); ?></i><br>
<?php
if (SHOUTBOX) {
    print("<br><b>".Lang::T("HIDE_SHOUT").":</b><br>
    <input type='checkbox' name='hideshoutbox' value='yes' ".($CURUSER['hideshoutbox'] == 'yes' ? 'checked="checked"' : '')." />
    &nbsp;".Lang::T("HIDE_SHOUT")."<br> ");
}?>
<br><br><b><?php echo Lang::T("TIMEZONE"); ?>:</b><br>&nbsp;<select class="form-control" name='tzoffset'' ><?php echo $data['tz']; ?></select>

    </div>
</div>

<?php
print("<br><center><button type='submit' class='btn btn-sm btn-primary' value='" . Lang::T("SUBMIT") . "' />Submit</button></center>");
 endforeach;
 
 ?>
</form>
</div>
<?php
Style::end();