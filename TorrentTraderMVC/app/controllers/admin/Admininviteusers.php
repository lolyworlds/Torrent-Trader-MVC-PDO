<?php
class Admininviteusers extends Controller
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
        $do = $_GET['do']; // todo
        if ($do == "del") {
            if (!@count($_POST["users"])) {
                show_error_msg(Lang::T("ERROR"), "Nothing Selected.", 1);
            }
            $ids = array_map("intval", $_POST["users"]);
            $ids = implode(", ", $ids);
            $res = DB::run("SELECT u.id, u.invited_by, i.invitees FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' AND u.id IN ($ids)");
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                # We remove the invitee from the inviter and give them back there invite.
                $invitees = str_replace("$row[id] ", "", $row["invitees"]);
                DB::run("UPDATE `users` SET `invites` = `invites` + 1, `invitees` = '$invitees' WHERE `id` = '$row[invited_by]'");
                //Users::deleteaccount($row['id']); todo
            }
            Redirect::autolink(URLROOT . "/admininviteusers", "Entries Deleted");
        }

        $count = DB::run("SELECT COUNT(*) FROM users WHERE status = 'confirmed' AND invited_by != '0'")->fetchColumn();
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'admininviteusers?');
        $res = DB::run("SELECT u.id, u.username, u.email, u.added, u.last_access, u.class, u.invited_by, i.username as inviter FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'confirmed' AND u.invited_by != '0' ORDER BY u.added DESC $limit");

        $title = Lang::T("Invited Users");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'res' => $res,
        ];
        $this->view('admin/invited', $data);
        require APPROOT . '/views/admin/footer.php';
    }
}