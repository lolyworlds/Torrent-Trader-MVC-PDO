<div class="card">
<div class="card-header">
    <?php echo Lang::T("USERS"); ?>
</div>
<div class="card-body">
<center><br />
<form method='get' action='<?php echo URLROOT; ?>/members'>
<?php echo Lang::T("SEARCH"); ?>: <input type='text' size='30' name='search' />
<select name='class'>
<option value='-'>(any class)</option>

<?php while ($row = $data['getgroups']->fetch(PDO::FETCH_ASSOC)) {
    print("<option value='$row[group_id]'" . ($class && $class == $row['group_id'] ? " selected='selected'" : "") . ">" . htmlspecialchars($row['level']) . "</option>\n");
}
    print("</select>\n");
?> 
    <button type='submit' class='btn btn-primary btn-sm'><?php echo Lang::T("APPLY"); ?></button>
    </form></center>
    <p align='center'>
        <a href='<?php echo URLROOT; ?>/members'><b><?php echo Lang::T("ALL") ?></b></a> -
    <?php foreach (range("a", "z") as $l) {
        $L = strtoupper($l);
        if ($l == $letter) {
            print("<b>$L</b>\n");
        } else {
            print("<a href='".URLROOT."/members?letter=$l'><b>$L</b></a>\n");
        }
    }
    print("</p>\n");

    $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    if ($page <= 0) {
        $page = 1;
    }

    $per_page = 5; // Set how many records do you want to display per page.
    $startpoint = ($page * $per_page) - $per_page;
    $statement = "`users` ORDER BY `id` ASC"; // Change `users` & 'id' according to your table name.
    $results = $this->groupsModel->getGroupsearch($data['query1'], $startpoint, $per_page);
    if ($results->rowCount()) { ?>
        <br />
        <div class='table-responsive'> <table class='table table-striped'><thead>
        <tr><thead><tr>
        <th><?php echo Lang::T("USERNAME") ?></th>
        <th><?php echo Lang::T("REGISTERED") ?></th>
        <th><?php echo Lang::T("LAST_ACCESS") ?></th>
        <th><?php echo Lang::T("CLASS") ?></th>
        <th><?php echo Lang::T("COUNTRY") ?></th>
        </tr></thead>
    <?php
        while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
            $country = Helper::showflag($row['country']);
    ?>
        <tbody><tr>
        <td><a href='<?php echo URLROOT; ?>/profile?id=<?php echo $row['id']; ?>'><b><?php echo Users::coloredname($row['username']) ?></b></a><?php echo  ($row["donated"] > 0 ? "<img src='".URLROOT."/assets/images/star.png' border='0' alt='Donated' />" : "") ?></td>
        <td><?php echo  TimeDate::utc_to_tz($row["added"]); ?></td>
        <td><?php echo  TimeDate::utc_to_tz($row["last_access"]) ?></td>
        <td><?php echo Lang::T($row["level"]); ?></td>
        <td><?php echo $country ?></td>
        </tr>
        </tbody>
    <?php } ?>
        </table></div>
    <?php } else { ?>
        No records are found.
     <?php   } ?>
<?php echo pagination($statement, $per_page, $page, $url = '?'); ?>
</div>
</div><br />