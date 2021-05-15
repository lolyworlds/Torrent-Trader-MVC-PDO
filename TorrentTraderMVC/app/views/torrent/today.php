<div class="card">
    <div class="card-header">
        <?php echo Lang::T("TODAYS_TORRENTS"); ?>
    </div>
    <div class="card-body">
         <b><a href='<?php echo URLROOT; ?>/search/browse?cat=<?php echo $data["id"]; ?>'><?php echo $data['name']; ?></a></b>
         <?php torrenttable($data['torrtable']); ?>
    </div>
</div>