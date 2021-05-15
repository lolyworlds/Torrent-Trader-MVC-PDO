<?php
class Block
{
    public static function left()
    { 
        $TTCache = new Cache();
        $db = Database::instance();
        if (($blocks = $TTCache->get("blocks_left", 900)) === false) {
            $res = $db->run("SELECT * FROM blocks WHERE position='left' AND enabled=1 ORDER BY sort");
            $blocks = array();
            while ($result = $res->fetch(PDO::FETCH_LAZY)) {
                $blocks[] = $result["name"];
            }
            $TTCache->Set("blocks_left", $blocks, 900);
        }
        foreach ($blocks as $blockfilename) {
            include "../app/views/blocks/" . $blockfilename . "_block.php";
        }
    }

    public static function right()
    {
        $TTCache = new Cache();
        $db = Database::instance();
        if (($blocks = $TTCache->get("blocks_right", 900)) === false) {
            $res = $db->run("SELECT * FROM blocks WHERE position='right' AND enabled=1 ORDER BY sort");
            $blocks = array();
            while ($result = $res->fetch(PDO::FETCH_LAZY)) {
                $blocks[] = $result["name"];
            }
            $TTCache->Set("blocks_right", $blocks, 900);
        }
        foreach ($blocks as $blockfilename) {
            include "../app/views/blocks/" . $blockfilename . "_block.php";
        }
    }

    public static function middle()
    {
        $TTCache = new Cache();
        $db = Database::instance();
        if (($blocks = $TTCache->get("blocks_middle", 900)) === false) {
            $res = $db->run("SELECT * FROM blocks WHERE position='middle' AND enabled=1 ORDER BY sort");
            $blocks = array();
            while ($result = $res->fetch(PDO::FETCH_LAZY)) {
                $blocks[] = $result["name"];
            }
            $TTCache->Set("blocks_middle", $blocks, 900);
        }
        foreach ($blocks as $blockfilename) {
            include "../app/views/blocks/" . $blockfilename . "_block.php";
        }
    }

    public static function resortleft()
    {
        $db = Database::instance();
        $sortleft = $db->run("SELECT sort, id FROM blocks WHERE position='left' AND enabled=1 ORDER BY sort ASC");
        $i = 1;
        while ($sort = $sortleft->fetch(PDO::FETCH_ASSOC)) {
            $db->run("UPDATE blocks SET sort = $i WHERE id=" . $sort["id"]);
            $i++;
        }
    }
    
    public static function resortmiddle()
    {
        $db = Database::instance();
        $sortmiddle = $db->run("SELECT sort, id FROM blocks WHERE position='middle' AND enabled=1 ORDER BY sort ASC");
        $i = 1;
        while ($sort = $sortmiddle->fetch(PDO::FETCH_ASSOC)) {
            $db->run("UPDATE blocks SET sort = $i WHERE id=" . $sort["id"]);
            $i++;
        }
    }
    
    public static function resortright()
    {
        $db = Database::instance();
        $sortright = $db->run("SELECT sort, id FROM blocks WHERE position='right' AND enabled=1 ORDER BY sort ASC");
        $i = 1;
        while ($sort = $sortright->fetch(PDO::FETCH_ASSOC)) {
            $db->run("UPDATE blocks SET sort = $i WHERE id=" . $sort["id"]);
            $i++;
        }
    }

    public static function begin($caption = "-", $align = "justify")
    {
        $blockId = 'b-' . sha1($caption);
        ?>
        <div class="card">
            <div class="card-header">
                <?php echo $caption ?>
                <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
            </div>
            <div class="card-body slidingDiv<?php echo $blockId; ?>">
        <?php
}

    public static function end()
    {
        ?>
            </div>
        </div><br />
        <?php
}

}