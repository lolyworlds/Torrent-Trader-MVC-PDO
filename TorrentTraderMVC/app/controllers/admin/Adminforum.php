<?php
class Adminforum extends Controller
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

    $error_ac == "";
    if ($_POST["do"] == "add_this_forum") {
        $new_forum_name = $_POST["new_forum_name"];
        $new_desc = $_POST["new_desc"];
        $new_forum_sort = (int) $_POST["new_forum_sort"];
        $new_forum_cat = (int) $_POST["new_forum_cat"];
        $minclassread = (int) $_POST["minclassread"];
        $minclasswrite = (int) $_POST["minclasswrite"];
        $guest_read = $_POST["guest_read"];
        if ($new_forum_name == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_NAME_WAS_EMPTY") . "</li>\n";
        }
        if ($new_desc == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_DESC_WAS_EMPTY") . "</li>\n";
        }
        if ($new_forum_sort == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_SORT_ORDER_WAS_EMPTY") . "</li>\n";
        }
        if ($new_forum_cat == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_CATAGORY_WAS_EMPTY") . "</li>\n";
        }
        if ($error_ac == "") {
            $res = DB::run("INSERT INTO forum_forums (`name`, `description`, `sort`, `category`, `minclassread`, `minclasswrite`, `guest_read`) VALUES (?,?,?,?,?,?,?)", [$new_forum_name, $new_desc, $new_forum_sort, $new_forum_cat, $minclassread, $minclasswrite, $guest_read]);
            if ($res) {
                Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_FORUM_NEW_ADDED_TO_DB"));
            } else {
                echo "<h4>" . Lang::T("CP_COULD_NOT_SAVE_TO_DB") . "</h4>";
            }
        } else {
            Redirect::autolink(URLROOT . "/adminforum", $error_ac);
        }
    }

    if ($_POST["do"] == "add_this_forumcat") {
        $new_forumcat_name = $_POST["new_forumcat_name"];
        $new_forumcat_sort = $_POST["new_forumcat_sort"];
        if ($new_forumcat_name == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_CAT_NAME_WAS_EMPTY") . "</li>\n";
        }
        if ($new_forumcat_sort == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_CAT_SORT_WAS_EMPTY") . "</li>\n";
        }
        if ($error_ac == "") {
            $res = DB::run("INSERT INTO forumcats (`name`, `sort`) VALUES (?,?)", [$new_forumcat_name, intval($new_forumcat_sort)]);
            if ($res) {
                Redirect::autolink(URLROOT . "/adminforum", "Thank you, new forum cat added to db ...");
            } else {
                echo "<h4>" . Lang::T("CP_COULD_NOT_SAVE_TO_DB") . "</h4>";
            }
        } else {
            Redirect::autolink(URLROOT . "/adminforum", $error_ac);
        }
    }

    if ($_POST["do"] == "save_edit") {
        $id = (int) $_POST["id"];
        $changed_sort = (int) $_POST["changed_sort"];
        $changed_forum = $_POST["changed_forum"];
        $changed_forum_desc = $_POST["changed_forum_desc"];
        $changed_forum_cat = (int) $_POST["changed_forum_cat"];
        $minclasswrite = (int) $_POST["minclasswrite"];
        $minclassread = (int) $_POST["minclassread"];
        $guest_read = $_POST["guest_read"];
        DB::run("UPDATE forum_forums SET sort =?, name =?, description =?, category =?, minclassread=?, minclasswrite=?, guest_read=? WHERE id=?", [$changed_sort, $changed_forum, $changed_forum_desc, $changed_forum_cat, $minclassread, $minclasswrite, $guest_read, $id]);
        Redirect::autolink(URLROOT . "/adminforum", "<center><b>" . Lang::T("CP_UPDATE_COMPLETED") . "</b></center>");
    }

    if ($_POST["do"] == "save_editcat") {
        $id = (int) $_POST["id"];
        $changed_sortcat = (int) $_POST["changed_sortcat"];
        DB::run("UPDATE forumcats SET sort = '$changed_sortcat', name = " . sqlesc($_POST["changed_forumcat"]) . " WHERE id='$id'");
        Redirect::autolink(URLROOT . "/adminforum", "<center><b>" . Lang::T("CP_UPDATE_COMPLETED") . "</b></center>");
    }

    if ($_POST["do"] == "delete_forum" && is_valid_id($_POST["id"])) {
        DB::run("DELETE FROM forum_forums WHERE id = $_POST[id]");
        DB::run("DELETE FROM forum_topics WHERE forumid = $_POST[id]");
        DB::run("DELETE FROM forum_posts WHERE topicid = $_POST[id]");
        DB::run("DELETE FROM forum_readposts WHERE topicid = $_POST[id]");
        Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_FORUM_DELETED"));
    }

    if ($_POST["do"] == "delete_forumcat" && is_valid_id($_POST["id"])) {
        DB::run("DELETE FROM forumcats WHERE id = $_POST[id]");
        $res = DB::run("SELECT id FROM forum_forums WHERE category = $_POST[id]");
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $res2 = DB::run("SELECT id FROM forum_topics WHERE forumid = $row[id]");
            while ($arr = $res2->fetch(PDO::FETCH_ASSOC)) {
                DB::run("DELETE FROM forum_posts WHERE topicid = $arr[id]");
                DB::run("DELETE FROM forum_readposts WHERE topicid = $arr[id]");
            }
            DB::run("DELETE FROM forum_topics WHERE forumid = $row[id]");
            DB::run("DELETE FROM forum_forums WHERE id = $row[id]");
        }
        Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_FORUM_CAT_DELETED"));
    }

    $title = Lang::T("FORUM_MANAGEMENT");
    require APPROOT . '/views/admin/header.php';
    $groupsres = DB::run("SELECT group_id, level FROM groups ORDER BY group_id ASC");
    while ($groupsrow = $groupsres->fetch()) {
        $groups[$groupsrow[0]] = $groupsrow[1];
    }

    if ($_GET["do"] == "edit_forum") {
        $id = (int) $_GET["id"];
        $q = DB::run("SELECT * FROM forum_forums WHERE id = '$id'");
        $r = $q->fetch();
        if (!$r) {
            Redirect::autolink(URLROOT . "/adminforum", Lang::T("FORUM_INVALID"));
        }
        require APPROOT . '/views/admin/forum/editforum.php';
        require APPROOT . '/views/admin/footer.php';
    }

    if ($_GET["do"] == "del_forum") {
        $id = (int) $_GET["id"];
        $v = DB::run("SELECT * FROM forum_forums WHERE id = '$id'")->fetch();
        if (!$v) {
            Redirect::autolink(URLROOT . "/adminforum", Lang::T("FORUM_INVALID"));
        }
        Style::begin(Lang::T("CONFIRM"));
        ?>
<form class='a-form' action="<?php echo URLROOT; ?>/adminforum" method="post">
<input type="hidden" name="do" value="delete_forum" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<?php echo Lang::T("CP_FORUM_REALY_DEL"); ?> <?php echo "<b>$v[name] with ID$v[id] ???</b>"; ?> <?php echo Lang::T("CP_FORUM_THIS_WILL_REALLY_DEL"); ?>.
<input type="submit" name="delcat" class="button" value="Delete" />
</form>
<?php
Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    if ($_GET["do"] == "del_forumcat") {
        $id = (int) $_GET["id"];
        $t = DB::run("SELECT * FROM forumcats WHERE id = '$id'");
        $v = $t->fetch();
        if (!$v) {
            Redirect::autolink(URLROOT . "/adminforum", Lang::T("FORUM_INVALID_CAT"));
        }
        Style::begin(Lang::T("CONFIRM"));
        ?>
<form class='a-form' action="<?php echo URLROOT; ?>/adminforum" method="post">
<input type="hidden" name="do" value="delete_forumcat" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
  <?php echo Lang::T("CP_FORUM_REALY_DEL_CAT"); ?><?php echo "<b>$v[name] with ID$v[id] ???</b>"; ?> <?php echo Lang::T("CP_FORUM_THIS_WILL_REALLY_DEL"); ?>.
  <input type="submit" name="delcat" class="button" value="Delete" />
  </form>
   <?php
Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    if ($_GET["do"] == "edit_forumcat") {
        $id = (int) $_GET["id"];
        $r = DB::run("SELECT * FROM forumcats WHERE id = '$id'")->fetch();
        if (!$r) {
            Redirect::autolink(URLROOT . "/adminforum", Lang::T("FORUM_INVALID_CAT"));
        }
        require APPROOT . '/views/admin/forum/editcat.php';
        require APPROOT . '/views/admin/footer.php';
    }

    if (!$do) {
        Style::adminnavmenu();
        require APPROOT . '/views/admin/forum/index.php';
        require APPROOT . '/views/admin/footer.php';
    } // End New Forum

} // End Forum management

}