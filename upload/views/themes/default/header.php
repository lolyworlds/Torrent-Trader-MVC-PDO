<!doctype html>
<html lang="en">
<head>
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Theme css -->
    <link rel="shortcut icon" href="<?php echo $site_config["SITEURL"]; ?>/views/themes/default/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="<?php echo $site_config["SITEURL"]; ?>/views/themes/default/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $site_config["SITEURL"]; ?>/views/themes/default/theme.css" />
</head>

<body>
<?php require TTROOT."/views/themes/" . $THEME . "/topnavbar.php";?>
  <div class="container-fluid">
  <div class="row content">
  <!-- START LEFT COLUMN -->
  <?php if ($site_config["LEFTNAV"]) {?>
  <div class="col-sm-2 sidenav">
  <?php leftblocks();?>
  </div>
  <?php }?>
  <!-- END LEFT COLUMN -->
  <!-- START MIDDLE COLUMN -->
  <?php if ($site_config["MIDDLENAV"]) {?>
  <div class="col-sm-8 middlebit">
  <?php middleblocks();?>
  <?php }?>