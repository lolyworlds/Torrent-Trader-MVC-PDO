<?php
class Admincleanup extends Controller
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
        $now = TimeDate::gmtime();
        DB::run("UPDATE tasks SET last_time=$now WHERE task='cleanup'");
        require_once APPROOT . "/helpers/cleanup_helper.php";
        do_cleanup();
        Redirect::autolink(URLROOT . '/admincp', Lang::T("FORCE_CLEAN_COMPLETED"));
    }

}