<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $config['SITENAME'] . T_("SHOUTBOX"); ?></title>
<?php /* If you do change the refresh interval, you should also change index.php printf(T_("SHOUTBOX_REFRESH"), 5) the 5 is in minutes */?>
<meta http-equiv="refresh" content="300" />
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <!-- Bootstrap -->
  <link rel="stylesheet" href="<?php echo TTURL; ?>/themes/<?php echo $_SESSION['stylesheet'] ?: $config['default_theme']; ?>/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <!-- TT old JS -->
  <script src="<?php echo TTURL; ?>/themes/<?php echo $_SESSION['stylesheet'] ?: $config['default_theme']; ?>/js/java_klappe.js"></script>
  <!-- TT Custom CSS, any edits must go here-->
  <link href="<?php echo TTURL; ?>/themes/<?php echo $_SESSION['stylesheet'] ?: $config['default_theme']; ?>/css/customstyle.css" rel="stylesheet">
</head>
<body class="shoutbox_body">