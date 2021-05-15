<?php
class Adminprivacy extends Controller
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
        $where = array();
        switch ($_GET['type']) {
            case 'low':
                $where[] = "privacy = 'low'";
                break;
            case 'normal':
                $where[] = "privacy = 'normal'";
                break;
            case 'strong':
                $where[] = "privacy = 'strong'";
                break;
            default:
                break;
        }
        $where[] = "enabled = 'yes'";
        $where[] = "status = 'confirmed'";
        $where = implode(' AND ', $where);
        $count = get_row_count("users", "WHERE $where");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, htmlspecialchars($_SERVER['REQUEST_URI'] . '&'));
        $res = DB::run("SELECT id, username, class, email, ip, added, last_access FROM users WHERE $where ORDER BY username DESC $limit");

        $title = Lang::T("PRIVACY_LEVEL");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin("Privacy Level");
        $data = [
            'count' => $count,
            'res' => $res,
            'pagerbottom' => $pagerbottom,
        ];
        $this->view('admin/privacylevel', $data);
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }
}