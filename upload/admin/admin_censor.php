<?php
#======================================================================#
# Word Censor Filter
#======================================================================#
if($action == "censor") {
stdhead("Censor");
adminnavmenu();
if($site_config["OLD_CENSOR"])
{
//Output
if ($_POST['submit'] == 'Add Censor'){

    DB::run("INSERT INTO censor (word, censor) VALUES (?,?)", [$_POST['word'], $_POST['censor']]);
}
if ($_POST['submit'] == 'Delete Censor'){
     
      DB::run("DELETE FROM censor WHERE word =? LIMIT 1", [$_POST['censor']]);
}

begin_frame(T_("WORD_CENSOR"));  
/*------------------
|HTML form for Word Censor
------------------*/
?>

<form method="post" action="/admincp?action=censor">  
<table width='100%' cellspacing='3' cellpadding='3' align='center'>
<tr>
<td bgcolor='#eeeeee'><font face="verdana" size="1">Word:  <input type="text" name="word" id="word" size="50" maxlength="255" value="" /></font></td></tr>
<tr><td bgcolor='#eeeeee'><font face="verdana" size="1">Censor With:  <input type="text" name="censor" id="censor" size="50" maxlength="255" value="" /></font></td></tr>
<tr><td bgcolor='#eeeeee' align='left'>
<font size="1" face="verdana"><input type="submit" name="submit" value="Add Censor" /></font></td>
</tr>
</table>
</form>

<form method="post" action="/admincp?action=censor">
<table>
<tr>
<td bgcolor='#eeeeee'><font face="verdana" size="1">Remove Censor For: <select name="censor">
<?php
/*-------------
|Get the words currently censored
-------------*/

$sres = DB::run("SELECT word FROM censor ORDER BY word");
while ($srow = $sres->fetch())
{
        echo "<option>" . $srow[0] . "</option>\n";
        }
echo'</select></font></td></tr><tr><td bgcolor="#eeeeee" align="left">
<font size="1" face="verdana"><input type="submit" name="submit" value="Delete Censor" /></font></td>
</tr></table></form>';
}
else
{
$to=isset($_GET["to"])?htmlentities($_GET["to"]):$to='';
switch ($to)
  {
    case 'write':
         begin_frame($LANG['ACP_CENSORED']);
         if (isset($_POST["badwords"]))
            {
            $f=fopen("censor.txt","w+");
            @fwrite($f,$_POST["badwords"]);
            fclose($f);
            }
			autolink("/admincp?action=censor", T_("SUCCESS"),"Censor Updated!");
         break;


    case '':
    case 'read':
    default:
      $f=@fopen("censor.txt","r");
      $badwords=@fread($f,filesize("censor.txt"));
      @fclose($f);
	  begin_frame($LANG['ACP_CENSORED']);
      echo'<form action="/admincp?action=censor&to=write" method="post" enctype="multipart/form-data">
  <table width="100%" align="center">
    <tr>
      <td align="center">'.$LANG['ACP_CENSORED_NOTE'].'</td>
    </tr>
    <tr>
      <td align="center"><textarea name="badwords" rows="20" cols="60">'.$badwords.'</textarea></td>
    </tr>
    <tr>
      <td align="center">
        <input type="submit" name="write" value="'.T_("CONFIRM").'" />&nbsp;&nbsp;
        <input type="submit" name="write" value="'.T_("CANCEL").'" />
      </td>
    </tr>
  </table>
</form><br />';
break;
}
}
end_frame();
stdfoot();
}
// End forum Censored Words