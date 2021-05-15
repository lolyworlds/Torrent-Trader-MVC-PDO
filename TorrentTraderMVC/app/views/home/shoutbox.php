<?php
Style::begin(Lang::T("SHOUTBOX"));
if ($_SESSION['class'] > _UPLOADER) {
    echo "<center><a href='" . URLROOT . "/shoutbox/staff'>View Staff Chat</a></center>";
} ?>
<p id="shoutbox"></p>
<form name='shoutboxform' action='<?php echo URLROOT ?>/shoutbox/add' method='post'>
<div class="row">
    <div class="col-md-12">
    <?php
        include APPROOT.'/helpers/bbcode_helper.php';
        echo shoutbbcode("shoutboxform", "message");
    ?>
    </div>
</div>
</form>
<?php
Style::end();