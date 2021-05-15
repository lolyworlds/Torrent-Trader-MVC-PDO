<?php
Style::begin($data['title']);
torrentmenu($data['id'], $row['external']);
echo '<br><br><table cellpadding="1" cellspacing="2" class="table_table"><tr>';
echo "<b>" . Lang::T("FILE_LIST") . ":</b>&nbsp;<img src='assets/images/plus.gif' id='pic1' onclick='klappe_torrent(1)' alt='' /><div id='k1' style='display: none;'><table align='center' cellpadding='0' cellspacing='0' class='table_table' border='1' width='100%'><tr><th class='table_head' align='left'>&nbsp;" . Lang::T("FILE") . "</th><th width='50' class='table_head'>&nbsp;" . Lang::T("SIZE") . "</th></tr>";
if ($data['fres']->rowCount()) {
    while ($frow = $data['fres']->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td class='table_col1'>" . htmlspecialchars($frow['path']) . "</td><td class='table_col2'>" . mksize($frow['filesize']) . "</td></tr>";
    }
} else {
     echo "<tr><td class='table_col1'>" . htmlspecialchars($data["name"]) . "</td><td class='table_col2'>" . mksize($data["size"]) . "</td></tr>";
}
echo "</table>";
Style::end();