<?php
class Adminadvancedsearch extends Controller
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
    $do = $_GET['do']; // todo
    if ($do == "warndisable") {
        if (empty($_POST["warndisable"])) {
            show_error_msg(Lang::T("ERROR"), "You must select a user to edit.", 1);
        }
        if (!empty($_POST["warndisable"])) {
            $enable = $_POST["enable"];
            $disable = $_POST["disable"];
            $unwarn = $_POST["unwarn"];
            $warnlength = (int) $_POST["warnlength"];
            $warnpm = $_POST["warnpm"];
            $_POST['warndisable'] = array_map("intval", $_POST['warndisable']);
            $userid = implode(", ", $_POST['warndisable']);
            if ($disable != '') {
                DB::run("UPDATE users SET enabled='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
            }
            if ($enable != '') {
                DB::run("UPDATE users SET enabled='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
            }
            if ($unwarn != '') {
                $msg = "Your Warning Has Been Removed";
                foreach ($_POST["warndisable"] as $userid) {
                    $qry = DB::run("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES (?,?,?,?,?)", [0, 0, $userid, TimeDate::get_date_time(), $msg]);
                    if (!$qry) {
                        die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $qry->errorCode() . ") " . $qry->errorInfo());
                    }
                }
                $r = DB::run("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $r->errorCode() . ") " . $r->errorInfo());
                $user = $r->fetch(PDO::FETCH_LAZY);
                $exmodcomment = $user["modcomment"];
                $modcomment = gmdate("Y-m-d") . " - Warning Removed By " . $_SESSION['username'] . ".\n" . $modcomment . $exmodcomment;
                $query = "UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
                $q = DB::run($query);
                if (!$q) {
                    die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $q->errorCode() . ") " . $q->errorInfo());
                }

                DB::run("UPDATE users SET warned='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
            }
            if ($warn != '') {
                if (empty($_POST["warnpm"])) {
                    show_error_msg(Lang::T("ERROR"), "You must type a reason/mod comment.", 1);
                }

                $msg = "You have received a warning, Reason: $warnpm";
                $user = DB::run("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")->fetch();
                $exmodcomment = $user["modcomment"];
                $modcomment = gmdate("Y-m-d") . " - Warned by " . $_SESSION['username'] . ".\nReason: $warnpm\n" . $modcomment . $exmodcomment;
                $query = "UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
                $upd = DB::run($query);
                if (!$upd) {
                    die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $upd->errorCode() . ") " . $upd->errorInfo());
                }

                DB::run("UPDATE users SET warned='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
                foreach ($_POST["warndisable"] as $userid) {
                    $ins = DB::run("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', '" . $userid . "', '" . get_date_time() . "', " . sqlesc($msg) . ")");
                    if (!$ins) {
                        die("<b>A fatal MySQL error occured</b>.\n <br />\n" . Lang::T("ERROR") . ": (" . $ins->errorCode() . ") " . $ins->errorInfo());
                    }

                }
            }
        }
        Redirect::autolink("$_POST[referer]", "Redirecting back");
        die;
    }
    
    $title = Lang::T("ADVANCED_USER_SEARCH");
    require APPROOT . '/views/admin/header.php';
    Style::adminnavmenu();
    require APPROOT . '/views/admin/advancedsearch.php';
    require APPROOT . '/views/admin/footer.php';
}

}