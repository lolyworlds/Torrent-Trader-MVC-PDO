<?php
class Admintheme extends Controller
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
        $res = DB::run("SELECT * FROM stylesheets");
        $title = Lang::T("Theme");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin(Lang::T("THEME_MANAGEMENT"));
        echo "<center><a href='" . URLROOT . "/admintheme/add'>" . Lang::T("THEME_ADD") . "</a><!-- - <b>" . Lang::T("THEME_CLICK_A_THEME_TO_EDIT") . "</b>--></center>";
        echo "<center>" . Lang::T("THEME_CURRENT") . ":
        <form id='deltheme' method='post' action='" . URLROOT . "/admintheme/delete'>
        </center><table class='table table-striped table-bordered table-hover'>
        <thead>" .
        "<tr><th>ID</th><th>" . Lang::T("NAME") . "</th>
        <th>" . Lang::T("THEME_FOLDER_NAME") . "</th>
        <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
        </tr></thead<tbody>";
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            if (!is_dir("assets/themes/$row[uri]") && !is_dir(APPROOT."/views/inc/$row[uri]")) {
                $row['uri'] .= " <b>- " . Lang::T("THEME_DIR_DONT_EXIST") . "</b>";
            }
            echo "<tr>
            <td class='table_col1' align='center'>$row[id]</td>
            <td class='table_col2' align='center'>$row[name]</td>
            <td class='table_col1' align='center'>$row[uri]</td>
            <td class='table_col2' align='center'><input name='ids[]' type='checkbox' value='$row[id]' /></td>
            </tr>";
        }
        echo "</tbody></table><center>
        <input type='submit' value='" . Lang::T("SELECTED_DELETE") . "' /><center>
        </form><br><br>";
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }



    public function add()
    {
        if ($_POST['do'] == "add") {
            if ($_POST) {
                if (empty($_POST['name'])) {
                    $error .= Lang::T("THEME_NAME_WAS_EMPTY") . "<br />";
                }
                if (empty($_POST['uri'])) {
                    $error .= Lang::T("THEME_FOLDER_NAME_WAS_EMPTY");
                }
                if ($error) {
                    show_error_msg(Lang::T("ERROR"), Lang::T("THEME_NOT_ADDED_REASON") . " $error", 1);
                }
                if ($qry = DB::run("INSERT INTO stylesheets (name, uri) VALUES (?, ?)", [$_POST["name"], $_POST["uri"]])) {
                    show_error_msg(Lang::T("SUCCESS"), "Theme '" . htmlspecialchars($_POST["name"]) . "' added.", 0);
                } elseif ($qry->errorCode() == 1062) {
                    show_error_msg(Lang::T("FAILED"), Lang::T("THEME_ALREADY_EXISTS"), 0);
                } else {
                    show_error_msg(Lang::T("FAILED"), Lang::T("THEME_NOT_ADDED_DB_ERROR") . " " . $qry->errorInfo(), 0);
                }
            }
        }
            $title = Lang::T("Theme");
            require APPROOT . '/views/admin/header.php';
            Style::adminnavmenu();
            //$data = [];
            //$this->view('admin/sitelog', $data);
            require APPROOT . '/views/admin/styledo.php'; // todo
            require APPROOT . '/views/admin/footer.php';
        
    }   
    
    public function delete()
    {
            if (!@count($_POST["ids"])) {
                show_error_msg(Lang::T("ERROR"), Lang::T("NOTHING_SELECTED"), 1);
            }
            $ids = array_map("intval", $_POST["ids"]);
            $ids = implode(', ', $ids);
            DB::run("DELETE FROM `stylesheets` WHERE `id` IN ($ids)");
            DB::run("UPDATE `users` SET `stylesheet` = " . DEFAULTTHEME . " WHERE stylesheet NOT IN (SELECT id FROM stylesheets)");
            Redirect::autolink(URLROOT . "/admintheme", Lang::T("THEME_SUCCESS_THEME_DELETED"));
        
    }
    
}