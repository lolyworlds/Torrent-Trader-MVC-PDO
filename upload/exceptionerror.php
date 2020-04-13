<?php
require_once("backend/functions.php");
dbconn();
loggedinonly ();
     show_error_msg(T_("ERROR"), T_("Oops somwthing went wrong, Admin have been notified if this continues please contact a member of staff. Thank you"), 1);
