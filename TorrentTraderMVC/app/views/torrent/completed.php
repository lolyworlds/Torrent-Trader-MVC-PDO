<?php
        Style::begin($data['title']);
        ?>
        <table cellpadding="3" cellspacing="0" align="center" class="table_table">
            <tr>
                <th class="table_head"><?php echo Lang::T("USERNAME"); ?></th>
                <th class="table_head"><?php echo Lang::T("CURRENTLY_SEEDING"); ?></th>
                <th class="table_head"><?php echo Lang::T("DATE_COMPLETED"); ?></th>
                <th class="table_head"><?php echo Lang::T("RATIO"); ?></th>
            </tr>
        <?php
        while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)) {

            if (($row["privacy"] == "strong") && ($_SESSION["edit_users"] == "no")) {
                continue;
            }

            $ratio = ($row["downloaded"] > 0) ? $row["uploaded"] / $row["downloaded"] : 0;
            $peers = (get_row_count("peers", "WHERE torrent = '$data[id]' AND userid = '$row[id]' AND seeder = 'yes'")) ? "<font color='green'>" . Lang::T("YES") . "</font>" : "<font color='#ff0000'>" . Lang::T("NO") . "</font>";
            ?>
                <tr>
                <td class="table_col1"><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $row["id"]; ?>"><?php echo Users::coloredname($row['username']); ?></a></td>
                <td class="table_col2"><?php echo $peers; ?></td>
                <td class="table_col1"><?php echo TimeDate::utc_to_tz($row["date"]); ?></td>
                <td class="table_col2"><?php echo number_format($ratio, 2); ?></td>
                </tr>
            <?php }?>
                </table>
                <center><a href="<?php echo URLROOT; ?>/torrents/read?id=<?php echo $data['id']; ?>"><?php echo Lang::T("BACK_TO_DETAILS"); ?></a></center>
            <?php
            Style::end();