<?php
// Function For Left Blocks
function leftblocks()
{
    global $site_config, $CURUSER, $THEME, $LANGUAGE, $pdo, $blockfilename; //Define globals
    $TTCache = new Cache();
    if (($blocks = $TTCache->get("blocks_left", 900)) === false) {
        $res = $pdo->run("SELECT * FROM blocks WHERE position='left' AND enabled=1 ORDER BY sort");
        $blocks = array();
        while ($result = $res->fetch(PDO::FETCH_LAZY)) {
            $blocks[] = $result["name"];
        }
        $TTCache->Set("blocks_left", $blocks, 900);
    }

    foreach ($blocks as $blockfilename) {
        include "blocks/" . $blockfilename . "_block.php";
    }
}
// Function For Right Blocks
function rightblocks()
{
    global $site_config, $CURUSER, $THEME, $LANGUAGE, $pdo, $blockfilename; //Define globals
    $TTCache = new Cache();
    if (($blocks = $TTCache->get("blocks_right", 900)) === false) {
        $res = $pdo->run("SELECT * FROM blocks WHERE position='right' AND enabled=1 ORDER BY sort");
        $blocks = array();
        while ($result = $res->fetch(PDO::FETCH_LAZY)) {
            $blocks[] = $result["name"];
        }
        $TTCache->Set("blocks_right", $blocks, 900);
    }

    foreach ($blocks as $blockfilename) {
        include "blocks/" . $blockfilename . "_block.php";
    }
}
// Function For Middle Blocks
function middleblocks()
{
    global $site_config, $CURUSER, $THEME, $LANGUAGE, $pdo; //Define globals
    $TTCache = new Cache();
    if (($blocks = $TTCache->get("blocks_middle", 900)) === false) {
        $res = $pdo->run("SELECT * FROM blocks WHERE position='middle' AND enabled=1 ORDER BY sort");
        $blocks = array();
        while ($result = $res->fetch(PDO::FETCH_LAZY)) {
            $blocks[] = $result["name"];
        }
        $TTCache->Set("blocks_middle", $blocks, 900);
    }

    foreach ($blocks as $blockfilename) {
        include "blocks/" . $blockfilename . "_block.php";
    }
}
// BEGIN BLOCK
function begin_block($caption = "-", $align = "justify")
{
    global $THEME, $pdo, $site_config;

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
    global $THEME, $pdo, $site_config;
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
