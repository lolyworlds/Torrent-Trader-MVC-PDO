<?php
/*
if (!$CURUSER) {
	begin_block(T_("LOGIN"));
?>
<form method="post" action="/accountlogin">
<table border="0" width="100%">
	<tr><td>
		<table border="0" align="center">
			<tr>
			<td align="center"><font face="verdana" size="1"><b><?php echo T_("USERNAME"); ?>:</b></font></td>
			</tr><tr>
			<td align="center"><input type="text" size="12" name="username" /></td>
			</tr><tr>
			<td align="center"><font face="verdana" size="1"><b><?php echo T_("PASSWORD"); ?>:</b></font></td>
			</tr><tr>
			<td align="center"><input type="password" size="12" name="password"  /></td>
			</tr><tr>
			<td align="center"><input type="submit" value="<?php echo T_("LOGIN"); ?>" /></td>
			</tr>
		</table>
		</td>
		</tr>
	<tr>
<td align="center">[<a href="/accountsignup"><?php echo T_("SIGNUP");?></a>]<br />[<a href="/accountrecover"><?php echo T_("RECOVER_ACCOUNT");?></a>]</td> </tr>
	</table>
    </form> 
<?php
end_block();

} else {
*/
if ($CURUSER){
begin_block(class_user($CURUSER["username"]));

	$avatar = htmlspecialchars($CURUSER["avatar"]);
	if (!$avatar)
		$avatar = $site_config["SITEURL"]."/images/default_avatar.png";

	$userdownloaded = mksize($CURUSER["downloaded"]);
	$useruploaded = mksize($CURUSER["uploaded"]);
	$privacylevel = T_($CURUSER["privacy"]);

	if ($CURUSER["uploaded"] > 0 && $CURUSER["downloaded"] == 0)
		$userratio = "Inf.";
	elseif ($CURUSER["downloaded"] > 0)
		$userratio = number_format($CURUSER["uploaded"] / $CURUSER["downloaded"], 2);
	else
		$userratio = "---";

	print ("<center><img width='80' height='80' src='$avatar' alt='' /></center><br />" . T_("DOWNLOADED") . ": $userdownloaded<br />" . T_("UPLOADED") . ": $useruploaded<br />".T_("CLASS").": ".T_($CURUSER["level"])."<br />" . T_("ACCOUNT_PRIVACY_LVL") . ": $privacylevel<br />". T_("RATIO") .": $userratio");

?>


<center><a href="/account"><?php echo T_("ACCOUNT"); ?></a> <br /> 
<?php if ($CURUSER["control_panel"]=="yes") {print("<a href=\"/admincp\">".T_("STAFFCP")."</a>");}?>
</center>
<?php
end_block();
}
?>