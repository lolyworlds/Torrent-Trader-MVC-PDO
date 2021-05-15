<?php
class Request extends Controller
{
    public function __construct()
    {
        Auth::user();
        $this->userModel = $this->model('User');
        $this->pdo = new Database();
        $this->valid = new Validation();
    }

    public function index()
    {
        if ($_SESSION["view_torrents"] == "no") {
            Session::flash("info", "You do not have the proper rights to view requests!", URLROOT . "/home");
        }

        if (REQUESTSON) {
            $categ = (int) $_GET["category"];
            $requestorid = (int) $_GET["requestorid"];
            $sort = $_GET["sort"];
            $search = $_GET["search"];
            $filter = $_GET["filter"];
            $search = " AND requests.request like '%$search%' ";
            if ($sort == "votes") {
                $sort = " order by hits desc ";
            } else if ($sort == "request") {
                $sort = " order by request ";
            } else {
                $sort = " order by filled asc ";
            }
            if ($filter == "true") {
                $filter = " AND requests.filledby = 0 ";
            } else {
                $filter = "";
            }
            if ($requestorid != null) {
                if (($categ != null) && ($categ != 0)) {
                    $categ = "WHERE requests.cat = " . $categ . " AND requests.userid = " . $requestorid;
                } else {
                    $categ = "WHERE requests.userid = " . $requestorid;
                }
            } else if ($categ == 0) {
                $categ = '';
            } else {
                $categ = "WHERE requests.cat = " . $categ;
            }
            $res = DB::run("SELECT count(requests.id) FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ $filter $search");
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $count = $row[0];
            $perpage = 50;
            list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, URLROOT . "/request?" . "category=" . $_GET["category"] . "&sort=" . $_GET["sort"] . "&");
            $res = DB::run("SELECT users.downloaded, users.uploaded, users.username, users.privacy, requests.filled, requests.comments,
            requests.filledby, requests.id, requests.userid, requests.request, requests.added, requests.hits, categories.name as cat,
             categories.parent_cat as parent_cat
             FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ
              $filter $search $sort $limit");
            $num = $res->rowCount();
            $data = [
                'pagertop' => $pagertop,
                'pagerbottom' => $pagerbottom,
                'num' => $num,
                'res' => $res,
            ];
            $this->view('request/index', $data, true);
        } else {
            Session::flash("info", "Request are not available", URLROOT . "/home");
            die;
        }
    }

    public function makereq()
    {
        if (REQUESTSON) {
            $data = [];
            $this->view('request/makereq', $data, true);
        } else {
            Session::flash("info", "Request are not available", URLROOT . "/home");
        }
    }

    public function confirmreq()
    {
        if ($_SESSION['class'] < _MODERATOR) {
            Session::flash('info', "Only Moderators can request - For show only", URLROOT . "/request/makereq");
        }
        $requesttitle = $_POST["requesttitle"];
        if (!$requesttitle) {
            Session::flash('info', "You must enter a request!", URLROOT . "/request/makereq");
        }
        $cat = $_POST["cat"];
        if ($cat == 0) {
            Session::flash('info', "Category cannot be empty!", URLROOT . "/request/makereq");
        }
        $descr = $_POST["descr"];
        DB::run("INSERT INTO requests (hits, userid, cat, request, descr, added) VALUES(?,?,?,?,?,?)", [1, $_SESSION['id'], $cat, $requesttitle, $descr, TimeDate::get_date_time()]);
        $id = DB::lastInsertId();
        DB::run("INSERT INTO addedrequests (requestid,userid) VALUES($id, $_SESSION[id])");
        DB::run("INSERT INTO shoutbox (user,message,date,userid) VALUES('System', '$_SESSION[username] has made a request for [url=" . URLROOT . "/request/reqdetails?id=" . $id . "]" . $requesttitle . "[/url]', now(), '0')");
        Logs::write("$requesttitle was added to the Request section");
        Redirect::to(URLROOT . "/request");
    }

    public function votesview()
    {
        $requestid = (int) $_GET['requestid'];
        $res2 = DB::run("select count(addedrequests.id) from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid");
        $row = $res2->fetch(PDO::FETCH_ASSOC);
        $res = DB::run("select users.id as userid,users.username, users.downloaded,users.uploaded, requests.id as requestid, requests.request from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid");
        $res2 = DB::run("select request from requests where id=$requestid");
        if (!$res->rowCount() == 0) {
            $data = [
                'requestid' => $requestid,
                'row' => $row,
                'res' => $res,
                'res2' => $res2,
            ];
            $this->view('request/voteview', $data, true);
        } else {
            Session::flash('info', Lang::T('No Votes Yet'), URLROOT . "/request");
        }
    }

