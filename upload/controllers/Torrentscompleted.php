<?php
  class Torrentscompleted extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
		// Set Current User
		// $curuser = $this->userModel->setCurrentUser();
		// Set Current User
		// $db = new Database;
  dbconn();
   global $site_config, $CURUSER;
$db = new Database;    
  if ($site_config["MEMBERSONLY"]) {
      loggedinonly();
      
      if ($CURUSER["view_torrents"] == "no")
          show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
  }
                  
  $id = (int) $_GET["id"];
  
  $res = DB::run("SELECT name, external, banned FROM torrents WHERE id =?", [$id]);
  $row = $res->fetch(PDO::FETCH_ASSOC);
  
  if ((!$row) || ($row["banned"] == "yes" && $CURUSER["edit_torrents"] == "no"))
       show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);
  if ($row["external"] == "yes")
       show_error_msg(T_("ERROR"), T_("THIS_TORRENT_IS_EXTERNALLY_TRACKED"), 1);

  $res = DB::run("SELECT users.id, users.username, users.uploaded, users.downloaded, users.privacy, completed.date FROM users LEFT JOIN completed ON users.id = completed.userid WHERE users.enabled = 'yes' AND completed.torrentid = '$id'");
  if ($res->rowCount() == 0)
      show_error_msg(T_("ERROR"), T_("NO_DOWNLOADS_YET"), 1);
  
  $title = sprintf(T_("COMPLETED_DOWNLOADS"), CutName($row["name"], 40));   
  
  stdhead($title);
  begin_frame($title);
  ?>
  
  <table cellpadding="3" cellspacing="0" align="center" class="table_table">
  <tr>
     <th class="table_head"><?php echo T_("USERNAME"); ?></th>
     <th class="table_head"><?php echo T_("CURRENTLY_SEEDING"); ?></th>
     <th class="table_head"><?php echo T_("DATE_COMPLETED"); ?></th>
     <th class="table_head"><?php echo T_("RATIO"); ?></th>
  </tr>
  <?php 
       while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
           
           if (($row["privacy"] == "strong") && ($CURUSER["edit_users"] == "no"))
                continue;
           
           $ratio = ($row["downloaded"] > 0) ? $row["uploaded"] / $row["downloaded"] : 0;
           $peers = (get_row_count("peers", "WHERE torrent = '$id' AND userid = '$row[id]' AND seeder = 'yes'")) ? "<font color='green'>" . T_("YES") . "</font>" : "<font color='#ff0000'>" . T_("NO") . "</font>";
  ?>
       <tr>
           <td class="table_col1"><a href="/accountdetails?id=<?php echo $row["id"]; ?>"><?php echo class_user($row['username']); ?></a></td>
           <td class="table_col2"><?php echo $peers; ?></td>
           <td class="table_col1"><?php echo utc_to_tz($row["date"]); ?></td>
           <td class="table_col2"><?php echo number_format($ratio, 2); ?></td>
       </tr>
  <?php } ?>
  </table>
  
  <center><a href="torrentsdetails?id=<?php echo $id; ?>"><?php echo T_("BACK_TO_DETAILS"); ?></a></center>
  
  <?php
  end_frame();
  stdfoot();
       }
     }