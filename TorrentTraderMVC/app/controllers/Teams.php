<?php
class Teams extends Controller
{

    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
        $this->log = $this->model('Logs');
    }
    public function index()
    {
        $res = DB::run("SELECT teams.id, teams.name, teams.image, teams.info, teams.owner, teams.added, users.username, (SELECT GROUP_CONCAT(id, ' ', username) FROM users WHERE FIND_IN_SET(users.team, teams.id) AND users.enabled = 'yes' AND users.status = 'confirmed') AS members FROM teams LEFT JOIN users ON teams.owner = users.id WHERE users.enabled = 'yes' AND users.status = 'confirmed'");
        if ($res->rowCount() == 0) {
            Session::flash("info", 'No teams available, to create a group please contact <a href='.URLROOT.'/staff>staff</a>', URLROOT . "/home");
        }
        $data = [
            'res' => $res
        ];
        $this->view('teams/index', $data, true);
    }

}