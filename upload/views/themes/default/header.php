<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="<?php echo $site_config["CHARSET"]; ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="M-jay" />
  <meta name="generator" content="TorrentTrader <?php echo $site_config['ttversion']; ?>" />
  <meta name="description" content="TorrentTrader is a feature packed and highly customisable PHP/PDO/MVC Based BitTorrent tracker. Featuring intergrated forums, and plenty of administration options. Please visit www.torrenttrader.xyx for the support forums. " />
  <meta name="keywords" content="https://github.com/M-jay84/Torrent-Trader-MVC-PDO-OOP" />

  <title><?php echo $title; ?></title>
  <link rel="shortcut icon" href="<?php echo $site_config["SITEURL"]; ?>/views/themes/<?php echo $THEME; ?>/images/favicon.ico" />
  <!-- Bootstrap core CSS -->
  <link href="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/css/bootstrap.min.css" rel="stylesheet">
  <!-- TT Custom CSS any edits must go here-->
  <link href="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/css/customstyle.css" rel="stylesheet">
  <!-- DELETE line below only for github -->
  <link href="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/theme.css" rel="stylesheet">
  <!-- Fonts -->
  <link href="stylesheet" href="<?php echo TTURL; ?>/views/themes/<?php echo $THEME; ?>/css/font-awesome.min.css">

</head>

<body>
<?php require "views/themes/" . $THEME . "/topnavbar.php";?>
  <!-- START MAIN COLUMN -->
  <div class="container-fluid">
  <div class="row content">
  <!-- START LEFT COLUMN -->
  <?php if ($site_config["LEFTNAV"]) {?>
  <div class="col-sm-2 d-none d-sm-block sidenav">
  <?php leftblocks();?>
  </div>
  <?php }?>
  <!-- END LEFT COLUMN -->
  <!-- START MIDDLE COLUMN -->
  <?php if ($site_config["MIDDLENAV"]) {?>
  <div class="col-sm-8">
  <?php middleblocks();?>
  <?php }?>