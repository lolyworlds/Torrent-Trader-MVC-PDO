<?php
class Bonus extends Controller
{

    public function __construct()
    {
        $this->bonusModel = $this->model('Bonusmodel');
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        dbconn();
        global $config;
        loggedinonly();
        $_POST['id'] = (int) ($_POST['id'] ?? 0);
        if (is_valid_id($_POST['id'])) {
            $row = $this->bonusModel->getBonusByPost($_POST['id']);

            if (!$row || $_SESSION['seedbonus'] < $row->cost) {
                autolink("bonus", "Demand not valid.");
            }

            $cost = $row->cost;
            $id = $_SESSION['id'];

            $this->bonusModel->setBonus($cost, $id);

            switch ($row->type) {
                case 'invite':
                    DB::run("UPDATE `users` SET `invites` = `invites` + '$row->value' WHERE `id` = '$_SESSION[id]'");
                    break;

                case 'traffic':
                    DB::run("UPDATE `users` SET `uploaded` = `uploaded` + '$row->value' WHERE `id` = '$_SESSION[id]'");
                    break;

                case 'HnR':
                    $uid = $_SESSION["class"] == "1" ? (int) $_POST["userid"] : (int) $_SESSION["id"];
                    $tid = (int) $_POST["torrentid"];

                    if (empty($tid)) {
                        autolink("bonus", "You must fill the box with the id of the torrent.");
                    }

                    if (isset($uid) && isset($tid)) {
                        $res = DB::run("SELECT * FROM `snatched` WHERE `tid` = '$tid' AND `uid` = '$uid' AND `hnr` = 'yes'");
                        if ($res->rowCount() == 0) {
                            autolink("bonus", "No HnR found with this information.");
                        }

                        $res1 = DB::run("SELECT `username` FROM `users` WHERE `id` = '$uid'");
                        $row1 = $res1->fetch(PDO::FETCH_ASSOC);
                        //$res2 = DB::run("SELECT `name` FROM `torrents` WHERE `id` = '$tid'");
                        //$row2 = $res2->fetch(PDO::FETCH_LAZY);
                        $row2 = DB::run("SELECT `name` FROM `torrents` WHERE `id` = '$tid'")->fetchColumn();

                        $username = htmlspecialchars($row1["username"]);
                        $torname = htmlspecialchars($row2["name"]);

                        write_log("The HnR of <a href='users/profile?id=" . $uid . "'>" . class_user_colour($username) . "</a> on the torrent <a href='torrents/read?id=" . $tid . "'>" . $torname . "</a> has been cleared by <a href='users/profile?id=" . $_SESSION['id'] . "'>" . class_user_colour($_SESSION['username']) . "</a>");

                        $new_modcomment = gmdate("d-m-Y \à H:i") . " - ";
                        if ($uid == $_SESSION["id"]) {
                            $new_modcomment .= "H&R on the torrent " . $torname . " cleared against " . $row->cost . " points \n";
                        } else {
                            $new_modcomment .= "H&R on the torrent " . $torname . " cleared by " . $_SESSION['username'] . " \n";
                        }

                        $modcom = sqlesc($new_modcomment);

                        DB::run("UPDATE `users` SET `modcomment` = CONCAT($modcom,modcomment) WHERE id = '$uid'");
                        DB::run("UPDATE `snatched` SET `ltime` = '129600', `hnr` = 'no' WHERE `tid` = '$tid' AND `uid` = '$uid'");
                    }
                    break;

                case 'other':
                    break;

                case 'VIP':
                    $days = $row->value;
                    $vipuntil = ($_SESSION["vipuntil"] > "0000-00-00 00:00:00") ? $vipuntil = get_date_time(strtotime($_SESSION["vipuntil"]) + (60 * 86400)) : $vipuntil = get_date_time(gmtime() + (60 * 86400));
                    $oldclass = ($_SESSION["vipuntil"] > "0000-00-00 00:00:00") ? $oldclass = $_SESSION["oldclass"] : $oldclass = $_SESSION["class"];
                    DB::run("UPDATE `users` SET `class` = '3', `oldclass`='$oldclass', `vipuntil` = '$vipuntil' WHERE `id` = '$_SESSION[id]'");
                    break;

            }

            autolink("bonus", "Your account has been credited.");
        }

        $row1 = $this->bonusModel->getAll();

        stdhead("Seedbonus");

        begin_frame("Bonus Exchange");
        $data = [
            'bonus' => $row1,
            'usersbonus' => $_SESSION['seedbonus'],
            'configbonuspertime' => $config['bonuspertime'],
            'configautoclean_interval' => floor($config['add_bonus'] / 60),
            'usersid' => $_SESSION['id'],
        ];

        $this->view('bonus/index', $data);
        end_frame();

        stdfoot();
    }

