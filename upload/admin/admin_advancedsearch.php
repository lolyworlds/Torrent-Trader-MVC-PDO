<?php
// Advanced User Search (Ported from v1 - TorrentialStorm)
if ($action == "usersearch") {
	if ($do == "warndisable") {
		if (empty($_POST["warndisable"]))
			show_error_msg(T_("ERROR"), "You must select a user to edit.", 1);
		if (!empty($_POST["warndisable"])){
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
				    $query = "INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', '".$userid."', '" . get_date_time() . "', " . sqlesc($msg) . ")";
					$qry = DB::run($query);
					if(!$qry){
                        die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . $qry->errorCode() . ") " . $qry->errorInfo());
					}
				}
				$r = DB::run("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . $r->errorCode() . ") " . $r->errorInfo());
				$user = $r->fetch(PDO::FETCH_LAZY);
				$exmodcomment = $user["modcomment"];
				$modcomment = gmdate("Y-m-d") . " - Warning Removed By " . $CURUSER['username'] . ".\n". $modcomment . $exmodcomment;
				$query = "UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
				$q = DB::run($query);
				if(!$q) die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . $q->errorCode() . ") " . $q->errorInfo());
				DB::run("UPDATE users SET warned='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
			}
			if ($warn != '') {
				if (empty($_POST["warnpm"]))
					show_error_msg(T_("ERROR"), "You must type a reason/mod comment.", 1);
					$msg = "You have received a warning, Reason: $warnpm";
					$user = DB::run("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")->fetch();
					$exmodcomment = $user["modcomment"];
					$modcomment = gmdate("Y-m-d") . " - Warned by " . $CURUSER['username'] . ".\nReason: $warnpm\n" . $modcomment . $exmodcomment;
					$query = "UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
					$upd = DB::run($query);
					if(!$upd)  die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . $upd->errorCode() . ") " . $upd->errorInfo());
					DB::run("UPDATE users SET warned='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
					foreach ($_POST["warndisable"] as $userid) {
						$ins = DB::run("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', '".$userid."', '" . get_date_time() . "', " . sqlesc($msg) . ")");
						if(!$ins) die("<b>A fatal MySQL error occured</b>.\n <br />\n".T_("ERROR").": (" . $ins->errorCode() . ") " . $ins->errorInfo());
					}
			}
		}
		autolink("$_POST[referer]", "Redirecting back");
		die;
	}
	stdhead(T_("ADVANCED_USER_SEARCH"));
	adminnavmenu();
	begin_frame("Search");
	if ($_GET['h']) {
		echo "<table width='65%' border='0' align='center'><tr><td align='left'>\n
			Fields left blank will be ignored;\n
			Wildcards * and ? may be used in Name, ".T_("EMAIL")." and Comments, as well as multiple values\n
			separated by spaces (e.g. 'wyz Max*' in Name will list both users named\n
			'wyz' and those whose names start by 'Max'. Similarly '~' can be used for\n
			negation, e.g. '~alfiest' in comments will restrict the search to users\n
			that do not have 'alfiest' in their comments).<br /><br />\n
			The Ratio field accepts 'Inf' and '---' besides the usual numeric values.<br /><br />\n
			The subnet mask may be entered either in dotted decimal or CIDR notation\n
			(e.g. 255.255.255.0 is the same as /24).<br /><br />\n
			Uploaded and Downloaded should be entered in GB.<br /><br />\n
			For search parameters with multiple text fields the second will be\n
			ignored unless relevant for the type of search chosen. <br /><br />\n
			The History column lists the number of forum posts and comments,\n
			respectively, as well as linking to the history page.\n
			</td></tr></table><br /><br />\n";
	} else {
		echo "<p align='center'>[<a href='/admincp?action=usersearch&amp;h=1'>Instructions</a>]";
		echo "&nbsp;-&nbsp;[<a href='/admincp?action=usersearch'>Reset</a>]</p>\n";
	}