    public function addvote()
    {
        $requestid = (int) $_GET["id"];
        $userid = (int) $_SESSION["id"];
        $res = DB::run("SELECT * FROM addedrequests WHERE requestid=$requestid and userid = $userid");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $voted = $arr;
        if ($voted) {
            Session::flash('info', 'Youve already voted for this request only 1 vote for each request is allowed', URLROOT . "/request");
        } else {
            DB::run("UPDATE requests SET hits = hits + 1 WHERE id=$requestid");
            DB::run("INSERT INTO addedrequests VALUES(0, $requestid, $userid)");
            $msg = "<p>Successfully voted for request $requestid</p><p>Back to <a href=".URLROOT."/request><b>requests</b></a></p>";
            Session::flash('info', $msg, URLROOT . "/request");
        }
    }

    public function reqdetails()
    {
        $id = (int) $_GET["id"];
        $res = DB::run("SELECT * FROM requests WHERE id = $id");
        if ($res->rowCount() != 1) {
            Session::flash('info', "That request id doesn't exist.", URLROOT."/request");
        }
        $num = $res->fetch(PDO::FETCH_ASSOC);
        $s = $num["request"];
        $filled = $num["filled"];
        $catid = $num["cat"];
        $catn = DB::run("SELECT parent_cat,name FROM categories WHERE id='$catid' ");
        $catname = $catn->fetch(PDO::FETCH_ASSOC);
        $pcat = $catname["parent_cat"];
        $ncat = $catname["name"];
        $cres = DB::run("SELECT username FROM users WHERE id=$num[userid]");
        if ($cres->rowCount() == 1) {
            $carr = $cres->fetch(PDO::FETCH_ASSOC);
            $username = "$carr[username]";
            $comment = "$carr[descr]";
        }
        $commcount = DB::run("SELECT COUNT(*) FROM comments WHERE req = $id")->fetchColumn();
        if ($commcount) {
            $commquery = "SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, username, title, class, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE req = $id ORDER BY comments.id";
            $commres = DB::run($commquery);
        } else {
            unset($commres);
        }
        $data = [
            'id' => $id,
            's' => $s,
            'filled' => $filled,
            'pcat' => $pcat,
            'ncat' => $ncat,
            'username' => $username,
            'comment' => $comment,
            'desc' => $num['descr'],
            'added' => $num['added'],
            'request' => $num['request'],
            'commcount' => $commcount,
            'commres' => $commres,
        ];
        $this->view('request/details', $data, true);
    }

