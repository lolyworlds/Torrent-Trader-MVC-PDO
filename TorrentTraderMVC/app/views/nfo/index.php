<?php if ($_SESSION["edit_torrents"] == "yes") { ?>
    <div class="card">
    <div class="card-header">
        <?php echo $data['titleedit']; ?>
    </div>
    <div class="card-body">
<?php } else { ?>
    <div class="card">
    <div class="card-header">
        <?php echo $data['title']; ?>
    </div>
    <div class="card-body">
<?php  } ?>
    <textarea class='nfo' style='width:98%;height:100%;' rows='50' cols='20' readonly='readonly'>
<?php echo stripslashes($data['nfo']); ?></textarea>
    </div>
    </div><br />