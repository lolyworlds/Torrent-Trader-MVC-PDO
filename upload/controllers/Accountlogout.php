<?php
  class Accountlogout extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
		// Set Current User
		// $curuser = $this->userModel->setCurrentUser();
		// Set Current User
		// $db = new Database;

 dbconn();
 
 logoutcookie();
 header("Location: index.php");
}
}