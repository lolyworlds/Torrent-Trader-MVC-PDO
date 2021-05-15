<div class="card">
    <div class="card-header">
        Edit
    </div>
        <div class="card-body">
        <?php include APPROOT.'/views/message/messagenavbar.php'; ?>
        <div><center>
        <form name="form" action="update?id=<?php echo $data['id']; ?>" method="post">
        <label for="receiver">To</label>
        <input type="text" name="receiver" value="<?php echo $data['username']; ?>" id="receiver"><br>

        <label for="template">Template:</label>&nbsp;
            <select name="template">
            <?php  Helper::echotemplates(); ?>
            </select><br>

        <label for="name">Subject</label>
        <input type="text" name="subject" placeholder="Subject" value="<?php echo $data['subject']; ?>" id="subject">
        <?php require_once APPROOT."/helpers/bbcode_helper.php";
        print textbbcode("form", "msg", $data['msg']);?>
        <input type="submit" value="Update">
        <button type="submit" class="btn-sm btn-primary" name="Update" value="Update">Update</button>&nbsp;
       </form>
        </center></div>
    </div>
</div><br />