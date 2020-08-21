<?php
  class Users extends Controller
  {
      public function __construct()
      {
          // $this->userModel = $this->model('User');
      }
    
      public function index()
      {
          dbconn();
          global $config, $THEME, $pdo;
          loggedinonly();
          $id = (int)$_GET["id"];
          if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            autolink(TTURL."/index", T_("You dont have permission"));
          } else {
           //   echo 'im staff or curuser';
          }
              stdhead(T_("user"));
          ?>
          <div class="card">
          <div class="card-header">
              <?php echo T_("Profile"); ?>
          </div>
          <div class="card-body">
you should not see this, there is a issue with a link try 
<a href='<?php echo TTURL; ?>/users/profile?id=<?php echo $_SESSION['id']; ?>'>adding id</a>

            </div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php";
      }



      public function profile()
      {
          dbconn();
          global $config;
          loggedinonly();
  
          $id = (int) $_GET["id"];
  
          if (!is_valid_id($id)) {
              show_error_msg(T_("NO_SHOW_DETAILS"), "Bad ID.", 1);
          }
  
          $user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
          if (!$user) {
              show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID") . " $id.", 1);
          }
  
          // view own but not others
          if ($_SESSION["view_users"] == "no" && $_SESSION["id"] != $id) {
              show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);
          }
  
          // user not ready to be seen yet
          if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $_SESSION["edit_users"] == "no") {
              show_error_msg(T_("ERROR"), T_("NO_ACCESS_ACCOUNT_DISABLED"), 1);
          }

          //===| Start Blocked Users
$blocked = DB::run("SELECT id FROM friends WHERE userid=$user[id] AND friend='enemy' AND friendid=$_SESSION[id]");
$show = $blocked->rowCount();
if ($show != 0 && $_SESSION["control_panel"] != "yes")
    show_error_msg("Error", "<div style='margin-top:10px; margin-bottom:10px' align='center'><font size=2 color=#FF2000><b>You're blocked by this member and you can not see his profile!</b></font></div>", 1);
//===| End Blocked Users
  
          // $country
          $res = DB::run("SELECT name FROM countries WHERE id=? LIMIT 1", [$user['country']]);
          if ($res->rowCount() == 1) {
              $arr = $res->fetch();
              $country = "$arr[name]";
          }
          if (!$country) {
              $country = "<b>Unknown</b>";
          }
  
          // $ratio
          if ($user["downloaded"] > 0) {
              $ratio = $user["uploaded"] / $user["downloaded"];
          } else {
              $ratio = "---";
          }
  
          $numtorrents = get_row_count("torrents", "WHERE owner = $id");
          $numcomments = get_row_count("comments", "WHERE user = $id");
          $numforumposts = get_row_count("forum_posts", "WHERE userid = $id");
  
          $avatar = htmlspecialchars($user["avatar"]);
          if (!$avatar) {
              $avatar = $config["SITEURL"] . "/images/default_avatar.png";
          }
  
          $usersignature = stripslashes($user["signature"]); // todo
  
          stdhead("User CP");
          // im staff i see all details
          if ($_SESSION['class'] >= $config['Moderator']) {
              begin_frame(sprintf(T_("USER_DETAILS_FOR"), class_user_colour($user["username"])));
              include 'views/user/myprofile.php';
              end_frame();
              stdfoot();
              // im not staff but i should see my own stuff
          } elseif ($id == $_SESSION['id'] && $_SESSION['class'] < $config['Moderator']) {
              begin_frame(sprintf(T_("USER_DETAILS_FOR"), class_user_colour($user["username"])));
              include 'views/user/myprofile.php';
              end_frame();
              stdfoot();
              // that leaves view users basics (if we have permission)
          } else {
              begin_frame(sprintf(T_("USER_DETAILS_FOR"), class_user_colour($user["username"])));
              include 'views/user/profile.php';
              end_frame();
              stdfoot();
          }
      }

      public function preferences()
      {
          dbconn();
          global $config, $THEME, $tzs, $pdo;
          loggedinonly();
          $id = (int)$_GET["id"];
          if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            autolink(TTURL."/index", T_("You dont have permission"));
          } else {
           //   echo 'im staff or curuser';
          }
