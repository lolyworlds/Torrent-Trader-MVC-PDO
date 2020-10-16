<?php

// BEGIN BLOCK
function begin_block($caption = "-", $align = "justify")
{
    global $THEME, $pdo, $config;

    $blockId = 'b-' . sha1($caption);
    ?>
    <div class="card">
        <div class="card-header">
            <?php echo $caption ?>
        </div>
        <div class="card-body">
    <?php
}

// END BLOCK
function end_block()
{
    global $THEME, $pdo, $config;
    ?>
        </div>
    </div><br />
    <?php
}
// resort left blocks
function resortleft()
{
    global $pdo;
    $sortleft = $pdo->run("SELECT sort, id FROM blocks WHERE position='left' AND enabled=1 ORDER BY sort ASC");
    $i = 1;
    while ($sort = $sortleft->fetch(PDO::FETCH_ASSOC)) {
        $pdo->run("UPDATE blocks SET sort = $i WHERE id=" . $sort["id"]);
        $i++;
    }
}
// resort middle blocks
function resortmiddle()
{
    global $pdo;
    $sortmiddle = $pdo->run("SELECT sort, id FROM blocks WHERE position='middle' AND enabled=1 ORDER BY sort ASC");
    $i = 1;
    while ($sort = $sortmiddle->fetch(PDO::FETCH_ASSOC)) {
        $pdo->run("UPDATE blocks SET sort = $i WHERE id=" . $sort["id"]);
        $i++;
    }
}
// resort right blocks
function resortright()
{
    global $pdo;
    $sortright = $pdo->run("SELECT sort, id FROM blocks WHERE position='right' AND enabled=1 ORDER BY sort ASC");
    $i = 1;
    while ($sort = $sortright->fetch(PDO::FETCH_ASSOC)) {
        $pdo->run("UPDATE blocks SET sort = $i WHERE id=" . $sort["id"]);
        $i++;
    }
}
