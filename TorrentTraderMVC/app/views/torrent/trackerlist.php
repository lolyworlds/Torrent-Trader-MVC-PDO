<?php
Style::begin(Lang::T("Tracker List"));
$row = $data['res']->fetch(PDO::FETCH_ASSOC);
torrentmenu($data['id'], $row['external'])
?>
<?php
if ($row["external"] == 'yes') {
    print("<br><b>Tracker:</b><br /> " . htmlspecialchars($row['announce']) . "<br />");
}
if ($data['tres']->rowCount() > 1) {
    echo "<br /><b>" . Lang::T("THIS_TORRENT_HAS_BACKUP_TRACKERS") . "</b><br />";
    echo '<table cellpadding="1" cellspacing="2" class="table_table"><tr>';
    echo '<th class="table_head">URL</th><th class="table_head">' . Lang::T("SEEDERS") . '</th><th class="table_head">' . Lang::T("LEECHERS") . '</th><th class="table_head">' . Lang::T("COMPLETED") . '</th></tr>';
    $x = 1;
    while ($trow = $data['tres']->fetch(PDO::FETCH_ASSOC)) {
        $colour = $trow["online"] == "yes" ? "green" : "red";
        echo "<tr class=\"table_col$x\"><td><font color=\"$colour\"><b>" . htmlspecialchars($trow['url']) . "</b></font></td><td align=\"center\">" . number_format($trow["seeders"]) . "</td><td align=\"center\">" . number_format($trow["leechers"]) . "</td><td align=\"center\">" . number_format($trow["times_completed"]) . "</td></tr>";
        $x = $x == 1 ? 2 : 1;
    }
    echo '</table>';
}
Style::end();