if ($_POST){
    $acceptpms = $_POST["acceptpms"];
    $pmnotif = $_POST["pmnotif"];
    $privacy = $_POST["privacy"];
    $notifs = ($pmnotif == 'yes' ? "[pm]" : "");
    if ($_POST['resetpasskey']) {
        $passkey = '';
    }
    $timezone = (int)$_POST['tzoffset'];

    if ($acceptpms == "yes") {
        $acceptpms = 'yes';
    } else {
        $acceptpms = 'no';
    }
    
    $hideshoutbox = ($_POST["hideshoutbox"] == "yes") ? "yes" : "no";

        $pdo->run("UPDATE users 
        SET hideshoutbox=?, acceptpms=?, privacy=?, notifs=?, passkey=?, tzoffset=?
        WHERE id =?",[$hideshoutbox, $acceptpms, $privacy, $notifs, $passkey, $timezone, $id]);
        autolink(TTURL."/users/preferences?id=$id", "Success");
        
}
$user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
stdhead(T_("user"));
            ?>
            <div class="card">
            <div class="card-header">
                <?php echo T_("Preferences"); ?>
            </div>
            <div class="card-body">
            <?php require_once "views/user/preferencelist.php"; ?>

            <form action="<?php echo TTURL; ?>/users/preferences?id=<?php echo $id; ?>" method="post">
	<table class="f-border" cellspacing="0" cellpadding="5" max-width="100%" align="center"> <?php
              $acceptpms = $user["acceptpms"] == "yes";
              print("<tr><td align='right' class='alt2'><b>" . T_("ACCEPT_PMS") . ":</b> </td><td class='alt2'><input type='radio' name='acceptpms'" . ($acceptpms ? " checked='checked'" : "") .
      " value='yes' /><b>".T_("FROM_ALL")."</b> <input type='radio' name='acceptpms'" .
      ($acceptpms ? "" : " checked='checked'") . " value='no' /><b>" . T_("FROM_STAFF_ONLY") . "</b><br /><i>".T_("ACCEPTPM_WHICH_USERS")."</i></td></tr>");

              print("<tr><td align='right' class='alt3'><b>" . T_("ACCOUNT_PRIVACY_LVL") . ":</b> </td><td align='left' class='alt3'>". priv("normal", "<b>" . T_("NORMAL") . "</b>") . " " . priv("low", "<b>" . T_("LOW") . "</b>") . " " . priv("strong", "<b>" . T_("STRONG") . "</b>") . "<br /><i>".T_("ACCOUNT_PRIVACY_LVL_MSG")."</i></td></tr>");
              print("<tr><td align='right' class='alt2'><b>" . T_("EMAIL_NOTIFICATION") . ":</b> </td><td align='left' class='alt2'><input type='checkbox' name='pmnotif' " . (strpos($user['notifs'], "[pm]") !== false ? " checked='checked'" : "") .
       " value='yes' /><b>" . T_("PM_NOTIFY_ME") . "</b><br /><i>".T_("EMAIL_WHEN_PM")."</i></td></tr>");
       print("<tr><td align='right' class='alt2'><b>".T_("RESET_PASSKEY").":</b> </td><td align='left' class='alt2'><input type='checkbox' name='resetpasskey' value='1' />&nbsp;<i>".T_("RESET_PASSKEY_MSG").".</i></td></tr>");

       if ($config["SHOUTBOX"]) {
           print("<tr><td align='right' class='table_col3'><b>".T_("HIDE_SHOUT").":</b></td><td align='left' class='table_col3'><input type='checkbox' name='hideshoutbox' value='yes' ".($user['hideshoutbox'] == 'yes' ? 'checked="checked"' : '')." />&nbsp;<i>".T_("HIDE_SHOUT")."</i></td></tr> ");
       }
       ksort($tzs);
       reset($tzs);
       while (list($key, $val) = thisEach($tzs)) {
           if ($user["tzoffset"] == $key) {
               $tz .= "<option value=\"$key\" selected='selected'>$val[0]</option>\n";
           } else {
               $tz .= "<option value=\"$key\">$val[0]</option>\n";
           }
       }

       print("<tr><td align='right' class='alt3'><b>".T_("TIMEZONE").":</b> </td><td align='left' class='alt3'><select name='tzoffset'>$tz</select></td></tr>"); ?>

<tr><td colspan="2" align="center"><button type='submit' class='btn btn-sm btn-primary'><?php echo T_("SUBMIT"); ?></button> <input type="reset" value="<?php echo T_("REVERT"); ?>" /></td></tr>
	</table></form>
            </div>
            </div>
            </div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php";
      }

      public function details()
      {
          dbconn();
          global $config, $THEME, $pdo;
          loggedinonly();
          $id = (int)$_GET["id"];
          if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            autolink(TTURL."/index", T_("You dont have permission"));
          } else {
           //   echo 'im staff or curuser';
          }
          if ($_POST) {
            $stylesheet = $_POST["stylesheet"];
            $client = strip_tags($_POST["client"]);
            $age = $_POST["age"];
            $gender = $_POST["gender"];
            $country = $_POST["country"];
            $teams = $_POST["teams"];

                  // Save New details.
                  DB::run("UPDATE users 
                  SET stylesheet=?, client=?, age=?, gender=?, country=?, team=?   
                  WHERE id=?", [$stylesheet, $client, $age, $gender, $country, $teams, $id]);
                  autolink(TTURL."/users/details?id=$id", "Success");
              }

        $user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);    
          stdhead(T_("user"));
          ?>
          <div class="card">
          <div class="card-header">
              <?php echo T_("Details"); ?>
          </div>
          <div class="card-body">
          <?php require_once "views/user/detaillist.php"; ?>
          <form action="<?php echo TTURL; ?>/users/details?id=<?php echo $id; ?>" method="post">
	<table class="f-border" cellspacing="0" cellpadding="5" max-width="100%" align="center">
	<?php

    $ss_r = $pdo->run("SELECT * from stylesheets");
              $ss_sa = array();
              while ($ss_a = $ss_r->fetch(PDO::FETCH_LAZY)) {
                  $ss_id = $ss_a["id"];
                  $ss_name = $ss_a["name"];
                  $ss_sa[$ss_name] = $ss_id;
              }
              ksort($ss_sa);
              reset($ss_sa);
              while (list($ss_name, $ss_id) = thisEach($ss_sa)) {
                  if ($ss_id == $user["stylesheet"]) {
                      $ss = " selected='selected'";
                  } else {
                      $ss = "";
                  }
                  $stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
              }

              $countries = "<option value='0'>----</option>\n";
              $ct_r = $pdo->run("SELECT id,name from countries ORDER BY name");
              while ($ct_a = $ct_r->fetch(PDO::FETCH_LAZY)) {
                  $countries .= "<option value='$ct_a[id]'" . ($user["country"] == $ct_a['id'] ? " selected='selected'" : "") . ">$ct_a[name]</option>\n";
              }

              $teams = "<option value='0'>--- ".T_("NONE_SELECTED")." ----</option>\n";
              $sashok = $pdo->run("SELECT id,name FROM teams ORDER BY name");
              while ($sasha = $sashok->fetch(PDO::FETCH_LAZY)) {
                  $teams .= "<option value='$sasha[id]'" . ($user["team"] == $sasha['id'] ? " selected='selected'" : "") . ">$sasha[name]</option>\n";
              }

              $gender = "<option value='Male'" . ($user["gender"] == "Male" ? " selected='selected'" : "") . ">" . T_("MALE") . "</option>\n"
         ."<option value='Female'" . ($user["gender"] == "Female" ? " selected='selected'" : "") . ">" . T_("FEMALE") . "</option>\n";


              print("<tr><td align='right' class='alt3'><b>" . T_("THEME") . ":</b> </td><td align='left' class='alt3'><select name='stylesheet'>\n$stylesheets\n</select></td></tr>");
              print("<tr><td align='right' class='alt2'><b>" . T_("PREFERRED_CLIENT") .":</b> </td><td align='left' class='alt2'><input type='text' size='20' maxlength='20' name='client' value=\"" . htmlspecialchars($user["client"]) . "\" /></td></tr>");
              print("<tr><td align='right' class='alt3'><b>" . T_("AGE") . ":</b> </td><td align='left' class='alt3'><input type='text' size='3' maxlength='2' name='age' value=\"" . htmlspecialchars($user["age"]) . "\" /></td></tr>");
              print("<tr><td align='right' class='alt2'><b>" . T_("GENDER") . ":</b> </td><td align='left' class='alt2'><select size='1' name='gender'>\n$gender\n</select></td></tr>");
              print("<tr><td align='right' class='alt3'><b>" . T_("COUNTRY") . ":</b> </td><td align='left' class='alt3'><select name='country'>\n$countries\n</select></td></tr>");

              if ($user["class"] > 1) {
                  print("<tr><td align='right' class='alt2'><b>".T_("TEAM").":</b> </td><td align='left' class='alt2'><select name='teams'>\n$teams\n</select></td></tr>");
              }

