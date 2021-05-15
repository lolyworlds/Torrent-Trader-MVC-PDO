<?php
class Adminfreetorrent extends Controller
{
    public function __construct()
    {
        Auth::user(); // should check admin here
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }


    public function index()
    {
        $search = trim($_GET['search']);
        if ($search != '') {
            $whereand = "AND name LIKE '%$search%";
        }
        $res2 = DB::run("SELECT COUNT(*) FROM torrents WHERE freeleech='1' $whereand");
        $row = $res2->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/adminfreetorrent?");

        $rqq = "SELECT id, name, seeders, leechers, visible, banned FROM torrents WHERE freeleech='1' $whereand ORDER BY name $limit";
        $resqq = DB::run($rqq);
        $title = Lang::T("Free Leech");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'pagertop' => $pagertop,
            'resqq' => $resqq,
            'pagerbottom' => $pagerbottom,
        ];
        $this->view('admin/freetorrent', $data);
        require APPROOT . '/views/admin/footer.php';
    }
}