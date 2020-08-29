<?php
function usertable($res, $frame_caption) {
    global $config;
      
    begin_frame($frame_caption, true);
    ?>
    
    <table class=table_table width=100% align=center border=0 cellpadding=4>
    <tr style="font:bold">
            <td class=table_head align=center width=10%>Rank</td>
            <td class=table_head align=left width=20%>User</td>
            <td class=table_head align=left width=10%>Uploaded</td>
            <td class=table_head align=left width=10%>UL speed</td>
            <td class=table_head align=left width=10%>Downloaded</td>
            <td class=table_head align=left width=10%>DL speed</td>
            <td class=table_head align=center width=10%>Ratio</td>
            <td class=table_head align=center width=20%>Joined</td>
    </tr>
    
    <?php
        $num = 0;
    while ($a = $res->fetch(PDO::FETCH_ASSOC)) {
          ++$num;
        $highlight = $_SESSION["id"] == $a["userid"] ? " bgcolor=#fff" : "";
    if ($a["downloaded"]) {
        $ratio = $a["uploaded"] / $a["downloaded"];
        $color = get_ratio_color($ratio);
        $ratio = number_format($ratio, 2);
          
    if ($color)
        $ratio = "<font color=#ffff00>$ratio</font>";
    } else
        $ratio = "Inf.";
        print("<tr$highlight>
                <td class=table_col1 align=center>$num</td>
                <td class=table_col2 align=left$highlight><a href=$config[SITEURL]/users/profile?id=" .$a["userid"] . "><b>" .class_user_colour($a["username"]). "</b>" ."</td>
                <td class=table_col1 align=left$highlight><font color=limegreen>" . mksize($a["uploaded"]) . "</font></td>
                <td class=table_col2 align=left$highlight><font color=limegreen>" . mksize($a["upspeed"]) . "/s" ."</font></td>
                <td class=table_col1 align=left$highlight><font color=#FF2400>" . mksize($a["downloaded"]) . "</font></td>
                <td class=table_col2 align=left$highlight><font color=#FF2400>" . mksize($a["downspeed"]) . "/s" . "</font></td>
                <td class=table_col1 align=center$highlight>" . $ratio ."</td>
                <td class=table_col2 align=center>" . gmdate("d-M-Y",strtotime($a["added"])) . " (" .get_elapsed_time(sql_timestamp_to_unix_timestamp($a["added"])) . " ago)</td>
          </tr>");
    }
    print("</table>");
    
    end_frame();
    }
    
    function _torrenttable($res, $frame_caption) {
    
    begin_frame($frame_caption, true);
    global $config;
    ?>
    
    <table class=table_table width=100% align=center border=0 cellpadding=4>
    <tr style="font:bold">
        <td class=table_head align=center>Rank</td>
        <td class=table_head align=left>Name</td>
        <td class=table_head align=center><img src="images/check.png" border=0 title=Completed></td>
        <td class=table_head align=right>Traffic</td>
        <td class=table_head align=right><img src="images/seed.gif" border=0 title=Seeders></td>
        <td class=table_head align=right><img src="images/leech.gif" border=0 title=Leechers></td>
        <td class=table_head align=right>Total</td>
        <td class=table_head align=right>Ratio</td>
    </tr>
    
    <?php
        $num = 0;
    while ($a = $res->fetch(PDO::FETCH_ASSOC)) {
          ++$num;
    if ($a["leechers"]) {
    $r = $a["seeders"] / $a["leechers"];
            $ratio = "<font color=#ff9900>" . number_format($r, 2) . "</font>";
    } else
    $ratio = "Inf.";
        print("<tr>
                <td align=center class=table_col1>$num</td>
                <td align=left class=table_col2><a href=$config[SITEURL]/torrents/read?id=" . $a["id"] . "&hit=1><b>" . $a["name"] . "</b></a></td>
                <td align=center class=table_col1><font color=#0080FF><b>" . number_format($a["times_completed"]) . "</b></font></td>
                <td align=right class=table_col2>" . mksize($a["data"]) . "</td>
                <td align=right class=table_col1><font color=limegreen><b>" . number_format($a["seeders"]) . "</b></font></td>
                <td align=right class=table_col2><font color=red><b>" . number_format($a["leechers"]) . "</b></font></td>
                <td align=right class=table_col1>" . ($a["leechers"] + $a["seeders"]) . "</td>
                <td align=right class=table_col2>$ratio</td>
        </tr>\n");
    }
        print("</table>");
    
    end_frame();
    }
    
    function countriestable($res, $frame_caption, $what) {
    
    begin_frame($frame_caption, true);
    ?>
    
    <table class=table_table width=50% align=center border=0 cellpadding=4>
    <tr style="font:bold">
        <td class=table_head align=center>Rank</td>
        <td class=table_head align=center>Country&nbsp;Flag</td>
        <td class=table_head align=left>Country&nbsp;Name</td>
        <td class=table_head align=right><?php echo $what;?></td>
    </tr>
    
    <?php
        $num = 0;
    while ($a = $res->fetch(PDO::FETCH_ASSOC)) {
        ++$num;
    
    if ($what == "Users")
       $value = number_format($a["num"]);
    elseif ($what == "Uploaded")
            $value = mksize($a["ul"]);
    elseif ($what == "Average")
        $value = mksize($a["ul_avg"]);
    elseif ($what == "Ratio")
            $value = number_format($a["r"],2);
    
    if ($a['flagpic']) {
        $flag = "<img align=center src=images/languages/$a[flagpic]>";
    } else {
        $flag = "<img align=center src=images/languages/unknown.gif>";
    }
    
    if ($a['name']) {
       $name = "<b>$a[name]</b>";
    } else {
       $name = "<b>Land of Homeless!</b>";
    }
        print("<tr>
                <td align=center class=table_col1>$num</td>
                <td align=center class=table_col2>" . "$flag</td>
                <td class=table_col1>$name</td>" . "
                <td align=right class=table_col2>$value</td>
            </tr>\n");
    }
        print("</table>");
    
    end_frame();
    }