    public function trade()
    {
        dbconn();
        global $config;
        loggedinonly();
        $uid = (int) $_SESSION['id'];
        $count_uid = get_row_count('snatched', 'WHERE `uid` = \'' . $uid . '\' AND `hnr` = \'yes\'');
        if ($count_uid > 0) {
          stdhead(T_("YOUR_RECORDINGS_OF_HIT_AND_RUN"));
          begin_frame(T_("YOUR_RECORDINGS_OF_HIT_AND_RUN"));
          $qry = "SELECT
          snatched.tid as tid,
          torrents.name,
          torrents.size,
	    snatched.uload,
          snatched.dload,
		snatched.ltime,
          snatched.hnr,
          users.uploaded,
          users.seedbonus,
          users.modcomment
          FROM snatched
          INNER JOIN users ON snatched.uid = users.id
		INNER JOIN torrents ON snatched.tid = torrents.id
          WHERE users.status = 'confirmed'
          AND snatched.uid = '$uid'
          AND snatched.hnr = 'yes'
          AND snatched.done = 'no'
          ORDER BY stime DESC";
          $res = DB::run($qry);

          if ($_POST["requestpoints"]) {
               while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                    $torid = $row[0];
                    $modcom = $row[9];
               }
               $modcomment = gmdate("d-M-Y") . " - " . T_("DELETED_RECORDING") . ": " . $torid . " " . T_("POINTS_OF_SEED_BONUS") . "\n" . $modcom;
               DB::run("UPDATE users SET seedbonus = seedbonus - '100', modcomment = " . sqlesc($modcomment) . " WHERE id = '$uid'");
               DB::run("UPDATE snatched SET ltime = '86400', hnr = 'no', done = 'yes' WHERE tid = '$torid' AND uid = '$uid'");
               write_log("<a href=$config[SITEURL]/users/profile?id=$_SESSION[id]><b>$_SESSION[username]</b></a> " . T_("DELETED_RECORDING") . ": <a href=$config[SITEURL]/torrents/read?id=$torid><b>$torid</b></a> " . T_("POINTS_OF_SEED_BONUS") . "");
               autolink(TTURL."/bonus/trade", T_("ONE_RECORDING_HIT_AND_RUN_DELETED"));
          }

          if ($_POST["requestupload"]) {
               while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                    $torid = $row[0];
                    $torsize = $row[2];
                    $viewsize = mksize($row[2]);
                    $modcom = $row[9];
               }
               $modcomment = gmdate("d-M-Y") . " - " . T_("DELETED_RECORDING") . ": " . $torid . " with " . $viewsize . " " . T_("OF_UPLOAD") . "\n" . $modcom;
               DB::run("UPDATE users SET uploaded = uploaded - '$torsize', modcomment = " . sqlesc($modcomment) . " WHERE id = '$uid'");
               DB::run("UPDATE snatched SET ltime = '86400', hnr = 'no', done = 'yes' WHERE tid = '$torid' AND uid = '$uid'");
               write_log("<a href=$config[SITEURL]/users/profile?id=$_SESSION[id]><b>$_SESSION[username]</b></a> " . T_("DELETED_RECORDING") . ": <a href=$config[SITEURL]/torrents/read?id=$torid><b>$torid</b></a> " . T_("HIT_AND_RUN_WITH") . " <b>$viewsize</b> " . T_("OF_UPLOAD") . "");
               autolink(TTURL."/bonus/trade", T_("ONE_RECORDING_HIT_AND_RUN_DELETED"));
          }
          echo "<div style='margin-top:5px; margin-bottom:20px;' align='center'>
          <font size='2' color='#0080FF'>
          <div style='margin-top:5px;' align='center'>
          " . T_("TO_SOLVE_THIS_PROBLEM_SEEDING") . " " . ($count_uid > 1 ? "these torrents" : "this torrent") . " " . T_("HIT_AND_RUN_FOR") . " <b>" . number_format($config["hnr_seedtime"] / 3600) . "</b> " . T_("HIT_AND_RUN_HOURS_RATIO_BECOMES") . " <b>1:1</b>
          </div>
          " . T_("HIT_AND_RUN_DELETE") . " " . ($count_uid > 1 ? "these recordings" : "this recording") . " " . T_("HIT_AND_RUN_WITH") . " <b>" . T_("OF_UPLOAD") . "</b>
		</font>
          </div>";

