<?php
if ($_SESSION['loggedin'] === true) {
    $title = "Qbit";
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
    print("<center><a href='https://www.qbittorrent.org/download.php'><font size='4' color='#ff9900'><b>Download</b></font></a></center>");
    print("<center><a href='https://www.qbittorrent.org/download.php'><img src='$config[SITEURL]/images/qbittorrent.png'  width='80%' height='80' alt='' /></a></center>");
    ?>
    <!-- end content -->
    </div>
</div>
<br />
<?php
}