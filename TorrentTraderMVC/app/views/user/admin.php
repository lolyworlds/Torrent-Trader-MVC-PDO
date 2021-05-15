<?php
Style::begin($data['title']);
usermenu($data['id']);
foreach ($data['selectuser'] as $selectedid):
    $uploaded = $selectedid["uploaded"];
    $downloaded = $selectedid["downloaded"];
    $enabled = $selectedid["enabled"] == 'yes';
    $warned = $selectedid["warned"] == 'yes';
    $forumbanned = $selectedid["forumbanned"] == 'yes';
    $downloadbanned = $selectedid["downloadbanned"] == 'yes';
    $shoutboxpos = $selectedid["shoutboxpos"] == 'yes';
    $modcomment = htmlspecialchars($selectedid["modcomment"]);
?>

<div class="jumbotron">
<form action="<?php echo URLROOT; ?>/profile/submited?id=<?php echo $data['id']; ?>" method="post">

<div class="row">
    <div class="col">
        
    <?php  
    print("" . Lang::T("UPLOADED") . ": <br><input type='text' size='30' name='uploaded' value=\"" . mksize($selectedid["uploaded"], 9) . "\" /><br>\n");
    print("<br>" . Lang::T("DOWNLOADED") . ": <br><input type='text' size='30' name='downloaded' value=\"" . mksize($selectedid["downloaded"], 9) . "\" /><br>\n");
    print("<br>" . Lang::T("EMAIL") . "<br><input type='text' size='40' name='email' value=\"$selectedid[email]\" /><br>\n");
    print("<br>" . Lang::T("IP_ADDRESS") . ": <br><input type='text' size='20' name='ip' value=\"$selectedid[ip]\" /><br>\n");
    print("<br>" . Lang::T("INVITES") . ": <br><input type='text' size='4' name='invites' value='" . $selectedid["invites"] . "' /><br>\n");

    if ($_SESSION["class"] > 1) { //todo
        print("<br>" .Lang::T("CLASS") . ": <br><select name='class'>");
        $maxclass = $_SESSION["class"] + 1;
        for ($i = 1; $i < $maxclass; ++$i) {
            print("<option value='$i' " . ($selectedid["class"] == $i ? " selected='selected'" : "") . ">$prefix" . get_user_class_name($i) . "\n");
        }
            print("</select><br>");
        }
    print("<br>" . Lang::T("DONATED_US") . ": </br><input type='text' size='4' name='donated' value='$selectedid[donated]' /><br>\n");
    print("<br>" . Lang::T("SEEDING_BONUS") . ": <br><input type='text' size='10' name='bonus' value='$selectedid[seedbonus]'><br>");
    ?>

    </div>
    <div class="col">

    <?php  
    print("".Lang::T("ACCOUNT_STATUS") . ": <br>&nbsp;<input name='enabled' value='yes' type='radio' " . ($enabled ? " checked='checked'" : "") . " />Enabled <input name='enabled' value='no' type='radio' " . (!$enabled ? " checked='checked' " : "") . " />Disabled<br>\n");
    print("<br>".Lang::T("WARNED") . ": <br>&nbsp;<input name='warned' value='yes' type='radio' " . ($warned ? " checked='checked'" : "") . " />Yes <input name='warned' value='no' type='radio' " . (!$warned ? " checked='checked'" : "") . " />No<br>\n");
    print("<br>".Lang::T("FORUM_BANNED") . ": <br>&nbsp;<input name='forumbanned' value='yes' type='radio' " . ($forumbanned ? " checked='checked'" : "") . " />Yes <input name='forumbanned' value='no' type='radio' " . (!$forumbanned ? " checked='checked'" : "") . " />No<br>\n");
    print("<br>Download Banned: <br>&nbsp;<input name='downloadbanned' value='yes' type='radio' " . ($downloadbanned ? " checked='checked'" : "") . " />Yes <input name='downloadbanned' value='no' type='radio' " . (!$downloadbanned ? " checked='checked'" : "") . " />No<br>\n");
    print("<br>Shoutbox Banned: <br>&nbsp;<input name='shoutboxpos' value='yes' type='radio' " . ($shoutboxpos ? " checked='checked'" : "") . " />Yes <input name='shoutboxpos' value='no' type='radio' " . (!$shoutboxpos ? " checked='checked'" : "") . " />No<br>\n");
    ?>
    </div>
    <div class="col">

    <?php  
    print(Lang::T("MOD_COMMENT") . ": <br>&nbsp;<textarea cols='40' rows='10' name='modcomment'>$modcomment</textarea><br>\n");
    print("<br>" . Lang::T("PASSWORD") . ": <br><input type='password' size='40' name='password' value=\"$selectedid[password]\" /><br>\n");
    print("<br>" . Lang::T("CHANGE_PASS") . ": <br><input type='checkbox' name='chgpasswd' value='yes'/><br>");
    print("<br>" . Lang::T("PASSKEY") . ": <br>$selectedid[passkey]<br /><input name='resetpasskey' value='yes' type='checkbox' />" . Lang::T("RESET_PASSKEY") . " (" . Lang::T("RESET_PASSKEY_MSG") . ")<br>\n");
?>
         
    </div>
</div>

<?php
print("<center><button type='submit' class='btn btn-sm btn-primary' value='" . Lang::T("SUBMIT") . "' />Submit</button></center>");
 endforeach;?>
</form>
</div>
<?php
Style::end();