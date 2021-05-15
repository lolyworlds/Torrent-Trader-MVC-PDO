<?php
class Admincensor extends Controller
{

    public function __construct()
    {
        Auth::user(); // should check admin here
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        $title = Lang::T("Censor");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        if (OLD_CENSOR) {
//Output
            if ($_POST['submit'] == 'Add Censor') {
                DB::run("INSERT INTO censor (word, censor) VALUES (?,?)", [$_POST['word'], $_POST['censor']]);
            }
            if ($_POST['submit'] == 'Delete Censor') {
                DB::run("DELETE FROM censor WHERE word =? LIMIT 1", [$_POST['censor']]);
            }

            Style::begin(Lang::T("WORD_CENSOR"));
            ?>

<form method="post" action="<?php echo URLROOT; ?>/admincensor">
<table width='100%' cellspacing='3' cellpadding='3' align='center'>
<tr>
<td bgcolor='#eeeeee'><font face="verdana" size="1">Word:  <input type="text" name="word" id="word" size="50" maxlength="255" value="" /></font></td></tr>
<tr><td bgcolor='#eeeeee'><font face="verdana" size="1">Censor With:  <input type="text" name="censor" id="censor" size="50" maxlength="255" value="" /></font></td></tr>
<tr><td bgcolor='#eeeeee' align='left'>
<font size="1" face="verdana"><input type="submit" name="submit" value="Add Censor" /></font></td>
</tr>
</table>
</form>

<form method="post" action="<?php echo URLROOT; ?>/admincensor">
<table>
<tr>
<td bgcolor='#eeeeee'><font face="verdana" size="1">Remove Censor For: <select name="censor">
<?php
/*-------------
            |Get the words currently censored
            -------------*/

            $sres = DB::run("SELECT word FROM censor ORDER BY word");
            while ($srow = $sres->fetch()) {
                echo "<option>" . $srow[0] . "</option>\n";
            }
            echo '</select></font></td></tr><tr><td bgcolor="#eeeeee" align="left">
<font size="1" face="verdana"><input type="submit" name="submit" value="Delete Censor" /></font></td>
</tr></table></form>';
        } else {
            $to = isset($_GET["to"]) ? htmlentities($_GET["to"]) : $to = '';
            switch ($to) {
                case 'write':
                    Style::begin($LANG['ACP_CENSORED']);
                    if (isset($_POST["badwords"])) {
                        $f = fopen(LOGGER."/censor.txt", "w+");
                        @fwrite($f, $_POST["badwords"]);
                        fclose($f);
                    }
                    Redirect::autolink(URLROOT . "/admincensor", Lang::T("SUCCESS"), "Censor Updated!");
                    break;

                case '':
                case 'read':
                default:
                    $f = @fopen(LOGGER."/censor.txt", "r");
                    $badwords = @fread($f, filesize(LOGGER."/censor.txt"));
                    @fclose($f);
                    Style::begin($LANG['ACP_CENSORED']);
                    echo '<form action="admincensor&to=write" method="post" enctype="multipart/form-data">
  <table width="100%" align="center">
    <tr>
      <td align="center">' . $LANG['ACP_CENSORED_NOTE'] . '</td>
    </tr>
    <tr>
      <td align="center"><textarea name="badwords" rows="20" cols="60">' . $badwords . '</textarea></td>
    </tr>
    <tr>
      <td align="center">
        <input type="submit" name="write" value="' . Lang::T("CONFIRM") . '" />&nbsp;&nbsp;
        <input type="submit" name="write" value="' . Lang::T("CANCEL") . '" />
      </td>
    </tr>
  </table>
</form><br />';
                    break;
            }
        }
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }
}