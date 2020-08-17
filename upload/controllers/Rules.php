<?php
  class Rules extends Controller {
    
    public function __construct(){
         $this->rulesModel = $this->model('Rule');
    }
    
    public function index(){

  dbconn();
  global $config;
  stdhead( T_("SITE_RULES") );
  
  $res = $this->rulesModel->getRules();
  foreach ($res as $row)
  {
      if ($row->public == "yes")
      {
          begin_frame($row->title);
          echo format_comment($row->text); 
          end_frame();
      }
      else if ($row->public == "no" && $row->class <= $_SESSION["class"])
      {
          begin_frame($row->title);
          echo format_comment($row->text);
          end_frame();
      }
  }
  
  stdfoot();
}
}