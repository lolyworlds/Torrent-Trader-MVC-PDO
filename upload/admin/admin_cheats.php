<?php

if ($action == "cheats") {
	stdhead("Possible Cheater Detection");
	navmenu();

    $megabts = (int) $_POST['megabts'];
    $daysago = (int) $_POST['daysago'];

	if ($daysago && $megabts){

		$timeago = 84600 * $daysago; //last 7 days
		$bytesover = 1048576 * $megabts; //over 500MB Upped

		$result = SQL_Query_exec("select * FROM users WHERE UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(added) < '$timeago' AND status='confirmed' AND uploaded > '$bytesover' ORDER BY uploaded DESC "); 
		$num = mysqli_num_rows($result); // how many uploaders

		begin_frame("Possible Cheater Detection");
		echo "<p>" . $num . " Users with found over last ".$daysago." days with more than ".$megabts." MB (".$bytesover.") Bytes Uploaded.</p>";

		$zerofix = $num - 1; // remove one row because mysql starts at zero

		if ($num > 0){
		echo "<table align='center' class='table_table'>";
		echo "<tr>";
		 echo "<th class='table_head'>No.</th>";
		 echo "<th class='table_head'>" .T_("USERNAME"). "</th>";
		 echo "<th class='table_head'>" .T_("UPLOADED"). "</th>";
		 echo "<th class='table_head'>" .T_("DOWNLOADED"). "</th>";
		 echo "<th class='table_head'>" .T_("RATIO"). "</th>";
		 echo "<th class='table_head'>" .T_("TORRENTS_POSTED"). "</th>";
		 echo "<th class='table_head'>AVG Daily Upload</th>";
		 echo "<th class='table_head'>" .T_("ACCOUNT_SEND_MSG"). "</th>";
		 echo "<th class='table_head'>Joined</th>";
		echo "</tr>";

		for ($i = 0; $i <= $zerofix; $i++) {
			 $id = mysqli_result($result, $i, "id");
			 $username = mysqli_result($result, $i, "username");
			 $added = mysqli_result($result, $i, "added");
			 $uploaded = mysqli_result($result, $i, "uploaded");
			 $downloaded = mysqli_result($result, $i, "downloaded");
			 $donated = mysqli_result($result, $i, "donated");
			 $warned = mysqli_result($result, $i, "warned");
			 $joindate = "" . get_elapsed_time(sql_timestamp_to_unix_timestamp($added)) . " ago";
			 $upperquery = "SELECT added FROM torrents WHERE owner = $id";
			 $upperresult = SQL_Query_exec($upperquery);
			 $seconds = mkprettytime(utc_to_tz_time() - utc_to_tz_time($added));
			 $days = explode("d ", $seconds);

			 if(sizeof($days) > 1) {
				 $dayUpload  = $uploaded / $days[0];
				 $dayDownload = $downloaded / $days[0];
			}
		 
		  $torrentinfo = mysqli_fetch_array($upperresult);
		 
		  $numtorrents = mysqli_num_rows($upperresult);
		   
		  if ($downloaded > 0){
		   $ratio = $uploaded / $downloaded;
		   $ratio = number_format($ratio, 3);
		   $color = get_ratio_color($ratio);
		   if ($color)
		   $ratio = "<font color='$color'>$ratio</font>";
		   }
		  else
		   if ($uploaded > 0)
			$ratio = "Inf.";
		   else
			$ratio = "---";
		  
		 
		 $counter = $i + 1;
		 
		 echo "<tr>";
		  echo "<td align='center class='table_col1'>$counter.</td>";
		  echo "<td class='table_col2'><a href='account-details.php?id=$id'>$username</a></td>";
		  echo "<td class='table_col1'>" . mksize($uploaded). "</td>";
		  echo "<td class='table_col2'>" . mksize($downloaded) . "</td>";
		  echo "<td class='table_col1'>$ratio</td>";
		  if ($numtorrents == 0) echo "<td class='table_col2'><font color='red'>$numtorrents torrents</font></td>";
		  else echo "<td class=table_col2>$numtorrents torrents</td>";

		  echo "<td class='table_col1'>" . mksize($dayUpload) . "</td>";

		  echo "<td align='center' class='table_col2'><a href='mailbox.php?compose&amp;id=$id'>PM</a></td>";
		  echo "<td class='table_col1'>" . $joindate . "</td>";
		 echo "</tr>";

		 
		 }
		echo "</table><br /><br />";
		end_frame();
		}

		if ($num == 0)
		{
		end_frame();
		}

	}else{
	begin_frame("Possible Cheater Detection");?>
	<center><form action='admincp.php?action=cheats' method='post'>
		Number of days joined: <input type='text' size='4' maxlength='4' name='daysago' /> Days<br /><br />
		MB Uploaded: <input type='text' size='6' maxlength='6' name='megabts' /> MB<br />
		<input type='submit' value='<?php echo T_("SUBMIT"); ?>' />
		</form></center><?php
	end_frame();
	}
	stdfoot();
}