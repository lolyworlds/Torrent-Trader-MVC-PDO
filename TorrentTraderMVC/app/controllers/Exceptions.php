<?php
class Exceptions extends Controller
{
    public function __construct()
    {}

    public function index()
    {
        Session::flash('info', Lang::T("Oops somwthing went wrong, Admin have been notified if this continues please contact a member of staff. Thank you"), URLROOT."/index");
    }

    public function admin()
    {
        Auth::user();
        if (!$_SESSION['class'] > 6 || $_SESSION["control_panel"] != "yes") {
            show_error_msg(Lang::T("ERROR"), Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
        }
        $exceptionfilelocation = LOGGER."/exception_log.txt";
        $filegetcontents = file_get_contents($exceptionfilelocation);
        $errorlog = htmlspecialchars($filegetcontents);

        function make_content_file($exceptionfilelocation, $content, $opentype = "w")
        {
            $fp_file = fopen($exceptionfilelocation, $opentype);
            fputs($fp_file, $content);
            fclose($fp_file);
        }

        if ($_POST) {
            $newcontents = $_POST['newcontents'];
            make_content_file($exceptionfilelocation, $newcontents);
        }
        $filecontents = file_get_contents($exceptionfilelocation);
        
        $data = [
            'filecontents' => $filecontents,
            'errorlog' => $errorlog,
        ];
        $this->view('error/admin', $data);
    }
}