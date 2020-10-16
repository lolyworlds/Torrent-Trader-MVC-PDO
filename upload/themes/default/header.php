<?php
// Micro Time
$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="<?php echo $config["CHARSET"]; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="M-jay" />
    <meta name="generator" content="TorrentTrader <?php echo $config['ttversion']; ?>" />
    <meta name="description" content="TorrentTrader is a feature packed and highly customisable PHP/PDO/MVC Based BitTorrent tracker. Featuring intergrated forums, and plenty of administration options. Please visit www.torrenttrader.xyx for the support forums. " />
    <meta name="keywords" content="https://github.com/M-jay84/Torrent-Trader-MVC-PDO-OOP" />
    <title><?php echo $title; ?></title>
  
    <!-- Bootstrap & core CSS -->
    <link href="<?= TTURL; ?>/themes/default/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= TTURL; ?>/themes/default/vendor/font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- TT Custom CSS, any edits must go here-->
    <link href="<?php echo TTURL; ?>/themes/<?php echo ($_SESSION['stylesheet'] ?: $config['default_theme']); ?>/css/customstyle.css" rel="stylesheet">
  </head>
<body>
  
<?php require "themes/" . ($_SESSION['stylesheet'] ?: $config['default_theme']) . "/topnavbar.php";?>

<!-- START MAIN COLUMN -->
<div class="container-fluid" style="padding-top: 10px;">
  <div class="row">
  <!-- START LEFT COLUMN -->
  <?php if ($config["LEFTNAV"]) {?>
    <div class="col-sm-2 d-none d-sm-block sidenav">
  <?php Blocks::left();?>
     </div>
  <?php }?>
  <!-- END LEFT COLUMN -->
  <!-- START MIDDLE COLUMN -->
  <?php if ($config["MIDDLENAV"]) {?>
    <div class="col-sm-8">
  <?php Blocks::middle();?>
  <?php }?>