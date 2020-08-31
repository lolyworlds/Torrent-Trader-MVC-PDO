<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="<?php echo $config["CHARSET"]; ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="M-jay" />
  <meta name="generator" content="TorrentTrader <?php echo $config['ttversion']; ?>" />
  <meta name="description" content="TorrentTrader is a feature packed and highly customisable PHP/PDO/MVC Based BitTorrent tracker. Featuring intergrated forums, and plenty of administration options. Please visit www.torrenttrader.xyx for the support forums. " />
  <meta name="keywords" content="https://github.com/M-jay84/Torrent-Trader-MVC-PDO-OOP" />

  <title><?php echo $title; ?></title>
  <link rel="shortcut icon" href="<?php echo $config["SITEURL"]; ?>/views/themes/<?php echo $_SESSION['stylesheet'] ?: $config['default_theme']; ?>/images/favicon.ico" />

  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <!-- Bootstrap -->
  <link rel="stylesheet" href="<?php echo TTURL; ?>/views/themes/<?php echo $_SESSION['stylesheet'] ?: $config['default_theme']; ?>/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <!-- TT old JS -->
  <script src="<?php echo TTURL; ?>/views/themes/<?php echo $_SESSION['stylesheet'] ?: $config['default_theme']; ?>/js/java_klappe.js"></script>
  <!-- TT Custom CSS, any edits must go here-->
  <link href="<?php echo TTURL; ?>/views/themes/<?php echo $_SESSION['stylesheet'] ?: $config['default_theme']; ?>/css/customstyle.css" rel="stylesheet">

</head>

<body>
<?php require "views/admin/navbar.php";?>
  <!-- START MAIN COLUMN -->
  <div class="container-fluid">
  <div class="row content">
  <!-- START LEFT COLUMN -->
  <?php if ($config["LEFTNAV"]) {?>
  <div class="col-sm-2 d-none d-sm-block sidenav">
  <?php include 'views/admin/left.php';?>
  </div>
  <?php }?>
  <!-- END LEFT COLUMN -->
  <!-- START MIDDLE COLUMN -->
  <?php if ($config["MIDDLENAV"]) {?>
  <div class="col-sm-8">
  <?php middleblocks();?>
  <?php }?>