<?php
        Style::begin("Avatar Log");
        echo ($data['pagertop']);
        ?>
        <table border="0" class="table_table" align="center">
        <tr>
        <th class="table_head"><?php echo Lang::T("USER") ?></th>
        <th class="table_head">Avatar</th>
        </tr><?php
        while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
            echo ("<tr><td class='table_col1'><b><a href=\"" . URLROOT . "/users/profile?id=" . $arr['id'] . "\">" . Users::coloredname($arr['username']) . "</a></b></td><td class='table_col2'>");

            if (!$arr['avatar']) {
                echo "<img width=\"80\" src=" . URLROOT . "/images/default_avatar.png' alt='' /></td></tr>";
            } else {
                echo "<img width=\"80\" src=\"" . htmlspecialchars($arr["avatar"]) . "\" alt='' /></td></tr>";
            }
        }
        ?>
        </table>
        <?php
        echo ($data['pagerbottom']);
        Style::end();