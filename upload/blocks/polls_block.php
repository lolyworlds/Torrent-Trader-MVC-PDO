<?php
if ($_SESSION['loggedin'] == true) {
    begin_block(T_("POLL"));
    dbconn(false);
    if (!function_exists("srt")) {
        function srt($a, $b)
        {
            if ($a[0] > $b[0]) {
                return -1;
            }
            if ($a[0] < $b[0]) {
                return 1;
            }
            return 0;
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION && $_POST["act"] == "takepoll") {
        $choice = $_POST["choice"];
        if ($choice != "" && $choice < 256 && $choice == floor($choice)) {
            $res = DB::run("SELECT * FROM polls ORDER BY added DESC LIMIT 1");
            $arr = $res->fetch(PDO::FETCH_ASSOC) or block_error_msg("Error", "No Poll", 1);
            $pollid = $arr["id"];
            $userid = $_SESSION["id"];
            $res = DB::run("SELECT * FROM pollanswers WHERE pollid=$pollid && userid=$userid");
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            if ($arr) {
                block_error_msg("Error", "You have already voted!", 0);
            } else {
                $stmt = DB::run("INSERT INTO pollanswers VALUES(0, $pollid, $userid, $choice)");
                if ($stmt != 1) {
                    block_error_msg("Error", "An error has occurred. Your vote has not been counted.", 0);
                }
            }
        } else {
            block_error_msg("Error", "Please select one option.", 0);
        }
    }
    // Get current poll
    if ($_SESSION) {
        $res = DB::run("SELECT * FROM polls ORDER BY added DESC LIMIT 1");
        if ($pollok = $res->rowCount()) {
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            $pollid = $arr["id"];
            $userid = $_SESSION["id"];
            $question = $arr["question"];
            $o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
                $arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
                $arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
                $arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);
            // Check if user has already voted
            $res = DB::run("SELECT * FROM pollanswers WHERE pollid=$pollid AND userid=$userid");
            $arr2 = $res->fetch(PDO::FETCH_ASSOC);
        }
        //Display Current Poll
        if ($pollok) {
            print("<div align=center><b>$question</b></div>\n");
            $voted = $arr2;
            // If member has voted already show results
            if ($voted) {
                if ($arr["selection"]) {
                    $uservote = $arr["selection"];
                } else {
                    $uservote = -1;
                }
                // we reserve 255 for blank vote.
                $res = DB::run("SELECT selection FROM pollanswers WHERE pollid=$pollid AND selection < 20");
                $tvotes = $res->rowCount();
                $vs = array(); // array of
                $os = array();
                // Count votes
                while ($arr2 = $res->fetch(PDO::FETCH_ASSOC)) {
                    $vs[$arr2[0]] += 1;
                }
                reset($o);
                for ($i = 0; $i < count($o); ++$i) {
                    if ($o[$i]) {
                        $os[$i] = array($vs[$i], $o[$i]);
                    }
                }
                // now os is an array like this: array(array(123, "Option 1"), array(45, "Option 2"))
                if ($arr["sort"] == "yes") {
                    usort($os, 'srt');
                }
                print("<table width=100% border=0 cellspacing=0 cellpadding=0>\n");
                $i = 0;
                while ($a = $os[$i]) {
                    if ($i == $uservote) {
                        $a[1] .= "&nbsp;*";
                    }
                    if ($tvotes == 0) {
                        $p = 0;
                    } else {
                        $p = round($a[0] / $tvotes * 100);
                    }
                    print("<tr><td class=table_head align=center width=100%>" . format_comment($a[1]) . "</td></tr><tr><td align=left width=100% class=pollspercent>" . get_poolsleft($i) . "" . get_poolsmiddle($i) . " height=12 width=" . ($p) . ">" . get_poolsright($i) . "&nbsp;$p%</td></tr><tr><td><br></td></tr>\n");
                    ++$i;
                }
                print("</table>\n");
                $tvotes = number_format($tvotes);
                print("<div align=center>Votes: $tvotes</div>\n");
            } else { //User has not voted, show options
                print("<form method=post action='?$_SERVER[QUERY_STRING]'>\n");
                print("<input type=hidden name=act value='takepoll'>");
                $i = 0;
                while ($a = $o[$i]) {
                    print("<input type=radio name=choice value=$i>" . format_comment($a) . "<br/>\n");
                    ++$i;
                }
                print("<br/>");
                print("<input type=radio name=choice value=255>Blank Vote (View Results)<br/>\n");
                print("<p align=center><input type=submit value='Vote!'></p></form>");
            }
        } else {
            echo "<center><i>..No active survey! </i></center>";
        }
    } else {
        echo "center>You must be logged in to view polls.</centrer>";
    }

    end_block();
}
