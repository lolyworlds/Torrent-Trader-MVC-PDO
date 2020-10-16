<?php
class Intro extends Controller
{

    public function __construct()
    {
        //$this->userModel = $this->model('User');
    }

    public function index()
    {
        dbconn();
        global $config;
        stdhead(T_("intro"));
        loggedinonly();
        begin_frame("Generl Functions");
        include "views/intro/header.php";
        $data = [];
        $this->view('intro/index', $data);
        end_frame();
        stdfoot();
    }

    public function folders()
    {
        dbconn();
        global $config;
        stdhead(T_("intro"));
        loggedinonly();
        begin_frame("Folders");
        include "views/intro/header.php";
        $data = [];
        $this->view('intro/folder', $data);
        end_frame();
        stdfoot();
    }

    public function core()
    {
        dbconn();
        global $config;
        stdhead(T_("intro"));
        loggedinonly();
        begin_frame("Core");
        include "views/intro/header.php";
        $data = [];
        $this->view('intro/core', $data);
        end_frame();
        stdfoot();
    }

    public function querys()
    {
        dbconn();
        global $config;
        stdhead(T_("intro"));
        loggedinonly();
        begin_frame("Querys");
        include "views/intro/header.php";
        $data = [];
        $this->view('intro/querys', $data);
        end_frame();
        stdfoot();
    }

    public function mvc()
    {
        dbconn();
        global $config;
        stdhead(T_("intro"));
        loggedinonly();
        begin_frame("Mvc");
        include "views/intro/header.php";
        $data = [];
        $this->view('intro/mvc', $data);
        end_frame();
        stdfoot();
    }

    public function classes()
    {
        dbconn();
        global $config;
        stdhead(T_("intro"));
        loggedinonly();
        begin_frame("Classes");
        include "views/intro/header.php";
        $data = [];
        $this->view('intro/class', $data);
        end_frame();
        stdfoot();
    }

    public function errors()
    {
        dbconn();
        global $config;
        stdhead(T_("intro"));
        loggedinonly();
        begin_frame("Errors");
        include "views/intro/header.php";
        $data = [];
        $this->view('intro/error', $data);
        end_frame();
        stdfoot();
    }
}
