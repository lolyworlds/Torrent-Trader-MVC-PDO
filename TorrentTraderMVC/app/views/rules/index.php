<div class="card">
    <div class="card-header">
        <?php echo Lang::T("SITE_RULES"); ?>
    </div>
    <div class="card-body">

<?php foreach ($data['res'] as $row) { ?>
    <?php if ($row->public == "yes") { ?>

    <div class="card">
        <div class="card-header">
        <?php echo $row->title; ?>
        </div>
        <div class="card-body">
        <?php echo format_comment($row->text); ?>
        </div>
    </div>

    <?php } else if ($row->public == "no" && $row->class <= $_SESSION["class"]) { ?>

    <div class="card">
        <div class="card-header">
        <?php echo $row->title; ?>
        </div>
        <div class="card-body">
        <?php echo format_comment($row->text); ?>
        </div>
    </div>

    <?php }?>
<?php }?>

    </div>
</div>