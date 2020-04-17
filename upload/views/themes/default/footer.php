</div>
<!-- END MIDDLE COLUMN -->
<!-- START RIGHT COLUMN -->
    <?php if ($site_config["RIGHTNAV"]){ ?>
<div class="col-sm-2 sidenav">
    <?php rightblocks(); ?>
</div>
    <?php } ?>
<!-- END RIGHT COLUMN -->
</div>
<?php require TTROOT . "/views/themes/" . $THEME . "/bottomnavbar.php";?>
</div>

<!-- JS -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $site_config["SITEURL"]; ?>/helpers/java_klappe.js"></script>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>
</body>

</html>
<?php ob_end_flush(); ?>