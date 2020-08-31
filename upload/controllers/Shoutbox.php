<?php
class Shoutbox extends Controller
{
    public function __construct()
    {
        // $this->shoutModel = $this->model('Shout');
    }

    public function index()
    {
        dbconn();
        global $config, $THEME;
        $id = $_GET['id'];

        // Get Page from url
        $history = isset($_GET['history']) ? $_GET['history'] : null;
        $delete = isset($_GET['delete']) ? $_GET['delete'] : null;
        $edit = isset($_GET['edit']) ? $_GET['edit'] : null;
        $staff = isset($_GET['staff']) ? $_GET['staff'] : null;
        $reply = isset($_GET['reply']) ? $_GET['reply'] : null;
        $quickedit = isset($_GET['quickedit']) ? $_GET['quickedit'] : null;

        // Get theme & language
        if ($_SESSION['loggedin'] == true) {
            $ss_a = DB::run("SELECT uri FROM stylesheets WHERE id=?", [$_SESSION["stylesheet"]])->fetch();
            if ($ss_a) {
                $THEME = $ss_a["uri"];
            }

        } else { // Not logged in so get default theme/language
            $ss_a = DB::run("SELECT uri FROM stylesheets WHERE id=?", [$config["default_theme"]])->fetch();
            if ($ss_a) {
                $THEME = $ss_a["uri"];
            }

        }

        // DELETE
        if (isset($delete)) {
            if (is_numeric($delete)) {
                $result = DB::run("SELECT * FROM shoutbox WHERE msgid=?", [$delete]);
            } else {
                echo "Failed to delete, invalid msg id";
                exit;
            }
            $row = $result->fetch(PDO::FETCH_LAZY);
            if ($row && ($_SESSION["edit_users"] == "yes" || $_SESSION['username'] == $row[1])) {
                write_log("<b><font color='orange'>Shout Deleted:</font> Deleted by   " . $_SESSION['username'] . "</b>");
                DB::run("DELETE FROM shoutbox WHERE msgid=?", [$delete]);
            }
        }
        // Edit
        if (!empty($_POST['update']) && $_SESSION['loggedin'] == true) {
            $update = $_POST['update'];
            DB::run("UPDATE shoutbox SET message=? WHERE msgid=?", [$update, $id]);
        }
        // Staff
        if (!empty($_POST['staffmsg']) && $_SESSION['loggedin'] == true) {
            $update = $_POST['staffmsg'];
            $qry = DB::run("INSERT INTO shoutbox (msgid, user, message, date, userid, staff) VALUES (?, ?, ?, ?, ?, ?)", [null, $_SESSION['username'], $update, get_date_time(), $_SESSION['id'], 1]);
        }

        // Set some conditions
        if (isset($history)) {
            stdhead();
            begin_frame(T_("SHOUTBOX_HISTORY"));
            require 'views/shoutbox/shoutboxhistory.php';
            end_frame();
            stdfoot();

        } elseif (isset($edit)) {
            if ($_SESSION['class'] < $config['Uploader']) {
                autolink(TTURL . "/index", T_("You dont have permission"));
            }
            require 'views/shoutbox/shoutboxheader.php';
            require 'views/shoutbox/shoutboxmessage.php';
            require 'views/shoutbox/shoutboxedit.php';
            require 'views/shoutbox/shoutboxfooter.php';

        } elseif (isset($quickedit)) {
            $stmt = DB::run("SELECT user, date FROM shoutbox WHERE msgid=?", [$quickedit]);
            while ($row = $stmt->fetch(PDO::FETCH_LAZY));
            if ($_SESSION['username'] == $stmt->user) {
                autolink(TTURL . "/index", T_("You dont have permission"));
            }
            require 'views/shoutbox/shoutboxheader.php';
            require 'views/shoutbox/shoutboxmessage.php';
            require 'views/shoutbox/shoutboxquickedit.php';
            require 'views/shoutbox/shoutboxfooter.php';

        } elseif (isset($reply)) {
            if (!$_SESSION['loggedin'] == true) {
                autolink(TTURL . "/index", T_("You dont have permission"));
            }
            require 'views/shoutbox/shoutboxheader.php';
            require 'views/shoutbox/shoutboxmessage.php';
            require 'views/shoutbox/shoutboxreply.php';
            require 'views/shoutbox/shoutboxfooter.php';

        } elseif (isset($staff)) {
            if ($_SESSION['class'] < $config['Uploader']) {
                autolink(TTURL . "/index", T_("You dont have permission"));
            }
            require 'views/shoutbox/shoutboxheader.php';
            require 'views/shoutbox/shoutboxstaffmessage.php';
            require 'views/shoutbox/shoutboxstaffmain.php';
            require 'views/shoutbox/shoutboxfooter.php';

        } else {
            if ($_SESSION["shoutboxpos"] == 'no') {
                //INSERT MESSAGE
                if (!empty($_POST['message']) && $_SESSION['loggedin'] == true) {
                    $_POST['message'] = $_POST['message'];
                    $result = DB::run("SELECT COUNT(*) FROM shoutbox WHERE message=? AND user=? AND UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) < ?", [$_POST['message'], $_SESSION['username'], get_date_time(), 30]);
                    $row = $result->fetch(PDO::FETCH_LAZY);
                    if ($row[0] == '0') {
                        $qry = DB::run("INSERT INTO shoutbox (msgid, user, message, date, userid) VALUES (?, ?, ?, ?, ?)", [null, $_SESSION['username'], $_POST['message'], get_date_time(), $_SESSION['id']]);
                    }
                }
                require 'views/shoutbox/shoutboxheader.php';
                if ($_SESSION['class'] > $config['Uploader']) {
                    echo "<center><a href='" . $config['SITEURL'] . "/shoutbox?staff'>View Staff Chat</a><center>";
                }
                require 'views/shoutbox/shoutboxmessage.php';
                require 'views/shoutbox/shoutboxmain.php';
                require 'views/shoutbox/shoutboxfooter.php';
            } elseif ($_SESSION["loggedin"] === true || !$_SESSION["loggedin"] === false) {
                require 'views/shoutbox/shoutboxheader.php';
                print("<br><br><center><font color=red><b>You dont have permissions to use the Shoutbox! Contact the staff!</b><br><b>Or You are not logged in</b></font></center>");
                require 'views/shoutbox/shoutboxfooter.php';
            }
        }

    }
}