?>
<tr><td colspan="2" align="center"><button type='submit' class='btn btn-sm btn-primary'><?php echo T_("SUBMIT"); ?></button></td></tr>
	</table>              
              </form>>      
            </div>
            </div>
            </div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php";
      }

      public function other()
      {
          dbconn();
          global $config, $THEME, $pdo;
          loggedinonly();
          $id = (int)$_GET["id"];
          if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            autolink(TTURL."/index", T_("You dont have permission"));
          } else {
           //   echo 'im staff or curuser';
          }
          stdhead(T_("user"));
          ?>
          <div class="card">
          <div class="card-header">
              <?php echo T_("Other"); ?>
          </div>
          <div class="card-body">
          <?php require_once "views/user/otherlist.php"; ?>
            Leave space for more
            </div>
            </div>
            </div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php";
      }


      public function changepw()
      {
          dbconn();
          global $config, $THEME, $pdo;
          loggedinonly();
          $id = (int)$_GET["id"];
          if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            autolink(TTURL."/index", T_("You dont have permission"));
          } else {
           //   echo 'im staff or curuser';
          }
          stdhead(T_("user"));
          $do = $_REQUEST["do"];

          if ($do=="newpassword") {
              $chpassword = $_POST['chpassword'];
              $passagain = $_POST['passagain'];

              if ($chpassword != "") {
                  if (strlen($chpassword) < 6) {
                      $message = T_("PASS_TOO_SHORT");
                  }
                  if ($chpassword != $passagain) {
                      $message = T_("PASSWORDS_NOT_MATCH");
                  }
                  $chpassword = password_hash($chpassword, PASSWORD_BCRYPT);
                  $secret = mksecret();
              }

              if ((!$chpassword) || (!$passagain)) {
                  $message = "You must enter something!";
              }

              ?>
              <div class="card">
              <div class="card-header">
                  <?php echo T_("Other"); ?>
              </div>
              <div class="card-body">
              <?php require_once "views/user/detaillist.php"; ?>  
                  <?php
              if (!$message) {
                  $pdo->run("UPDATE users 
                  SET password =?, secret =? WHERE id =?", [$chpassword, $secret, $id]);
                  echo "<br /><br /><center><b>".T_("PASSWORD_CHANGED_OK")."</b></center>";
                  Cookie::destroy();
              } else {
                  echo "<br /><br /><b><center>".$message."</center></b><br /><br />";
              }

?>
                          </div>
            </div>
        </div>
              </div><br />
          <?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php";
              die();
          }//do

          ?>
          <div class="card">
          <div class="card-header">
              <?php echo T_("Change Password"); ?>
          </div>
          <div class="card-body">
          <?php require_once "views/user/detaillist.php"; ?>
	<form method="post" action="<?php echo $config["SITEURL"]; ?>/users/changepw?id=<?php echo $id; ?>">
	<input type="hidden" name="do" value="newpassword" />
    <div class="f-border">
    <br />
    <table border="0" align="center" cellpadding="10">
    <tr class="alt3">
        <td align="right"><b><?php echo T_("NEW_PASSWORD"); ?>:</b></td>
        <td align="left"><input type="password" name="chpassword" size="50" /></td>
    </tr>
    <tr class="alt3">
        <td align="right"><b><?php echo T_("REPEAT"); ?>:</b></td>
        <td align="left"><input type="password" name="passagain" size="50" /></td>
    </tr>
    <tr class="alt2">
        <td colspan="2" align="center">
        <input type="reset" value="<?php echo T_("REVERT"); ?>" />
        <button type='submit' class='btn btn-sm btn-primary'><?php echo T_("SUBMIT"); ?></button>
        </td>
    </tr>
    </table>
    <br />
    </div>
	</form>
    </div>
            </div>
    </div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php";
      }

      public function signature()
      {
          dbconn();
          global $config, $THEME, $pdo;
          loggedinonly();
          $id = (int)$_GET["id"];
          if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            autolink(TTURL."/index", T_("You dont have permission"));
          } else {
           //   echo 'im staff or curuser';
          }
          if ($_POST) {
            $title = strip_tags($_POST["title"]);
            $signature = $_POST["signature"];

                  // Save New Signature.
                  DB::run("UPDATE users SET title=?, signature=? WHERE id=?", [$title, $signature, $id]);
                  autolink(TTURL."/users/signature?id=$id", T_("SUCCESS"));
              }
