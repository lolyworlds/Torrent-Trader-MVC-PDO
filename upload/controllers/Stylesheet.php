<?php
  class Stylesheet extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
 dbconn();
global $config;
 loggedinonly();
 
 $updateset = array();
 
 $stylesheet = $_POST['stylesheet'];
 $language = $_POST['language'];
 
 if (is_valid_id($stylesheet))
     $updateset[] = "stylesheet = '$stylesheet'";
 if (is_valid_id($language))
     $updateset[] = "language = '$language'";

 if (count($updateset))
     DB::run("UPDATE `users` SET " . implode(', ', $updateset) . " WHERE `id` =?", [$_SESSION["id"]]);
 
 if (empty($_SERVER["HTTP_REFERER"]))
 {
     header("Location: ".TTURL."/index.php"); 
     return;
 }     
 
 header("Location: " . $_SERVER["HTTP_REFERER"]);
}
}