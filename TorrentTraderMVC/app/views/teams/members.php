<?php
require APPROOT . '/views/admin/header.php';
?><br>
<div class="card">
    <div class="card-header">
        <?php echo Lang::T("TEAMS_MANAGEMENT"); ?>
    </div>
<div class="card-body">
    <div class='table-responsive'><table class='table table-striped'>
    <thead><tr>
        <th>Username</th>
        <th><?php echo Lang::T("UPLOADED"); ?>: </th>
        <th>Downloaded</th>
        </tr></thead></tbody>
        <?php
        while ($row = $data['sql']->fetch(PDO::FETCH_LAZY)) {
            $username = htmlspecialchars($row['username']);
            $uploaded = mksize($row['uploaded']);
            $downloaded = mksize($row['downloaded']);

        echo ("<tr><td><a href='".URLROOT."/profile?id=$row[id]'>" . Users::coloredname($username) . "</a></td>
        <td>$uploaded</td>
        <td>$downloaded</td></tr>");
        } ?>
    </tbody>
    </table></div>
</div>
</div>
<?php
require APPROOT . '/views/admin/footer.php';
?>