$user = DB::run("SELECT id, title, signature FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
          
              stdhead(T_("user"));
          ?>
          <div class="card">
          <div class="card-header">
              <?php echo T_("Signature"); ?>
          </div>
          <div class="card-body">
          <?php require_once "views/user/detaillist.php"; ?>
              <form action="<?php echo TTURL; ?>/users/signature?id=<?php echo $id; ?>" method="post">
              
              <b><?php echo T_("CUSTOM_TITLE"); ?></b> <br>
              <input type='text' name='title' size='50' value='<?php echo strip_tags($user['title']); ?>'><br>
              <i><?php echo T_("HTML_NOT_ALLOWED"); ?></i><br>
              <b><?php echo  T_("SIGNATURE"); ?></b><br>
            <textarea name='signature' cols='50' rows='10'><?php echo  htmlspecialchars($user['signature']); ?>
        </textarea><br />
        <i><?php echo sprintf(T_("MAX_CHARS"), 150); ?>&nbsp;<?php echo  T_("HTML_NOT_ALLOWED") ?></i><br>
        <input type="submit">
              </form>
              <br>
    </div>
            </div>
            </div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php";
      }




      public function avatar()
      {
          dbconn();
          global $config, $THEME, $pdo;
          loggedinonly();
          $id = (int)$_GET["id"];
          if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            autolink(TTURL."/index", T_("You dont have permission"));
          } else {
           //   echo 'im staff or curuser';
          }
          if ($_POST) {
              $avatar = strip_tags($_POST["avatar"]);
          
              if ($avatar != null) {
                  # Allowed Image Extenstions.
                  $allowed_types = &$config["allowed_image_types"];
          
                  # We force http://
                  if (!preg_match("#^\w+://#i", $avatar)) {
                      $avatar = "http://" . $avatar;
                  }

                  # Clean Avatar Path.
                  $avatar = cleanstr($avatar);
           
                  # Validate Image.
                  $im = @getimagesize($avatar);
           /*
                  if (!$im[ 2 ] || !@array_key_exists($im['mime'], $allowed_types)) {
                      echo "The avatar url was determined to be of a invalid nature.";
                  }
             */    
                  // Save New Avatar.
                  DB::run("UPDATE users SET avatar=? WHERE id=?", [$avatar, $id]);
                  autolink(TTURL."/users/details?id=$id", T_("SUCCESS"));
              }
          }

          $user = DB::run("SELECT avatar FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
 

          stdhead(T_("Details"));
          ?>
          <div class="card">
          <div class="card-header">
              <?php echo T_("Avatar"); ?>
          </div>
          <div class="card-body">
          <?php require_once "views/user/detaillist.php"; ?>
              <form action="<?php echo TTURL; ?>/users/avatar?id=<?php echo $id; ?>" method="post">
              <b><?php echo T_("AVATAR_UPLOAD"); ?></b><br>
              <input type='text' name='avatar' size='50' value='<?php echo htmlspecialchars($user["avatar"]); ?>'>
              <br><i><?php echo T_("AVATAR_LINK"); ?></i>
              <input type="submit">
              </form>
              <br>
       </div>
            </div>
            </div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php";
      }

      public function email()
      {
          dbconn();
          global $config, $THEME, $pdo;
          loggedinonly();
          $id = (int)$_GET["id"];
          if ($id != $_SESSION['id']) {
            autolink(TTURL."/index", T_("You dont have permission"));
          } else {
           //   echo 'im staff or curuser';
          }
          if ($_POST) {
                   $email = $_POST["email"];  
            $sec = mksecret();
                    //$hash = md5($sec . $email . $sec);
                    $obemail = rawurlencode($email);
                    //$updateset[] = "editsecret = " . sqlesc($sec);
                    $thishost = $_SERVER["HTTP_HOST"];
                    $thisdomain = preg_replace('/^www\./is', "", $thishost);
                    $body = <<<EOD
You have requested that your user profile (username {$_SESSION["username"]})
on {$config["SITEURL"]} should be updated with this email address ($email) as
user contact.
If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.
To complete the update of your user profile, please follow this link:
{$config["SITEURL"]}/account/ce?id={$_SESSION["id"]}&secret=$sec&email=$obemail
Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

$TTMail = new TTMail();
$var = $TTMail->Send($email, "$config[SITENAME] profile update confirmation", $body, "From: $config[SITEEMAIL]", "-f$config[SITEEMAIL]");

DB::run("UPDATE users SET editsecret =? WHERE id =?", [$sec, $_SESSION['id']]);

autolink(TTURL."/users/profile?id=$id", T_("Email Edited"));

        }
        $user = DB::run("SELECT email FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
        stdhead(T_("user"));
          ?>
          <div class="card">
          <div class="card-header">
              <?php echo T_("Email"); ?>
          </div>
          <div class="card-body">
          <?php require_once "views/user/emaillist.php"; ?>
          <form action="<?php echo TTURL; ?>/users/email?id=<?php echo $id; ?>" method="post">
          <input type="text" size="40" name="email" value='<?php echo htmlspecialchars($user["email"]); ?>'>&nbsp;<br>
          <input type="submit" value="<?php echo T_("SUBMIT");?>" />
        </form>
       </div>
            </div>
            </div>
    </div><br />
<?php require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php";
      }


      public function admin()
      {
          dbconn();
          global $config, $THEME, $pdo;
          loggedinonly();
          $id = (int)$_GET["id"];
          if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            autolink(TTURL."/index", T_("You dont have permission"));
          } else {
           //   echo 'im staff or curuser';
          }
          if ($_POST) {
            $userid = $_POST["userid"];
            $downloaded = strtobytes($_POST["downloaded"]);
            $uploaded = strtobytes($_POST["uploaded"]);
            $ip = $_POST["ip"];
            $class = (int) $_POST["class"];
            $donated = (float) $_POST["donated"];
            $password = $_POST["password"];
            $warned = $_POST["warned"];
            $forumbanned = $_POST["forumbanned"];
            $downloadbanned = $_POST["downloadbanned"];
            $shoutboxpos = $_POST["shoutboxpos"];
            $modcomment = $_POST["modcomment"];
            $enabled = $_POST["enabled"];
            $invites =(int) $_POST["invites"];
            $email = $_POST["email"];
        
            if (!validemail($email))
                show_error_msg(T_("EDITING_FAILED"), T_("EMAIL_ADDRESS_NOT_VALID"), 1);
        
            //change user class
            $arr = DB::run("SELECT class FROM users WHERE id=?", [$id])->fetch();
            $uc = $arr['class'];
        
            // skip if class is same as current
            //if ($uc != $class && $class > 0) {
                if ($uc <= get_others_class($id)) { // todo
                    show_error_msg(T_("EDITING_FAILED"), T_("11111111   YOU_CANT_DEMOTE_YOURSELF"),1);
                } elseif ($uc <= get_others_class($id)) {
                    show_error_msg(T_("EDITING_FAILED"), T_("222222   YOU_CANT_DEMOTE_SOMEONE_SAME_LVL"),1);
                } else {
                    DB::run("UPDATE users SET class=? WHERE id=?",[$class , $id]);
                    // Notify user
                    $prodemoted = ($class > $uc ? "promoted" : "demoted");
                    $msg = "You have been $prodemoted to " . get_user_class_name($class) . " by " . $_SESSION["username"] . "";
                    $added = get_date_time();
        
                  //  DB::run("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $_SESSION[id], $msg, $added)");
                         
                }
           // }
            //continue updates
        
        
            DB::run("UPDATE users 
            SET email=?, downloaded=?, uploaded=?, ip=?, donated=?, forumbanned=?, warned=?,
             modcomment=?, enabled=?, invites=? , downloadbanned=?, shoutboxpos=?
            WHERE id=?", [$email, $downloaded, $uploaded, $ip, $donated, $forumbanned, $warned, $modcomment,
             $enabled, $invites, $downloadbanned, $shoutboxpos, $id]);
         
        
            write_log($_SESSION['username']." has edited user: $id details");
        
            if ($_POST['resetpasskey']=='yes'){
                DB::run("UPDATE users SET passkey=? WHERE id=?",['',$uploaded]);
            }
        
            $chgpasswd = $_POST['chgpasswd']=='yes' ? true : false;
            if ($chgpasswd) {
        //		$passreq = DB::run("SELECT password FROM users WHERE id=$userid");
                $passres = DB::run("SELECT password FROM users WHERE id=?", [$id])->fetch();
                if($password != $passres['password']){
                    $password = password_hash($password, PASSWORD_BCRYPT);
                    DB::run("UPDATE users SET password=? WHERE id=?",[$password,$id]);
                    write_log($_SESSION['username']." has changed password for user: $id");
                }
            }
            autolink(TTURL."/users/admin?id=$id", T_("SUCCESS"));
          die;
        }
