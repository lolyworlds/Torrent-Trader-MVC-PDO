<?php
  class Bonus extends Controller {
    
    public function __construct(){
         $this->bonusModel = $this->model('Bonusmodel');
         $this->userModel = $this->model('User');
    }
    
    public function index(){

  dbconn();
  global $site_config, $CURUSER;
  loggedinonly();
  $_POST['id'] = (int) ($_POST['id'] ?? 0);
  if ( is_valid_id($_POST['id']) )
  {
       $row = $this->bonusModel->getBonusByPost($_POST['id']);

       if ( !$row || $CURUSER['seedbonus'] < $row->cost )
       {
            autolink("bonus", "Demand not valid.");
       }
                 
       $cost = $row->cost;
       $id =  $CURUSER['id'];
       
       $this->bonusModel->setBonus($cost, $id);
                 
       switch ( $row->type )
       {
           case 'invite':
                 DB::run("UPDATE `users` SET `invites` = `invites` + '$row->value' WHERE `id` = '$CURUSER[id]'");
                 break;
                                 
           case 'traffic':
                 DB::run("UPDATE `users` SET `uploaded` = `uploaded` + '$row->value' WHERE `id` = '$CURUSER[id]'");
                 break;
           
           case 'HnR':
			    $uid = $CURUSER["class"] == "1" ? (int) $_POST["userid"] : (int) $CURUSER["id"];
			    $tid = (int) $_POST["torrentid"];
			
			if ( empty($tid) )
				autolink("bonus", "You must fill the box with the id of the torrent.");
			if ( isset ($uid) && isset ($tid) ) 
			{
				$res = DB::run("SELECT * FROM `snatched` WHERE `tid` = '$tid' AND `uid` = '$uid' AND `hnr` = 'yes'");
				if ( $res->rowCount() == 0 )
					autolink("bonus", "No HnR found with this information.");
				$res1 = DB::run("SELECT `username` FROM `users` WHERE `id` = '$uid'");
				$row1 = $res1->fetch(PDO::FETCH_ASSOC);
				$res2 = DB::run("SELECT `name` FROM `torrents` WHERE `id` = '$tid'");
				$row2 = $res2->fetch(PDO::FETCH_ASSOC);
				$username = htmlspecialchars($row1["username"]);
				$torname = htmlspecialchars($row2["name"]);
			
				write_log ("The HnR of <a href='accountdetails?id=".$uid."'>".class_user($username)."</a> on the torrent <a href='torrents/details?id=".$tid."'>".$torname."</a> has been cleared by <a href='accountdetails?id=".$CURUSER['id']."'>".class_user($CURUSER['username'])."</a>");
				
				$new_modcomment = gmdate("d-m-Y \à H:i") . " - ";
				if ( $uid == $CURUSER["id"] )
					$new_modcomment.= "H&R on the torrent ".$torname." cleared against ".$row->cost." points \n";
				else
					$new_modcomment.= "H&R on the torrent ".$torname." cleared by ".$CURUSER['username']." \n";
				$modcom = sqlesc($new_modcomment);
				
				DB::run("UPDATE `users` SET `modcomment` = CONCAT($modcom,modcomment) WHERE id = '$uid'");
				DB::run("UPDATE `snatched` SET `ltime` = '129600', `hnr` = 'no' WHERE `tid` = '$tid' AND `uid` = '$uid'");
			}
		    break;
		                                                    
           case 'other':
                 break;
                 
            case 'VIP':
                $days = $row->value;
                $vipuntil = ( $CURUSER["vipuntil"] > "0000-00-00 00:00:00" ) ? $vipuntil = get_date_time( strtotime( $CURUSER["vipuntil"] ) + ( 60*86400 ) ) : $vipuntil = get_date_time( gmtime() + ( 60*86400 ) );
                $oldclass = ( $CURUSER["vipuntil"] > "0000-00-00 00:00:00" ) ? $oldclass = $CURUSER["oldclass"] : $oldclass = $CURUSER["class"];
                DB::run("UPDATE `users` SET `class` = '3', `oldclass`='$oldclass', `vipuntil` = '$vipuntil' WHERE `id` = '$CURUSER[id]'");
                break;      
                 
       }
                 
       autolink("bonus", "Your account has been credited.");
  }
           
  $row1 = $this->bonusModel->getAll();
  
  stdhead("Seedbonus");

  begin_frame("Bonus Exchange");
      $data = [
        'bonus' => $row1,
        'usersbonus' => $CURUSER['seedbonus'],
        'configbonuspertime' => $site_config['bonuspertime'],
        'configautoclean_interval' => floor($site_config['add_bonus'] / 60),
        'usersid' => $CURUSER['id'],
      ];

      $this->view('bonus/index', $data);
  end_frame();
  
  stdfoot();
}
}