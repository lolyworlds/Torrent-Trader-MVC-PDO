<?php

class Users
{
    public $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    private function __clone()
    {}

    public static function coloredname($name)
    {
        $db = new Database();
        $classy = $db->run("SELECT u.class, u.donated, u.warned, u.enabled, g.Color, g.level, u.uploaded, u.downloaded FROM `users` `u` INNER JOIN `groups` `g` ON g.group_id=u.class WHERE username ='" . $name . "'")->fetch();
        $gcolor = $classy->Color;
        if ($classy->donated > 0) {
            $star = "<img src='" . URLROOT . "/assets/images/donor.png' alt='donated' border='0' width='15' height='15'>";
        } else {
            $star = "";
        }
        if ($classy->warned == "yes") {
            $warn = "<img src='" . URLROOT . "/assets/images/warn.png' alt='Warn' border='0'>";
        } else {
            $warn = "";
        }
        if ($classy->enabled == "no") {
            $disabled = "<img src='" . URLROOT . "/assets/images/disabled.png' title='Disabled' border='0'>";
        } else {
            $disabled = "";
        }
        return stripslashes("<font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font>");
    }

}
