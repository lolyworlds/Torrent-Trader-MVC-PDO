<?php
Style::adminheader(Lang::T('EXCEPTION_VIEW'));
Style::begin(Lang::T("EXCEPTION_EDIT"));
//Style::adminnavmenu();
?>
<form method="post">
<div class="form-group">
    <center><textarea  class="form-control" name="newcontents" rows="12">
    <?php echo $data['filecontents']; ?></textarea></center>
</div><br>
    <center><font size="4" color="#ff0000">Please Double Click</font></center>
    <center><input type="submit" value="Save"></center>
</form>
<?php
Style::end();
Style::begin(Lang::T('EXCEPTION_READ'));
?>
<div class="form-group">
    <center><textarea  class="form-control" rows="12" readonly='readonly'>
    <?php echo stripslashes($data['errorlog']); ?></textarea></center>
</div><br>
<?php
Style::end();Style::adminfooter();