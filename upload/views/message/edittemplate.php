<?php
        include 'views/message/usernavbar.php';
        include 'views/message/messagenavbar.php';

?>
        <br><div><center>
        <form name="form" action="update?templates&id=<?php echo $row['id']; ?>" method="post">
        <label for="name">Subject</label>
        <input type="text" name="subject" placeholder="Subject" value="<?php echo $row['subject'];?>" id="subject">
        <?php require_once "helpers/bbcode_helper.php";
        print textbbcode("form", "msg", "$msg");?>
        <input type="submit" value="Update">
       </form>
        </center></div>