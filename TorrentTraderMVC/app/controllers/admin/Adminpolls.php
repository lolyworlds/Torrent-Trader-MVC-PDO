<?php
class Adminpolls extends Controller
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
        $query = DB::run("SELECT id,question,added FROM polls ORDER BY added DESC");
        $title = Lang::T("POLLS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin(Lang::T("POLLS_MANAGEMENT"));
        $data = [
            'query' => $query,
        ];
        $this->view('admin/pollsview', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    private function isadmin()
    {
        if (!$_SESSION['loggedin'] == true || $_SESSION["control_panel"] != "yes") {
            Session::flash('info', Lang::T("_ACCESS_DEN_"), URLROOT . "/home");
        }
    }

    public function pollsresults()
    {
        $this->isadmin();
        $poll = DB::run("SELECT * FROM pollanswers ORDER BY pollid DESC");
        $title = Lang::T("POLLS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin("Results");
        $data = [
            'poll' => $poll,
        ];
        $this->view('admin/pollsresults', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function pollsdelete()
    {
        $this->isadmin();
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            show_error_msg(Lang::T("ERROR"), sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID"), $newsid), 1);
        }
        DB::run("DELETE FROM polls WHERE id=?", [$id]);
        DB::run("DELETE FROM pollanswers WHERE  pollid=?", [$id]);
        Redirect::autolink(URLROOT . "/adminpolls", Lang::T("Poll and answers deleted"));
    }

    public function pollsadd() // todo edit bit works

    {
        $this->isadmin();
        $pollid = (int) $_GET["pollid"];
        $res = DB::run("SELECT * FROM polls WHERE id =?", [$pollid]);
        $title = Lang::T("POLLS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin("Polls");
         if ($_GET["subact"] == "edit") {
            $poll = $res->fetch(PDO::FETCH_LAZY);
        } ?>
<form method="post" action="<?php echo URLROOT; ?>/adminpolls/pollssave">
<table border="0" cellspacing="0" class="table_table" align="center">
<tr><td class="table_col1">Question <font class="error">*</font></td><td class="table_col2" align="left"><input name="question" size="60" maxlength="255" value="<?php echo $poll['question']; ?>" /></td></tr>
<tr><td class="table_col1">Option 1 <font class="error">*</font></td><td class="table_col2" align="left"><input name="option0" size="60" maxlength="40" value="<?php echo $poll['option0']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 2 <font class="error">*</font></td><td class="table_col2" align="left"><input name="option1" size="60" maxlength="40" value="<?php echo $poll['option1']; ?>" /><br /></td></tr>
 <tr><td class="table_col1">Option 3</td><td class="table_col2" align="left"><input name="option2" size="60" maxlength="40" value="<?php echo $poll['option2']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 4</td><td class="table_col2" align="left"><input name="option3" size="60" maxlength="40" value="<?php echo $poll['option3']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 5</td><td class="table_col2" align="left"><input name="option4" size="60" maxlength="40" value="<?php echo $poll['option4']; ?>" /><br /></td></tr>
 <tr><td class="table_col1">Option 6</td><td class="table_col2" align="left"><input name="option5" size="60" maxlength="40" value="<?php echo $poll['option5']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 7</td><td class="table_col2" align="left"><input name="option6" size="60" maxlength="40" value="<?php echo $poll['option6']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 8</td><td class="table_col2" align="left"><input name="option7" size="60" maxlength="40" value="<?php echo $poll['option7']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 9</td><td class="table_col2" align="left"><input name="option8" size="60" maxlength="40" value="<?php echo $poll['option8']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 10</td><td class="table_col2" align="left"><input name="option9" size="60" maxlength="40" value="<?php echo $poll['option9']; ?>" /><br /></td></tr>
 <tr><td class="table_col1">Option 11</td><td class="table_col2" align="left"><input name="option10" size="60" maxlength="40" value="<?php echo $poll['option10']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 12</td><td class="table_col2" align="left"><input name="option11" size="60" maxlength="40" value="<?php echo $poll['option11']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 13</td><td class="table_col2" align="left"><input name="option12" size="60" maxlength="40" value="<?php echo $poll['option12']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 14</td><td class="table_col2" align="left"><input name="option13" size="60" maxlength="40" value="<?php echo $poll['option13']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 15</td><td class="table_col2" align="left"><input name="option14" size="60" maxlength="40" value="<?php echo $poll['option14']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 16</td><td class="table_col2" align="left"><input name="option15" size="60" maxlength="40" value="<?php echo $poll['option15']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 17</td><td class="table_col2" align="left"><input name="option16" size="60" maxlength="40" value="<?php echo $poll['option16']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 18</td><td class="table_col2" align="left"><input name="option17" size="60" maxlength="40" value="<?php echo $poll['option17']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Option 19</td><td class="table_col2" align="left"><input name="option18" size="60" maxlength="40" value="<?php echo $poll['option18']; ?>" /><br /></td></tr>
<tr><td class="table_col1">Sort</td><td class="table_col2">
<input type="radio" name="sort" value="yes" <?php echo $poll["sort"] != "no" ? " checked='checked'" : "" ?> />Yes
<input type="radio" name="sort" value="no" <?php echo $poll["sort"] == "no" ? " checked='checked'" : "" ?> /> No
</td></tr>
<tr><td class="table_head" colspan="2" align="center"><input type="submit" value="<?php echo $pollid ? "Edit poll" : "Create poll"; ?>" /></td></tr>
</table>
<p><font class="error">*</font> required</p>
<input type="hidden" name="pollid" value="<?php echo $poll["id"] ?>" />
<input type="hidden" name="subact" value="<?php echo $pollid ? 'edit' : 'create' ?>" />
</form>
<?php
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function pollssave()
    {
        $this->isadmin();
        $subact = $_POST["subact"];
        $pollid = (int) $_POST["pollid"];

        $question = $_POST["question"];
        $option0 = $_POST["option0"];
        $option1 = $_POST["option1"];
        $option2 = $_POST["option2"];
        $option3 = $_POST["option3"];
        $option4 = $_POST["option4"];
        $option5 = $_POST["option5"];
        $option6 = $_POST["option6"];
        $option7 = $_POST["option7"];
        $option8 = $_POST["option8"];
        $option9 = $_POST["option9"];
        $option10 = $_POST["option10"];
        $sort = (int) $_POST["sort"];

        if (!$question || !$option0 || !$option1) {
            show_error_msg(Lang::T("ERROR"), Lang::T("MISSING_FORM_DATA") . "!", 1);
        }
        if ($subact == "edit") {
            if (!$this->valid->validId($pollid)) {
                show_error_msg(Lang::T("ERROR"), Lang::T("INVALID_ID"), 1);
            }
            DB::run("UPDATE polls SET " .
                "question = ?, " .
                "option0 = ?, " .
                "option1 = ?, " .
                "option2 = ?, " .
                "option3 = ?, " .
                "option4 = ?, " .
                "option5 = ?, " .
                "option6 = ?, " .
                "option7 = ?, " .
                "option8 = ?, " .
                "option9 = ?, " .
                "option10 =?, " .
                "sort =? " .
                "WHERE id = $pollid", [$question, $option0, $option1, $option2, $option3, $option4, $option5,
                    $option6, $option7, $option8, $option9, $option10, $sort]);
        } else {
            DB::run("INSERT INTO polls (added,question,option0,option1,option2,option3,option4,option5,
                option6,option7,option8,option9,sort)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", [TimeDate::get_date_time(), $question, $option0, $option1,
                $option2, $option3, $option4, $option5,
                $option6, $option7, $option8, $option9, $sort]);
        }
        Redirect::autolink(URLROOT . "/adminpolls", Lang::T("COMPLETE"));
    }
}