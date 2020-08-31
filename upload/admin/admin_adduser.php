<?php
if ($action == "adduser") {

    if ($_SESSION["class"] < "7") {
        show_error_msg("Error", "Sorry you do not have the rights to view this page!", 1);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_POST["username"] == "" || $_POST["password"] == "" || $_POST["email"] == "") {
            show_error_msg("Error", "Missing form data.");
        }

        if ($_POST["password"] != $_POST["password2"]) {
            show_error_msg("Error", "Passwords mismatch.");
        }

        $username = $_POST["username"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $secret = mksecret();
        $passhash = md5($password);
        $secret = $secret;
        /*
        $count = get_row_count("users", "WHERE username=$username");
        if (!$count !=0) {
        show_error_msg("Error", "Unable to create the account. The user name is possibly already taken.");
        header("Refresh: 2; url=".TTURL."/admincp");
        die;
        }
         */
        DB::run("INSERT INTO users (added, last_access, secret, username, password, status, email) VALUES (?,?,?,?,?,?,?)", [get_date_time(), get_date_time(), $secret, $username, $passhash, 'confirmed', $email]);
        autolink(TTURL . "/admincp", T_("COMPLETE"));
    }

    $title = "Add User";
    require 'views/admin/header.php';
    adminnavmenu();
    begin_frame();
    ?>
    <center><b>Add user</b></center>
    <div align=center>
    <form method=get action=?>
    <input type=hidden name=action value=users>
    Search Users: <input type=text size=30 name=search>
    <input type=submit value='Search'>
    </form>
    <form method=post action='<?php echo $config['SITEURL']; ?>/admincp?action=adduser'>
    <table border="0" class="table_table" align="center">
    <tr><td>Username</td><td><input type=text name=username size=40></td></tr>
    <tr><td>Password</td><td><input type=password name=password size=40></td></tr>
    <tr><td>Re-type password</td><td><input type=password name=password2 size=40></td></tr>
    <tr><td>E-mail</td><td><input type=text name=email size=40></td></tr>
    <tr><td><center><input type=submit value="Okay" class=btn></center></td></tr>
    </table>
    </form>

    </div>
    <?php
end_frame();
    stdfoot();
}