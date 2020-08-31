<?php
if ($action == "hnr") {
    if ($_POST['do'] == 'delete') {
        if (!@count($_POST['ids'])) {
            show_error_msg(T_("ERROR"), "Nothing Selected.", 1);
        }

        $ids = array_map('intval', $_POST['ids']);
        $ids = implode(',', $ids);
        DB::run("UPDATE snatched SET ltime = '86400', hnr = 'no', done = 'yes' WHERE `sid` IN ($ids)");
        autolink(TTURL . "/admincp?action=hnr", "Entries deleted.");
    }

    $title = "List of Hit and Run";
    require 'views/admin/header.php';
    adminnavmenu();
    if ($config["hnr_on"]) {
        $res = DB::run("SELECT * FROM `snatched` where hnr='yes' ");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $count = $row[0];

        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=hnr&amp;");

        begin_frame($title);
        print("<div style='margin-top:4px; margin-bottom:4px' align='center'><font size=2>We have <font color=red><b>$count</b></font> User" . ($count > 1 ? "s" : "") . " with Hit and Run</font></div>");

        $sql = "SELECT *,s.tid FROM users u left join snatched s on s.uid=u.id  where hnr='yes' ORDER BY s.uid DESC $limit";
        $result = DB::run($sql);
        if ($result->rowCount() != 0) {
            print("$pagertop");

            print '<form id="snatched" method="post" action="admincp.php?action=hnr">';
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
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                if ($site_config['MEMBERSONLY']) {
                    $sql1 = "SELECT id, username FROM users WHERE id = $row[uid]";
                    $result1 = DB::run($sql1);
                    $row1 = $result1->fetch(PDO::FETCH_ASSOC);
                }
                if ($row1['username']) {
                    print '<tr><td><a href="' . $config['SITEURL'] . '/users/profile?id=' . $row['uid'] . '"><b>' . class_user_colour($row1['username']) . '</b></a></td>';
                } else {
                    print '<tr><td>' . $row['ip'] . '</td>';
                }

                $sql2 = "SELECT name FROM torrents WHERE id = $row[tid]";
                $result2 = DB::run($sql2);
                while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                    $smallname = substr(htmlspecialchars($row2["name"]), 0, 35);
                    if ($smallname != htmlspecialchars($row2["name"])) {$smallname .= '...';}

                    $stime = mkprettytime($row['ltime']);
                    $startdate = utc_to_tz(get_date_time($row['stime']));
                    $lastaction = utc_to_tz(get_date_time($row['utime']));

                    print '<td><a href="' . $config['SITEURL'] . '/torrents/read?id=' . $row['tid'] . '">' . $smallname . '</td>';
                    print '<td><font color=limegreen>' . mksize($row['uload']) . '</font></td>';
                    print '<td><font color=red>' . mksize($row['dload']) . '</font></td>';
                    print '<td>' . (is_null($stime) ? '0' : $stime) . '</td>';
                    print '<td>' . date('d.M.Y H:i', sql_timestamp_to_unix_timestamp($startdate)) . '</td>';
                    print '<td>' . date('d.M.Y H:i', sql_timestamp_to_unix_timestamp($lastaction)) . '</td>';
                    print '<td><input type=checkbox name=ids[] value=' . mksize($row['sid']) . '/></td>';
                }
            }

            print '</tr></tbody></table><br>';
            echo "<center><input type='submit' value='Delete' /></center>";
            print("$pagerbottom");
        } else {
            print '<b><center>No recordings of Hit and Run</center></b>';
        }
        end_frame();
    } else {
        begin_frame($title);
        print '<b><center>Hit & Run Disabled in Config.php (mod in progress)</center></b>';
        end_frame();
    }
    stdfoot();
}
