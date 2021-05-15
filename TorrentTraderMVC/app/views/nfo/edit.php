<div class="card">
    <div class="card-header">
        <?php echo Lang::T("NFO_EDIT"); ?>
    </div>
    <div class="card-body">
        <center>
        <form method="post" action="<?php echo URLROOT; ?>/nfo/submit">
        <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
        <input type="hidden" name="do" value="update" />
        <textarea class="nfo" name="content" cols="60%" rows="20"><?php echo $data['contents']; ?></textarea><br />
        <input type="reset" value="<?php echo Lang::T("RESET"); ?>" />
        <button type='submit' class='btn btn-sm btn-primary'><?php echo Lang::T("SAVE"); ?></button>
        </form>
        </center>
    </div>
</div><br />

<div class="card">
    <div class="card-header">
        <?php echo Lang::T("NFO_DELETE"); ?>
    </div>
    <div class="card-body">
        <center><form method="post" action="<?php echo URLROOT; ?>/nfo/delete"><input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
        <input type="hidden" name="do" value="delete" />
        <b><?php echo Lang::T("NFO_REASON"); ?>:</b> <input type="text" name="reason" size="40" />
        <button type='submit' class='btn btn-sm btn-primary'><?php echo Lang::T("DEL"); ?></button>
        </form></center>
    </div>
</div><br />