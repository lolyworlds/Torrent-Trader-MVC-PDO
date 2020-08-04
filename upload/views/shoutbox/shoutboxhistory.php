<?php
echo '<div class="shoutbox_history">';
            $result = DB::run('SELECT COUNT(*) FROM shoutbox');
            $row = $result->fetch(PDO::FETCH_LAZY);
            echo '<div align="center">Pages: ';
            $pages = round($row[0] / 100) + 1;
            $i = 1;
            while ($pages > 0){
                echo "<a href='".$site_config['SITEURL']."/shoutbox?history=1&amp;page=".$i."'>[".$i."]</a>&nbsp;";
                $i++;
                $pages--;
            }
        
            echo '</div><br /><table class="table" border="0" style="width: 99%; table-layout:fixed">';
        
        
        if (isset($history)) {
            if (isset($_GET['page'])) {
                if($_GET['page'] > '1') {
                    $lowerlimit = $_GET['page'] * 100 - 100;
                    $upperlimit = $_GET['page'] * 100;
                }else{
                    $lowerlimit = 0;
                    $upperlimit = 100;
                }
            }else{
                $lowerlimit = 0;
                $upperlimit = 100;
            }	
            $query = 'SELECT * FROM shoutbox ORDER BY msgid DESC LIMIT '.$lowerlimit.','.$upperlimit;
        }
        
        $result = DB::run($query);
        $alt = false;
        
        while ($row = $result->fetch(PDO::FETCH_LAZY)) {
            if ($alt){	
                echo '<tr class="shoutbox_noalt">';
                $alt = false;
            }else{
                echo '<tr class="shoutbox_alt">';
                $alt = true;
            } 
            
    // below shouts
    echo '<td style="font-size: 12px; width: 70px;">';
    // date, time, delete, user part
    echo "<div align='left' style='float: left'>";
    echo date('jS M,  g:ia', utc_to_tz_time($row['date']));
    $ol3 = DB::run("SELECT avatar FROM users WHERE id=" . $row["userid"])->fetch(PDO::FETCH_ASSOC);
    $av = $ol3['avatar'];
    if (!empty($av)) {
        $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
    } else {
        $av = "<img src='images/default_avatar.png' alt='my_avatar' width='20' height='20'>";
    }
    if ($row['userid'] == 0) {
        $av = "<img src='images/default_avatar.png' alt='default_avatar' width='20' height='20'>";
    }
    // message part
    echo '</td><td>'.$av.'<a href="' . $site_config['SITEURL'] . '/users/profile?id=' . $row['userid'] . '" target="_parent"><b>' . class_user_colour($row['user']) . ':</b></a>&nbsp;&nbsp;' . nl2br(format_comment($row['message']));
       
    echo '</td></tr>';
        }
        
        echo	'</table> </div><br/>';