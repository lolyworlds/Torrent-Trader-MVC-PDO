<?php
require_once("core/init.php");
dbconn();

if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
     show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}

stdhead(T_('EXCEPTION_VIEW'));
    $nfofilelocation = "exception_log.txt";
    $filegetcontents = file_get_contents($nfofilelocation);
    $nfo = htmlspecialchars($filegetcontents);

  stdhead(T_("EXCEPTION_EDITOR"));
  begin_frame(T_("EXCEPTION_EDIT"));
    function make_content_file($nfofilelocation,$content,$opentype="w")
    {
        $fp_file = fopen($nfofilelocation, $opentype);
        fputs($fp_file, $content);
        fclose($fp_file);
    }
    if($_POST)
    {
         $newcontents=$_POST['newcontents'];
         make_content_file($nfofilelocation,$newcontents);
    }
    $filecontents = file_get_contents($nfofilelocation);
?>
<form method="post">
    <center><textarea name="newcontents" style=width="100%" cols="110" rows="40"><?=$filecontents?></textarea></center>
    <br>
    <center><font size="4" color="#ff0000">Please Double Click</font></center>
    <center><input type="submit" value="Save"></center>
</form>
  <?php
  end_frame();
  
  // Read
  begin_frame(T_('EXCEPTION_READ'));
    print("<textarea class='exceptionedit' style=\"width:98%;height:100%;\" rows='50' cols='20' readonly='readonly'>".stripslashes($nfo)."</textarea>");
  end_frame();

stdfoot();
?>