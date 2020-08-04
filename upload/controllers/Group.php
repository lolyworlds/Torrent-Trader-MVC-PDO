<?php
  class Group extends Controller {
    
    public function __construct(){
      $this->countriesModel = $this->model('Countries');
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
        "<a href='".$site_config['SITEURL']."/users/profile?id=".$row["id"]."'>" . class_user_colour($row["username"]) . "</a> ".       
        "<a href='".$site_config['SITEURL']."/messages/create?id=".$row["id"]."'><img src='".$site_config['SITEURL']."/images/button_pm.gif' border='0' alt='' /></a></td>";
        
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


	
public function member()
{

    dbconn();
    global $site_config, $CURUSER, $pdo;
    loggedinonly();

    if ($CURUSER["view_users"] == "no") {
        show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);
    }

    $search = trim($_GET['search'] ?? '');
    $class = (int) ($_GET['class'] ?? 0);
    $letter = trim($_GET['letter'] ?? '');

    if (!$class) {
        unset($class);
    }

    $q = $query = null;
    if ($search) {
        $query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
        if ($search) {
            $q = "search=" . htmlspecialchars($search);
        }
    } elseif ($letter) {
        if (strlen($letter) > 1) {
            unset($letter);
        }

        if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
            unset($letter);
        } else {
            $query = "username LIKE '$letter%' AND status='confirmed'";
        }
        $q = "letter=$letter";
    }

    if (!$query) {
        $query = "status='confirmed'";
    }

    if ($class) {
        $query .= " AND class=$class";
        $q .= ($q ? "&amp;" : "") . "class=$class";
    }

    stdhead(T_("USERS"));
    begin_frame(T_("USERS"));

    print("<center><br /><form method='get' action='$site_config[SITEURL]/group/member'>\n");
    print(T_("SEARCH") . ": <input type='text' size='30' name='search' />\n");
    print("<select name='class'>\n");
    print("<option value='-'>(any class)</option>\n");
    $res = $this->groupsModel->getGroups();
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        print("<option value='$row[group_id]'" . ($class && $class == $row['group_id'] ? " selected='selected'" : "") . ">" . htmlspecialchars($row['level']) . "</option>\n");
    }
    print("</select>\n");
    print("<button type='submit' class='btn btn-primary btn-sm'>" . T_("APPLY") . "</button>");
    print("</form></center>\n");

    print("<p align='center'>\n");

    print("<a href='$site_config[SITEURL]/group/member'><b>" . T_("ALL") . "</b></a> - \n");
    foreach (range("a", "z") as $l) {
        $L = strtoupper($l);
        if ($l == $letter) {
            print("<b>$L</b>\n");
        } else {
            print("<a href='$site_config[SITEURL]/group/member?letter=$l'><b>$L</b></a>\n");
        }

    }

    print("</p>\n");

    $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    if ($page <= 0) {
        $page = 1;
    }

    $per_page = 5; // Set how many records do you want to display per page.
    $startpoint = ($page * $per_page) - $per_page;
    $statement = "`users` ORDER BY `id` ASC"; // Change `users` & 'id' according to your table name.
    $results = $this->groupsModel->getGroupsearch($query, $startpoint, $per_page);

    if ($results->rowCount()) {

        // call function for table
        print("<br />");

        print("<div class='table-responsive'> <table class='table table-striped'><thead><tr><thead><tr>
<th>" . T_("USERNAME") . "</th>
<th>" . T_("REGISTERED") . "</th>
<th>" . T_("LAST_ACCESS") . "</th>
<th>" . T_("CLASS") . "</th>
<th>" . T_("COUNTRY") . "</th>
</tr></thead>");

        while ($row = $results->fetch(PDO::FETCH_ASSOC)) {

            $cres = $this->countriesModel->getCountry($row);

            if ($carr = $cres->fetch(PDO::FETCH_ASSOC)) {
                $country = "<td><img src='$site_config[SITEURL]/images/languages/$carr[flagpic]' title='" . htmlspecialchars($carr['name']) . "' alt='" . htmlspecialchars($carr['name']) . "' /></td>";
            } else {
                $country = "<td><img src='$site_config[SITEURL]/images/languages/unknown.gif' alt='Unknown' /></td>";
            }

            print("<tbody><tr>
<td><a href='$site_config[SITEURL]/users/profile?id=$row[id]'><b>" . class_user_colour($row['username']) . "</b></a>" . ($row["donated"] > 0 ? "<img src='$site_config[SITEURL]/images/star.png' border='0' alt='Donated' />" : "") . "</td>" . "
<td>" . utc_to_tz($row["added"]) . "</td>
<td>" . utc_to_tz($row["last_access"]) . "</td>" . "
<td>" . T_($row["level"]) . "</td>$country</tr></tbody>");
        }

        print("</table></div>");
    } else {
        echo "No records are found.";
    }

// displaying paginaiton function
    echo pagination($statement, $per_page, $page, $url = '?');

    end_frame();
    stdfoot();
}
}