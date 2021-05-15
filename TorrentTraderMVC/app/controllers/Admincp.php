<?php
class Admincp extends Controller
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
        if (!$_SESSION['class'] > 5 || $_SESSION["control_panel"] != "yes") {
            show_error_msg(Lang::T("ERROR"), Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
        }
        $title = 'admin';
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        echo '<div class="border border-primary">';
        echo '<center>';
        echo '<b>Welcome To The Staff Panel</b>';
        echo '</center>';
        echo '</div>';
        require APPROOT . '/views/admin/footer.php';
    }
    
}