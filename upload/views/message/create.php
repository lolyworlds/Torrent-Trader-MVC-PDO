<?php
            usermenu($_SESSION["id"]);
            include 'views/message/messagenavbar.php';
            ?>
            <div><center>
            <form name="form" action="create" method="post">
                <label for="reciever">Reciever:</label>&nbsp;
                <select name="receiver">
                <option name='0'>---</option>
                <?php foreach ($ress as $receiver): ?>
                    <option value="<?php echo $receiver['id']; ?>"><?php echo $receiver['username']; ?></option>
                <?php endforeach;?>
                </select><br>
                <label for="template">Template:</label>&nbsp;
            <select name="template">
            <option name='0'>---</option>
            <?php foreach ($ress1 as $stmt): ?>
                <option value="<?php echo $stmt['id']; ?>"><?php echo $stmt['subject']; ?></option>
            <?php endforeach;?>
            </select><br>
                <label for="subject">Subject:</label>&nbsp;
                <input type="text" name="subject" size="60" placeholder="Subject" id="subject">
                <?php require_once "helpers/bbcode_helper.php";
            print textbbcode("form", "body", "$body");?><br>

        <input type="submit"  name="Update" value="create">&nbsp;
        <label>Save Copy In Outbox</label>
        <input type="checkbox" name="save" checked='Checked'>&nbsp;
        <input type="submit" name="Update" value="draft">
        <input type="submit" name="Update" value="template">


            </form>
            </center></div>