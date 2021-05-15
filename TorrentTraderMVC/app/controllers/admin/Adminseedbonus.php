<?php
class Adminseedbonus extends Controller
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
        if ($_SESSION['class'] < 7) {
            show_error_msg("STOP", "Area reserved for people entitled! and you do not have permission !!!");
        }
    
        if ($_POST['do'] == "del") {
            if (!@count($_POST["ids"])) {
                show_error_msg("Error", "select nothing.", 1);
            }
    
            $ids = array_map("intval", $_POST["ids"]);
            $ids = implode(", ", $ids);
    
            DB::run("DELETE FROM `bonus` WHERE `id` IN ($ids)");
            Redirect::autolink(URLROOT."/adminseedbonus", "deleted entries");
        }
    
        $count = get_row_count("bonus");
    
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'adminseedbonus&amp;');
    
        $res = DB::run("SELECT id, title, cost, value, descr, type FROM `bonus` ORDER BY `type` $limit");
    
        $title = Lang::T("Seedbonus Manager");
        require APPROOT . '/views/admin/header.php';
        echo '<br>';
        Style::begin("Management of Seed Bonus");
        ?>
    
        <center>
        This page displays all available options trade which users can exchange for seedbonus <?php echo number_format($count); ?>
        </center>
        <center>
    <   a href="adminseedbonus/change&amp;id=null">Add</a> a new option?
        </center>
        <?php if ($count > 0): ?>
        <form id="seedbonus" method="post" action="<?php echo URLROOT; ?>/adminseedbonus">
        <input type="hidden" name="do" value="del" />
        <div class='table-responsive'> <table class='table table-striped'><thead><tr>
            <th>Title</th>
            <th>Description</th>
            <th>Points</th>
            <th>Value</th>
            <th>Type</th>
            <th>Edit</th>
            <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
        </tr></thead>
    
        <?php while ($row = $res->fetch(PDO::FETCH_OBJ)):
            //  while ($row = mysqli_fetch_object($res)):
            $row->value = ($row->type == "traffic") ? mksize($row->value) : (int) $row->value;
            ?>
            <tbody><tr>
            <td><?php echo htmlspecialchars($row->title); ?></td>
            <td><?php echo htmlspecialchars($row->descr); ?></td>
            <td><?php echo $row->cost; ?></td>
            <td><?php echo $row->value; ?></td>
            <td><?php echo $row->type; ?></td>
            <td><a href='<?php echo URLROOT ?>/adminseedbonus/change&amp;id=<?php echo $row->id; ?>'>Edit</a></td>
            <td><input type="checkbox" name="ids[]" value="<?php echo $row->id; ?>" /></td>
            </tr></tbody>
            <?php endwhile;?>
    
          </table>
       <ul>
            <li><input type="submit" value="Remove Selected" /></li>
            </ul>
    
        </form>
        </div>
        <?php
    endif;
    
        if ($count > 25) {
            echo $pagerbottom;
        }

        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function change()
    {
        $row = null;
        if ($this->valid->validId($_REQUEST['id'])) {
            $res = DB::run("SELECT id, title, cost, value, descr, type FROM `bonus` WHERE `id` = '$_REQUEST[id]'");
            $row = $res->fetch(PDO::FETCH_LAZY);
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['title']) or empty($_POST['descr']) or empty($_POST['type']) or !is_numeric($_POST['cost'])) {
                Redirect::autolink($_SERVER['HTTP_REFERER'], "missing information.");
            }
            $_POST["value"] = ($_POST["type"] == "traffic") ? strtobytes($_POST["value"]) : (int) $_POST["value"];
            $var = array_map('sqlesc', $_POST);
            extract($var);
            if ($row == null) {
                DB::run("INSERT INTO `bonus` (`title`, `descr`, `cost`, `value`, `type`) VALUES ($title, $descr, $cost, $value, $type)");
            } else {
                DB::run("UPDATE `bonus` SET `title` = $title, `descr` = $descr, `cost` = $cost, `value` = $value, `type` = $type WHERE `id` = $id");
            }
            Redirect::autolink(URLROOT . "/adminseedbonus", "Updating the bonus seed.");
        }

        $title = Lang::T("Seedbonus Manager");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin("Seedbonus Management");
        $data = [
            'row' => $row,
        ];
        $this->view('admin/seedbonuschange', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }
}