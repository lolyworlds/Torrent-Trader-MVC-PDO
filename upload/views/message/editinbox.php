<?php
        include 'views/message/usernavbar.php';
        include 'views/message/messagenavbar.php';

?>
        <div><center>
        <form name="form" action="update?inbox&id=<?php echo $row['id']; ?>" method="post">
        <label for="receiver">To</label>
        <input type="text" name="receiver" value="<?php echo $username;?>" id="receiver"><br>
        
        <label for="template">Template:</label>&nbsp;
            <select name="template">
            <option name='0'>---</option>
            <?php foreach ($ress1 as $stmt): ?>
                <option value="<?php echo $stmt['id']; ?>"><?php echo $stmt['subject']; ?></option>
            <?php endforeach;?>
            </select><br>
        
        <label for="name">Subject</label>
        <input type="text" name="subject" placeholder="Subject" value="<?php echo $row['subject'];?>" id="subject">
        <?php require_once "helpers/bbcode_helper.php";
        print textbbcode("form", "msg", "$msg");?>
        <input type="submit" value="Update">
       </form>
        </center></div>