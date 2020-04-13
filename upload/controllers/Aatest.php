<?php
  class Aatest extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
		// Set Current User
		// $curuser = $this->userModel->setCurrentUser();
		// Set Current User
		// $db = new Database;
dbconn(true);
global $site_config, $CURUSER;

loggedinonly ();
stdhead(T_("HOME"));


	begin_frame(T_("DISCLAIMER"));
	
	
    echo '';
	
	
	end_frame();


stdfoot();
	}
}