    public function takereqedit()
    {
        if ($_SESSION["class"] < 5) {
            Session::flash('info', "Access denied.", URLROOT."/request");
        }
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            Session::flash('info', "You must select a category to put the request in!", URLROOT."/request");
        }
        $descr = $_POST["desc"];
        $cat = $_POST["cat"];
        $filled = $_POST["filled"];
        $request = $_POST["request"];
        $filledby = $_POST["filledby"];
        if (!empty($_POST)) {
            if (!$filled) {
                DB::run("UPDATE requests SET cat=?, request=?, descr=?, filled =?, filled=? WHERE id = ?", [$cat, $request, $descr, 'yes', $filled, $id]);
            } else {
                DB::run("UPDATE requests SET cat=?, filledby =?, request=?, descr=?, filled =?  WHERE id =? ", [$cat, 0, $request, $descr, 'no', $id]);
            }
            Redirect::to(URLROOT . "/request/reqdetails?id=$id");
        }
        $res = DB::run("SELECT * FROM requests WHERE id =$id");
        $data = [
            'res' => $res,
        ];
        $this->view('request/edit', $data, true);
    }

    public function takedelreq()
    {
        if (($_SESSION['class']) > 5) {
            if (empty($_POST["delreq"])) {
                Session::flash('info', "You must select at least one request to delete", URLROOT."/request");
                die;
            }
            $do = "DELETE FROM requests WHERE id IN (" . implode(", ", $_POST['delreq']) . ")";
            $do2 = "DELETE FROM addedrequests WHERE requestid IN (" . implode(", ", $_POST['delreq']) . ")";
            $res2 = DB::run($do2);
            $res = DB::run($do);
            Session::flash('info', "Request Deleted OK", URLROOT."/request");
        } else {
            foreach ($_POST['delreq'] as $del_req) {
                $query = DB::run("SELECT * FROM requests WHERE userid=$_SESSION[id] AND id = $del_req");
                $num = $query->rowCount();
                if ($num > 0) {
                    $res2 = DB::run("DELETE FROM requests WHERE id IN ($del_req)");
                    $res = DB::run("DELETE FROM addedrequests WHERE requestid IN ($del_req)");
                    Session::flash('info', "Request ID $del_req Deleted", URLROOT."/request");
                } else {
                    Session::flash('info', "No Permission to delete Request ID $del_req", URLROOT."/request");
                }
            }
        }
    }

    public function reqreset()
    {
        $requestid = (int) $_GET["requestid"];
        $res = DB::run("SELECT userid, filledby FROM requests WHERE id =$requestid");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if (($_SESSION['id'] == $arr['userid']) || ($_SESSION["class"] >= 4) || ($_SESSION['id'] == $arr['filledby'])) {
            DB::run("UPDATE requests SET filled='', filledby=0 WHERE id =$requestid");
            Session::flash('info', "Request $requestid successfully reset.", URLROOT."/request");
        } else {
            Session::flash('info', "Sorry, cannot reset a request when you are not the owner", URLROOT."/request");
        }
    }

    public function reqfilled()
    {
        $filledurl = $_GET["filledurl"];
        $requestid = (int) $_GET["requestid"];
        $res = DB::run("SELECT users.username, requests.userid, requests.request FROM requests inner join users on requests.userid = users.id where requests.id = $requestid");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $res2 = DB::run("SELECT username FROM users where id =" . $_SESSION['id']);
        $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
        $msg = "Your request $requestid ";
        $msg2 = "Your Request Filled";
        DB::run("UPDATE requests SET filled = '$filledurl', filledby = $_SESSION[id] WHERE id = $requestid");
        DB::run("INSERT INTO messages (poster, sender, receiver, added, subject, msg) VALUES (?,?,?,?,?,?)", [0, 0, $arr['userid'], TimeDate::get_date_time(), $msg2, $msg]);
        $msg = "<div align=left>Request $requestid was successfully filled with <a href=$filledurl>$filledurl</a>.  User <a href=".URLROOT."/profile?id=$arr[userid]><b>$arr[username]</b></a> automatically PMd.  <br>Filled that accidently? No worries, <a href=".URLROOT."/request/reqreset?requestid=$requestid>CLICK HERE</a> to mark the request as unfilled.  Do <b>NOT</b> follow this link unless you are sure there is a problem.<br></div>";
        Session::flash('info', $msg, URLROOT."/request");
    }


    public function reqcomment()
    {
        $action = $_GET["action"];
        require_once APPROOT."/helpers/bbcode_helper.php";
        if ($action == "add") {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $reqid = 0 + $_POST["tid"];
                if (!$this->valid->validId($reqid)) {
                    show_error_msg("Error", "Wrong ID $reqid.", 1);
                }
                $res = DB::run("SELECT request FROM requests WHERE id = $reqid");
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                if (!$arr) {
                    show_error_msg("Error", "No request with ID $reqid.", 1);
                }
                $text = trim($_POST["msg"]);
                if (!$text) {
                    show_error_msg("Error", "Don't leave any fields blank!", 1);
                }
                DB::run("INSERT INTO comments (user, req, added, text, ori_text) VALUES (" . $_SESSION["id"] . ",$reqid, '" . TimeDate::get_date_time() . "', " . sqlesc($text) . "," . sqlesc($text) . ")");
                $reqid = DB::lastInsertId();
                DB::run("UPDATE requests SET comments = comments + 1 WHERE id = $reqid");
                header("Refresh: 0; url=".URLROOT."/request/reqdetails?id=$reqid");
                exit();
            }
            $reqid = 0 + $_GET["tid"];
            if (!$this->valid->validId($reqid)) {
                show_error_msg("Error", "Wrong ID $reqid.", 1);
            }
            $res = DB::run("SELECT request FROM requests WHERE id = $reqid");
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            if (!$arr) {
                show_error_msg("Error", "Wrong ID $reqid.", 1);
            }
            Style::header("Add comment to \"" . $arr["request"] . "\"");
            Style::begin("Add a request comment");
            print("<a href=".URLROOT."/request><button  class='btn btn-sm btn-success'>All Request</button></a>&nbsp;
            <a href=".URLROOT."/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>");
            print("<b>Add comment to \"" . htmlspecialchars($arr["request"]) . "\"</b>\n");
            print("<p><form name=\"Form\" method=\"post\" action=\"".URLROOT."/request/reqcomment?action=add\">\n");
            print("<input type=\"hidden\" name=\"tid\" value=\"$reqid\"/>\n");
            print("" . textbbcode("Form", "msg") . "");
            print("<center><button  class='btn btn-sm btn-success'>Add</button></center></form>\n");
            $res = DB::run("SELECT comments.id, text, comments.added, username, users.id as user, users.avatar FROM comments LEFT JOIN users ON comments.user = users.id WHERE req = $reqid ORDER BY comments.id DESC LIMIT 5");
            $allrows = array();
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $row[] = $row;
            }
            if (count($allrows)) {
                print("<b>Last comments in reverse order.</b>\n");
                commenttable($row);
            }
            Style::end();
            Style::footer();
            die;
        } elseif ($action == "edit") {
            $commentid = 0 + $_GET["cid"];
            if (!$this->valid->validId($commentid)) {
                show_error_msg("Error", "Wrong ID $commentid.", 1);
            }
            $res = DB::run("SELECT c.*, o.request FROM comments AS c JOIN requests AS o ON c.req = o.id WHERE c.id=$commentid");
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            if (!$arr) {
                show_error_msg("Error", "Wrong ID $commentid.", 1);
            }
            if ($arr["user"] != $_SESSION["id"] && $_SESSION['class'] < 5) {
                show_error_msg("Error", "Access denied.", 1);
            }
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $text = $_POST["msg"];
                $returnto = $_POST["returnto"];
                if ($text == "") {
                    show_error_msg("Error", "Don't leave any fields blank!", 1);
                }
                $text = sqlesc($text);
                $editedat = sqlesc(TimeDate::get_date_time());
                DB::run("UPDATE comments SET text=$text, editedby=$_SESSION[id], editedat=$editedat WHERE id=$commentid");
                if ($returnto) {
                    header("Location: $returnto");
                } else {
                    header("Location: home");
                }
                die;
            }
            Style::header("Edit comment for \"" . $arr["request"] . "\"");
            Style::begin("");
            print("<b>Edit comment for \"" . htmlspecialchars($arr["request"]) . "\"</b>\n");
            print("<form name=Form method=\"post\" action=\"".URLROOT."/request/reqcomment?action=edit&amp;cid=$commentid\">\n");
            print("<input type=\"hidden\" name=\"returnto\" value=\"" . $_SERVER["HTTP_REFERER"] . "\" />\n");
            print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" />\n");
            print("" . textbbcode("Form", "msg", $content = $arr["text"]) . "");
            print("<button class='btn btn-sm btn-success'>Edit</button></form>\n");
            Style::end();
            Style::footer();
            die;
        } elseif ($action == "delete") {
            if ($_SESSION['class'] < 5) {
                show_error_msg("Error", "Access denied.", 1);
            }
            $commentid = 0 + $_GET["cid"];
            if (!$this->valid->validId($commentid)) {
                show_error_msg("Error", "Invalid ID $commentid.", 1);
            }
            $sure = $_GET["sure"];
            if (!$sure) {
                $referer = $_SERVER["HTTP_REFERER"];
                show_error_msg("Delete comment", "You`re about to delete this comment. Click " . "<a href=?action=delete&cid=$commentid&sure=1" . ($referer ? "&returnto=" . urlencode($referer) : "") . ">here</a> if you're sure.", 1);
            }
            $res = DB::run("SELECT req FROM comments WHERE id=$commentid");
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            if ($arr) {
                $reqid = $arr["req"];
            }
            $stmt = DB::run("DELETE FROM comments WHERE id=$commentid");
            if ($stmt > 0) {
                DB::run("UPDATE requests SET comments = comments - 1 WHERE id = $reqid");
            }
            $returnto = (int) $_GET["returnto"];
            if ($returnto) {
                header("Location: $returnto");
            } else {
                header("Location: request");
            }
            die;
        } else {
            show_error_msg("Error", "Unknown action $action", 1);
        }
        die;
    }

}