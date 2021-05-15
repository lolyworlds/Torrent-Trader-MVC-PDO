<?php
if ($_SESSION['loggedin'] === true) {
    Block::begin("Qbit");
    print("<center><a href='https://www.qbittorrent.org/download.php'><font size='4' color='#ff9900'><b>Download</b></font></a></center>");
    print("<center><a href='https://www.qbittorrent.org/download.php'><img src='".URLROOT."/assets/images/qbittorrent.png'  width='80%' height='80' alt='' /></a></center>");
    ?>
    <!-- end content -->

<?php block::end();
}