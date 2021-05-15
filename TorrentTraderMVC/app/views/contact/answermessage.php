<?php
$user = $data['res']->fetch(PDO::FETCH_ASSOC);
$array = $data['res2']->fetch(PDO::FETCH_ASSOC);
Style::begin('Answer to Staff PM');
?>
<center><b>Answering to <a href='<?php echo URLROOT; ?>/contactstaff/viewpm?pmid=<?php echo $array['id']; ?>'><i><?php echo $array["subject"]; ?></i></a> sent by <i><?php echo $user["username"]; ?></i></b></center>

<form method=post name=message action='<?php echo URLROOT; ?>/contactstaff/takeanswer'>
<table class='table table-striped table-bordered table-hover'><thead><tr><td> 
    <b><font color=red>Message:</font></b><br>
    <textarea name=msg cols=50 rows=5><?php echo htmlspecialchars($body); ?></textarea>
    <?php
    if ($spam == 1) {
        print("<center><a href=#><font color=red><b>--- </a> ---</b></font color></center>");
    }
        echo $replyto ? " colspan=2" : ""; ?>
    <button type="submit" class="btn btn-primary">Send it!</a>
    <input type=hidden name=receiver value=<?php echo $data['receiver']; ?>>
    <input type=hidden name=answeringto value=<?php echo $data['answeringto']; ?>>
</td></tr></table>
</form>
<?php
Style::end();