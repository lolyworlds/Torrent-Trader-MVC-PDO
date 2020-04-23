<?php

if ($action=="backups" && $do=="delete"){
   $filename = $_GET["filename"];
//   stdhead("Delete Backup");
//   begin_frame("Delete Backup");
   $delete_error = false;
   if (!unlink('backups/'.$filename)) { $delete_error = true; }
//   if (!unlink('backups/'.$filename.'.gz')) { $delete_error = true; }
   header("Refresh: 3 ;url=".TTURL."/admincp?action=backups");
//   end_frame();
stdhead();
show_error_msg(T_("SUCCESS"), "Selected Backup Files deleted", 0);
    if ($delete_error) {
          echo("<br><center><b>Has encountered a problem during the deletion</b></center><br><br><br>");
   } else {
          echo("<br><center><b>$filename<br><br><br>DELETED !!!</b></center><br><br><br>");
   }
          echo("<center>You'll be redirected in about 3 secs. If not, click <a href='/admincp?action=backups'>here</a></center>");
   stdfoot();
}
if ($action=="backups"){
  stdhead("Backups");
  adminnavmenu();
  begin_frame("Backups");
  $Namebk = array();
  $Sizebk = array();
  // CHECK ALL SQL FILES INTO THE BACKUPS FOLDER AND CREATE AN LIST
  $dir = opendir("backups/");
  while ($dir && ($file = readdir($dir)) !== false)
  {
            $ext = explode('.',$file);
            if ( $ext[1] == "sql")
            {
                  if ( $ext[2] != "gz" )
                  {
                            $Namebk[] = $ext[0];
                            $Sizebk[] = round( filesize("backups/".$file) / 1024, 2);
                  }
            }
  }
  // SORT THE LIST
  sort($Namebk);
  // OPEN TABLE
  echo ("<br/><br/><table style='text-align:center;' width='100%'>");
  // TABLE HEADER
  echo ("<tr bgcolor='#3895D3'>"); // Start table row
  echo ("<th scope='colgroup'><b>Date</b></th>"); // Date
  echo ("<th scope='colgroup'><b>Time</b></th>"); // Time
  echo ("<th scope='colgroup'><b>Size</b></th>"); // Size
  echo ("<th scope='colgroup'><b>Hash</b></th>"); // Hash
  echo ("<th scope='colgroup'><b>Download</b></th>"); // Download
  echo ("<th></th>"); // Delete
  echo ("</tr>"); // End table row
  // TABLE ROWS
  for( $x = count($Namebk) - 1; $x >= 0; $x--)
  {
            $data = explode('_', $Namebk[$x]);
            echo ("<tr bgcolor='#CCCCCC'>"); // Start table row
            echo ("<td>".$data[1]."</td>"); // Date
            echo ("<td>".$data[2]."</td>"); // Time
            echo ("<td>".$Sizebk[$x]." KByte</td>"); // Size
            echo ("<td>".$data[3]."</td>"); // Hash
            echo ("<td><a href='".$site_config['SITEURL']."/backups/".$Namebk[$x].".sql'>SQL</a> - <a href='".$site_config['SITEURL']."/backups/".$Namebk[$x].".sql.gz'>GZ</a></td>"); // Download
            echo ("<td><a href='".$site_config['SITEURL']."/admincp?action=backups&amp;do=delete&amp;filename=".$Namebk[$x].".sql'><img src='images/delete.png'></a></td>"); // Delete
            echo ("</tr>"); // End table row
  }
  // CLOSE TABLE
  echo ("</table>");
  // CREATE BACKUP LINK
  echo ("<br><br><center><a href='".$site_config['SITEURL']."/backupdatabase'>Backup Database</a> (or create a CRON task on ".$site_config["SITEURL"]."/backupdatabase)</center>");
  end_frame();
  stdfoot();
}