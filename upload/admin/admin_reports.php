<?php

if ($action == "reports" && $do == "view") {

      $page = '/admincp?action=reports&amp;do=view&amp;';
      $pager[] = substr($page, 0, -4);

      if ($_POST["mark"])
      {
          if (!@count($_POST["reports"])) show_error_msg(T_("ERROR"), "Nothing selected to mark.", 1);
          $ids = array_map("intval", $_POST["reports"]);
          $ids = implode(",", $ids);
          DB::run("UPDATE reports SET complete = '1', dealtwith = '1', dealtby = '$CURUSER[id]' WHERE id IN ($ids)");
          header("Refresh: 2; url=".TTURL."/admincp?action=reports&do=view");
          show_error_msg(T_("SUCCESS"), T_("CP_ENTRIES_MARK_COMP"), 1);
      }
      
      if ($_POST["del"])
      {
          if (!@count($_POST["reports"])) show_error_msg(T_("ERROR"), "Nothing selected to delete.", 1);
          $ids = array_map("intval", $_POST["reports"]);
          $ids = implode(",", $ids);
          DB::run("DELETE FROM reports WHERE id IN ($ids)");
          header("Refresh: 2; url=".TTURL."/admincp?action=reports&do=view");
          show_error_msg(T_("SUCCESS"), "Entries marked deleted.", 1);
      }
      
      $where = array();
      
      switch ( $_GET["type"] )
      {
          case "user":
            $where[] = "type = 'user'";
            $pager[] = "type=user";    
            break;
          case "torrent":
            $where[] = "type = 'torrent'";
            $pager[] = "type=torrent";
            break;
          case "comment":
            $where[] = "type = 'comment'";
            $pager[] = "type=comment";  
            break;
          case "forum":
            $where[] = "type = 'forum'";
            $pager[] = "type=forum";  
            break;
          default:
            $where = null;
            break;
      }
  
      switch ( $_GET["completed"] )
      {
          case 1:
            $where[] = "complete = '1'";
            $pager[] = "complete=1";
            break;
          default:
            $where[] = "complete = '0'";
            $pager[] = "complete=0";
            break;
      }
      
      $where = implode(" AND ", $where);
      $pager = implode("&amp;", $pager);
                                
      $num = get_row_count("reports", "WHERE $where");
      
      list($pagertop, $pagerbottom, $limit) = pager(25, $num, "$pager&amp;");
      
      $res = DB::run("SELECT reports.id, reports.dealtwith, reports.dealtby, reports.addedby, reports.votedfor, reports.votedfor_xtra, reports.reason, reports.type, users.username, reports.complete FROM `reports` INNER JOIN users ON reports.addedby = users.id WHERE $where ORDER BY reports.id DESC $limit");
      
      stdhead("Reported Items");
      adminnavmenu();    

      begin_frame("Reported Items");
      ?>
        
      <table align="right">
      <tr>
          <td valign="top">
          <form id='sort' action=''>
          <b>Type:</b>
          <select name="type" onchange="window.location='<?php echo $page; ?>type='+this.options[this.selectedIndex].value+'&amp;completed='+document.forms['sort'].completed.options[document.forms['sort'].completed.selectedIndex].value">
          <option value="">All Types</option>
          <option value="user" <?php echo ($_GET['type'] == "user" ? " selected='selected'" : ""); ?>>Users</option>
          <option value="torrent" <?php echo ($_GET['type'] == "torrent" ? " selected='selected'" : ""); ?>>Torrents</option>
          <option value="comment" <?php echo ($_GET['type'] == "comment" ? " selected='selected'" : ""); ?>>Comments</option>
          <option value="forum" <?php echo ($_GET['type'] == "forum" ? " selected='selected'" : ""); ?>>Forum</option>
          </select>
          <b>Completed:</b>
          <select name="completed" onchange="window.location='<?php echo $page; ?>completed='+this.options[this.selectedIndex].value+'&amp;type='+document.forms['sort'].type.options[document.forms['sort'].type.selectedIndex].value">
          <option value="0" <?php echo ($_GET['completed'] == 0 ? " selected='selected'" : ""); ?>>No</option>
          <option value="1" <?php echo ($_GET['completed'] == 1 ? " selected='selected'" : ""); ?>>Yes</option>
          </select>
          </form>     
          </td>
      </tr>
      </table>
      <br />
      <br />
      <br />
      
      <form id="reports" method="post" action="<?php echo TTURL; ?>/admincp?action=reports&amp;do=view">
      <table cellpadding="3" cellspacing="3" class="table_table" width="100%" align="center">
      <tr>
          <th class="table_head">Reported By</th>
          <th class="table_head">Subject</th>
          <th class="table_head">Type</th>
          <th class="table_head">Reason</th>
          <th class="table_head">Dealt With</th>
          <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
      </tr>
      
      <?php if ($res->rowCount() <= 0): ?>
      <tr>
          <td class="table_col1" colspan="6" align="center">No reports found.</td>
      </tr>
      <?php endif; ?>
      
      <?php
      while ($row = $res->fetch(PDO::FETCH_LAZY)):
          
      
      $dealtwith = '<b>No</b>';
      if ($row["dealtby"] > 0)
      {
          $r = DB::run("SELECT username FROM users WHERE id = '$row[dealtby]'")->fetch();
          $dealtwith = 'By <a href="'.TTURL.'/users/profile?id='.$row['dealtby'].'">'.$r['username'].'</a>';
      }    
      
      switch ( $row["type"] )
      {
          case "user":
            $q = DB::run("SELECT username FROM users WHERE id = '$row[votedfor]'");
            break;
          case "torrent":
            $q = DB::run("SELECT name FROM torrents WHERE id = '$row[votedfor]'");
            break;
          case "comment":
            $q = DB::run("SELECT text, news, torrent FROM comments WHERE id = '$row[votedfor]'");
            break;
          case "forum":
            $q = DB::run("SELECT subject FROM forum_topics WHERE id = '$row[votedfor]'");
            break;
      }
      
      $r = $q->fetch(PDO::FETCH_LAZY);
      
      if ($row["type"] == "user")
          $link = "/users/profile?id=$row[votedfor]";
      else if ($row["type"] == "torrent")
          $link = "torrents/read?id=$row[votedfor]";
      else if ($row["type"] == "comment")
          $link = "/comments?type=".($r[1] > 0 ? "news" : "torrent")."&amp;id=".($r[1] > 0 ? $r[1] : $r[2])."#comment$row[votedfor]";
      else if ($row["type"] == "forum")
          $link = "/forums/viewtopic&amp;topicid=$row[votedfor]&amp;page=last#post$row[votedfor_xtra]";
      ?>
      <tr>
          <td class="table_col1" align="center" width="10%"><a href="<?php echo TTURL; ?>/users/profile?id=<?php echo $row['addedby']; ?>"><?php echo class_user_colour($row['username']); ?></a></td>
          <td class="table_col2" align="center" width="15%"><a href="<?php echo $link; ?>"><?php echo CutName($r[0], 40); ?></a></td>
          <td class="table_col1" align="center" width="10%"><?php echo $row['type']; ?></td>
          <td class="table_col2" align="center" width="50%"><?php echo htmlspecialchars($row['reason']); ?></td>
          <td class="table_col1" align="center" width="10%"><?php echo $dealtwith; ?></td>
          <td class="table_col2" align="center" width="5%"><input type="checkbox" name="reports[]" value="<?php echo $row["id"]; ?>" /></td>
      </tr>
      <?php endwhile; ?>
      
      <tr>
          <td colspan="6" align="center" class="table_head">
          <?php if ($_GET["completed"] != 1): ?>
          <input type="submit" name="mark" value="Mark Completed" />
          <?php endif; ?>
          <input type="submit" name="del" value="Delete" />
          </td>
      </tr>
      </table>
      </form>
  
      <?php
    
      print $pagerbottom;
      
      end_frame();
      stdfoot();
  }