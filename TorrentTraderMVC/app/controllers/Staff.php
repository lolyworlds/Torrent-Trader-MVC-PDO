<?php
class Staff extends Controller {


    public function __construct()
    {
        Auth::user();
        $this->countriesModel = $this->model('Countries');
        $this->groupsModel = $this->model('Groups');
    }
    
    public function index()
    {
        $dt = TimeDate::get_date_time(TimeDate::gmtime() - 180);
        $res = $this->groupsModel->getStaff();
        $col = []; //undefined var
        $table = []; //undefined var
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $table[$row['class']] = ($table[$row['class']] ?? '') .
            "<td><img src='" . URLROOT . "/assets/images/button_o" . ($row["last_access"] > $dt ? "n" : "ff") . "line.png' alt='' /> " .
            "<a href='" . URLROOT . "/profile?id=" . $row["id"] . "'>" . Users::coloredname($row["username"]) . "</a> " .
                "<a href='" . URLROOT . "/messages/create?id=" . $row["id"] . "'><img src='" . URLROOT . "/assets/images/button_pm.gif' border='0' alt='' /></a></td>";
            $col[$row['class']] = ($col[$row['class']] ?? 0) + 1;
            if ($col[$row["class"]] <= 4) {
                $table[$row["class"]] = $table[$row["class"]] . "<td></td>";
            } else {
                $table[$row["class"]] = $table[$row["class"]] . "</tr><tr>";
                $col[$row["class"]] = 2;
            }
        }

        $where = null;
        if ($_SESSION["edit_users"] == "no") {
            $where = "AND `staff_public` = 'yes'";
        }

        $res = $this->groupsModel->getStaffLevel($where);
        if ($res->rowCount() == 0) {
            Session::flash('info', Lang::T("NO_STAFF_HERE"), URLROOT."/home");
        }
        $data = [
            'sql' => $res,
            'table' => $table,
        ];
        $this->view('staff/index', $data, true);
    }

}