////////////////////////////////////////////////////////////////////
$user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
//$ratio
        if ($user["downloaded"] > 0) {
            $ratio = $user["uploaded"] / $user["downloaded"];
        } else {
            $ratio = "---";
        }
        $uploaded = $user["uploaded"];
        $downloaded = $user["downloaded"];
        $enabled = $user["enabled"] == 'yes';
        $warned = $user["warned"] == 'yes';
        $forumbanned = $user["forumbanned"] == 'yes';
        $downloadbanned = $user["downloadbanned"] == 'yes';
        $shoutboxpos = $user["shoutboxpos"] == 'yes';
        $modcomment = htmlspecialchars($user["modcomment"]);

          stdhead(T_("user"));
          ?>
          <div class="card">
          <div class="card-header">
              <?php echo T_("Edit"); ?>
          </div>
          <div class="card-body">
          <?php require_once "views/user/adminlist.php";
          print("<form method='post' action='$config[SITEURL]/users/admin?id=$id'>\n");
            print("<table border='0' cellspacing='0' cellpadding='3'>\n"); 
          print("<tr><td>" . T_("UPLOADED") . ": </td><td align='left'><input type='text' size='30' name='uploaded' value=\"" . mksize($user["uploaded"], 9) . "\" /></td></tr>\n");
            print("<tr><td>" . T_("DOWNLOADED") . ": </td><td align='left'><input type='text' size='30' name='downloaded' value=\"" . mksize($user["downloaded"], 9) . "\" /></td></tr>\n");
            print("<tr><td>" . T_("EMAIL") . "</td><td align='left'><input type='text' size='40' name='email' value=\"$user[email]\" /></td></tr>\n");
             
                    print("<tr><td>" . T_("IP_ADDRESS") . ": </td><td align='left'><input type='text' size='20' name='ip' value=\"$user[ip]\" /></td></tr>\n");
                    print("<tr><td>" . T_("INVITES") . ": </td><td align='left'><input type='text' size='4' name='invites' value='" . $user["invites"] . "' /></td></tr>\n");

                    if ($_SESSION["class"] > 1) { //todo
                        print("<tr><td>" . T_("CLASS") . ": </td><td align='left'><select name='class'>\n");
                        $maxclass = $_SESSION["class"] + 1;
                        for ($i = 1; $i < $maxclass; ++$i) {
                            print("<option value='$i' " . ($user["class"] == $i ? " selected='selected'" : "") . ">$prefix" . get_user_class_name($i) . "\n");
                        }
        
                        print("</select></td></tr>\n");
                    }



                print("<tr><td>" . T_("DONATED_US") . ": </td><td align='left'><input type='text' size='4' name='donated' value='$user[donated]' /></td></tr>\n");
                print("<tr><td>" . T_("PASSWORD") . ": </td><td align='left'><input type='password' size='40' name='password' value=\"$user[password]\" /></td></tr>\n");
                print("<tr><td>" . T_("CHANGE_PASS") . ": </td><td align='left'><input type='checkbox' name='chgpasswd' value='yes'/></td></tr>");
                print("<tr><td>" . T_("MOD_COMMENT") . ": </td><td align='left'><textarea cols='40' rows='10' name='modcomment'>$modcomment</textarea></td></tr>\n");
                print("<tr><td>" . T_("ACCOUNT_STATUS") . ": </td><td align='left'><input name='enabled' value='yes' type='radio' " . ($enabled ? " checked='checked'" : "") . " />Enabled <input name='enabled' value='no' type='radio' " . (!$enabled ? " checked='checked' " : "") . " />Disabled</td></tr>\n");
                print("<tr><td>" . T_("WARNED") . ": </td><td align='left'><input name='warned' value='yes' type='radio' " . ($warned ? " checked='checked'" : "") . " />Yes <input name='warned' value='no' type='radio' " . (!$warned ? " checked='checked'" : "") . " />No</td></tr>\n");
                print("<tr><td>" . T_("FORUM_BANNED") . ": </td><td align='left'><input name='forumbanned' value='yes' type='radio' " . ($forumbanned ? " checked='checked'" : "") . " />Yes <input name='forumbanned' value='no' type='radio' " . (!$forumbanned ? " checked='checked'" : "") . " />No</td></tr>\n");
                print("<tr><td>Download Banned: </td><td align='left'><input name='downloadbanned' value='yes' type='radio' " . ($downloadbanned ? " checked='checked'" : "") . " />Yes <input name='downloadbanned' value='no' type='radio' " . (!$downloadbanned ? " checked='checked'" : "") . " />No</td></tr>\n");
                print("<tr><td>Shoutbox Banned: </td><td align='left'><input name='shoutboxpos' value='yes' type='radio' " . ($shoutboxpos ? " checked='checked'" : "") . " />Yes <input name='shoutboxpos' value='no' type='radio' " . (!$shoutboxpos ? " checked='checked'" : "") . " />No</td></tr>\n");
                print("<tr><td>" . T_("PASSKEY") . ": </td><td align='left'>$user[passkey]<br /><input name='resetpasskey' value='yes' type='checkbox' />" . T_("RESET_PASSKEY") . " (" . T_("RESET_PASSKEY_MSG") . ")</td></tr>\n");
                print("<tr><td colspan='2' align='center'><input type='submit' value='" . T_("SUBMIT") . "' /></td></tr>\n");
                print("</table>\n");
                print("</form>\n");
                ?>
       </div>
            </div>
            </div>
    </div><br />
    <?php
    ///IP history///
