<?php
class Scrape extends Controller
{
    public function __construct()
    {
        Auth::user();
        $this->torrentModel = $this->model('Torrent');
        $this->valid = new Validation();
        $this->logsModel = $this->model('Logs');
    }
    public function index()
    {

        $id = $_GET['id'];

        $resu = DB::run("SELECT id, info_hash FROM torrents WHERE external = 'yes' AND id = $id");
        while ($rowu = $resu->fetch(PDO::FETCH_ASSOC)) {
            //parse torrent file
            $torrent_dir = TORRENTDIR;
            $TorrentInfo = array();
            $TorrentInfo = Parse::torr("$torrent_dir/$rowu[id].torrent");

            $ann = $TorrentInfo[0];
            $annlist = array();

            if ($TorrentInfo[6]) {
                foreach ($TorrentInfo[6] as $ann) {
                    $annlist[] = $ann[0];
                }
            } else {
                $annlist = array($ann);
            }

            $seeders = $leechers = $downloaded = null;
            foreach ($annlist as $ann) {
                $tracker = explode("/", $ann);
                $path = array_pop($tracker);
                $oldpath = $path;
                $path = str_replace("announce", "scrape", $path);
                $tracker = implode("/", $tracker) . "/" . $path;

                if ($oldpath == $path) {
                    continue;
                }

                // Is It udp OR http
                if (preg_match('/udp:\/\//', $tracker)) {
                    $udp = true;
                    try
                    {
                        $timeout = 5;
                        $udp = new Udptscraper($timeout);
                        $stats = $udp->scrape($tracker, $rowu["info_hash"]);
                        //print_r($stats); exit();
                        foreach ($stats as $idu => $scrape) {
                            $seeders += $scrape['seeders'];
                            $leechers += $scrape['leechers'];
                            $downloaded += $scrape['completed'];
                        }
                    } catch (ScraperException $e) {
                        $e->isConnectionError();
                    }
                } /* else {
                $stats = torrent_scrape_url($tracker, $rowu["info_hash"]);

                $http = true;
                try {
                $timeout = 5;
                $http = new HttpScraper($timeout);
                $stats = $http->scrape($tracker, $rowu["info_hash"]);
                //print_r($stats); exit();
                foreach ($stats as $idu => $scrape) {
                $seeders += $scrape['seeders'];
                $leechers += $scrape['leechers'];
                $downloaded += $scrape['completed'];
                }
                } catch (ScraperException $exc) {
                $exc->isConnectionError();
                }

                }*/

                // Update the Announce
                if ($stats['seeds'] != -1) {
                    $seeders += $stats['seeds'];
                    $leechers += $stats['peers'];
                    $downloaded += $stats['downloaded'];

                    DB::run("
                    UPDATE `announce`
                    SET `online` = ?, `seeders` = ?, `leechers` = ?, `times_completed` = ?
                    WHERE `url` = ?
                    AND `torrent` = ?",
                        ['yes', $stats['seeds'], $stats['peers'], $stats['downloaded'], $ann, $rowu['id']]);
                } else {
                    DB::run("
                    UPDATE `announce`
                    SET `online` = ?
                    WHERE `url` = ?
                    AND `torrent` = ?",
                        ['no', $ann, $rowu['id']]);
                }
            }

            // Update the Torrent
            if ($seeders !== null) {
                DB::run("
                UPDATE torrents
                SET leechers = ?, seeders = ?, times_completed = ?, last_action = ?, visible = ?
                WHERE id = ?",
                    [$leechers, $seeders, $downloaded, TimeDate::get_date_time(), 'yes', $rowu['id']]);
            } else {
                DB::run("
                UPDATE torrents
                SET last_action = ?
                WHERE id=?", [TimeDate::get_date_time(), $rowu['id']]);
            }

            // Redirect with message
            if ($seeders !== null) {
                Redirect::autolink(URLROOT . "/torrents/read?id=$id", Lang::T("The Tracker is Updated"));
            } else {
                Redirect::autolink(URLROOT . "/torrents/read?id=$id", Lang::T("The Torrent seems to be dead"));
            }
        }

    }
}
