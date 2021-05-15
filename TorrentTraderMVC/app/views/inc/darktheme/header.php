<?php
// Micro Time
$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="<?php echo DB_CHAR; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="M-jay" />
    <meta name="generator" content="TorrentTrader <?php echo VERSION; ?>" />
    <meta name="description" content="TorrentTrader is a feature packed and highly customisable PHP/PDO/MVC Based BitTorrent tracker. Featuring intergrated forums, and plenty of administration options. Please visit www.torrenttrader.xyx for the support forums. " />
    <meta name="keywords" content="https://github.com/M-jay84/Torrent-Trader-MVC-PDO-OOP" />
    <title><?php echo $data['title']; ?></title>
  
    <!-- Bootstrap & core CSS -->
    <link href="<?php echo URLROOT; ?>/assets/themes/darktheme/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URLROOT; ?>/assets/vendor/font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- TT Custom CSS, any edits must go here-->
    <link href="<?php echo URLROOT; ?>/assets/themes/darktheme/customstyle.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/sceditor/minified/themes/defaultdark.min.css" id="theme-style" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/monokai-sublime.min.css">
  </head>
<body>
  <?php require APPROOT . '/views/inc/default/navbar.php'; ?>
<!-- START MAIN COLUMN -->
<div class="container-fluid" style="padding-top: 10px;">
  <div class="row">
  <!-- START LEFT COLUMN -->
  <?php if (LEFTNAV) {?>
    <div class="col-sm-2 d-none d-sm-block sidenav">
  <?php Block::left();?>
     </div>
  <?php }?>
  <!-- END LEFT COLUMN -->
  <!-- START MIDDLE COLUMN -->
  <?php if (MIDDLENAV) {?>
    <div class="col-sm-8">
  <?php Block::middle();?>
  <?php }?>