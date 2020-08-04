<?php

 if ($action == "torrentmanage") {
        
        if ($_POST["do"] == "delete") {
            if (!@count($_POST["torrentids"]))
                  show_error_msg(T_("ERROR"), "Nothing selected click <a href='/admincp?action=torrentmanage'>here</a> to go back.", 1);
            foreach ($_POST["torrentids"] as $id) {
                deletetorrent(intval($id));
                write_log("Torrent ID $id was deleted by $CURUSER[username]");
            }
            show_error_msg("Torrents Deleted", "Go <a href='/admincp?action=torrentmanage'>back</a>?", 1);
        }
        
        $search = (!empty($_GET["search"])) ? htmlspecialchars(trim($_GET["search"])) : "";
        
        $where = ($search == "") ? "" : "WHERE name LIKE " . sqlesc("%$search%") . "";
        
        $count = get_row_count("torrents", $where);
        
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, "/admincp?action=torrentmanage&amp;");
        
        $res = DB::run("SELECT id, name, seeders, leechers, visible, banned, external FROM torrents $where ORDER BY name $limit");
        
        stdhead("Torrent Management");
        adminnavmenu();
        
        begin_frame("Torrent Management");

        ?>

        <center>
        <form method='get' action='<?php echo TTURL; ?>/admincp'>
        <input type='hidden' name='action' value='torrentmanage' />
        Search: <input type='text' name='search' value='<?php echo $search; ?>' size='30' />
        <input type='submit' value='Search' />
        </form>

        <form id="myform" method='post' action='<?php echo TTURL; ?>/admincp?action=torrentmanage'>
        <input type='hidden' name='do' value='delete' />
        <table cellpadding='5' cellspacing='3' width='100%' align='center' class='table_table'>
        <tr>
            <th class='table_head'><?php echo T_("NAME"); ?></th>
            <th class='table_head'>Visible</th>
            <th class='table_head'>Banned</th>
            <th class='table_head'>Seeders</th>
            <th class='table_head'>Leechers</th>
            <th class='table_head'>External</th>
            <th class='table_head'>Edit</th>
            <th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
        </tr>
        
        <?php while ($row = $res->fetch(PDO::FETCH_LAZY)) { ?>
        
        <tr>
            <td class='table_col1'><a href='<?php echo TTURL; ?>/torrents/read?id=<?php echo $row["id"]; ?>'><?php echo CutName(htmlspecialchars($row["name"]), 40); ?></a></td>
            <td class='table_col2'><?php echo $row["visible"]; ?></td>
            <td class='table_col1'><?php echo $row["banned"]; ?></td>
            <td class='table_col2'><?php echo number_format($row["seeders"]); ?></td>
            <td class='table_col1'><?php echo number_format($row["leechers"]); ?></td>
            <td class='table_col2'><?php echo $row["external"]; ?></td>
            <td class='table_col1'><a href='<?php echo TTURL; ?>torrents/edit?id=<?php echo $row["id"]; ?>&amp;returnto=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>'>Edit</a></td>
            <td class='table_col2' align='center'><input type='checkbox' name='torrentids[]' value='<?php echo $row["id"]; ?>' /></td>    
        </tr>
        
        <?php } ?>
        
        </table>
        <br />
        <input type='submit' value='Delete checked' />
        </form>
        <br />
        <?php echo $pagerbottom; ?>
        </center>
        
        <?php
        
        end_frame();
        stdfoot();
        
    }
