<?php
class Admincheats extends Controller
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
        $title = "Possible Cheater Detection";
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();

        $megabts = (int) $_POST['megabts'];
        $daysago = (int) $_POST['daysago'];
        if ($daysago && $megabts) {
            $timeago = 84600 * $daysago; //last 7 days
            $bytesover = 1048576 * $megabts; //over 500MB Upped
            $result = DB::run("select * FROM users WHERE UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(added) < '$timeago' AND status='confirmed' AND uploaded > '$bytesover' ORDER BY uploaded DESC ");
            $num = $result->rowCount(); // how many uploaders
            Style::begin("Possible Cheater Detection");
            echo "<p>" . $num . " Users with found over last " . $daysago . " days with more than " . $megabts . " MB (" . $bytesover . ") Bytes Uploaded.</p>";
            $zerofix = $num - 1; // remove one row because mysql starts at zero
            if ($num > 0) {
                echo "<table align='center' class='table_table'>";
                echo "<tr>";
                echo "<th class='table_head'>No.</th>";
                echo "<th class='table_head'>" . Lang::T("USERNAME") . "</th>";
                echo "<th class='table_head'>" . Lang::T("UPLOADED") . "</th>";
                echo "<th class='table_head'>" . Lang::T("DOWNLOADED") . "</th>";
                echo "<th class='table_head'>" . Lang::T("RATIO") . "</th>";
                echo "<th class='table_head'>" . Lang::T("TORRENTS_POSTED") . "</th>";
                echo "<th class='table_head'>AVG Daily Upload</th>";
                echo "<th class='table_head'>" . Lang::T("ACCOUNT_SEND_MSG") . "</th>";
                echo "<th class='table_head'>Joined</th>";
                echo "</tr>";
                for ($i = 0; $i <= $zerofix; $i++) {
                    $id = $result->fetch($i, "id");
                    $username = $result->fetch($i, "username");
                    $added = $result->fetch($i, "added");
                    $uploaded = $result->fetch($i, "uploaded");
                    $downloaded = $result->fetch($i, "downloaded");
                    $donated = $result->fetch($i, "donated");
                    $warned = $result->fetch($i, "warned");
                    $joindate = "" . TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($added)) . " ago";
                    $upperresult = DB::run("SELECT added FROM torrents WHERE owner =?", [$id]);
                    $seconds = TimeDate::mkprettytime(TimeDate::utc_to_tz_time() - TimeDate::utc_to_tz_time($added));
                    $days = explode("d ", $seconds);
                    if (sizeof($days) > 1) {
                        $dayUpload = $uploaded / $days[0];
                        $dayDownload = $downloaded / $days[0];
                    }
                    $torrentinfo = $upperresult->fetch(PDO::FETCH_LAZY);
                    $numtorrents = $upperresult->rowCount();
                    if ($downloaded > 0) {
                        $ratio = $uploaded / $downloaded;
                        $ratio = number_format($ratio, 3);
                        $color = get_ratio_color($ratio);
                        if ($color) {
                            $ratio = "<font color='$color'>$ratio</font>";
                        }

                    } else
                    if ($uploaded > 0) {
                        $ratio = "Inf.";
                    } else {
                        $ratio = "---";
                    }
                    $counter = $i + 1;
                    echo "<tr>";
                    echo "<td align='center class='table_col1'>$counter.</td>";
                    echo "<td class='table_col2'><a href='" . URLROOT . "/users/profile?id=$id'>$username</a></td>";
                    echo "<td class='table_col1'>" . mksize($uploaded) . "</td>";
                    echo "<td class='table_col2'>" . mksize($downloaded) . "</td>";
                    echo "<td class='table_col1'>$ratio</td>";
                    if ($numtorrents == 0) {
                        echo "<td class='table_col2'><font color='red'>$numtorrents torrents</font></td>";
                    } else {
                        echo "<td class=table_col2>$numtorrents torrents</td>";
                    }
                    echo "<td class='table_col1'>" . mksize($dayUpload) . "</td>";

                    echo "<td align='center' class='table_col2'><a href='messages/create?id=$id'>PM</a></td>";
                    echo "<td class='table_col1'>" . $joindate . "</td>";
                    echo "</tr>";

                }
                echo "</table><br /><br />";
                Style::end();
            }

            if ($num == 0) {
                Style::end();
            }

        } else {
            Style::begin("Possible Cheater Detection");?>
        <center><form action='admincensor/cheats' method='post'>
            Number of days joined: <input type='text' size='4' maxlength='4' name='daysago' /> Days<br /><br />
            MB Uploaded: <input type='text' size='6' maxlength='6' name='megabts' /> MB<br />
            <input type='submit' value='<?php echo Lang::T("SUBMIT"); ?>' />
            </form></center><?php
Style::end();
        }
        require APPROOT . '/views/admin/footer.php';
    }

}