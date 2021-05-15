<?php
if ($_SESSION['loggedin'] == true) {
    Block::begin(Lang::T("Ip Details"));
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

<?php block::end();
}