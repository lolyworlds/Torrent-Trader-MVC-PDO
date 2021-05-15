<?php
require APPROOT . '/views/admin/header.php';
?><br>
        <div class="card">
        <div class="card-header">
        <?php echo Lang::T("TEAMS_MANAGEMENT"); ?>
        </div>
        <div class="card-body">
        <b>Add new team:</b>
        <br><br>
        <form name='create' method='post' action='<?php echo URLROOT ?>/adminteams/add'>
        <center><table cellspacing='0' cellpadding='5' width='50%'>
        <tr><td><?php echo  Lang::T("TEAM") ?>: </td><td align='left'><input class="form-control" type='text' size='50' name='team_name' /></td></tr>
        <tr><td><?php echo  Lang::T("TEAM_OWNER_NAME") ?>: </td><td align='left'><input class="form-control" type='text' size='50' name='team_owner' /></td></tr>
        <tr><td valign='top'><?php echo  Lang::T("DESCRIPTION") ?>: </td><td align='left'><textarea class="form-control" name='team_description' cols='35' rows='5'></textarea><br />(BBCode is allowed)</td></tr>
        <tr><td><?php echo  Lang::T("TEAM_LOGO_URL") ?>: </td><td align='left'><input class="form-control" type='text' size='50' name='team_image' /><input type='hidden' name='add' value='true' /></td></tr>
        <tr><td></td><td><div align='left'><button type='submit' class='btn btn-primary btn-sm'><?php echo  Lang::T("TEAM_CREATE") ?></button></div></td></tr>
        </table></center>
        <br>
        </form>

        <b>Current <?php echo  Lang::T("TEAMS") ?>:</b>
        <br />
        <br />
        <center><div class='table-responsive'><table class='table table-striped'>
        <thead><tr>
        <th>ID</th><th><?php echo  Lang::T("TEAM_LOGO") ?></th><th><?php echo  Lang::T("TEAM_NAME") ?></th><th><?php echo  Lang::T("TEAM_OWNER_NAME") ?></th><th><?php echo  Lang::T("DESCRIPTION") ?></th><th><?php echo  Lang::T("OTHER") ?></th></tr></thead>
        <?php
        while ($row = $data['sql']->fetch(PDO::FETCH_LAZY)) {
            $id = (int) $row['id'];
            $name = htmlspecialchars($row['name']);
            $image = htmlspecialchars($row['image']);
            $owner = (int) $row['owner'];
            $info = format_comment($row['info']);
            $OWNERNAME2 = DB::run("SELECT username, class FROM users WHERE id=$owner")->fetch();
            $OWNERNAME = $OWNERNAME2['username'];

        ?>
        <tbody><tr>
            <td><b><?php echo $id ?></b> </td> 
            <td><img src='<?php echo $image ?>' alt='' /></td> 
            <td><b><?php echo $name ?></b></td>
            <td><a href='<?php echo URLROOT ?>/profile?id=<?php echo $owner ?>'><?php echo $OWNERNAME ?></a></td>
            <td><?php echo $info ?></td>
            <td><a href='<?php echo URLROOT ?>/adminteams/members?teamid=<?php echo $id ?>'>[Members]</a>&nbsp;
                <a href='<?php echo URLROOT ?>/adminteams/edit?editid=<?php echo $id ?>&amp;name=<?php echo $name ?>&amp;image=<?php echo $image ?>&amp;info=<?php echo $info ?>&amp;owner=<?php echo $OWNERNAME ?>'>[<?php echo Lang::T("EDIT") ?>]</a>&nbsp;
                <a href='<?php echo URLROOT ?>/adminteams/delete?del=<?php echo $id ?>&amp;team=<?php echo $name ?>'>[Delete]</a></td></tr></tbody>
 <?php    } ?>
        </table></center>
        </div>
        </div>
<?php
require APPROOT . '/views/admin/footer.php';
?>