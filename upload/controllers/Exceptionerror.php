<?php
  class Exceptionerror extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
		// Set Current User
		// $curuser = $this->userModel->setCurrentUser();
		// Set Current User
		// $db = new Database;

dbconn();
global $site_config, $CURUSER;
dbconn();
loggedinonly ();
     show_error_msg(T_("ERROR"), T_("Oops somwthing went wrong, Admin have been notified if this continues please contact a member of staff. Thank you"), 1);

}
}