<?php
class Admincategories extends Controller
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
        Redirect::to(URLROOT.'/admincategories/categoriesview');
    }

    public function categoriesview()
    {
        $title = Lang::T("Categories Management");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();

        Style::begin(Lang::T("TORRENT_CATEGORIES"));
        echo "<center><a href='".URLROOT."/admincategories/categoriesadd'><b>Add New Category</b></a></center><br />";
        print("<i>Please note that if no image is specified, the category name will be displayed</i><br />");
        echo ("<table class='table table-striped table-bordered table-hover'><thead>");
        echo ("<tr><th width='10' class='table_head'>Sort</th><th class='table_head'>Parent Cat</th><th class='table_head'>Sub Cat</th><th class='table_head'>Image</th><th width='30' class='table_head'></th></tr></thead></tbody>");
        $query = "SELECT * FROM categories ORDER BY parent_cat ASC, sort_index ASC";
        $sql = DB::run($query);
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $name = $row['name'];
            $priority = $row['sort_index'];
            $parent = $row['parent_cat'];
            print("<tr><td class='table_col1'>$priority</td><td class='table_col2'>$parent</td><td class='table_col1'>$name</td><td class='table_col2' align='center'>");
            if (isset($row["image"]) && $row["image"] != "") {
                print("<img border=\"0\" src=\"" . URLROOT . "/assets/images/categories/" . $row["image"] . "\" alt=\"" . $row["name"] . "\" />");
            } else {
                print("-");
            }
            print("</td><td class='table_col1'><a href=" . URLROOT . "/admincategories/categoriesedit?id=$id'>[EDIT]</a> <a href='" . URLROOT . "/admincategories/categoriesdelete?id=$id'>[DELETE]</a></td></tr>");
        }
        echo ("</tbody></table></center>");
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function categoriesedit()
    {
        $title = Lang::T("Categories Management");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();

        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            show_error_msg(Lang::T("ERROR"), Lang::T("INVALID_ID"), 1);
        }
        $res = DB::run("SELECT * FROM categories WHERE id=?", [$id]);
        if ($res->rowCount() != 1) {
            show_error_msg(Lang::T("ERROR"), "No category with ID $id.", 1);
        }
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if ($_GET["save"] == '1') {
            $parent_cat = $_POST['parent_cat'];
            if ($parent_cat == "") {
                show_error_msg(Lang::T("ERROR"), "Parent Cat cannot be empty!", 1);
            }
            $name = $_POST['name'];
            if ($name == "") {
                show_error_msg(Lang::T("ERROR"), "Sub cat cannot be empty!", 1);
            }
            $sort_index = $_POST['sort_index'];
            $image = $_POST['image'];
            $parent_cat = $parent_cat;
            $name = $name;
            $sort_index = $sort_index;
            $image = $image;
            DB::run("UPDATE categories SET parent_cat=?, name=?, sort_index=?, image=? WHERE id=?", [$parent_cat, $name, $sort_index, $image, $id]);
            Redirect::autolink(URLROOT . "/admincategories/categoriesview", Lang::T("SUCCESS"), "category was edited successfully!");

        } else {
            Style::begin(Lang::T("CP_CATEGORY_EDIT"));
            print("<form method='post' action='" . URLROOT . "/admincategories/categoriesedit?id=$id&amp;save=1'>\n");
            print("<center><table border='0' cellspacing='0' cellpadding='5'>\n");
            print("<tr><td align='left'><b>Parent Category: </b><input type='text' name='parent_cat' value=\"" . $arr['parent_cat'] . "\" /> All Subcats with EXACTLY the same parent cat are grouped</td></tr>\n");
            print("<tr><td align='left'><b>Sub Category: </b><input type='text' name='name' value=\"" . $arr['name'] . "\" /></td></tr>\n");
            print("<tr><td align='left'><b>Sort: </b><input type='text' name='sort_index' value=\"" . $arr['sort_index'] . "\" /></td></tr>\n");
            print("<tr><td align='left'><b>Image: </b><input type='text' name='image' value=\"" . $arr['image'] . "\" /> single filename</td></tr>\n");
            print("<tr><td align='center'><input type='submit' value='" . Lang::T("SUBMIT") . "' /></td></tr>\n");
            print("</table></center>\n");
            print("</form>\n");
        }
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function categoriesdelete()
    {
        $title = Lang::T("Categories Management");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();

        $id = (int) $_GET["id"];
        if ($_GET["sure"] == '1') {
            if (!$this->valid->validId($id)) {
                Redirect::autolink(URLROOT . "/admincategories/categoriesview", Lang::T("CP_NEWS_INVAILD_ITEM_ID = $newsid"));
            }
            $newcatid = (int) $_POST["newcat"];
            DB::run("UPDATE torrents SET category=$newcatid WHERE category=$id"); //move torrents to a new cat
            DB::run("DELETE FROM categories WHERE id=?", [$id]); //delete old cat
            Redirect::autolink(URLROOT . "/admincategories/categoriesview", Lang::T("Category Deleted OK."));
        } else {
            Style::begin(Lang::T("CATEGORY_DEL"));
            print("<form method='post' action='" . URLROOT . "/admincategories/categoriesdelete?id=$id&amp;sure=1'>\n");
            print("<center><table border='0' cellspacing='0' cellpadding='5'>\n");
            print("<tr><td align='left'><b>Category ID to move all Torrents To: </b><input type='text' name='newcat' /> (Cat ID)</td></tr>\n");
            print("<tr><td align='center'><input type='submit' value='" . Lang::T("SUBMIT") . "' /></td></tr>\n");
            print("</table></center>\n");
            print("</form>\n");
        }
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function categoriestakeadd()
    {
        $name = $_POST['name'];
        if ($name == "") {
            show_error_msg(Lang::T("ERROR"), "Sub Cat cannot be empty!", 1);
        }
        $parent_cat = $_POST['parent_cat'];
        if ($parent_cat == "") {
            show_error_msg(Lang::T("ERROR"), "Parent Cat cannot be empty!", 1);
        }
        $sort_index = $_POST['sort_index'];
        $image = $_POST['image'];
        $parent_cat = $parent_cat;
        $name = $name;
        $sort_index = $sort_index;
        $image = $image;
        $ins = DB::run("INSERT INTO categories (name, parent_cat, sort_index, image) VALUES (?,?,?,?)", [$name, $parent_cat, $sort_index, $image]);
        if ($ins) {
            Redirect::autolink(URLROOT . "/admincategories/categoriesview", Lang::T("Category was added successfully."));
        } else {
            show_error_msg(Lang::T("ERROR"), "Unable to add category", 1);
        }

    }

    public function categoriesadd()
    {
        $title = Lang::T("Categories Management");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();

        Style::begin(Lang::T("CATEGORY_ADD"));
        print("<center><form method='post' action='" . URLROOT . "/admincategories/categoriestakeadd'>\n");
        print("<input type='hidden' name='action' value='categories' />\n");
        print("<input type='hidden' name='do' value='takeadd' />\n");
        print("<table border='0' cellspacing='0' cellpadding='5'>\n");
        print("<tr><td align='left'><b>Parent Category:</b> <input type='text' name='parent_cat' /></td></tr>\n");
        print("<tr><td align='left'><b>Sub Category:</b> <input type='text' name='name' /></td></tr>\n");
        print("<tr><td align='left'><b>Sort:</b> <input type='text' name='sort_index' /></td></tr>\n");
        print("<tr><td align='left'><b>Image:</b> <input type='text' name='image' /></td></tr>\n");
        print("<tr><td colspan='2'><input type='submit' value='" . Lang::T("SUBMIT") . "' /></td></tr>\n");
        print("</table></form></center>\n");
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

}