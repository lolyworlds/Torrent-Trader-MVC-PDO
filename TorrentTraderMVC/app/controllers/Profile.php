<?php
class Profile extends Controller
{
    public function __construct()
    {
        Auth::user();
        $this->userModel = $this->model('User');
        $this->valid = new Validation();
        $this->log = $this->model('Logs');
    }

    public function index()
    {
        $id = (int) Input::get("id");
        if (!$this->valid->validId($id)) {
            Session::flash('info', "Bad ID.", URLROOT."/home");
        }
        // can view own but not others
        if ($_SESSION["view_users"] == "no" && $_SESSION["id"] != $id) {
            Session::flash('info', Lang::T("NO_USER_VIEW"), URLROOT."/home");
        }
        $user = User::getUserById($id);
        if (!$user) {
            Session::flash('info', Lang::T("NO_USER_WITH_ID") . " $id.", URLROOT."/home");
        }
        // user not ready to be seen yet
        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $_SESSION["edit_users"] == "no") {
            Session::flash('info', Lang::T("NO_ACCESS_ACCOUNT_DISABLED"), URLROOT."/home");
        }
        // Start Blocked Users
        $blocked = DB::run("SELECT id FROM friends WHERE userid=$user[id] AND friend='enemy' AND friendid=$_SESSION[id]");
        $show = $blocked->rowCount();
        if ($show != 0 && $_SESSION["control_panel"] != "yes") {
            Session::flash('info', "You're blocked by this member and you can not see his profile!", URLROOT."/home");
        }
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
        $qry = DB::run("SELECT COUNT(`hnr`) FROM `snatched` WHERE `uid` = '$id' AND `hnr` = 'yes'");
        $res = $qry->fetch(PDO::FETCH_ASSOC);
        $numhnr = $res[0];

        $avatar = htmlspecialchars($user["avatar"]);
        if (!$avatar) {
            $avatar = URLROOT . "/assets/images/default_avatar.png";
        }

        $usersignature = stripslashes($user["signature"]); // todo

        $r = DB::run("SELECT id FROM friends WHERE userid=? AND friend=? AND friendid=?", [$_SESSION['id'], 'friend', $id]);
        $friend = $r->rowCount();
        $r = DB::run("SELECT id FROM friends WHERE userid=? AND friend=? AND friendid=?", [$_SESSION['id'], 'enemy', $id]);
        $block = $r->rowCount();

