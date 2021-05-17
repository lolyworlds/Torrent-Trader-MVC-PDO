<?php
class Shoutbox extends Controller
{
    public function __construct()
    {
        Auth::user();
        //$this->shoutModel = $this->model('Shout');
        $this->logModel = $this->model('Logs');
    }

    public function index()
    {
        Redirect::to(URLROOT);
    }

    public function chat()
    {
        $query = 'SELECT * FROM shoutbox WHERE staff = 0 ORDER BY msgid DESC LIMIT 20';
        $result = DB::run($query);
        ?>
        <div class="shoutbox_contain">
            <div class="msg-wrap">
            <?php
            while ($row = $result->fetch(PDO::FETCH_LAZY)) {
            $ol3 = DB::run("SELECT avatar FROM users WHERE id=" . $row["userid"])->fetch(PDO::FETCH_ASSOC);
            $av = $ol3['avatar'];
            if (!empty($av)) {
                $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
            } else {
                $av = "<img src='".URLROOT."/assets/images/default_avatar.png' alt='my_avatar' width='20' height='20'>";
            }
            if ($row['userid'] == 0) {
                $av = "<img src='".URLROOT."/assets/images/default_avatar.png' alt='default_avatar' width='20' height='20'>";
            }
            ?>
                <div class="media msg ">
                    <a class="pull-left d-none d-sm-block" href="#">
                    <?php echo $av ?>
                    <a class="pull-left" href="<?php echo URLROOT ?>/profile?id=<?php echo $row['userid'] ?>" target="_parent">
                    <b><?php echo Users::coloredname($row['user']) ?>:</b></a>
                    </a>
                    <?php
            if ($_SESSION['class'] > _UPLOADER) {
                //echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=" . $row['msgid'] . "'>[E]</a>";
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete?id=" . $row['msgid'] . "''><i class='fa fa-remove' aria-hidden='true'></i></a>&nbsp";
                                ?>
                <!-- Trigger/Open The Model -->
                <a data-toggle="modal" data-target="#editshout-<?= $row['msgid']; ?>">
  <i class='fa fa-pencil' aria-hidden='true'></i>
</a>
                <!-- The Modal -->
                <div id="editshout-<?= $row['msgid']; ?>" class="modal">
                  <!-- Modal content -->
                  <div class="modal-content">
                        <!-- Close Modal -->
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><b>CLOSE</b></button><br>
                        <!-- The Message to edit -->
                        <form method="POST" action="<?php echo URLROOT; ?>/shoutbox/edit?id=<?php echo $row['msgid']; ?>">
                        <input class="form-control" type="text" name="message" value="<?php echo $row['message'] ?>" size="60" /><br>
                        <!-- The submit button -->
                        <center><input class="btn btn-sm btn-primary"  type="submit" value='<?php echo Lang::T("SUBMIT"); ?>'></center>
                        </form>
                  </div>
                </div>
            <?php
            }?>
                    <div class="media-body">
                        <small class="pull-right time d-none d-sm-block"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('jS M,  g:ia', TimeDate::utc_to_tz_time($row['date'])); ?></small>
                        <small class="col-lg-10"><?php echo nl2br(format_comment($row['message'])); ?></small>
                    </div>
                </div>
            <?php }?>
            </div>
        </div>
        <?php
}

    public function add()
    {
        if ($_SESSION["shoutboxpos"] == 'no') {
            //INSERT MESSAGE
            if (!empty($_POST['message']) && $_SESSION['loggedin'] == true) {
                $_POST['message'] = $_POST['message'];
                $result = DB::run("SELECT COUNT(*) FROM shoutbox WHERE message=? AND user=? AND UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) < ?", [$_POST['message'], $_SESSION['username'], TimeDate::get_date_time(), 30]);
                $row = $result->fetch(PDO::FETCH_LAZY);
                if ($row[0] == '0') {
                    $qry = DB::run("INSERT INTO shoutbox (msgid, user, message, date, userid) VALUES (?, ?, ?, ?, ?)", [null, $_SESSION['username'], $_POST['message'], TimeDate::get_date_time(), $_SESSION['id']]);
                }
            }
        }
        Redirect::to(URLROOT);
    }

