<?php
  class Intro extends Controller {
    
    public function __construct(){
        // $this->userModel = $this->model('User');
    }
    
    public function index(){
  dbconn();
  global $config;
  stdhead( T_("intro") );
  loggedinonly ();
  begin_frame("Generl Functions");
  include("views/intro/header.php");
  $data = [ ];
  $this->view('intro/index', $data);
  end_frame();
  stdfoot();
}

public function querys(){
  dbconn();
  global $config;
  stdhead( T_("intro") );
  loggedinonly ();
  begin_frame("Querys");
  include("views/intro/header.php");
  $data = [ ];
  $this->view('intro/querys', $data);
  end_frame();
  stdfoot();
}

public function models(){
  dbconn();
  global $config;
  stdhead( T_("intro") );
  loggedinonly ();
  begin_frame("Models");
  include("views/intro/header.php");
  $data = [ ];
  $this->view('intro/model', $data);
  end_frame();
  stdfoot();
}

public function controllers(){
  dbconn();
  global $config;
  stdhead( T_("intro") );
  loggedinonly ();
  begin_frame("Controllers");
  include("views/intro/header.php");
  $data = [ ];
  $this->view('intro/controller', $data);
  end_frame();
  stdfoot();
}

public function classes(){
  dbconn();
  global $config;
  stdhead( T_("intro") );
  loggedinonly ();
  begin_frame("Classes");
  include("views/intro/header.php");
  $data = [ ];
  $this->view('intro/class', $data);
  end_frame();
  stdfoot();
}

public function views(){
  dbconn();
  global $config;
  stdhead( T_("intro") );
  loggedinonly ();
  begin_frame("Views");
  include("views/intro/header.php");
  $data = [ ];
  $this->view('intro/view', $data);
  end_frame();
  stdfoot();
}

public function errors(){
  dbconn();
  global $config;
  stdhead( T_("intro") );
  loggedinonly ();
  begin_frame("Errors");
  include("views/intro/header.php");
  $data = [ ];
  $this->view('intro/error', $data);
  end_frame();
  stdfoot();
}
  }