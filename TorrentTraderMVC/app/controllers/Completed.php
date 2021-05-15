<?php

class Completed extends Controller {

    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
        $this->valid = new Validation();
    }
    
    public function index()
    {
        $db = new Database;
        if ($_SESSION["view_torrents"] == "no") {
            show_error_msg(Lang::T("ERROR"), Lang::T("NO_TORRENT_VIEW"), 1);
        }
        $id = (int) $_GET["id"];
        $res = DB::run("SELECT name, external, banned FROM torrents WHERE id =?", [$id]);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        if ((!$row) || ($row["banned"] == "yes" && $_SESSION["edit_torrents"] == "no")) {
            show_error_msg(Lang::T("ERROR"), Lang::T("TORRENT_NOT_FOUND"), 1);
        }
        if ($row["external"] == "yes") {
            show_error_msg(Lang::T("ERROR"), Lang::T("THIS_TORRENT_IS_EXTERNALLY_TRACKED"), 1);
        }
        $res = DB::run("SELECT users.id, users.username, users.uploaded, users.downloaded, users.privacy, completed.date FROM users LEFT JOIN completed ON users.id = completed.userid WHERE users.enabled = 'yes' AND completed.torrentid = '$id'");
        if ($res->rowCount() == 0) {
            show_error_msg(Lang::T("ERROR"), Lang::T("NO_DOWNLOADS_YET"), 1);
        }
        $title = sprintf(Lang::T("COMPLETED_DOWNLOADS"), CutName($row["name"], 40));
        $data = [
            'title' => $title,
            'res' => $res,
            'id' => $id,
        ];
        $this->view('torrent/completed', $data, true);
    }

}