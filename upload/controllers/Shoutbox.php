<?php
  class Shoutbox extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
dbconn();
global $site_config, $CURUSER;

if ($site_config['MEMBERSONLY']){
loggedinonly ();
}

if ($site_config['SHOUTBOX']){

//DELETE MESSAGES
if (isset($_GET['del'])){

	if (is_numeric($_GET['del'])){
		$result = DB::run("SELECT * FROM shoutbox WHERE msgid=?", [$_GET['del']]);
	}else{
		echo "invalid msg id STOP TRYING TO INJECT SQL";
		exit;
	}
	$row = $result->fetch(PDO::FETCH_LAZY);
	if ($row && ($CURUSER["edit_users"]=="yes" || $CURUSER['username'] == $row[1])) {
		write_log("<b><font color='orange'>Shout Deleted:</font> Deleted by   ".$CURUSER['username']."</b>");
		DB::run("DELETE FROM shoutbox WHERE msgid=?", [$_GET['del']]);
	}
}

//INSERT MESSAGE
if (!empty($_POST['message']) && $CURUSER) {	
	$_POST['message'] = $_POST['message'];
    $result = DB::run("SELECT COUNT(*) FROM shoutbox WHERE message=? AND user=? AND UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) < ?", [$_POST['message'], $CURUSER['username'], get_date_time(), 30]);
    $row = $result->fetch(PDO::FETCH_LAZY);
    if ($row[0] == '0') {
		$qry = DB::run("INSERT INTO shoutbox (msgid, user, message, date, userid) VALUES (?, ?, ?, ?, ?)", [NULL,$CURUSER['username'], $_POST['message'], get_date_time(), $CURUSER['id']]);
	}
}

//GET CURRENT USERS THEME AND LANGUAGE
if ($CURUSER){
    $ss_a = DB::run("SELECT uri FROM stylesheets WHERE id=?", [$CURUSER["stylesheet"]])->fetch();
	if ($ss_a)
		$THEME = $ss_a["uri"];
}else{//not logged in so get default theme/language
    $ss_a = DB::run("SELECT uri FROM stylesheets WHERE id=?", [$site_config["default_theme"]])->fetch();
	if ($ss_a)
		$THEME = $ss_a["uri"];
}

if(!isset($_GET['history'])){ 
?>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $site_config['SITENAME'] . T_("SHOUTBOX"); ?></title>
<?php /* If you do change the refresh interval, you should also change index.php printf(T_("SHOUTBOX_REFRESH"), 5) the 5 is in minutes */ ?>
<meta http-equiv="refresh" content="300" />
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <!-- Bootstrap -->
  <link rel="stylesheet" href="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <!-- TT old JS -->
  <script src="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/js/java_klappe.js"></script>
  <!-- TT Custom CSS, any edits must go here-->
  <link href="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/css/customstyle.css" rel="stylesheet">
</head>
<body class="shoutbox_body">
<?php

echo '<div class="table">';

echo '<div class="shoutbox_contain">
<table class="table" border="0" style="width: 99%; table-layout:fixed">';
}else{
    
    if ($site_config["MEMBERSONLY"]) {
        loggedinonly();
    }
    
	stdhead();
	begin_frame(T_("SHOUTBOX_HISTORY"));
	echo '<div class="shoutbox_history">';
    $result = DB::run('SELECT COUNT(*) FROM shoutbox');
    $row = $result->fetch(PDO::FETCH_LAZY);
	echo '<div align="center">Pages: ';
	$pages = round($row[0] / 100) + 1;
	$i = 1;
	while ($pages > 0){
		echo "<a href='".$site_config['SITEURL']."/shoutbox?history=1&amp;page=".$i."'>[".$i."]</a>&nbsp;";
		$i++;
		$pages--;
	}

	echo '</div><br /><table class="table" border="0" style="width: 99%; table-layout:fixed">';
}

if (isset($_GET['history'])) {
	if (isset($_GET['page'])) {
		if($_GET['page'] > '1') {
			$lowerlimit = $_GET['page'] * 100 - 100;
			$upperlimit = $_GET['page'] * 100;
		}else{
			$lowerlimit = 0;
			$upperlimit = 100;
		}
	}else{
		$lowerlimit = 0;
		$upperlimit = 100;
	}	
	$query = 'SELECT * FROM shoutbox ORDER BY msgid DESC LIMIT '.$lowerlimit.','.$upperlimit;
}else{
	$query = 'SELECT * FROM shoutbox ORDER BY msgid DESC LIMIT 20';
}


$result = DB::run($query);
$alt = false;

while ($row = $result->fetch(PDO::FETCH_LAZY)) {
	if ($alt){	
		echo '<tr class="shoutbox_noalt">';
		$alt = false;
	}else{
		echo '<tr class="shoutbox_alt">';
		$alt = true;
	} 
	
    // below shouts
	echo '<td style="font-size: 12px; width: 88px;">'; // echo '<td style="font-size: 12px; width: 88px;">';
	// date, time, delete, user part
	echo "<div align='left' style='float: left'>";
	echo date('g:ia', utc_to_tz_time($row['date'])); // echo date('jS M,  g:ia', utc_to_tz_time($row['date']));
	if ( ($CURUSER["edit_users"]=="yes") || ($CURUSER['username'] == class_user($row['user'])) ){
		echo "&nbsp<a href='".$site_config['SITEURL']."/shoutbox?del=".$row['msgid']."' style='font-size: 12px'>[D]</a>";
	}	
	echo "</div>";

    // message part
    echo	'</td><td><a href="'.$site_config['SITEURL'].'/accountdetails?id='.$row['userid'].'" target="_parent"><b>'.class_user($row['user']).':</b></a>&nbsp;&nbsp;'.nl2br(format_comment($row['message']));
	echo	'</td></tr>';
}

?>
</table>
</div>
<br/>

<?php

//if the user is logged in, show the shoutbox, if not, dont.
if(!isset($_GET['history'])) {
	if (isset($_SESSION["password"])){

		echo "<form name='shoutboxform' action='shoutbox' method='post'>";
		echo "<center><table width='100%' border='0' cellpadding='1' cellspacing='1'>";
		echo "<tr class='shoutbox_messageboxback'>";
		echo "<td width='75%' align='center'>";
		echo "<input type='text' name='message' class='shoutbox_msgbox' />";
		echo "</td>";
		echo "<td>";
		echo "<input type='submit' name='submit' value='".T_("SHOUT")."' class='btn btn-sm btn-primary' />";
		echo "</td>";
		echo "<td>";
        echo '<a href="javascript:PopMoreSmiles(\'shoutboxform\', \'message\');"><small>'.T_("Smilies").'</small></a>';
        echo ' <small>-</small> <a href="javascript:PopMoreTags();"><small>'.T_("TAGS").'</small></a>';
		//echo "<br />";
		echo "<small>-</small> <a href='shoutbox.php'><small>".T_("REFRESH")."</small></a>";              
		echo " <small>-</small> <a href='".$site_config['SITEURL']."/shoutbox?history=1' target='_blank'><small>".T_("HISTORY")."</small></a>";
		echo "</td>";
		echo "</tr>";
		echo "</table></center>";
		echo "</form>";
	}else{
		echo "<br /><div class='shoutbox_error'>".T_("SHOUTBOX_MUST_LOGIN")."</div>";
	}
}

if(!isset($_GET['history'])){ 
	echo "</body></html>";
}else{
	end_frame();
	stdfoot();
}


}//END IF $SHOUTBOX
else{
	echo T_("SHOUTBOX_DISABLED");
}
?>
</body>
<?php
}
}
?>