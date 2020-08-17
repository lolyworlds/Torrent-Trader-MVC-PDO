<?php
if ($_SESSION['loggedin']) {
begin_block("Powered By");
?>

<center>
    <a href="https://getbootstrap.com/" target="_blank"><img
      src="<?php echo $config["SITEURL"]; ?>/blocks/images/bootstrap.png" alt="Bootstrap" title="Bootstrap" height="60" width="50" /></a>
      
    <a href="#" target="_blank"><img
      src="<?php echo $config["SITEURL"]; ?>/blocks/images/mvc.png" alt="MVC" title="MVC" height="60" width="60" /></a>
      
    <a href="https://phpdelusions.net/pdo" target="_blank"><img
      src="<?php echo $config["SITEURL"]; ?>/blocks/images/pdo.png" alt="PDO" title="PDO" height="60" width="60" /></a>

    <a href="https://www.php.net/" target="_blank"><img
      src="<?php echo $config["SITEURL"]; ?>/blocks/images/php.png" alt="PHP" title="PHP" height="60" width="60" /></a>
</center>
<?php
end_block();
}
?>