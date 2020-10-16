<?php
class Blocks
{
    
    public static function left()
    {
        global $config, $THEME, $LANGUAGE;
        $TTCache = new Cache();
        $pdo = Database::instance();
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
    
    public static function right()
    {
        global $config, $THEME, $LANGUAGE;
        $TTCache = new Cache();
        $pdo = Database::instance();
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
    
    public static function middle()
    {
        global $config, $THEME, $LANGUAGE;
        $TTCache = new Cache();
        $pdo = Database::instance();
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
}
