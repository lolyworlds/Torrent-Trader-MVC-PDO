<?php
  require_once("backend/init.php");
  dbconn();
  loggedinonly();
  stdhead("Staff");

  $dt = get_date_time(gmtime() - 180);
  
  $res = DB::run("SELECT `users`.`id`, `users`.`username`, `users`.`class`, `users`.`last_access` FROM `users` INNER JOIN `groups` ON `users`.`class` = `groups`.`group_id` WHERE `users`.`enabled` =? AND `users`.`status` =? AND `groups`.`staff_page` =? ORDER BY `username`", ['yes', 'confirmed', 'yes']);
  $col = [];  //undefined var
  $table = []; //undefined var
  while ( $row = $res->fetch(PDO::FETCH_ASSOC) )
  {
      $table[$row["class"]] = ($table[$row["class"]]).
        "<td><img src='images/button_o".($row["last_access"] > $dt ? "n" : "ff")."line.png' alt='' /> ". 
        "<a href='account-details.php?id=".$row["id"]."'>" . class_user($row["username"]) . "</a> ".       
        "<a href='mailbox.php?compose&amp;id=".$row["id"]."'><img src='images/button_pm.gif' border='0' alt='' /></a></td>";
        
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
  
  $res = DB::run("SELECT `group_id`, `level`, `staff_public` FROM `groups` WHERE `staff_page` = 'yes' $where ORDER BY `staff_sort`");

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