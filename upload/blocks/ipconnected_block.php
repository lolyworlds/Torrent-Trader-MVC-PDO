<?php
if (!$config["MEMBERSONLY"] || $_SESSION['loggedin'] == true) {
    $title = T_("Ip Details");
    $blockId = "b-" . sha1($title);
    ?>
    <div class="card">
        <div class="card-header">
            <?php echo $title ?>
            <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
        </div>
        <div class="card-body slidingDiv<?php echo $blockId; ?>">
        <!-- content -->
        <?php
    $info = new IPconnected();

    $osName = $info->operatingSystem();
    $osVersion = $info->osVersion();
    $browserName = $info->browser()['browser'];
    $browserVersion = $info->browserVersion();
    $ip = $info->ip();

    echo '<font color=orange><b>Op Sys&nbsp;</b></font>' . $osName;
    echo '<br><font color=orange><b>Version&nbsp;</b></font>' . $osVersion;
    echo '<br><font color=orange><b>Browser&nbsp;</b></font>' . $browserName;
    echo '<br><font color=orange><b>Version&nbsp;</b></font>' . $browserVersion;
    echo '<br><font color=orange><b>Ip&nbsp;</b></font>' . $ip; ?>
    <!-- end content -->
    </div>
</div>
<br />
<?php
}