<?php
if ($_SESSION['loggedin']  === true) {
    begin_block("Qbit");
    print("<center><a href='https://www.qbittorrent.org/download.php'><font size='4' color='#ff9900'><b>Download</b></font></a></center>");
    print("<center><a href='https://www.qbittorrent.org/download.php'><img src='$config[SITEURL]/images/qbittorrent.png'  width='80%' height='80' alt='' /></a></center>");
    end_block();
}
?>