    public function delete()
    {
        $delete = isset($_GET['id']) ? $_GET['id'] : null;
        if (isset($delete)) {
            if (is_numeric($delete)) {
                $result = DB::run("SELECT * FROM shoutbox WHERE msgid=?", [$delete]);
            } else {
                echo "Failed to delete, invalid msg id";
                exit;
            }
            $row = $result->fetch(PDO::FETCH_LAZY);
            if ($row && ($_SESSION["edit_users"] == "yes" || $_SESSION['username'] == $row[1])) {
                Logs::write("<b><font color='orange'>Shout Deleted:</font> Deleted by   " . $_SESSION['username'] . "</b>");
                DB::run("DELETE FROM shoutbox WHERE msgid=?", [$delete]);
            }
        }
        Redirect::to(URLROOT);
    }

    public function edit()
    {
    if ($_SESSION['class'] > _UPLOADER) {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    if (!empty($_POST['message'])) {
    $message = $_POST['message'];
    DB::run("UPDATE shoutbox SET message=? WHERE msgid=?", [$message, $id]);
    Session::flash('info', Lang::T("Message edited"), URLROOT);
    }
    } else {
        Session::flash('info', Lang::T("You do not have permission"), URLROOT."/logout");
    }
    
    }

    public function history()
    {
        
        Style::header('SHOUTBOX_HISTORY');
        Style::begin('SHOUTBOX_HISTORY');
if ($_SESSION['class'] > _UPLOADER) {
    echo "<center><a href='" . URLROOT . "/shoutbox/staff'>View Staff Chat</a></center>";
} ?>
        <div class="shoutbox_contain h-100">
            <div class="msg-wrap">
            <?php
            $query = 'SELECT * FROM shoutbox WHERE staff = 0 ORDER BY msgid DESC LIMIT 80';
        $result = DB::run($query);
            while ($row = $result->fetch(PDO::FETCH_LAZY)) {
            $ol3 = DB::run("SELECT avatar FROM users WHERE id=" . $row["userid"])->fetch(PDO::FETCH_ASSOC);
            $av = $ol3['avatar'];
            if (!empty($av)) {
                $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
            } else {
                $av = "<img src='" . URLROOT . "/assets/images/default_avatar.png' alt='my_avatar' width='20' height='20'>";
            }
            if ($row['userid'] == 0) {
                $av = "<img src='" . URLROOT . "/assets/images/default_avatar.png' alt='default_avatar' width='20' height='20'>";
            }
            ?>
                <div class="media msg ">
                    <small class="pull-left time d-none d-sm-block"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('jS M,  g:ia', TimeDate::utc_to_tz_time($row['date'])); ?></small>&nbsp;
                    <a class="pull-left d-none d-sm-block" href="#">
                    <?php echo $av ?>
                    <a class="pull-left" href="<?php echo URLROOT ?>/profile?id=<?php echo $row['userid'] ?>" target="_parent">
                    <b><?php echo Users::coloredname($row['user']) ?>:</b></a>
                    </a>
                    <?php
            if ($_SESSION['class'] > _UPLOADER) {
                //echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=" . $row['msgid'] . "'>[E]</a>";
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete?id=" . $row['msgid'] . "''><i class='fa fa-remove' aria-hidden='true'></i></a>&nbsp";
                                ?>
                <!-- Trigger/Open The Model -->
                <a data-toggle="modal" data-target="#editshout-<?= $row['msgid']; ?>">
  <i class='fa fa-pencil' aria-hidden='true'></i>
</a>
                <!-- The Modal -->
                <div id="editshout-<?= $row['msgid']; ?>" class="modal">
                  <!-- Modal content -->
                  <div class="modal-content">
                        <!-- Close Modal -->
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><b>CLOSE</b></button><br>
                        <!-- The Message to edit -->
                        <form method="POST" action="<?php echo URLROOT; ?>/shoutbox/edit?id=<?php echo $row['msgid']; ?>">
                        <input class="form-control" type="text" name="message" value="<?php echo $row['message'] ?>" size="60" /><br>
                        <!-- The submit button -->
                        <center><input class="btn btn-sm btn-primary"  type="submit" value='<?php echo Lang::T("SUBMIT"); ?>'></center>
                        </form>
                  </div>
                </div>
            <?php
            }?>
                    <div class="media-body">
                        <small class="col-lg-10"><?php echo nl2br(format_comment($row['message'])); ?></small>
                    </div>
                </div>
            <?php }?>
            </div>
        </div>
<?php
        Style::end();
        Style::footer();
    }