begin_frame( "IP History" );
echo "<table align=center cellpadding=0 cellspacing=0 class='ttable_headinner' width='99%'>";
$res = DB::run( "SELECT * FROM iplog WHERE userid=$id ORDER BY lastused DESC, timesused ASC" );
echo "<tr><td class='ttable_head'>".T_("IP_ADDRESS")."</td><td class='ttable_head'>".T_("DATE_ADDED")."</td><td class='ttable_head'>".T_("LAST_ACCESS")."</td><td class='ttable_head'>".T_("TIMES_USED")."</td><td class='ttable_head'>Other Users</td></tr>";
$x = 1;
while ( $row = $res->fetch(PDO::FETCH_ASSOC) ) {
    //Find other users with the same IP
    $res2 = DB::run( "SELECT users.id AS id,users.username as username FROM users INNER JOIN iplog ON (iplog.ip = '$row[ip]' AND users.id=iplog.userid AND users.id<>$id)" );
    $usersSame = "";
    while ( $userrow = $res2->fetch(PDO::FETCH_ASSOC) ) {
        $usersSame .= "<a href='$config[SITEURL]users?id=$userrow[id]'>".class_user_colour($userrow['username'])."</a>,&nbsp;";
    }
    echo "<tr align='center'><td>$row[ip]</td><td>" . date( "M d, Y H:i:s", utc_to_tz_time( $row[ 'added' ] ) ) . "</td><td>" . date( "M d, Y H:i:s", utc_to_tz_time( $row[ 'lastused' ] ) ) . "</td><td>" . number_format( $row[ 'timesused' ] ) . "</td><td>$usersSame</td></tr>";
    if ( $x == 1 )
        $x = 2;
    else
        $x = 1;
}
unset( $x );
echo "</table>";
end_frame();
    ///end IP history///
 require_once "views/themes/" . ($_SESSION['stylesheet'] ?: 'default') . "/footer.php";
      }

  }