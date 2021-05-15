<?php
class Stylesheet extends Controller
{

    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
    }

    public function index()
    {
        $updateset = array();
        $stylesheet = $_POST['stylesheet'];
        $language = $_POST['language'];
        $updateset[] = "stylesheet = '$stylesheet'";
        $updateset[] = "language = '$language'";
        if (count($updateset)) {
            DB::run("UPDATE `users` SET " . implode(', ', $updateset) . " WHERE `id` =?", [$_SESSION["id"]]);
        }
        if (empty($_SERVER["HTTP_REFERER"])) {
            Redirect::to(URLROOT."/home");
            return;
        }
        Redirect::to($_SERVER["HTTP_REFERER"]);
    }
}