    public function isstaff()
    {
        if ($_SESSION['class'] < _UPLOADER) {
            Session::flash('info', Lang::T("You dont have permission"), URLROOT . "/index");
        }
    }

    public function staffhistory()
    {
        $this->isstaff();
         Style::header('SHOUTBOX_HISTORY');
        Style::begin('SHOUTBOX_HISTORY');
if ($_SESSION['class'] > _UPLOADER) {
    echo "<center><a href='" . URLROOT . "/shoutbox/staff'>View Staff Chat</a></center>";
} ?>
        <div class="shoutbox_contain h-100">
            <div class="msg-wrap">
            <?php
            $query = 'SELECT * FROM shoutbox WHERE staff = 1 ORDER BY msgid DESC LIMIT 80';
        $result = DB::run($query);
            while ($row = $result->fetch(PDO::FETCH_LAZY)) {
            $ol3 = DB::run("SELECT avatar FROM users WHERE id=" . $row["userid"])->fetch(PDO::FETCH_ASSOC);
            $av = $ol3['avatar'];
            if (!empty($av)) {
                $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
            } else {
                $av = "<img src='" . URLROOT . "/assets/images/default_avatar.png' alt='my_avatar' width='20' height='20'>";
            }
            if ($row['userid'] == 0) {
                $av = "<img src='" . URLROOT . "/assets/images/default_avatar.png' alt='default_avatar' width='20' height='20'>";
            }
            ?>
                <div class="media msg ">
                    <a class="pull-left d-none d-sm-block" href="#">
                    <?php echo $av ?>
                    <a class="pull-left" href="<?php echo URLROOT ?>/profile?id=<?php echo $row['userid'] ?>" target="_parent">
                    <b><?php echo Users::coloredname($row['user']) ?>:</b></a>
                    </a>
                    <?php
            if ($_SESSION['class'] > _UPLOADER) {
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=" . $row['msgid'] . "'>[E]</a>";
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete?id=" . $row['msgid'] . "''>[D]</a>";
            }?>
                    <div class="media-body">
                        <small class="pull-right time d-none d-sm-block"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('jS M,  g:ia', TimeDate::utc_to_tz_time($row['date'])); ?></small>
                        <small class="col-lg-10"><?php echo nl2br(format_comment($row['message'])); ?></small>
                    </div>
                </div>
            <?php }?>
            </div>
        </div>
<?php
        Style::end();
        Style::footer();
    }

    public function staff()
    {
        $this->isstaff();
        Style::header('SHOUTBOX_HISTORY');
        Style::begin(Lang::T("SHOUTBOX"));
        if ($_SESSION['class'] > _UPLOADER) {
            echo "<center><a href='" . URLROOT . "/shoutbox/staff'>View Staff Chat</a></center>";
        } ?>
        <p id="shoutboxstaff"></p>
        <form name='shoutboxform' action='<?php echo URLROOT ?>/shoutbox/staffadd' method='post'>
        <div class="row">
            <div class="col-md-12">
            <?php
                include APPROOT.'/helpers/bbcode_helper.php';
                echo shoutbbcode("shoutboxform", "message");
            ?>
            </div>
        </div>
        </form>
        <?php
        Style::end();
        Style::footer();
    }

