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
        global $site_config, $THEME, $CURUSER;
        $id = $_GET['id'];

        // Get Page from url
        $history = isset($_GET['history']) ? $_GET['history'] : null;
        $delete = isset($_GET['delete']) ? $_GET['delete'] : null;
        $edit = isset($_GET['edit']) ? $_GET['edit'] : null;
        $staff = isset($_GET['staff']) ? $_GET['staff'] : null;

        // Get theme & language
        if ($CURUSER) {
            $ss_a = DB::run("SELECT uri FROM stylesheets WHERE id=?", [$CURUSER["stylesheet"]])->fetch();
            if ($ss_a) {
                $THEME = $ss_a["uri"];
            }

        } else { // Not logged in so get default theme/language
            $ss_a = DB::run("SELECT uri FROM stylesheets WHERE id=?", [$site_config["default_theme"]])->fetch();
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
            if ($row && ($CURUSER["edit_users"] == "yes" || $CURUSER['username'] == $row[1])) {
                write_log("<b><font color='orange'>Shout Deleted:</font> Deleted by   " . $CURUSER['username'] . "</b>");
                DB::run("DELETE FROM shoutbox WHERE msgid=?", [$delete]);
            }
        }
        // Edit
        if (!empty($_POST['update']) && $CURUSER) {
            $update = $_POST['update'];
            DB::run("UPDATE shoutbox SET message=? WHERE msgid=?", [$update, $id]);
        }
        // Staff
        if (!empty($_POST['staffmsg']) && $CURUSER) {
            $update = $_POST['staffmsg'];
            $qry = DB::run("INSERT INTO shoutbox (msgid, user, message, date, userid, staff) VALUES (?, ?, ?, ?, ?, ?)", [null, $CURUSER['username'], $update, get_date_time(), $CURUSER['id'], 1]);
        }

        // Set some conditions
        if (isset($history)) {
            stdhead();
            begin_frame(T_("SHOUTBOX_HISTORY"));
            require 'views/shoutbox/shoutboxhistory.php';
            end_frame();
            stdfoot();

        } elseif (isset($edit)) {
            if ($CURUSER['class'] > $site_config['Uploader']) {
                autolink(TTURL."/index", T_("You dont have permission"));
            }
            require 'views/shoutbox/shoutboxheader.php';
            require 'views/shoutbox/shoutboxedit.php';
            require 'views/shoutbox/shoutboxfooter.php';

        } elseif (isset($staff)) {
            if ($CURUSER['class'] < $site_config['Uploader']) {
                autolink(TTURL."/index", T_("You dont have permission"));
            }
            require 'views/shoutbox/shoutboxheader.php';
            require 'views/shoutbox/shoutboxstaffmessage.php';
            require 'views/shoutbox/shoutboxstaffmain.php';
            require 'views/shoutbox/shoutboxfooter.php';

        } else {
            //INSERT MESSAGE
            if (!empty($_POST['message']) && $CURUSER) {
                $_POST['message'] = $_POST['message'];
                $result = DB::run("SELECT COUNT(*) FROM shoutbox WHERE message=? AND user=? AND UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) < ?", [$_POST['message'], $CURUSER['username'], get_date_time(), 30]);
                $row = $result->fetch(PDO::FETCH_LAZY);
                if ($row[0] == '0') {
                    $qry = DB::run("INSERT INTO shoutbox (msgid, user, message, date, userid) VALUES (?, ?, ?, ?, ?)", [null, $CURUSER['username'], $_POST['message'], get_date_time(), $CURUSER['id']]);
                }
            }
            require 'views/shoutbox/shoutboxheader.php';
            if ($CURUSER['class'] > $site_config['Uploader']) {
                echo "<center><a href='" . $site_config['SITEURL'] . "/shoutbox?staff'>View Staff Chat</a><center>";
            }
            require 'views/shoutbox/shoutboxmessage.php';
            require 'views/shoutbox/shoutboxmain.php';
            require 'views/shoutbox/shoutboxfooter.php';
        }

    }
}