        $cardheader = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($user["username"]));
        $user1 = $this->userModel->getAll($id);
        $data = [
            'title' => $cardheader,
            'id' => $id,
            'friend' => $friend,
            'block' => $block,
            'country' => $country,
            'ratio' => $ratio,
            'numhnr' => $numhnr,
            'avatar' => $avatar,
            'numtorrents' => $numtorrents,
            'numcomments' => $numcomments,
            'numforumposts' => $numforumposts,
            'usersignature' => $usersignature,
            'selectuser' => $user1,
        ];
        $this->view('user/profile', $data, true);
    }

    public function edit()
    {
        global $tzs;
        $id = (int) $_GET["id"];
        // Clear error
        $stylesheets = '';
        $tz = '';
        if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            Session::flash('info', Lang::T("You dont have permission"), URLROOT."/home");
        } else {
            //   echo 'im staff or curuser';
        }
        $user = User::getUserById($id);
        // Stylesheet
        $ss_r = DB::run("SELECT * from stylesheets");
        $ss_sa = array();
        while ($ss_a = $ss_r->fetch(PDO::FETCH_LAZY)) {
            $ss_id = $ss_a["uri"];
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
        // Country
        $countries = "<option value='0'>----</option>\n";
        $ct_r = DB::run("SELECT id,name from countries ORDER BY name");
        while ($ct_a = $ct_r->fetch(PDO::FETCH_LAZY)) {
            $countries .= "<option value='$ct_a[id]'" . ($user['country'] == $ct_a['id'] ? " selected='selected'" : "") . ">$ct_a[name]</option>\n";
        }
        // Timezone
        ksort($tzs);
        reset($tzs);
        while (list($key, $val) = thisEach($tzs)) {
            if ($user["tzoffset"] == $key) {
                $tz .= "<option value=\"$key\" selected='selected'>$val[0]</option>\n";
            } else {
                $tz .= "<option value=\"$key\">$val[0]</option>\n";
            }
        }
        //Teams
        $teams = "<option value='0'>--- " . Lang::T("NONE_SELECTED") . " ----</option>\n";
        $sashok = DB::run("SELECT id,name FROM teams ORDER BY name");
        while ($sasha = $sashok->fetch(PDO::FETCH_LAZY)) {
            $teams .= "<option value='$sasha[id]'" . ($user['team'] == $sasha['id'] ? " selected='selected'" : "") . ">$sasha[name]</option>\n";
        }
        $gender = "<option value='Male'" . ($user['gender'] == "Male" ? " selected='selected'" : "") . ">" . Lang::T("MALE") . "</option>\n"
        . "<option value='Female'" . ($user['gender'] == "Female" ? " selected='selected'" : "") . ">" . Lang::T("FEMALE") . "</option>\n";
        
        $user1 = $this->userModel->getAll($id);
        $cardheader = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($user["username"]));
        $data = [
            'title' => $cardheader,
            'stylesheets' => $stylesheets,
            'countries' => $countries,
            'teams' => $teams,
            'tz' => $tz,
            'gender' => $gender,
            'id' => $id,
            'selectuser' => $user1,
        ];
        $this->view('user/edit', $data, true);
    }

    public function submit()
    {
        $db = new Database();
        $id = (int) $_GET["id"];
        if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            Session::flash('info', Lang::T("You dont have permission"), URLROOT."/home");
        } else {
            //   echo 'im staff or curuser';
        }
        if ($_POST) {
            $avatar = strip_tags($_POST["avatar"]);
            $title = strip_tags($_POST["title"]);
            $signature = $_POST["signature"];
            $stylesheet = $_POST["stylesheet"];
            $client = strip_tags($_POST["client"]);
            $age = $_POST["age"];
            $gender = $_POST["gender"];
            $country = $_POST["country"];
            $teams = $_POST["teams"];
            $acceptpms = $_POST["acceptpms"];
            $pmnotif = $_POST["pmnotif"];
            $privacy = $_POST["privacy"];
            $notifs = ($pmnotif == 'yes' ? "[pm]" : "");
            if ($_POST['resetpasskey']) {
                $passkey = '';
                $db->run("UPDATE users
                       SET passkey=?
                       WHERE id =?", [$passkey, $id]);
            }
            $timezone = (int) $_POST['tzoffset'];
            if ($acceptpms == "yes") {
                $acceptpms = 'yes';
            } else {
                $acceptpms = 'no';
            }
            $hideshoutbox = ($_POST["hideshoutbox"] == "yes") ? "yes" : "no";
            // Save New details. todo removed passkey
            $db->run("UPDATE users
                       SET avatar=?, title=?, signature=?, stylesheet=?, client=?, age=?, gender=?, country=?, team=?, hideshoutbox=?, acceptpms=?, privacy=?, notifs=?, tzoffset=?
                       WHERE id =?", [$avatar, $title, $signature, $stylesheet, $client, $age, $gender, $country, $teams, $hideshoutbox, $acceptpms, $privacy, $notifs, $timezone, $id]);
            Session::flash('info', "User Edited", URLROOT."/profile/edit?id=$id");
        }
    }

    public function admin()
    {
        $id = (int) $_GET["id"];
        if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            Session::flash('info', Lang::T("You dont have permission"), URLROOT."/admin?id=$id");
        } else {
            //   echo 'im staff or curuser';
        }
        $user1 = User::getUserById($id);
        $user = $this->userModel->getAll($id);
        //$ratio
        if ($user["downloaded"] > 0) {
            $ratio = $user["uploaded"] / $user["downloaded"];
        } else {
            $ratio = "---";
        }
        $cardheader = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($user1["username"]));
        $data = [
            'id' => $id,
            'title' => $cardheader,
            'selectuser' => $user,
        ];
        $this->view('user/admin', $data, true);
    }

    public function submited()
    {
        $id = (int) $_GET["id"];
        if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            Session::flash('info', Lang::T("You dont have permission"), URLROOT."/admin?id=$id");
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
            $invites = (int) $_POST["invites"];
            $email = $_POST["email"];
            $bonus = $_POST["bonus"];
            $valid = new Validation();
            if (!$valid->validEmail($email)) {
                Session::flash('info', Lang::T("EMAIL_ADDRESS_NOT_VALID"), URLROOT."/admint?id=$id");
            }
            //change user class
            $arr = DB::run("SELECT class FROM users WHERE id=?", [$id])->fetch();
            $uc = $arr['class'];
            // skip if class is same as current
            //if ($uc != $class && $class > 0) {
            if ($uc <= get_others_class($id)) { // todo
                Session::flash('info', Lang::T("YOU_CANT_DEMOTE_YOURSELF"), URLROOT."/admin?id=$id");
            } elseif ($uc <= get_others_class($id)) {
                Session::flash('info', Lang::T("YOU_CANT_DEMOTE_SOMEONE_SAME_LVL"), URLROOT."/admin?id=$id");
            } else {
                DB::run("UPDATE users SET class=? WHERE id=?", [$class, $id]);
                // Notify user
                $prodemoted = ($class > $uc ? "promoted" : "demoted");
                $msg = "You have been $prodemoted to " . get_user_class_name($class) . " by " . $_SESSION["username"] . "";
                $added = TimeDate::get_date_time();
                //  DB::run("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $_SESSION[id], $msg, $added)");
            }
            // }
            //continue updates
            DB::run("UPDATE users
            SET email=?, downloaded=?, uploaded=?, ip=?, donated=?, forumbanned=?, warned=?,
             modcomment=?, enabled=?, invites=? , downloadbanned=?, shoutboxpos=?, seedbonus=?
            WHERE id=?", [$email, $downloaded, $uploaded, $ip, $donated, $forumbanned, $warned, $modcomment,
                $enabled, $invites, $downloadbanned, $shoutboxpos, $bonus, $id]);

            Logs::write($_SESSION['username'] . " has edited user: $id details");

            if ($_POST['resetpasskey'] == 'yes') {
                DB::run("UPDATE users SET passkey=? WHERE id=?", ['', $uploaded]);
            }

            $chgpasswd = $_POST['chgpasswd'] == 'yes' ? true : false;
            if ($chgpasswd) {
                //        $passreq = DB::run("SELECT password FROM users WHERE id=$userid");
                $passres = DB::run("SELECT password FROM users WHERE id=?", [$id])->fetch();
                if ($password != $passres['password']) {
                    $password = password_hash($password, PASSWORD_BCRYPT);
                    DB::run("UPDATE users SET password=? WHERE id=?", [$password, $id]);
                    Logs::write($_SESSION['username'] . " has changed password for user: $id");
                }
            }
            Session::flash('info', Lang::T("User Edited"), URLROOT."/profile?id=$id");
            die;
        }
    }

}