    public function staffchat()
    {
        $query = 'SELECT * FROM shoutbox WHERE staff = 1 ORDER BY msgid DESC LIMIT 20';
        $result = DB::run($query);
        ?>
        <div class="shoutbox_contain">
            <div class="msg-wrap">
            <?php
            while ($row = $result->fetch(PDO::FETCH_LAZY)) {
            $ol3 = DB::run("SELECT avatar FROM users WHERE id=" . $row["userid"])->fetch(PDO::FETCH_ASSOC);
            $av = $ol3['avatar'];
            if (!empty($av)) {
                $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
            } else {
                $av = "<img src='".URLROOT."/assets/images/default_avatar.png' alt='my_avatar' width='20' height='20'>";
            }
            if ($row['userid'] == 0) {
                $av = "<img src='".URLROOT."/assets/images/default_avatar.png' alt='default_avatar' width='20' height='20'>";
            }
            ?>
                <div class="media msg ">
                    <a class="pull-left d-none d-sm-block" href="#">
                    <?php echo $av ?>
                    <a class="pull-left" href="<?php echo URLROOT ?>/profile?id=<?php echo $row['userid'] ?>" target="_parent">
                    <b><?php echo Users::coloredname($row['user']) ?>:</b></a>
                    </a>
                    <?php
            if ($_SESSION['class'] > _UPLOADER) {
                //echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=" . $row['msgid'] . "'>[E]</a>";
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete?id=" . $row['msgid'] . "''><i class='fa fa-remove' aria-hidden='true'></i></a>&nbsp";
                                ?>
                <!-- Trigger/Open The Model -->
                <a data-toggle="modal" data-target="#editshout-<?= $row['msgid']; ?>">
  <i class='fa fa-pencil' aria-hidden='true'></i>
</a>
                <!-- The Modal -->
                <div id="editshout-<?= $row['msgid']; ?>" class="modal">
                  <!-- Modal content -->
                  <div class="modal-content">
                        <!-- Close Modal -->
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><b>CLOSE</b></button><br>
                        <!-- The Message to edit -->
                        <form method="POST" action="<?php echo URLROOT; ?>/shoutbox/edit?id=<?php echo $row['msgid']; ?>">
                        <input class="form-control" type="text" name="message" value="<?php echo $row['message'] ?>" size="60" /><br>
                        <!-- The submit button -->
                        <center><input class="btn btn-sm btn-primary"  type="submit" value='<?php echo Lang::T("SUBMIT"); ?>'></center>
                        </form>
                  </div>
                </div>
            <?php
            }?>
                    <div class="media-body">
                        <small class="pull-right time d-none d-sm-block"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('jS M,  g:ia', TimeDate::utc_to_tz_time($row['date'])); ?></small>
                        <small class="col-lg-10"><?php echo nl2br(format_comment($row['message'])); ?></small>
                    </div>
                </div>
            <?php }?>
            </div>
        </div>
        <?php
}

    public function staffadd()
    {
        if ($_SESSION["shoutboxpos"] == 'no') {
            $this->isstaff();
            //INSERT MESSAGE
            if (!empty($_POST['message']) && $_SESSION['loggedin'] == true) {
                $_POST['message'] = $_POST['message'];
                $result = DB::run("SELECT COUNT(*) FROM shoutbox WHERE message=? AND user=? AND UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) < ?", [$_POST['message'], $_SESSION['username'], TimeDate::get_date_time(), 30]);
                $row = $result->fetch(PDO::FETCH_LAZY);
                if ($row[0] == '0') {
                    $qry = DB::run("INSERT INTO shoutbox (msgid, user, message, date, userid, staff) VALUES (?, ?, ?, ?, ?, ?)", [null, $_SESSION['username'], $_POST['message'], TimeDate::get_date_time(), $_SESSION['id'], 1]);
                }
            }
        }
        Redirect::to(URLROOT.'/shoutbox/staff');
    }
}