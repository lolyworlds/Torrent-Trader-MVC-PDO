<?php
class Client extends Controller
{

    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
    }

    public function index()
    {
        if ($_SESSION["class"] < 6) {
            show_error_msg("Error", "Access denied.");
        }
        if (isset($_POST['ban'])) {
            DB::run("INSERT INTO agents (agent_name, hits, ins_date) VALUES (?,?,?)", [$_POST['ban'], 1, TimeDate::get_date_time()]);
        }
        $res11 = DB::run("SELECT client, peer_id FROM peers GROUP BY client");
        $title = Lang::T("Clients");

        Style::adminheader("All Clients");
        $data = [
            'res11' => $res11,
        ];
        $this->view('client/index', $data);
        Style::footer();
    }

    public function banned()
    {
        
        if ($_SESSION["class"] < 6) {
            show_error_msg("Error", "Access denied.");
        }
        if (isset($_POST['unban'])) {
            foreach ($_POST['unban'] as $deleteid) {
                DB::run("DELETE FROM agents WHERE agent_id=?", [$deleteid]);
            }
        }

        $sql = DB::run("SELECT * FROM agents")->fetchAll(PDO::FETCH_ASSOC);
        $title = Lang::T("Clients");

        Style::adminheader("Banned Clients");
        $data = [
            'sql' => $sql,
        ];
        $this->view('client/banned', $data);
        Style::adminfooter();
    }
}