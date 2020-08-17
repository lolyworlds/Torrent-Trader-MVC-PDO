<?php
  class Bonus extends Controller {
    
    public function __construct(){
         $this->bonusModel = $this->model('Bonusmodel');
         $this->userModel = $this->model('User');
    }
    
    public function index(){

  dbconn();
  global $config;
  loggedinonly();
  $_POST['id'] = (int) ($_POST['id'] ?? 0);
  if ( is_valid_id($_POST['id']) )
  {
       $row = $this->bonusModel->getBonusByPost($_POST['id']);

       if ( !$row || $_SESSION['seedbonus'] < $row->cost )
       {
            autolink("bonus", "Demand not valid.");
       }
                 
       $cost = $row->cost;
       $id =  $_SESSION['id'];
       
       $this->bonusModel->setBonus($cost, $id);
                 
       switch ( $row->type )
       {
           case 'invite':
                 DB::run("UPDATE `users` SET `invites` = `invites` + '$row->value' WHERE `id` = '$_SESSION[id]'");
                 break;
                                 
           case 'traffic':
                 DB::run("UPDATE `users` SET `uploaded` = `uploaded` + '$row->value' WHERE `id` = '$_SESSION[id]'");
                 break;
           
           case 'HnR':
			    $uid = $_SESSION["class"] == "1" ? (int) $_POST["userid"] : (int) $_SESSION["id"];
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
				//$res2 = DB::run("SELECT `name` FROM `torrents` WHERE `id` = '$tid'");
                    //$row2 = $res2->fetch(PDO::FETCH_LAZY);
                    $row2 = DB::run("SELECT `name` FROM `torrents` WHERE `id` = '$tid'")->fetchColumn();

				$username = htmlspecialchars($row1["username"]);
				$torname = htmlspecialchars($row2["name"]);
			
				write_log ("The HnR of <a href='users/profile?id=".$uid."'>".class_user_colour($username)."</a> on the torrent <a href='torrents/read?id=".$tid."'>".$torname."</a> has been cleared by <a href='users/profile?id=".$_SESSION['id']."'>".class_user_colour($_SESSION['username'])."</a>");
				
				$new_modcomment = gmdate("d-m-Y \à H:i") . " - ";
				if ( $uid == $_SESSION["id"] )
					$new_modcomment.= "H&R on the torrent ".$torname." cleared against ".$row->cost." points \n";
				else
					$new_modcomment.= "H&R on the torrent ".$torname." cleared by ".$_SESSION['username']." \n";
				$modcom = sqlesc($new_modcomment);
				
				DB::run("UPDATE `users` SET `modcomment` = CONCAT($modcom,modcomment) WHERE id = '$uid'");
				DB::run("UPDATE `snatched` SET `ltime` = '129600', `hnr` = 'no' WHERE `tid` = '$tid' AND `uid` = '$uid'");
			}
		    break;
		                                                    
           case 'other':
                 break;
                 
            case 'VIP':
                $days = $row->value;
                $vipuntil = ( $_SESSION["vipuntil"] > "0000-00-00 00:00:00" ) ? $vipuntil = get_date_time( strtotime( $_SESSION["vipuntil"] ) + ( 60*86400 ) ) : $vipuntil = get_date_time( gmtime() + ( 60*86400 ) );
                $oldclass = ( $_SESSION["vipuntil"] > "0000-00-00 00:00:00" ) ? $oldclass = $_SESSION["oldclass"] : $oldclass = $_SESSION["class"];
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
}