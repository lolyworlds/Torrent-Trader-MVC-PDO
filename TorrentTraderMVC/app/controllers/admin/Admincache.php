<?php
class Admincache extends Controller
{

    public function __construct()
    {
        Auth::user(); // should check admin here
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        $title = Lang::T("_BLC_MAN_");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin('Purge');
        # Prune Block Cache.
        $TTCache = new Cache();
        $TTCache->Delete("blocks_left");
        $TTCache->Delete("blocks_middle");
        $TTCache->Delete("blocks_right");
        $TTCache->Delete("latestuploadsblock");
        $TTCache->Delete("mostactivetorrents_block");
        $TTCache->Delete("newestmember_block");
        $TTCache->Delete("seedwanted_block");
        $TTCache->Delete("usersonline_block");
        $TTCache->Delete("request_block");
        echo 'Purge Cache Successful';
        Style::end();
        require APPROOT . '/views/admin/footer.php';
        Redirect::autolink(URLROOT . "/admincp", 'Purge Cache Successful');
    }
}