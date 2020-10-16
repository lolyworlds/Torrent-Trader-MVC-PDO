<?php
if ($_SESSION['loggedin'] == true) {
    $title = "Powered By";
    $blockId = "b-" . sha1($title);
    ?>
    <div class="card">
        <div class="card-header">
            <?php echo $title ?>
            <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
        </div>
        <div class="card-body slidingDiv<?php echo $blockId; ?>">
        <!-- content -->
    <center>
    <a href="https://getbootstrap.com/" target="_blank"><img
      src="<?php echo $config["SITEURL"]; ?>/images/blocks/bootstrap.png" alt="Bootstrap" title="Bootstrap" height="60" width="60" /></a>

    <a href="#" target="_blank"><img
      src="<?php echo $config["SITEURL"]; ?>/images/blocks/mvc.png" alt="MVC" title="MVC" height="40" width="40" /></a>

    <a href="https://phpdelusions.net/pdo" target="_blank"><img
      src="<?php echo $config["SITEURL"]; ?>/images/blocks/pdo.png" alt="PDO" title="PDO" height="40" width="40" /></a>

    <a href="https://www.php.net/" target="_blank"><img
      src="<?php echo $config["SITEURL"]; ?>/images/blocks/php.png" alt="PHP" title="PHP" height="40" width="40" /></a>
    </center>
    <!-- end content -->
    </div>
</div>
<br />
<?php
}