?>
    <br />
	<form method="get" action="/admincp">
	<input type="hidden" name="action" value="usersearch" />
	<table border="0" class="table_table" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <th class="table_head" colspan="6">Search Filter</th>
    </tr>
	<tr>

	<td class="table_col1" valign="middle">Name:</td>
	<td class="table_col2"><input name="n" type="text" value="<?php echo $_GET['n']?>" size="35" /></td>

	<td class="table_col1" valign="middle">Ratio:</td>
	<td class="table_col2"><select name="rt">
	<?php
	$options = array("equal","above","below","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['rt']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>
	<input name="r" type="text" value="<?php echo $_GET['r']?>" size="5" maxlength="4" />
	<input name="r2" type="text" value="<?php echo $_GET['r2']?>" size="5" maxlength="4" /></td>

	<td class="table_col1" valign="middle">Member status:</td>
	<td class="table_col2"><select name="st">
	<?php
	$options = array("(any)","confirmed","pending");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['st']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select></td></tr>
	<tr><td class="table_col1" valign="middle"><?php echo T_("EMAIL")?>:</td>
	<td class="table_col2"><input name="em" type="text" value="<?php echo $_GET['em']?>" size="35" /></td>
	<td class="table_col1" valign="middle">IP:</td>
	<td class="table_col2"><input name="ip" type="text" value="<?php echo $_GET['ip']?>" maxlength="17" /></td>

	<td class="table_col1" valign="middle">Account status:</td>
	<td class="table_col2"><select name="as">
	<?php
	$options = array("(any)", "enabled", "disabled");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i'  ".(($_GET['as']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select></td></tr>
	<tr>
	<td class="table_col1" valign="middle">Comment:</td>
	<td class="table_col2"><input name="co" type="text" value="<?php echo $_GET['co']?>" size="35" /></td>
	<td class="table_col1" valign="middle">Mask:</td>
	<td class="table_col2"><input name="ma" type="text" value="<?php echo $_GET['ma']?>" maxlength="17" /></td>
	<td class="table_col1" valign="middle">Class:</td>
	<td class="table_col2"><select name="c"><option value='1'>(any)</option>
	<?php
	$class = $_GET['c'];
	if (!is_valid_id($class)) {
		$class = '';
	}
	$groups = classlist();
	foreach ($groups as $group) {
		$id = $group["group_id"] + 2;
		echo "<option value='$id' ".($class == $id ? " selected='selected'" : "").">".htmlspecialchars($group["level"])."</option>\n";
	}
	?>
	</select></td></tr>
	<tr>

	<td class="table_col1" valign="middle">Joined:</td>

	<td class="table_col2"><select name="dt">
	<?php
	$options = array("on","before","after","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['dt']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="d" type="text" value="<?php echo $_GET['d']?>" size="12" maxlength="10" />

	<input name="d2" type="text" value="<?php echo $_GET['d2']?>" size="12" maxlength="10" /></td>


	<td class="table_col1" valign="middle">Uploaded (GB):</td>

	<td class="table_col2"><select name="ult" id="ult">
	<?php
	$options = array("equal","above","below","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['ult']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="ul" type="text" id="ul" size="8" maxlength="7" value="<?php echo $_GET['ul']?>" />

	<input name="ul2" type="text" id="ul2" size="8" maxlength="7" value="<?php echo $_GET['ul2']?>" /></td>
	<td class="table_col1">&nbsp;</td>

	<td class="table_col2">&nbsp;</td></tr>
	<tr>

	<td class="table_col1" valign="middle">Last Seen:</td>

	<td class="table_col2"><select name="lst">
	<?php
	$options = array("on","before","after","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['lst']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="ls" type="text" value="<?php echo $_GET['ls']?>" size="12" maxlength="10" />


	<input name="ls2" type="text" value="<?php echo $_GET['ls2']?>" size="12" maxlength="10" /></td>
	<td class="table_col1" valign="middle">Downloaded (GB):</td>

	<td class="table_col2"><select name="dlt" id="dlt">
	<?php
	$options = array("equal","above","below","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['dlt']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="dl" type="text" id="dl" size="8" maxlength="7" value="<?php echo $_GET['dl']?>" />

	<input name="dl2" type="text" id="dl2" size="8" maxlength="7" value="<?php echo $_GET['dl2']?>" /></td>

	<td class="table_col1" valign="middle">Warned:</td>

	<td class="table_col2"><select name="w">
	<?php
	$options = array("(any)","Yes","No");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['w']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select></td></tr>
	<tr><td colspan="6" align="center"><input name="submit" value="Search" type="submit" /></td></tr>
	</table>
	<br /><br />
	</form>

<?php
	// Validates date in the form [yy]yy-mm-dd;
	// Returns date if valid, 0 otherwise.
	function mkdate($date) {
		if (strpos($date, '-'))
			$a = explode('-', $date);
		elseif (strpos($date, '/'))
			$a = explode('/', $date);
		else
			return 0;
		for ($i = 0; $i < 3; $i++) {
			if (!is_numeric($a[$i]))
				return 0;
		}
		if (checkdate($a[1], $a[2], $a[0]))
			return date ("Y-m-d", mktime (0,0,0,$a[1],$a[2],$a[0]));
		else
			return 0;
	}
	// ratio as a string
	function ratios ($up, $down, $color = true) {
		if ($down > 0) {
			$r = number_format($up / $down, 2);
			if ($color)
				$r = "<font color='".get_ratio_color($r)."'>$r</font>";
		} elseif ($up > 0)
			$r = "Inf.";
		else
			$r = "---";
		return $r;
	}
	// checks for the usual wildcards *, ? plus mySQL ones
	function haswildcard ($text){
		if (strpos($text, '*') === false && strpos($text, '?') === false && strpos($text,'%') === False && strpos($text,'_') === False)
			return False;
		else
			return True;
	}
	///////////////////////////////////////////////////////////////////////////////
	if (count($_GET) > 0 && !$_GET['h']) {
		// name
		$names = explode(' ',trim($_GET['n']));
		if ($names[0] !== "") {
			foreach($names as $name) {
				if (substr($name,0,1) == '~') {
					if ($name == '~') continue;
					$names_exc[] = substr($name,1);
				} else
					$names_inc[] = $name;
			}
			if (is_array($names_inc)) {
				$where_is .= isset($where_is)?" AND (":"(";
				foreach($names_inc as $name) {
					if (!haswildcard($name))
						$name_is .= (isset($name_is)?" OR ":"")."u.username = ".sqlesc($name);
					else {
						$name = str_replace(array('?','*'), array('_','%'), $name);
						$name_is .= (isset($name_is)?" OR ":"")."u.username LIKE ".sqlesc($name);
					}
				}
				$where_is .= $name_is.")";
				unset($name_is);
			}
			if (is_array($names_exc)) {
				$where_is .= isset($where_is)?" AND NOT (":" NOT (";
				foreach($names_exc as $name) {
					if (!haswildcard($name))
						$name_is .= (isset($name_is)?" OR ":"")."u.username = ".sqlesc($name);
					else {
						$name = str_replace(array('?','*'), array('_','%'), $name);
						$name_is .= (isset($name_is)?" OR ":"")."u.username LIKE ".sqlesc($name);
					}
				}
				$where_is .= $name_is.")";
			}
			$q .= ($q ? "&amp;" : "") . "n=".urlencode(trim($_GET['n']));
		}
		// email
		$emaila = explode(' ', trim($_GET['em']));
		if ($emaila[0] !== "") {
			$where_is .= isset($where_is)?" AND (":"(";
			foreach($emaila as $email) {
				if (strpos($email,'*') === False && strpos($email,'?') === False && strpos($email,'%') === False) {
					if (!validemail($email)) {
						show_error_msg(T_("ERROR"), "Bad email.");
					}
					$email_is .= (isset($email_is)?" OR ":"")."u.email =".sqlesc($email);
				} else {
					$sql_email = str_replace(array('?','*'), array('_','%'), $email);
					$email_is .= (isset($email_is)?" OR ":"")."u.email LIKE ".sqlesc($sql_email);
				}
			}
			$where_is .= $email_is.")";
			$q .= ($q ? "&amp;" : "") . "em=".urlencode(trim($_GET['em']));
		}
		//class
		// NB: the c parameter is passed as two units above the real one
		$class = $_GET['c'] - 2;
		if (is_valid_id($class + 1)) {
			$where_is .= (isset($where_is)?" AND ":"")."u.class=$class";
			$q .= ($q ? "&amp;" : "") . "c=".($class+2);
		}
		// IP
		$ip = trim($_GET['ip']);
		if ($ip) {
			$regex = "/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))(\.\b|$)){4}$/";
			if (!preg_match($regex, $ip)) {
				show_error_msg(T_("ERROR"), "Bad IP.");
			}
			$mask = trim($_GET['ma']);
			if ($mask == "" || $mask == "255.255.255.255") {
				$where_is .= (isset($where_is)?" AND ":"")."u.ip = '$ip'";
			} else {
				if (substr($mask,0,1) == "/") {
					$n = substr($mask, 1, strlen($mask) - 1);
					if (!is_numeric($n) or $n < 0 or $n > 32) {
						show_error_msg(T_("ERROR"), "Bad subnet mask.");
					} else {
						$mask = long2ip(pow(2,32) - pow(2,32-$n));
					}
				} elseif (!preg_match($regex, $mask)) {
					show_error_msg(T_("ERROR"), "Bad subnet mask.");
				}
				$where_is .= (isset($where_is)?" AND ":"")."INET_ATON(u.ip) & INET_ATON('$mask') = INET_ATON('$ip') & INET_ATON('$mask')";
				$q .= ($q ? "&amp;" : "") . "ma=$mask";
			}
			$q .= ($q ? "&amp;" : "") . "ip=$ip";
		}
		// ratio
		$ratio = trim($_GET['r']);
		if ($ratio) {
			if ($ratio == '---') {
				$ratio2 = "";
				$where_is .= isset($where_is)?" AND ":"";
				$where_is .= " u.uploaded = 0 and u.downloaded = 0";
			} elseif (strtolower(substr($ratio,0,3)) == 'inf') {
				$ratio2 = "";
				$where_is .= isset($where_is)?" AND ":"";
				$where_is .= " u.uploaded > 0 and u.downloaded = 0";
			} else {
				if (!is_numeric($ratio) || $ratio < 0) {
					show_error_msg(T_("ERROR"), "Bad ratio.");
				}
				$where_is .= isset($where_is)?" AND ":"";
				$where_is .= " (u.uploaded/u.downloaded)";
				$ratiotype = $_GET['rt'];
				$q .= ($q ? "&amp;" : "") . "rt=$ratiotype";
				if ($ratiotype == "3") {
					$ratio2 = trim($_GET['r2']);
					if (!$ratio2) {
						show_error_msg(T_("ERROR"), "Two ratios needed for this type of search.");
					}
					if (!is_numeric($ratio2) or $ratio2 < $ratio) {
						show_error_msg(T_("ERROR"), "Bad second ratio.");
					}
					$where_is .= " BETWEEN $ratio and $ratio2";
					$q .= ($q ? "&amp;" : "") . "r2=$ratio2";
				} elseif ($ratiotype == "2") {
					$where_is .= " < $ratio";
				} elseif ($ratiotype == "1") {
					$where_is .= " > $ratio";
				} else {
					$where_is .= " BETWEEN ($ratio - 0.004) and ($ratio + 0.004)";
				}
			}
			$q .= ($q ? "&amp;" : "") . "r=$ratio";
		}
		// comment
		$comments = explode(' ',trim($_GET['co']));
		if ($comments[0] !== "") {
			foreach($comments as $comment) {
				if (substr($comment,0,1) == '~') {
					if ($comment == '~') continue;
					$comments_exc[] = substr($comment,1);
				} else {
					$comments_inc[] = $comment;
				}
				if (is_array($comments_inc)) {
					$where_is .= isset($where_is)?" AND (":"(";
					foreach($comments_inc as $comment) {
						if (!haswildcard($comment))
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc("%".$comment."%");
						else {
							$comment = str_replace(array('?','*'), array('_','%'), $comment);
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc($comment);
						}
					}
					$where_is .= $comment_is.")";
					unset($comment_is);
				}
				if (is_array($comments_exc)) {
					$where_is .= isset($where_is)?" AND NOT (":" NOT (";
					foreach($comments_exc as $comment) {
						if (!haswildcard($comment))
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc("%".$comment."%");
						else {
							$comment = str_replace(array('?','*'), array('_','%'), $comment);
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc($comment);
						}
					}
					$where_is .= $comment_is.")";
				}
			}
				$q .= ($q ? "&amp;" : "") . "co=".urlencode(trim($_GET['co']));
		}
		$unit = 1073741824; // 1GB
		// uploaded
		$ul = trim($_GET['ul']);
		if ($ul) {
			if (!is_numeric($ul) || $ul < 0) {
				show_error_msg(T_("ERROR"), "Bad uploaded amount.");
			}
			$where_is .= isset($where_is)?" AND ":"";
			$where_is .= " u.uploaded ";
			$ultype = $_GET['ult'];
			$q .= ($q ? "&amp;" : "") . "ult=$ultype";
			if ($ultype == "3") {
				$ul2 = trim($_GET['ul2']);
				if(!$ul2) {
					show_error_msg(T_("ERROR"), "Two uploaded amounts needed for this type of search.");
				}
				if (!is_numeric($ul2) or $ul2 < $ul) {
					show_error_msg(T_("ERROR"), "Bad second uploaded amount.");
				}
				$where_is .= " BETWEEN ".$ul*$unit." and ".$ul2*$unit;
				$q .= ($q ? "&amp;" : "") . "ul2=$ul2";
			} elseif ($ultype == "2") {
				$where_is .= " < ".$ul*$unit;
			} elseif ($ultype == "1") {
				$where_is .= " >". $ul*$unit;
			} else {
				$where_is .= " BETWEEN ".($ul - 0.004)*$unit." and ".($ul + 0.004)*$unit;
			}
			$q .= ($q ? "&amp;" : "") . "ul=$ul";
		}
		// downloaded
		$dl = trim($_GET['dl']);
		if ($dl) {
			if (!is_numeric($dl) || $dl < 0) {
				show_error_msg(T_("ERROR"), "Bad downloaded amount.");
			}
			$where_is .= isset($where_is)?" AND ":"";
			$where_is .= " u.downloaded ";
			$dltype = $_GET['dlt'];
			$q .= ($q ? "&amp;" : "") . "dlt=$dltype";
			if ($dltype == "3") {
				$dl2 = trim($_GET['dl2']);
				if(!$dl2) {
					show_error_msg(T_("ERROR"), "Two downloaded amounts needed for this type of search.");
				}
				if (!is_numeric($dl2) or $dl2 < $dl) {
					show_error_msg(T_("ERROR"), "Bad second downloaded amount.");
				}
				$where_is .= " BETWEEN ".$dl*$unit." and ".$dl2*$unit;
				$q .= ($q ? "&amp;" : "") . "dl2=$dl2";
			} elseif ($dltype == "2") {
				$where_is .= " < ".$dl*$unit;
			} elseif ($dltype == "1") {
				$where_is .= " > ".$dl*$unit;
			} else {
				$where_is .= " BETWEEN ".($dl - 0.004)*$unit." and ".($dl + 0.004)*$unit;
			}
			$q .= ($q ? "&amp;" : "") . "dl=$dl";
		}
		// date joined
		$date = trim($_GET['d']);
		if ($date) {
			if (!$date = mkdate($date)) {
				show_error_msg(T_("ERROR"), "Invalid date.");
			}
			$q .= ($q ? "&amp;" : "") . "d=$date";
			$datetype = $_GET['dt'];
			$q .= ($q ? "&amp;" : "") . "dt=$datetype";
			if ($datetype == "0") {
				// For mySQL 4.1.1 or above use instead
				// $where_is .= (isset($where_is)?" AND ":"")."DATE(added) = DATE('$date')";
				$where_is .= (isset($where_is)?" AND ":"")."(UNIX_TIMESTAMP(added) - UNIX_TIMESTAMP('$date')) BETWEEN 0 and 86400";
			} else {
				$where_is .= (isset($where_is)?" AND ":"")."u.added ";
				if ($datetype == "3") {
					$date2 = mkdate(trim($_GET['d2']));
					if ($date2) {
						if (!$date = mkdate($date)) {
							show_error_msg(T_("ERROR"), "Invalid date.");
						}
						$q .= ($q ? "&amp;" : "") . "d2=$date2";
						$where_is .= " BETWEEN '$date' and '$date2'";
					} else {
						show_error_msg(T_("ERROR"), "Two dates needed for this type of search.");
					}
				} elseif ($datetype == "1") {
					$where_is .= "< '$date'";
				} elseif ($datetype == "2") {
					$where_is .= "> '$date'";
				}
			}
		}
		// date last seen
		$last = trim($_GET['ls']);
		if ($last) {
			if (!$last = mkdate($last)) {
				show_error_msg(T_("ERROR"), "Invalid date.");
			}
			$q .= ($q ? "&amp;" : "") . "ls=$last";
			$lasttype = $_GET['lst'];
			$q .= ($q ? "&amp;" : "") . "lst=$lasttype";
			if ($lasttype == "0") {
				// For mySQL 4.1.1 or above use instead
				// $where_is .= (isset($where_is)?" AND ":"")."DATE(added) = DATE('$date')";
				$where_is .= (isset($where_is)?" AND ":"")."(UNIX_TIMESTAMP(last_access) - UNIX_TIMESTAMP('$last')) BETWEEN 0 and 86400";
			} else {
				$where_is .= (isset($where_is)?" AND ":"")."u.last_access ";
				if ($lasttype == "3") {
					$last2 = mkdate(trim($_GET['ls2']));
					if ($last2) {
						$where_is .= " BETWEEN '$last' and '$last2'";
						$q .= ($q ? "&amp;" : "") . "ls2=$last2";
					} else {
						show_error_msg(T_("ERROR"), "The second date is not valid.");
					}
				} elseif ($lasttype == "1") {
					$where_is .= "< '$last'";
				} elseif ($lasttype == "2") {
					$where_is .= "> '$last'";
				}
			}
		}
		// status
		$status = $_GET['st'];
		if ($status) {
			$where_is .= ((isset($where_is))?" AND ":"");
			if ($status == "1") {
				$where_is .= "u.status = 'confirmed'";
			} else {
				$where_is .= "u.status = 'pending' AND u.invited_by = '0'";
			}
			$q .= ($q ? "&amp;" : "") . "st=$status";
		} 
		// account status
		$accountstatus = $_GET['as'];
		if ($accountstatus) {
			$where_is .= (isset($where_is))?" AND ":"";
			if ($accountstatus == "1") {
				$where_is .= " u.enabled = 'yes'";
			} else {
				$where_is .= " u.enabled = 'no'";
			}
			$q .= ($q ? "&amp;" : "") . "as=$accountstatus";
		}
		//donor
		$donor = $_GET['do'];
		if ($donor) {
			$where_is .= (isset($where_is))?" AND ":"";
			if ($donor == 1) {
				$where_is .= " u.donated > '1'";
			} else {
				$where_is .= " u.donated < '1'";
			}
			$q .= ($q ? "&amp;" : "") . "do=$donor";
		}
		//warned
		$warned = $_GET['w'];
		if ($warned) {
			$where_is .= (isset($where_is))?" AND ":"";
			if ($warned == 1) {
				$where_is .= " u.warned = 'yes'";
			} else {
				$where_is .= " u.warned = 'no'";
			}
			$q .= ($q ? "&amp;" : "") . "w=$warned";
		}
		// disabled IP
		$disabled = $_GET['dip'];
		if ($disabled) {
			$distinct = "DISTINCT ";
			$join_is .= " LEFT JOIN users AS u2 ON u.ip = u2.ip";
			$where_is .= ((isset($where_is))?" AND ":"")."u2.enabled = 'no'";
			$q .= ($q ? "&amp;" : "") . "dip=$disabled";
		}
		// active
		$active = $_GET['ac'];
		if ($active == "1") {
			$distinct = "DISTINCT ";
			$join_is .= " LEFT JOIN peers AS p ON u.id = p.userid";
			$q .= ($q ? "&amp;" : "") . "ac=$active";
		}
		$from_is = "users AS u".$join_is;
		$distinct = isset($distinct)?$distinct:"";
        # To Avoid Confusion we skip invite_* which are invited users which haven't confirmed yet, visit /admincp?action=pendinginvited
        $where_is .= (isset($where_is))?" AND ":"";   
        $where_is .= "u.username NOT LIKE '%invite_%'";
        
		$queryc = "SELECT COUNT(".$distinct."u.id) FROM ".$from_is.
		(($where_is == "")?"":" WHERE $where_is ");
		$querypm = "FROM ".$from_is.(($where_is == "")?" ":" WHERE $where_is ");
		$select_is = "u.id, u.username, u.email, u.status, u.added, u.last_access, u.ip,
		u.class, u.uploaded, u.downloaded, u.donated, u.modcomment, u.enabled, u.warned, u.invited_by";
		$query = "SELECT ".$distinct." ".$select_is." ".$querypm;
		$count = DB::run($queryc, $params)->fetchColumn();
		$q = isset($q)?($q."&amp;"):"";
		$perpage = 25;
		list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/admincp?action=usersearch&amp;$q");
		$query .= $limit;
		$res = DB::run($query, $params)->fetchAll();

		if (! $res) {
		show_error_msg("Warning","No user was found.", 0);
		} else {
			if ($count > $perpage) {
				echo $pagertop;
			}
            echo "<form action='/admincp?action=usersearch&amp;do=warndisable' method='post'>";
			echo "<table border='0' class='table_table' cellspacing='0' cellpadding='0' width='100%'>\n";
			echo "<tr><th class='table_head'>".T_("NAME")."</th>
			<th class='table_head'>IP</th>
			<th class='table_head'>".T_("EMAIL")."</th>".
			"<th class='table_head'>Joined:</th>".
			"<th class='table_head'>Last Seen:</th>".
			"<th class='table_head'>Status</th>".
			"<th class='table_head'>Enabled</th>".
			"<th class='table_head'>Ratio</th>".
			"<th class='table_head'>Uploaded</th>".
			"<th class='table_head'>Downloaded</th>".
			"<th class='table_head'>History</th>".
			"<th class='table_head' colspan='2'>Status</th></tr>\n";

			foreach ($res as $user) {
				if ($user['added'] == '0000-00-00 00:00:00')
					$user['added'] = '---';
				if ($user['last_access'] == '0000-00-00 00:00:00')
					$user['last_access'] = '---';
			if ($user['ip']) {
				$ipstr = $user['ip'];
			} else {
				$ipstr = "---";
			}
			$pul = $user['uploaded'];
			$pdl = $user['downloaded'];
			$auxres = DB::run("SELECT COUNT(DISTINCT p.id) FROM forum_posts AS p LEFT JOIN forum_topics as t ON p.topicid = t.id
			LEFT JOIN forum_forums AS f ON t.forumid = f.id WHERE p.userid = " . $user['id'] . " AND f.minclassread <= " .
			$CURUSER['class']);
			$n = $auxres->fetch(PDO::FETCH_LAZY);
			$n_posts = $n[0];
			$auxres = DB::run("SELECT COUNT(id) FROM comments WHERE user = ".$user['id']);
			$n = $auxres->fetch();
			$n_comments = $n[0];
			echo "<tr><td class='table_col1' align='center'><b><a href='/accountdetails?id=$user[id]'>" . class_user($user['username']) . "</a></b></td>" .
				"<td class='table_col2' align='center'>" . $ipstr . "</td><td class='table_col1' align='center'>" . $user['email'] . "</td>".
				"<td class='table_col2' align='center'>" . utc_to_tz($user['added']) . "</td>".
				"<td class='table_col1' align='center'>" . $user['last_access'] . "</td>".
				"<td class='table_col2' align='center'>" . $user['status'] . "</td>".
				"<td class='table_col1' align='center'>" . $user['enabled']."</td>".
				"<td class='table_col2' align='center'>" . ratios($pul,$pdl) . "</td>".
				"<td class='table_col1' align='center'>" . mksize($user['uploaded']) . "</td>".
				"<td class='table_col2' align='center'>" . mksize($user['downloaded']) . "</td>".
				"<td class='table_col1' align='center'>$n_posts ".P_("POST", $n_posts)."<br />$n_comments ".P_("COMMENT", $n_comments)."</td>".
				// This line actually needs rewriting, difficult to edit.                                                                                                                                                                                                                                                                                                                                          
				"<td class='table_col2' align='center'>".($user["enabled"] == "yes" && $user["warned"] == "no" ? "--" : ($user["enabled"] == "no" ? "<img src=\"images/disable.png\" title=\"".T_("DISABLED")."\" alt=\"Disabled\" />" : "") . ($user["warned"] == "yes" ? "<img src=\"images/warned.png\" title=\"".T_("WARNED")."\" alt=\"Warned\" />" : "")) . "</td>"."<td class='table_col1' align='center'><input type='checkbox' name=\"warndisable[]\" value='" . $user['id'] . "' /><input type='hidden' name=\"referer\" value=\"$_SERVER[REQUEST_URI]\" /></td></tr>\n";
			}
			echo "</table>
            <br />
			<table border='0' align='center' cellspacing='0' cellpadding='0'>
			<tr><td colspan='2'></td></tr>
			<tr><td align='right'><img src=\"images/disable.png\" alt=\"Disabled\" /> <input type='submit' name='disable' value=\"Disable Selected Accounts\" /></td><td style=\"border: none; padding: 2px;\" align='left'><input type='submit' name='enable' value=\"Enable Selected Accounts\" /> <img src=\"images/disable.png\" alt=\"Disabled\" /> <img src=\"images/check.gif\" alt=\"Ok\" /></td></tr>
			<tr><td colspan='2'><br /><br /></td></tr>
			<tr><td align='center'><img src=\"images/warned.png\" alt=\"Warned\" /> <input type='submit' name='warn' value=\"Warn Selected\" /></td><td align='left'><input type='submit' name='unwarn' value=\"Remove Warning Selected\" /> <img src=\"images/warned.png\" alt=\"Warned\" /> <img src=\"images/check.gif\" alt=\"Ok\" /></td></tr>
			<tr><td align='center' colspan='2'>Mod Comment (reason):<input type='text' size='30' name='warnpm' /></td></tr>
			</table></form>\n";
   
			if ($count > $perpage) {
				echo $pagerbottom;
			}
		}
	}
	end_frame();
	stdfoot();
}
// End Advanced User Search