          if ($res->rowCount() > 0):
          ?>
          <form method="post" action="<?php echo $config['SITEURL']; ?>/bonus/trade">
          <table border="0" class="table_table" cellpadding="4" cellspacing="0" align="center">
          <tr>
          <th class="table_head" align="left"><?php echo T_("TORRENT_NAME"); ?></th>
          <th class="table_head" width="1%"><img src="images/seed.gif" border="0" title="Uploaded"></th>
          <th class="table_head" width="1%"><img src="images/leech.gif" border="0" title="Downloaded"></th>
          <th class="table_head"><?php echo T_("SEED_TIME"); ?></th>
          <th class="table_head" align="left"><?php echo T_("DELETE"); ?> <?php echo $count_uid > 1 ? "These Recordings" : "This Recording"; ?>!</th>
          </tr>
          <?php
          while ($row = $res->fetch(PDO::FETCH_ASSOC)):
               $tosize = $row[2];
               $upload = $row[7];
               $points = $row[8];
               $maxchar = 40; //===| cut name length
               $smallname = htmlspecialchars(CutName($row[1], $maxchar));
               $dispname = "<b>" . $smallname . "</b>";?>

		<tr align="center">
          <?php print("<td align='left' class='table_col1' nowrap='nowrap'>" . (count($expandrows) ? "<a href=\"javascript: klappe_torrent('t" . $row['0'] . "')\"><img border=\"0\" src=\"" . $config["SITEURL"] . "/images/plus.gif\" id=\"pict" . $row['0'] . "\" alt=\"Show/Hide\" class=\"showthecross\" /></a>" : "") . " <a title=\"" . $row["1"] . "\" href=\"$config[SITEURL]/torrents/read?id=$row[0]&amp;hit=1\">$dispname</a></td>");?>
	     <td class="table_col2"><font color="#27B500"><?php echo mksize($row[3]); ?></font></td>
	     <td class="table_col1"><font color="#FF2200"><?php echo mksize($row[4]); ?></font></td>
	     <td class="table_col2"><?php echo ($row[6]) ? mkprettytime($row[5]) : '---'; ?></td>
	     <td class="table_col1" align="left">

          <?php if ($points >= 100) {?>
	     <input type="submit" class="button" name="requestpoints" value="Delete">&nbsp; <?php echo T_("SNATCHLIST_COST"); ?> <font color="#FF2200"><b>100</b></font> <?php echo T_("SNATCHLIST_POINTS_OF_SEED_BONUS"); ?>
          <?php } else {?>
          <font color="#FF1200">&nbsp;<?php echo T_("SNATCHLIST_YOU_DONT_HAVE_ENOUGH"); ?> <b><?php echo T_("SNATCHLIST_SEEDBONUS"); ?></b> <?php echo T_("SNATCHLIST_FOR_TRADING"); ?></font>
	     <?php }?>
	     <?php if ($upload > $tosize) {?>
	     <div style="margin-top:2px"><input type="submit" class="button" name="requestupload" value="Delete">&nbsp; <?php echo T_("SNATCHLIST_COST"); ?> <font color="#FF2200"><b><?php echo mksize($tosize); ?></b></font> <?php echo T_("SNATCHLIST_UPLOAD"); ?></div>
          <?php } else {?>
          <div style="margin-top:2px"><font color="#FF1200">&nbsp;<?php echo T_("SNATCHLIST_YOU_DONT_HAVE_ENOUGH"); ?> <b><?php echo T_("SNATCHLIST_UPLOAD"); ?></b> <?php echo T_("SNATCHLIST_FOR_TRADING"); ?></font></div>
          <?php }?>
          </td>
	     </tr>
		<?php endwhile;?>
          </table>
          </form>
          <?php
          echo "<br />";
          endif;
          end_frame();
          stdfoot();
          die;
        } else {
            stdhead(T_("YOUR_LIST_OF_HITS_AND_RUN"));
            begin_frame(T_("YOUR_LIST_OF_HITS_AND_RUN"));
            echo '<div style="margin-top:10px; margin-bottom:10px;" align="center"><font size="2">' . T_("THERE_ARE_NO_RECORDINGS") . '</font></div>';
            echo '<div style="margin-bottom:10px;" align="center"> [ <a href="'.$config['SITEURL'].'/snatched"><b>' . T_("HIT_AND_RUN_YOUR_SNATCH_LIST") . '</b></a> ] </div>';
            end_frame();
            stdfoot();
        }
    }

}