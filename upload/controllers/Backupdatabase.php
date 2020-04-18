<?php
  class Backupdatabase extends Controller {
    
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
// CHECK THE ADMIN PRIVILEGES
if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
    show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}
  
 
$DBH = DB::instance ();
 
//put table names you want backed up in this array.
//leave empty to do all
$tables = array();

backup_tables($DBH, $tables);


}
}