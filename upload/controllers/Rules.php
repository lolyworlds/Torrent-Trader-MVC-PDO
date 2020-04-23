<?php
  class Rules extends Controller {
    
    public function __construct(){
         $this->rulesModel = $this->model('Rule');
    }
    
    public function index(){

  dbconn();
global $site_config, $CURUSER;
  stdhead( T_("SITE_RULES") );
  
  $res = $this->rulesModel->getRules();
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