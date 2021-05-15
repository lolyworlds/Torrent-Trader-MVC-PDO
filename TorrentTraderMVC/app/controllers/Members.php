<?php
class Members extends Controller
{

    public function __construct()
    {
        Auth::user();
        $this->countriesModel = $this->model('Countries');
        $this->groupsModel = $this->model('Groups');
    }

    public function index()
    {
        if ($_SESSION["view_users"] == "no") {
            Session::flash('info', Lang::T("NO_USER_VIEW"), URLROOT."/home");
        }

        $search = trim(Input::get('search'));
        $class = (int) (Input::get('class'));
        $letter = trim(Input::get('letter'));
        if (!$class) {
            unset($class);
        }
        $q = $query = null;
        if ($search) {
            $query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
            if ($search) {
                $q = "search=" . htmlspecialchars($search);
            }
        } elseif ($letter) {
            if (strlen($letter) > 1) {
                unset($letter);
            }
            if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
                unset($letter);
            } else {
                $query = "username LIKE '$letter%' AND status='confirmed'";
            }
            $q = "letter=$letter";
        }
        if (!$query) {
            $query = "status='confirmed'";
        }
        if ($class) {
            $query .= " AND class=$class";
            $q .= ($q ? "&amp;" : "") . "class=$class";
        }

        $res = $this->groupsModel->getGroups();
        $data = [
            'getgroups' => $res,
            'query1' => $query,
            'query2' => $q
        ];
        $this->view('members/index', $data, true);
    }
}