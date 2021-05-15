<?php
Style::begin(Lang::T("FORUMS_EDIT_POST"));
?>
<center><b>Edit Message</b></center>
<div>
<form name='Form' method='post' action='<?php echo URLROOT; ?>/forums/editpost&amp;postid=<?php echo $data['postid']; ?>''>
<input type='hidden' name='returnto' value='<?php echo  htmlspecialchars($_SERVER["HTTP_REFERER"]); ?>' />
<div class='row justify-content-md-center'>
    <div class='col-md-8'>
        <textarea  id='example' style='height:300px;width:100%;' name='body' rows='13'><?php echo htmlspecialchars($data["arrbody"]) ?></textarea>
    </div>
</div>
<center><button type='submit' class='btn btn-sm btn-primary'><?php echo Lang::T("SUBMIT"); ?></button></center>
</form>
</div>
<?php
Style::end();
?>