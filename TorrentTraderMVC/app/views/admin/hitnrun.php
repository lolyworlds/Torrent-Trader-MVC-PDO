<?php
Style::begin($data['title']);
        print("<div style='margin-top:4px; margin-bottom:4px' align='center'><font size=2>We have <font color=red><b>$data[count]</b></font> User" . ($data['count'] > 1 ? "s" : "") . " with Hit and Run</font></div>");
        if ($data['res']->rowCount() != 0) {
            print("$data[pagertop]");
            print '<form id="snatched" method="post" action="'.URLROOT.'/adminhitnrun">';
            print '<input type="hidden" name="do" value="delete" />';
            print '<table class="table table-striped table-bordered table-hover"><thead>';
            print '<tr>';
            print '<th class="table_head"><b>User</b></th>';
            print '<th class="table_head"><b>Torrent</b></th>';
            print '<th class="table_head"><b>Uploaded</b></th>';
            print '<th class="table_head"><b>Downloaded</b></th>';
            print '<th class="table_head"><b>Seed&nbsp;Time</b></th>';
            print '<th class="table_head"><b>Started</b></th>';
            print '<th class="table_head"><b><b>Last&nbsp;Action</b></th>';
            print '<th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id)" /></th>';
            print '</tr></thead><tbody>';
            while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)) {
                if (MEMBERSONLY) {
                    $sql1 = "SELECT id, username FROM users WHERE id = $row[uid]";
                    $result1 = DB::run($sql1);
                    $row1 = $result1->fetch(PDO::FETCH_ASSOC);
                }
                if ($row1['username']) {
                    print '<tr><td><a href="' . URLROOT . '/users/profile?id=' . $row['uid'] . '"><b>' . Users::coloredname($row1['username']) . '</b></a></td>';
                } else {
                    print '<tr><td>' . $row['ip'] . '</td>';
                }
                $sql2 = "SELECT name FROM torrents WHERE id = $row[tid]";
                $result2 = DB::run($sql2);
                while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                    $smallname = substr(htmlspecialchars($row2["name"]), 0, 35);
                    if ($smallname != htmlspecialchars($row2["name"])) {$smallname .= '...';}
                    $stime = TimeDate::mkprettytime($row['ltime']);
                    $startdate = TimeDate::utc_to_tz(get_date_time($row['stime']));
                    $lastaction = TimeDate::utc_to_tz(get_date_time($row['utime']));
                    print '<td><a href="' . $config['SITEURL'] . '/torrents/read?id=' . $row['tid'] . '">' . $smallname . '</td>';
                    print '<td><font color=limegreen>' . mksize($row['uload']) . '</font></td>';
                    print '<td><font color=red>' . mksize($row['dload']) . '</font></td>';
                    print '<td>' . (is_null($stime) ? '0' : $stime) . '</td>';
                    print '<td>' . date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($startdate)) . '</td>';
                    print '<td>' . date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($lastaction)) . '</td>';
                    print '<td><input type=checkbox name=ids[] value=' . mksize($row['sid']) . '/></td>';
                }
            }
            print '</tr></tbody></table><br>';
            echo "<center><input type='submit' value='Delete' /></center>";
            print("$data[pagerbottom]");
        } else {
            print '<b><center>No recordings of Hit and Run</center></b>';
        }
        Style::end();