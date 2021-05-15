<?php
class Adminteams extends Controller
{

    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
        $this->log = $this->model('Logs');
    }

    private function isadmin()
    {
        if (!$_SESSION['loggedin'] == true || $_SESSION["control_panel"] != "yes") {
            Session::flash('info', Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"), URLROOT . "/home");
        }
    }

    public function index()
    {
        require_once APPROOT."/helpers/bbcode_helper.php";
        $this->isadmin();
        $sql = DB::run("SELECT * FROM teams");
        $data = [
            'sql' => $sql
        ];
        $this->view('teams/admin', $data);
    }

    public function add()
    {
        $this->isadmin();

        $team_name = $_POST['team_name'];
        $team_image = $_POST['team_image'];
        $team_description = $_POST['team_description'];
        $teamownername = $_POST['team_owner'];
        $add = $_POST['add'];
        $added = TimeDate::get_date_time();

        if ($add == 'true') {
            if (!$team_name || !$teamownername || !$team_description) {
                Session::flash('info', Lang::T("One or more fields left empty."), URLROOT . "/adminteams");
                die;
            }
            $team_name = $team_name;
            $team_description = $team_description;
            $team_image = $team_image;
            $teamownername = $teamownername;
            $aa = DB::run("SELECT id FROM users WHERE username =?", [$teamownername]);
            $ar = $aa->fetch(PDO::FETCH_ASSOC);
            $team_owner = $ar["id"];
            if (!$team_owner) {
                Session::flash('info', Lang::T("This user does not exist"), URLROOT . "/adminteams");
                die;
            }
            $sql = DB::run("INSERT INTO teams SET name =?, owner =?, info =?, image =?, added =?", [$team_name, $team_owner, $team_description, $team_image, $added]);
            $tid = DB::lastInsertId();
            DB::run("UPDATE users SET team = $tid WHERE id= $team_owner");
        }
        Session::flash('info', Lang::T("Team Added"), URLROOT."/adminteams");
    }

    public function delete()
    {
        $this->isadmin();
        $sure = $_GET['sure'];
        $del = $_GET['del'];
        $team = htmlspecialchars($_GET['team']);
        if ($sure == "yes") {
            $sql = DB::run("UPDATE users SET team=? WHERE team=?", ['0', $del]);
            $sql = DB::run("DELETE FROM teams WHERE id=? LIMIT 1", [$del]);
            Logs::write($_SESSION['username'] . " has deleted team id:$del");
            Session::flash('info', Lang::T("Team Successfully Deleted!"), URLROOT . "/adminteams");
            die();
        }
        if ($del > 0) {
            Session::flash('info', Lang::T("You and in the truth wish to delete team? ($team) ( <b><a href='".URLROOT."/adminteams/delete?del=$del&amp;team=$team&amp;sure=yes'>Yes!</a></b> / <b><a href='".URLROOT."/adminteams'>No!</a></b> )"), URLROOT . "/adminteams");
            die();
        }
    }

    public function edit()
    {
        $this->isadmin();
        $edited = (int) $_GET['edited'];
        $id = (int) $_GET['id'];
        $team_name = $_GET['team_name'];
        $team_info = $_GET['team_info'];
        $team_image = $_GET['team_image'];
        $teamownername = $_GET['team_owner'];
        $editid = $_GET['editid'];
        $name = $_GET['name'];
        $image = $_GET['image'];
        $owner = $_GET['owner'];
        $info = $_GET['info'];

        // Post/Get
        if ($edited == 1) {
            if (!$team_name || !$teamownername || !$team_info) {
                Session::flash('info', 'One or more fields left empty.', URLROOT."/adminteams");
                Style::end();
                require APPROOT . '/views/admin/footer.php';
                die;
            }
            $team_name = $team_name;
            $team_image = $team_image;
            $teamownername = $teamownername;
            $team_info = $team_info;
            $aa = DB::run("SELECT class, id FROM users WHERE username=?", [$teamownername]);
            $ar = $aa->fetch(PDO::FETCH_ASSOC);
            $team_owner = $ar["id"];
            $sql = DB::run("UPDATE teams SET name =?, info =?, owner =?, image =?  WHERE id=?", [$team_name, $team_info, $team_owner, $team_image, $id]);
            DB::run("UPDATE users SET team =? WHERE id=?", [$id, $team_owner]);
            if ($sql) {
                $mss = "<b>Successfully Edited</b>[<a href='".URLROOT."/adminteams'>Back</a>]";
                Logs::write($_SESSION['username'] . " has edited team ($team_name)");
                Session::flash('info', $mss, URLROOT."/adminteams");
                die();
            }
        }

        if ($editid > 0) {
            $data = [
                'editid' => $editid,
                'name' => $name,
                'image' => $image,
                'owner' => $owner,
                'info' => $info,
            ];
            $this->view('teams/edit', $data);
            die();
        }
	}

    public function members()
    {
        $this->isadmin();
        $teamid = $_GET['teamid'];
        $sql = DB::run("SELECT id,username,uploaded,downloaded FROM users WHERE team=$teamid");
        $data = [
            'sql' => $sql
        ];
        $this->view('teams/members', $data);
	}
}