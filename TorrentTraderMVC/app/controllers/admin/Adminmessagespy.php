<?php
class Adminmessagespy extends Controller
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
        $row = DB::run("SELECT COUNT(*) FROM messages WHERE location in ('in', 'both')")->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/adminmessagespy?;");

        $res = DB::run("SELECT * FROM messages WHERE location in ('in', 'both') ORDER BY id DESC $limit");

        $title = Lang::T("Message Spy");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();

        Style::begin("Message Spy");
        echo $pagertop;
        print("<form id='messagespy' method='post' action='adminmessagespy/delete'><table class='table table-striped table-bordered table-hover'><thead>\n");
        print("<tr><th class='table_head' align='left'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th><th class='table_head' align='left'>Sender</th><th class='table_head' align='left'>Receiver</th><th class='table_head' align='left'>Text</th><th class='table_head' align='left'>Date</th></tr></thead><tbody>\n");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            $res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr["receiver"]]);
            if ($arr2 = $res2->fetch()) {
                $receiver = "<a href='" . URLROOT . "/users/profile?id=" . $arr["receiver"] . "'><b>" . Users::coloredname($arr2["username"]) . "</b></a>";
            } else {
                $receiver = "<i>Deleted</i>";
            }
            $arr3 = DB::run("SELECT username FROM users WHERE id=?", [$arr["sender"]])->fetch();
            $sender = "<a href='" . URLROOT . "/users/profile?id=" . $arr["sender"] . "'><b>" . Users::coloredname($arr3["username"]) . "</b></a>";
            if ($arr["sender"] == 0) {
                $sender = "<font class='error'><b>System</b></font>";
            }
            $msg = format_comment($arr["msg"]);
            $added = TimeDate::utc_to_tz($arr["added"]);
            print("<tr><td class='table_col2'><input type='checkbox' name='del[]' value='$arr[id]' /></td><td align='left' class='table_col1'>$sender</td><td align='left' class='table_col2'>$receiver</td><td align='left' class='table_col1'>$msg</td><td align='left' class='table_col2'>$added</td></tr>");
        }
        print("</tbody></table><br />");
        echo "<center><input type='submit' value='Delete Checked' /> <input type='submit' value='Delete All' name='delall' /></center></form>";
        print($pagerbottom);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function delete()
    {
        if ($_POST["delall"]) {
            DB::run("DELETE FROM `messages`");
        } else {
            if (!@count($_POST["del"])) {
                show_error_msg(Lang::T("ERROR"), Lang::T("NOTHING_SELECTED"), 1);
            }
            $ids = array_map("intval", $_POST["del"]);
            $ids = implode(", ", $ids);
            DB::run("DELETE FROM `messages` WHERE `id` IN ($ids)");
        }
        Session::flash('info', Lang::T("CP_DELETED_ENTRIES"), URLROOT . "/adminmessagespy");
    }
}