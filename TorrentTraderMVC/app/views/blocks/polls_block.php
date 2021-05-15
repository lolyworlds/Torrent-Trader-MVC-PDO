<?php

if ($_SESSION['loggedin'] == true) {
	$db = Database::instance();
    Block::begin(Users::coloredname(Lang::T("POLL")));
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

    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['loggedin'] && $_POST["act"] == "takepoll") {
        $choice = $_POST["choice"];
        if ($choice != "" && $choice < 256 && $choice == floor($choice)) {
			$res = $db->run("SELECT * FROM polls ORDER BY added DESC LIMIT 1");
			$arr = $res->fetch(PDO::FETCH_ASSOC) or show_error_msg(Lang::T("ERROR"), "No Poll", 1);

            $pollid = $arr["id"];
            $userid = $_SESSION["id"];

			$res = $db->run("SELECT * FROM pollanswers WHERE pollid=? && userid=?", [$pollid, $userid]);
			$arr = $res->fetch(PDO::FETCH_ASSOC);
	
			if ($arr){
				show_error_msg(Lang::T("ERROR"), "You have already voted!", 0);
			}else{
	
				$ins = $db->run("INSERT INTO pollanswers VALUES(?, ?, ?, ?)", [0, $pollid, $userid, $choice]);
				if (!$ins)
						show_error_msg(Lang::T("ERROR"), "An error occured. Your vote has not been counted.", 0);
			}
		}else{
			show_error_msg(Lang::T("ERROR"), "Please select an option.", 0);
		}
	}

    // Get current poll
    if ($_SESSION['loggedin']) {
		$res = $db->run("SELECT * FROM polls ORDER BY added DESC LIMIT 1");

		if($pollok=($res->rowCount())) {
			$arr = $res->fetch(PDO::FETCH_ASSOC);
            $pollid = $arr["id"];
            $userid = $_SESSION["id"];
            $question = $arr["question"];

            $o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
        $arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
        $arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
        $arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);

            // Check if user has already voted
			$res = $db->run("SELECT * FROM pollanswers WHERE pollid=? AND userid=?", [$pollid, $userid]);
			$arr2 = $res->fetch(PDO::FETCH_ASSOC);
        }

        //Display Current Poll
        if ($pollok) { ?>
		
    <p class="text-center"><strong><?php echo $question; ?></strong></p>

  		<?php
      $voted = $arr2;

        // If member has voted already show results
        if ($voted) {
            if ($arr["selection"]) {
                $uservote = $arr["selection"];
            } else {
                $uservote = -1;
            }

            // we reserve 255 for blank vote.
    		$res = $db->run("SELECT selection FROM pollanswers WHERE pollid=$pollid AND selection < 20");

    		$tvotes = $res->rowCount();

    		$vs = array(); // array of
    		$os = array();

    		// Count votes
    		while ($arr2 = $res->fetch(PDO::FETCH_LAZY))
                $vs[$arr2[0]] += 1;
  

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

            $i = 0;

            while ($a = $os[$i]) {
                if ($i == $uservote) {
                    $a[1] .= "";
                }
                if ($tvotes == 0) {
                    $p = 0;
                } else {
                    $p = round($a[0] / $tvotes * 100);
                }
                if ($i % 2) {
                    $c = "";
                } else {
                    $c = "";
                } ?>
            <div class="row">
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <strong><?php echo format_comment($a[1]); ?></strong>
                  </div>
                  <div class="col-lg-6">
                    <div class="progress">
                      <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $p; ?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em; width: <?php echo $p; ?>%;">
                      <?php echo $p; ?>%
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
      			<!-- <tr><td width='1%'$c>" . format_comment($a[1]) . "&nbsp;&nbsp;</td><td width='99%'$c><img src='".URLROOT."/assets/images/poll/bar_left.gif' alt='' /><img src='".URLROOT."/assets/images/poll/bar.gif' height='9' width='" . ($p / 2) . "' alt='' /><img src='".URLROOT."/assets/images/poll/bar_right.gif' alt='' />$p%</td></tr> -->
            <?php
                  ++$i;
            }

            $tvotes = number_format($tvotes); ?>
    	<div class="text-center"><b><span class="label label-success"><?php echo Lang::T("VOTES").": ". $tvotes; ?></span></b></div>

  	<?php
        } else {//User has not voted, show options?>

    <form method='post' action='<?php echo encodehtml($_SERVER["REQUEST_URI"]); ?>'>
    <input type='hidden' name='act' value='takepoll' />

    <?php $i = 0;

        while ($a = $o[$i]) { ?>

      <div class="radio">
        <label>
      		<input type='radio' name='choice' value='<?php echo $i; ?>' /><?php echo format_comment($a); ?>
        </label>
      </div>

      	<?php	++$i;
        } ?>
      <div class="radio">
        <label>
    	     <input type='radio' name='choice' value='255' /><?php echo Lang::T("BLANK_VOTE"); ?>
        </label>
      </div>

    	<button type='submit' class="btn btn-primary center-block" /><?php echo Lang::T("VOTE"); ?></button>
      </form>

  	<?php }

    } else {?>

  		<p class="text-center">No Active Polls</p>

	<?php }
    } else {?>

	<p class="text-center"><?php echo Lang::T("POLL_MUST_LOGIN"); ?></p>

<?php }
    ?>
    <!-- end content -->

<?php block::end();
}