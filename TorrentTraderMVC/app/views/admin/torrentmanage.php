<center>
        <form method='get' action='<?php echo URLROOT; ?>/admintorrentmanage'>
        <input type='hidden' name='action' value='torrentmanage' />
        Search: <input type='text' name='search' value='<?php echo $data['search']; ?>' size='30' />
        <input type='submit' value='Search' />
        </form>
        <center><a href='<?php echo URLROOT; ?>/peers/dead'>Dead Torrents</a></center>
        <br>
        <form id="myform" method='post' action='<?php echo URLROOT; ?>/admintorrents'>
        <input type='hidden' name='do' value='delete' />
        <table class='table table-striped table-bordered table-hover'><thead>
        <tr>
            <th class='table_head'><?php echo Lang::T("NAME"); ?></th>
            <th class='table_head'>Visible</th>
            <th class='table_head'>Banned</th>
            <th class='table_head'>Seeders</th>
            <th class='table_head'>Leechers</th>
            <th class='table_head'>External</th>
            <th class='table_head'>Edit</th>
            <th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
        </tr></thead><tbody>
        <?php while ($row = $data['res']->fetch(PDO::FETCH_LAZY)) {?>
        <tr>
            <td class='table_col1'><a href='<?php echo URLROOT; ?>/torrents/read?id=<?php echo $row["id"]; ?>'><?php echo CutName(htmlspecialchars($row["name"]), 40); ?></a></td>
            <td class='table_col2'><?php echo $row["visible"]; ?></td>
            <td class='table_col1'><?php echo $row["banned"]; ?></td>
            <td class='table_col2'><?php echo number_format($row["seeders"]); ?></td>
            <td class='table_col1'><?php echo number_format($row["leechers"]); ?></td>
            <td class='table_col2'><?php echo $row["external"]; ?></td>
            <td class='table_col1'><a href='<?php echo URLROOT; ?>/torrents/edit?id=<?php echo $row["id"]; ?>&amp;returnto=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>'>Edit</a></td>
            <td class='table_col2' align='center'><input type='checkbox' name='torrentids[]' value='<?php echo $row["id"]; ?>' /></td>
        </tr>
        <?php }?>
        </tbody></table>
        <input type='submit' value='Delete checked' />
        </form>
        <?php echo $data['pagerbottom']; ?>
        </center>