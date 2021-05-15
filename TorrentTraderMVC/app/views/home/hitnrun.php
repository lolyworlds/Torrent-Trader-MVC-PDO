<div class="card">
        <div class="card-header">
            <?php echo Lang::T("Warning"); ?>
            </div>
        <div class="card-body">
        <table><tr>
Hey <?php echo $_SESSION['username']; ?>,
you have <?php echo  $hnr; ?> recording <?php echo ($hnr2 > 1 ? "s" : "") ?> for Hit and Run!&nbsp; 
View the recordings into <a href='<?php echo URLROOT; ?>/snatched'><b>Your Snatch List</b></a>
You must to keep seeding or you can <a href='<?php echo URLROOT; ?>/bonus/trade'><b>Trade to Delete</b></a>
<?php echo  ($hnr2 > 1 ? "these recordings" : "this recording") ?> with Seed Bonus or Upload
</td></tr></table
    </div><br />