<?php
class Seed extends Controller {

    public function __construct()
    {
        Auth::user();
        // $this->shoutModel = $this->model('Shout');
    }

    public function needseed()
    {
        if ($_SESSION["view_torrents"] == "no") {
            Session::flash('info', Lang::T("NO_TORRENT_VIEW"), URLROOT."/home");
        }
        $res = DB::run("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size, torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, users.username FROM torrents LEFT JOIN users ON torrents.owner = users.id WHERE torrents.banned = 'no' AND torrents.leechers > 0 AND torrents.seeders <= 1 ORDER BY torrents.seeders");
        if ($res->rowCount() == 0) {
            Session::flash('info', Lang::T("NO_TORRENT_NEED_SEED"), URLROOT."/home");
        }
        $data = [
            'res' => $res
        ];
        $this->view('torrent/needseed', $data, true);
    }


    public function reseed()
    {
        if ($_SESSION["view_torrents"] == "no") {
            Session::flash('info', Lang::T("NO_TORRENT_VIEW"), URLROOT."/home");
        }
        $id = (int) $_GET["id"];
        if (isset($_COOKIE["reseed$id"])) {
            Session::flash('info', Lang::T("RESEED_ALREADY_ASK"), URLROOT."/home");
        }
        $res = DB::run("SELECT `owner`, `banned`, `external` FROM `torrents` WHERE `id` = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        if (!$row || $row["banned"] == "yes" || $row["external"] == "yes") {
            Session::flash('info', Lang::T("TORRENT_NOT_FOUND"), URLROOT."/home");
        }
        $res2 = DB::run("SELECT users.id FROM completed LEFT JOIN users ON completed.userid = users.id WHERE users.enabled = 'yes' AND users.status = 'confirmed' AND completed.torrentid = $id");
        $message = sprintf(Lang::T('RESEED_MESSAGE'), $_SESSION['username'], URLROOT, $id);
        while ($row2 = $res2->fetch(PDO::FETCH_ASSOC)) {
            DB::run("INSERT INTO `messages` (`subject`, `sender`, `receiver`, `added`, `msg`) VALUES ('" . Lang::T("RESEED_MES_SUBJECT") . "', '" . $_SESSION['id'] . "', '" . $row2['id'] . "', '" . TimeDate::get_date_time() . "', " . sqlesc($message) . ")");
        }
        if ($row["owner"] && $row["owner"] != $_SESSION["id"]) {
            DB::run("INSERT INTO `messages` (`subject`, `sender`, `receiver`, `added`, `msg`) VALUES ('Torrent Reseed Request', '" . $_SESSION['id'] . "', '" . $row['owner'] . "', '" . TimeDate::get_date_time() . "', " . sqlesc($message) . ")");
        }
        setcookie("reseed$id", $id, time() + 86400, '/');
        Session::flash('info', Lang::T("RESEED_SENT"), URLROOT."/home");
    }

}