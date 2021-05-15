<?php
if ($_SESSION['loggedin'] == true) {
    Block::begin("Powered By");
    ?>

    <center>
    <a href="https://getbootstrap.com/" target="_blank"><img
      src="<?php echo URLROOT; ?>/assets/images/blocks/bootstrap.png" alt="Bootstrap" title="Bootstrap" height="60" width="60" /></a>

    <a href="#" target="_blank"><img
      src="<?php echo URLROOT; ?>/assets/images/blocks/mvc.png" alt="MVC" title="MVC" height="40" width="40" /></a>

    <a href="https://phpdelusions.net/pdo" target="_blank"><img
      src="<?php echo URLROOT; ?>/assets/images/blocks/pdo.png" alt="PDO" title="PDO" height="40" width="40" /></a>

    <a href="https://www.php.net/" target="_blank"><img
      src="<?php echo URLROOT; ?>/assets/images/blocks/php.png" alt="PHP" title="PHP" height="40" width="40" /></a>
    </center>
    <!-- end content -->

<?php block::end();
}