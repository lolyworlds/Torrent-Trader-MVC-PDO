<?php
class Adminipban extends Controller
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
        $do = $_GET['do'];
        if ($do == "del") {
            if (!@count($_POST["delids"])) {
                show_error_msg(Lang::T("ERROR"), Lang::T("NONE_SELECTED"), 1);
            }
            $delids = array_map('intval', $_POST["delids"]);
            $delids = implode(', ', $delids);
            $res = DB::run("SELECT * FROM bans WHERE id IN ($delids)");
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                DB::run("DELETE FROM bans WHERE id=$row[id]");
                # Needs to be tested...
                if (Ip::is_ipv6($row["first"]) && Ip::is_ipv6($row["last"])) {
                    $first = Ip::long2ip6($row["first"]);
                    $last = Ip::long2ip6($row["last"]);
                } else {
                    $first = long2ip($row["first"]);
                    $last = long2ip($row["last"]);
                }
                Logs::write("IP Ban ($first - $last) was removed by $_SESSION[id] ($_SESSION[username])");
            }
            Session::flash('info', "Ban(s) deleted.", URLROOT."/adminipban");
        }

        if ($do == "add") {
            $first = trim($_POST["first"]);
            $last = trim($_POST["last"]);
            $comment = trim($_POST["comment"]);
            if ($first == "" || $last == "" || $comment == "") {
                Session::flash('info', Lang::T("MISSING_FORM_DATA") . ". Go back and try again",  URLROOT."/adminipban");
            }
            $comment = $comment;
            $added = Helper::get_date_time();
            $bins = DB::run("INSERT INTO bans (added, addedby, first, last, comment) VALUES(?,?,?,?,?)", [$added, $_SESSION['id'], $first, $last, $comment]);
            $err = $bins->errorCode();
            switch ($err) {
                case 1062:
                    Session::flash('info', "Duplicate ban.",  URLROOT."/adminipban");
                    break;
                case 0:
                    Session::flash('info', "Ban added.",  URLROOT."/adminipban");
                    break;
                default:
                    Session::flash('info', Lang::T("THEME_DATEBASE_ERROR") . " " . htmlspecialchars($bins->errorInfo()),  URLROOT."/adminipban");
            }
        }

        $title = Lang::T("BANNED_IPS");
        $count = get_row_count("bans");
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, "/adminipban?"); // 50 per page
        $res = DB::run("SELECT bans.*, users.username FROM bans LEFT JOIN users ON bans.addedby=users.id ORDER BY added $limit");

        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'pagertop' => $pagertop,
            'res' => $res,
        ];
        $this->view('admin/bannedip', $data);
        require APPROOT . '/views/admin/footer.php';
    }

}