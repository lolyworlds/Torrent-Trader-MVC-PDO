<div class="card">
        <div class="card-header">
        <?php echo Lang::T("LATEST_TORRENTS"); ?>
            </div>
        <div class="card-body">
<br /><center><a href='<?php echo URLROOT; ?>/search/browse'><?php echo Lang::T("BROWSE_TORRENTS") ?></a> - <a href='<?php echo URLROOT; ?>/search'><?php echo Lang::T("SEARCH_TORRENTS"); ?></a></center><br />
<?php torrenttable1($data['torrtable']); ?>
</div>
    </div><br />