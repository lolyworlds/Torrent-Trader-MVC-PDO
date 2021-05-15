<?php
class Admincomments extends Controller
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
        $count = get_row_count("comments");
        list($pagertop, $pagerbottom, $limit) = pager(10, $count, URLROOT."/admincomments?");
        $title = "Latest Comments";
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();

        Style::begin("Last Comments");

        $res = DB::run("SELECT c.id, c.text, c.user, c.torrent, c.news, t.name, n.title, u.username, c.added FROM comments c LEFT JOIN torrents t ON c.torrent = t.id LEFT JOIN news n ON c.news = n.id LEFT JOIN users u ON c.user = u.id ORDER BY c.added DESC $limit");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            $userid = $arr["user"];
            $username = Users::coloredname($arr["username"]);
            $data = $arr["added"];
            $tid = $arr["torrent"];
            $nid = $arr["news"];
            $title = ($arr['title']) ? $arr['title'] : $arr['name'];
            $comentario = stripslashes(format_comment($arr["text"]));
            $cid = $arr["id"];

            $type = 'Torrent: <a href="'.URLROOT.'/torrents/read?id=' . $tid . '">' . $title . '</a>';

            if ($nid > 0) {
                $type = 'News: <a href="'.URLROOT.'/comments?id=' . $nid . '&amp;type=news">' . $title . '</a>';
            }

            echo "<table class='table_table' align='center' cellspacing='0' width='100%'><tr><th class='table_head' align='center'>" . $type . "</td></tr><tr><td class='table_col2'>" . $comentario . "</th></tr><tr><td class='table_col1' align='center'>Posted in <b>" . $data . "</b> by <a href=\"" . URLROOT . "/users/profile?id=" . $userid . "\">" . $username . "</a><!--  [ <a href=\"edit-/comments?cid=" . $cid . "\">edit</a> | <a href=\"edit-/comments?action=delete&amp;cid=" . $cid . "\">delete</a> ] --></td></tr></table><br />";
            #$rows[] = $arr;
        }

        if ($count > 10) {
            echo $pagerbottom;
        }

        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

}