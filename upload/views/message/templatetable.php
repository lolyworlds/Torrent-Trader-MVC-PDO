<?php
            include 'views/message/usernavbar.php';
            include 'views/message/messagenavbar.php';
            ?>
            <form id='messagespy' method='post' action='messages&amp;do=del'>
            
                   
            <div class='table-responsive'><table class='table table-striped'>
                   <thead>
                   <tr>
                   <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
                   <th>Subject</th>
                   <th>Date</th></tr></thead>
<?php
while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {

            $res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr["receiver"]]);
            
            $subject = "<a href='" . TTURL . "/messages/read?templates&amp;id=" . $arr["id"] . "'><b>" . format_comment($arr["subject"]) . "</b></a>";
  
            $added = utc_to_tz($arr["added"]);


            ?>
        <tbody><tr>
        <td><input type='checkbox' name='del[]' value='<?php echo $arr['id']; ?>' /></td>
        <td><?php echo $subject; ?></td>
        <td><?php echo $added; ?></td></tr>
        <?php
}?>

    <tbody></table></div>
    <center><a href='<?php echo TTURL; ?>/messages/create?&templates'><button type='button' class='btn btn-sm btn-success'><b>Make New Template</b></button></a>
    <input type='submit' value='Delete Checked' /></center>
    </form>