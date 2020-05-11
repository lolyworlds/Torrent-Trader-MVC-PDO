<?php
  class Group extends Controller {
    
    public function __construct(){
         $this->groupsModel = $this->model('Groups');
    }
    
	    public function index(){
			// for now just to prevent display warning
		}
	
    public function staff(){
  dbconn();
global $site_config, $CURUSER, $pdo;
  loggedinonly();
  stdhead("Staff");

  $dt = get_date_time(gmtime() - 180);
  
  $res = $this->groupsModel->getStaff();
  
  $col = [];  //undefined var
  $table = []; //undefined var
  while ( $row = $res->fetch(PDO::FETCH_ASSOC) )
  {
      $table[$row['class']] = ($table[$row['class']] ?? '').
        "<td><img src='".$site_config['SITEURL']."/images/button_o".($row["last_access"] > $dt ? "n" : "ff")."line.png' alt='' /> ". 
        "<a href='".$site_config['SITEURL']."/accountdetails?id=".$row["id"]."'>" . class_user($row["username"]) . "</a> ".       
        "<a href='".$site_config['SITEURL']."/mailbox?compose&amp;id=".$row["id"]."'><img src='".$site_config['SITEURL']."/images/button_pm.gif' border='0' alt='' /></a></td>";
        
       $col[$row['class']] = ($col[$row['class']] ?? 0) + 1;
      
      if ($col[$row["class"]] <= 4)
          $table[$row["class"]] = $table[$row["class"]] . "<td></td>";
      else 
      {
          $table[$row["class"]] = $table[$row["class"]] . "</tr><tr>";
          $col[$row["class"]] = 2;
      }
  }

  $where = null;
  if ($CURUSER["edit_users"] == "no")
      $where = "AND `staff_public` = 'yes'";
  
  $res = $this->groupsModel->getStaffLevel($where);
  
  if ($res->rowCount() == 0)
      show_error_msg(T_("ERROR"), T_("NO_STAFF_HERE"), 1);
      
  stdhead(T_("STAFF"));
  begin_frame(T_("STAFF"));

 ?> 
  <div class="table-responsive"><table class="table">
  <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): if ( !isset($table[$row["group_id"]]) ) continue; ?>

  <tr>
      <td colspan="14" class="newtd"><center><b><?php echo T_($row["level"]); ?></b> <?php if ($row["staff_public"] == "no") echo("<font color='#ff0000'>[".T_("HIDDEN FROM PUBLIC")."]</font>"); ?><center></td>
  </tr>
  <tr>
      <?php echo $table[$row["group_id"]]; ?>
  </tr>

  <?php endwhile; ?>
  </table></div>
  <?php

  end_frame();
  stdfoot();
}
}