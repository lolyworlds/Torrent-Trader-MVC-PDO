<div class="card">
        <div class="card-header">
            Create
        </div>
        <div class="card-body">
        <?php include APPROOT.'/views/message/messagenavbar.php'; ?>
<div><center>
    <form name="form" action="<?php echo URLROOT; ?>/messages/submit" method="post">
    
    <label for="reciever">Reciever:</label>&nbsp;
    <select name="receiver">
    <?php
    Helper::echouser($data['id']);
    ?>
    </select><br>
    
    <label for="template">Template:</label>&nbsp;
    <select name="template">
    <?php  Helper::echotemplates(); ?>
    </select><br>
    
    <label for="subject">Subject:</label>&nbsp;
    <input type="text" name="subject" size="50" placeholder="Subject" id="subject">

    <?php require_once APPROOT."/helpers/bbcode_helper.php";
    print textbbcode("form", "body", "$body");?><br>

    <button type="submit" class="btn-sm btn-primary" name="Update" value="create">Create</button>&nbsp;
    <label>Save Copy In Outbox</label>
    <input type="checkbox" name="save" checked='Checked'>&nbsp;
    <button type="submit" class="btn btn-sm btn-primary" name="Update" value="draft">Draft</button>
    <button type="submit" class="btn btn-sm btn-primary" name="Update" value="template">Template</button>
    </form>
    </center></div>
</div>
</div><br />