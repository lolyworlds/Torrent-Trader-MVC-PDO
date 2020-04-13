<?php
  class Rules extends Controller {
    
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
  stdhead( T_("SITE_RULES") );
  
  $res = DB::run("SELECT * FROM `rules` ORDER BY `id`");
  while ($row = $res->fetch(PDO::FETCH_ASSOC))
  {
      if ($row["public"] == "yes")
      {
          begin_frame($row["title"]);
          echo format_comment($row["text"]); 
          end_frame();
      }
      else if ($row["public"] == "no" && $row["class"] <= $CURUSER["class"])
      {
          begin_frame($row["title"]);
          echo format_comment($row["text"]);
          end_frame();
      }
  }
  
  stdfoot();
}
}