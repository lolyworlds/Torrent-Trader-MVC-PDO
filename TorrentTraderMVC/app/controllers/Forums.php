<?php
class Forums extends Controller
{

    public function __construct()
    {
        Auth::user();
        $this->valid = new Validation();
        $this->forumModel = $this->model('Forum');
    }

    /**
     * Lets show the validation in one place so we dont have to repeat :)
     */
    private function validForumUser($extra = false)
    {
        if (!FORUMS_GUESTREAD) {

        }
        if ($_SESSION["forumbanned"] == "yes" || $_SESSION["view_forum"] == "no") {
            Session::flash("info", Lang::T("FORUM_BANNED"), URLROOT . "/home");
        }
        if ($extra = true) {
            // maybe add some topic/forum id checks on some
        }
    }

    /**
     * View Forum Index.
     */
    public function index()
    {
        $this->validForumUser();
        if (FORUMS) {
            if ($_GET["do"] == 'catchup') {
                catch_up();
            }

            // Action: SHOW MAIN FORUM INDEX
            $forums_res = $this->forumModel->getIndex();
            if ($forums_res->rowCount() == 0) {
                Session::flash("info", 'There is no Forums available', URLROOT . "/home");
            }

            //topic count and post counts
            $postcount = number_format(get_row_count("forum_posts"));
            $topiccount = number_format(get_row_count("forum_topics"));

            $data = [
                'mainquery' => $forums_res,
                'postcount' => $postcount,
                'topiccount' => $topiccount,
            ];
            $this->view('forum/index', $data, true);
        } else {
            Session::flash('INFO', Lang::T("Unfortunatley the forums are not currently available."), URLROOT . "/home");
        }
    }

    /**
     * Post New Topic.
     */
    public function newtopic()
    {
        $this->validForumUser();
        if (FORUMS) {
            $forumid = $_GET["forumid"];
            if (!$this->valid->validId($forumid)) {
                Session::flash('info', "No Forum ID $forumid", URLROOT . "/forums");
            }
            $data = [
                'id' => $forumid,
            ];
            $this->view('forum/newtopic', $data, true);
            die;
        } else {
            Session::flash('INFO', Lang::T("Unfortunatley the forums are not currently available."), URLROOT . "/home");
        }
    }

    /**
     * Search Forum.
     */
    public function search()
    {
        $this->validForumUser();
        
        Style::header('search');
        Style::begin(Lang::T("Search Forums"));
        forumheader('search');
        
        $keywords = trim($_GET["keywords"]);
        
        if ($keywords != "") {
            print("<p>Search Phrase: <b>" . htmlspecialchars($keywords) . "</b></p>\n");
            $maxresults = 50;
            /*
            $res = DB::run("SELECT forum_posts.topicid, forum_posts.userid, forum_posts.id, forum_posts.added, forum_posts.body
                FROM forum_posts
                WHERE forum_posts.body  LIKE ?", ['%'.$keyword.'%']);
               */
            $res = DB::run("SELECT forum_posts.topicid, forum_posts.userid, forum_posts.id, forum_posts.added,
                MATCH ( forum_posts.body ) AGAINST ( ? ) AS relevancy
                FROM forum_posts
                WHERE MATCH ( forum_posts.body ) AGAINST ( ? IN BOOLEAN MODE )
                ORDER BY relevancy DESC", ['%'.$keywords.'%', '%'.$keywords.'%']);
               // var_dump($res);

                
            // search and display results...
            $num = $res->rowCount();
            if ($num > $maxresults) {
                $num = $maxresults;
                print("<p>Found more than $maxresults posts; displaying first $num.</p>\n");
            }
            if ($num == 0) {
                print("<p><b>Sorry, nothing found!</b></p>");
            } else {
                print("<p><center><div class='table'><table class='table table-striped'>
		          	<thead>");
                print("<tr><th>Post ID</th><th align='left'>Topic</th><th align='left'>Forum</th><th align='left'>Posted by</th></tr></tbody>");
                for ($i = 0; $i < $num; ++$i) {
                    $post = $res->fetch(PDO::FETCH_ASSOC);
                    $res2 = DB::run("SELECT forumid, subject FROM forum_topics WHERE id=$post[topicid]");
                    $topic = $res2->fetch(PDO::FETCH_ASSOC);
                    $res2 = DB::run("SELECT name,minclassread, guest_read FROM forum_forums WHERE id=$topic[forumid]");
                    $forum = $res2->fetch(PDO::FETCH_ASSOC);
                    if ($forum["name"] == "" || ($forum["minclassread"] > $_SESSION["class"] && $forum["guest_read"] == "no")) {
                        continue;
                    }
                    $res2 = DB::run("SELECT username FROM users WHERE id=$post[userid]");
                    $user = $res2->fetch(PDO::FETCH_ASSOC);
                    if ($user["username"] == "") {
                        $user["username"] = "Deluser";
                    }
                    print("<tr><td>$post[id]</td><td align='left'><a href='".URLROOT."/forums/viewtopic&amp;topicid=$post[topicid]#post$post[id]'><b>" . htmlspecialchars($topic["subject"]) . "</b></a></td><td align='left'><a href='".URLROOT."/forums/viewforum&amp;forumid=$topic[forumid]'><b>" . htmlspecialchars($forum["name"]) . "</b></a></td><td align='left'><a href='".URLROOT."/profile?id=$post[userid]'><b>$user[username]</b></a><br />at " . TimeDate::utc_to_tz($post["added"]) . "</td></tr>\n");
                }
                print("</table></div></center></p>\n");
                print("<p><b>Search again</b></p>\n");
            }
        }
 ?>
<center><form method='get' action='<?php echo URLROOT; ?>/forums/search'>
<table cellspacing='0' cellpadding='5'>
<tr><td valign='bottom' align='right'>Search For: </td><td align='left'><input type='text' size='40' name='keywords' /><br /></td></tr>
<tr><td colspan='2' align='center'><input type='submit' value='Search' /></td></tr>
</table></form></center>
<?php
Style::end();
Style::footer();
    }

    /**
     * View Unread Topics.
     */
    public function viewunread()
    {
        $this->validForumUser();
        $res = $this->forumModel->viewunread();
        $data = [
            'res' => $res,
            'n' => 0,
        ];
        $this->view('forum/viewunread', $data, true);
        die;
    }

    /**
     * View Forum.
     */
    public function viewforum()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        if (!$this->valid->validId($forumid)) {
            Session::flash('info', Lang::T("FORUMS_DENIED"), URLROOT . "/forums");
        }
        // Get forum name
        $res = DB::run("SELECT name, minclassread, guest_read FROM forum_forums WHERE id=?", [$forumid]);
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $forumname = $arr["name"];
        if (!$forumname || $_SESSION['class'] < $arr["minclassread"] && $arr["guest_read"] == "no") {
            Session::flash('info', Lang::T("FORUMS_NOT_PERMIT"), URLROOT . "/forums");
        }
        // Master pagination examplehttp://localhost/TorrentTraderMVC/forums/viewforum&forumid=28
        $count = get_row_count("forum_topics", "WHERE forumid=$forumid");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, URLROOT . "/forums/viewforum&forumid=$forumid&");
        //$res = DB::run("SELECT * FROM `something` ORDER BY `?` DESC $limit");

        // Get topics data and display category
        $topicsres = DB::run("SELECT * FROM forum_topics WHERE forumid=$forumid ORDER BY sticky, lastpost  DESC $limit")->fetchAll();
        $data = [
            'topicsres' => $topicsres,
            'forumname' => $forumname,
            'forumid' => $forumid,
            'pagerbottom' => $pagerbottom,
        ];
        $this->view('forum/viewforum', $data, true);
        die;
    }

    /**
     *Reply To Post. not in use yet
     */
    public function reply()
    {
        $this->validForumUser();
        require_once APPROOT . "/helpers/bbcode_helper.php";
        $topicid = Input::get("topicid");
        if (!$this->valid->validId($topicid)) {
            Session::flash('info', sprintf(Lang::T("FORUMS_NO_ID_FORUM"), $topicid), URLROOT . "/forums");
        }
        $data = [
            'topicid' => $topicid,
        ];
        $this->view('forum/reply', $data, true);
        die;
    }

    /**
     * Edit a Post.
     */
    public function editpost()
    {
        $this->validForumUser();
        $postid = Input::get("postid");
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $body = $_POST['body'];
            if ($body == "") {
                Session::flash('info', "Body cannot be empty!", URLROOT . "/forums");
            }
            $body = htmlspecialchars_decode($body);
            $editedat = TimeDate::get_date_time();
            DB::run("UPDATE forum_posts SET body=?, editedat=?, editedby=? WHERE id=?", [$body, $editedat, $_SESSION['id'], $postid]);
            $returnto = Input::get("returnto");
            if ($returnto != "") {
                Redirect::to($returnto);
            } else {
                Session::flash('info', "Post was edited successfully.", URLROOT . "/forums");
            }
        }

        if (!$this->valid->validId($postid)) {
            Session::flash('info', Lang::T("FORUMS_DENIED"), URLROOT . "/forums");
        }
        $res = DB::run("SELECT * FROM forum_posts WHERE id=?", [$postid]);
        if ($res->rowCount() != 1) {
            Session::flash('info', "Where is id $postid", URLROOT . "/forums");
        }
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if ($_SESSION["id"] != $arr["userid"] && $_SESSION["delete_forum"] != "yes" && $_SESSION["edit_forum"] != "yes") {
            Session::flash('info', Lang::T("FORUMS_DENIED"), URLROOT . "/forums");
        }
        $data = [
            'postid' => $postid,
            'arrbody' => $arr['body'],
        ];
        $this->view('forum/edit', $data, true);
        die;
    }

    /**
     * Confirm Post/Reply. from function insert_compose_frame
     */
    public function submittopic()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        if (!$this->valid->validId($forumid) && !$this->valid->validId($topicid)) {
            Session::flash('info', Lang::T("FORUM_ERROR"), URLROOT . "/forums");
        }
        $newtopic = $forumid > 0;
        $subject = $_POST["subject"];
        if ($newtopic) {
            if (!$subject) {
                Session::flash('info', "You must enter a subject.", URLROOT . "/forums");
            }
            $subject = trim($subject);
        } else {
            $forumid = get_topic_forum($topicid) or Session::flash('info', "Bad topic ID", URLROOT . "/forums");
        }
        // Make sure sure user has write access in forum
        $arr = get_forum_access_levels($forumid) or Session::flash('info', "Bad forum ID", URLROOT . "/forums");
        if ($_SESSION['class'] < $arr["write"]) {
            Session::flash('info', Lang::T("FORUMS_NOT_PERMIT"), URLROOT . "/forums");
        }
        $body = htmlspecialchars_decode($_POST["body"]);
        if (!$body) {
            Session::flash('info', "No body text.", URLROOT . "/forums");
        }
        if ($newtopic) { //Create topic
            $subject = $subject;
            DB::run("INSERT INTO forum_topics (userid, forumid, subject) VALUES(?,?,?)", [$_SESSION["id"], $forumid, $subject]);
            $topicid = DB::lastInsertId() or Session::flash('info', "Topics id n/a", URLROOT . "/forums");
        } else {
            //Make sure topic exists and is unlocked
            $res = DB::run("SELECT * FROM forum_topics WHERE id=?", [$topicid]);
            $arr = $res->fetch(PDO::FETCH_ASSOC) or Session::flash('info', "Topic id n/a", URLROOT . "/forums");
            if ($arr["locked"] == 'yes') {
                Session::flash('info', "Topic locked", URLROOT . "/forums");
            }
            //Get forum ID
            $forumid = $arr["forumid"];
        }
        //Insert the new post
        $added = "'" . TimeDate::get_date_time() . "'";
        $body = htmlspecialchars_decode($body);
        DB::run("INSERT INTO forum_posts (topicid, userid, added, body) VALUES(?, ?, ?, ?)", [$topicid, $_SESSION["id"], TimeDate::get_date_time(), $body]);
        $postid = DB::lastInsertId();
        // attachments todo
        if (isset($_FILES['upfile'])):
            $array = array($_FILES['upfile']['name']);
            $result = array_filter($array);
            foreach ($result as $k => $ar):
                // check if file has one of the following extensions
                $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip');
                if (in_array($_FILES['upfile']['type'][$k], $allowedfileExtensions)) {
                    Session::flash('info', "Sorry, only zip, JPG, JPEG, PNG, GIF files are allowed.", URLROOT . "/forums");
                }
                $sourcePath = $_FILES['upfile']['tmp_name'][$k]; // Storing source path of the file in a variable
                $fileSize = $_FILES['upfile']['size'][$k];
                $fileName = $_FILES['upfile']['name'][$k];
                $extension = substr($fileName, -3);
                $hash = sha1($sourcePath);

                $newfile = $hash . "." . $extension;
                $targetPath = TORRENTDIR."/attachment/" . $hash.".data"; // Target path where file is to be stored
                $thumbPath = "uploads/thumbnail/" . $hash.".jpg"; // Target path where attachment as jpg is to be stored

                if ($extension == 'zip') {
                    move_uploaded_file($sourcePath, $targetPath); // Moving Uploaded file
                    DB::run("
				                        INSERT INTO attachments (content_id, user_id, upload_date, filename, file_size, file_hash, topicid)
				                        VALUES (?,?,?,?,?,?,?)",
                        [$postid, $_SESSION['id'], TimeDate::gmtime(), $fileName,
                            $fileSize, $hash, $topicid]);
                } else {
                    if (move_uploaded_file($sourcePath, $targetPath)) { // Moving Uploaded file
                        SimpleThumbnail::create()->image($targetPath)->thumbnail(100)->to($thumbPath);
                        DB::run("
				                            INSERT INTO attachments (content_id, user_id, upload_date, filename, file_size, file_hash, topicid)
				                            VALUES (?,?,?,?,?,?,?)",
                            [$postid, $_SESSION['id'], TimeDate::gmtime(), $fileName,
                                $fileSize, $hash, $topicid]);
                    }
                }

            endforeach;
        endif;
        //Update topic last post
        update_topic_last_post($topicid);
		//All done, redirect user to the post
        if ($newtopic) {
            // shout it
            $msg_shout = "New Forum Topic: [url=" . URLROOT . "/forums/viewtopic&topicid=" . $topicid . "]" . $subject . "[/url] posted by [url=" . URLROOT . "/profile?id=" . $_SESSION['id'] . "]" . $_SESSION['username'] . "[/url]";
            DB::run("INSERT INTO shoutbox (userid, date, user, message) VALUES(?,?,?,?)", [0, TimeDate::get_date_time(), $_SESSION["username"], $msg_shout]);
            Redirect::to(URLROOT . "/forums/viewtopic&topicid=$topicid&page=last");
        } else {
            Redirect::to(URLROOT . "/forums/viewtopic&topicid=$topicid&page=last#post$postid");
        }
        die;
    }

    /**
     * View Forum Topic.  still todo
     */
    public function viewtopic()
    {
        $this->validForumUser();
        require_once APPROOT . "/helpers/bbcode_helper.php";
        $postsperpage = 20;
        $maxsubjectlength = 50;
        $topicid = $_GET["topicid"];
        $page = $_GET["page"];
        if (!$this->valid->validId($topicid)) {
            Session::flash('warning', "Topic Not Valid", 1);
        }

        $userid = $_SESSION["id"];

        //------ Get topic info
        $res = DB::run("SELECT * FROM forum_topics WHERE id=?", [$topicid]);
        $arr = $res->fetch(PDO::FETCH_ASSOC) or Session::flash('warning', "Topic not found", 1);
        $locked = ($arr["locked"] == 'yes');
        $subject = stripslashes($arr["subject"]);
        $sticky = $arr["sticky"] == "yes";
        $forumid = $arr["forumid"];

        // Check if user has access to this forum
        $res2 = DB::run("SELECT minclassread, guest_read FROM forum_forums WHERE id=?", [$forumid]);
        $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
        if (!$arr2 || $_SESSION["class"] < $arr2["minclassread"] && $arr2["guest_read"] == "no") {
            Session::flash('warning', "You do not have access to the forum this topic is in.", 1);
        }

        // Update Topic Views
        $viewsq = DB::run("SELECT views FROM forum_topics WHERE id=$topicid");
        $viewsa = $viewsq->fetch(PDO::FETCH_LAZY);
        $views = $viewsa[0];
        $new_views = $views + 1;
        $uviews = DB::run("UPDATE forum_topics SET views = $new_views WHERE id=$topicid");
        // End

        //------ Get forum
        $res = DB::run("SELECT * FROM forum_forums WHERE id=?", [$forumid]);
        $arr = $res->fetch(PDO::FETCH_ASSOC) or showerror("Forum is empty", Lang::T("FORUM_ERROR"));
        $forum = stripslashes($arr["name"]);

        //------ Get post count
        $res = DB::run("SELECT COUNT(*) FROM forum_posts WHERE topicid=?", [$topicid]);
        $arr = $res->fetch(PDO::FETCH_LAZY);
        $postcount = $arr[0];

        //------ Make page menu
        $pagemenu = "<br /><small>\n";
        $perpage = $postsperpage;
        $pages = floor($postcount / $perpage);
        if ($pages * $perpage < $postcount) {
            ++$pages;
        }

        if ($page == "last") {
            $page = $pages;
        } else {
            if ($page < 1) {
                $page = 1;
            } elseif ($page > $pages) {
                $page = $pages;
            }

        }
        $offset = max(0, ($page * $perpage) - $perpage);

        //
        if ($page == 1) {
            $pagemenu .= "<b>&lt;&lt; Prev</b>";
        } else {
            $pagemenu .= "<a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$topicid&amp;page=" . ($page - 1) . "'><b>&lt;&lt; Prev</b></a>";
        }

        //
        $pagemenu .= "&nbsp;&nbsp;";
        for ($i = 1; $i <= $pages; ++$i) {
            if ($i == $page) {
                $pagemenu .= "<b>$i</b>\n";
            } else {
                $pagemenu .= "<a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$topicid&amp;page=$i'><b>$i</b></a>\n";
            }

        }
        //
        $pagemenu .= "&nbsp;&nbsp;";
        if ($page == $pages) {
            $pagemenu .= "<b>Next &gt;&gt;</b><br /><br />\n";
        } else {
            $pagemenu .= "<a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$topicid&amp;page=" . ($page + 1) . "'><b>Next &gt;&gt;</b></a><br /><br />\n";
        }

        $pagemenu .= "</small>";

        //Get topic posts
        $res = DB::run("SELECT * FROM forum_posts WHERE topicid=$topicid ORDER BY id LIMIT $offset,$perpage");

        Style::header("View Topic: $subject");
        Style::begin("$forum &gt; $subject");
        forumheader("<a href='" . URLROOT . "/forums/viewforum&amp;forumid=$forumid'>$forum</a> <b style='font-size:16px; vertical-align:middle'>/</b> $subject");

        print("<div style='padding: 6px'>");

        $levels = get_forum_access_levels($forumid) or die;
        if ($_SESSION["class"] >= $levels["write"]) {
            $maypost = true;
        } else {
            $maypost = false;
        }

        if (!$locked && $maypost) {
            print("<div align='right'><a href='#bottom'><img src='" . URLROOT . "/assets/images/forum/button_reply.png' border='0' alt='' /></a></div>");
        } else {
            print("<div align='right'><img src='" . URLROOT . "/assets/images/forum/button_locked.png'  alt='" . Lang::T("FORUMS_LOCKED") . "' /></div>");
        }
        print("</div>");

        //------ Print table of posts
        $pc = $res->rowCount();
        $pn = 0;
        if ($_SESSION['loggedin'] == true) {
            $r = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=? AND topicid=?", [$_SESSION['id'], $topicid]);
            $a = $r->fetch(PDO::FETCH_LAZY);
            $lpr = $a[0];
            if (!$lpr) {
                DB::run("INSERT INTO forum_readposts (userid, topicid) VALUES($userid, $topicid)");
            }

        }

        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            ++$pn;
            $postid = $arr["id"];
            $posterid = $arr["userid"];
            $added = TimeDate::utc_to_tz($arr["added"]) . "(" . (TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

            //---- Get poster details
            $res4 = DB::run("SELECT COUNT(*) FROM forum_posts WHERE userid=?", [$posterid]);
            $arr33 = $res4->fetch(PDO::FETCH_LAZY);
            $forumposts = $arr33[0];

            $res2 = DB::run("SELECT * FROM users WHERE id=?", [$posterid]);
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $postername = Users::coloredname($arr2["username"]);

            if ($postername == "") {
                $by = "Deluser";
                $title = "Deleted Account";
                $privacylevel = "strong";
                $usersignature = "";
                $userdownloaded = "0";
                $useruploaded = "0";
                $avatar = "";
                $nposts = "-";
                $tposts = "-";
            } else {
                $avatar = htmlspecialchars($arr2["avatar"]);
                $userdownloaded = mksize($arr2["downloaded"]);
                $useruploaded = mksize($arr2["uploaded"]);
                $privacylevel = $arr2["privacy"];
                $usersignature = stripslashes(format_comment($arr2["signature"]));
                if ($arr2["downloaded"] > 0) {
                    $userratio = number_format($arr2["uploaded"] / $arr2["downloaded"], 2);
                } else
                if ($arr2["uploaded"] > 0) {
                    $userratio = "Inf.";
                } else {
                    $userratio = "---";
                }

                if (!$arr2["country"]) {
                    $usercountry = "unknown";
                } else {
                    $res4 = DB::run("SELECT name,flagpic FROM countries WHERE id=? LIMIT 1", [$arr2['country']]);
                    $arr4 = $res4->fetch(PDO::FETCH_ASSOC);
                    $usercountry = $arr4["name"];
                }

                $title = format_comment($arr2["title"]);
                $donated = $arr2['donated'];
                $by = "<a href='" . URLROOT . "/profile?id=$posterid'>$postername</a>" . ($donated > 0 ? "<img src='" . URLROOT . "/assets/images/star.png' alt='Donated' />" : "") . "";
            }

            if (!$avatar) {
                $avatar = URLROOT . "/assets/images/default_avatar.png";
            }

            # print("<a name=$postid>\n");
            print("<a id='post$postid'></a>");
            if ($pn == $pc) {
                print("<a name='last'></a>\n");
                if ($postid > $lpr && $_SESSION['loggedin'] == true) {
                    DB::run("UPDATE forum_readposts SET lastpostread=$postid WHERE userid=? AND topicid=?", [$userid, $topicid]);
                }

            }
            //Post Top
            /*
            print("<div class='table'><table class='table table-striped'><thead>
            <tr><th width='150'>$by</th><th align='left'><small>Posted at $added </small></th></tr></thead><tbody>");
             */
            ?>
        <div class="row navbarone">
        <div class="col-md-3 border border-info">
            <?php echo $by; ?>
        </div>
        <div class="col-md-9 border border-info">
            Posted at <?php echo $added; ?>
        </div>
        </div>

<?php
//Post Middle
            
$body = format_comment($arr["body"]); //htmlspecialchars_decode($arr["body"]);
//$body1 = stripslashes(format_comment($arr["body"]));
//$body = "<div style='white-space: pre'>".$body1."</div>";
            if ($this->valid->validId($arr['editedby'])) {
                $res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr['editedby']]);

                if ($res2->rowCount() == 1) {
                    $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
                    //edited by comment out if needed
                    $body .= "<br /><br /><small><i>Last edited by <a href='" . URLROOT . "/profile?id=$arr[editedby]'>$arr2[username]</b></a> on " . TimeDate::utc_to_tz($arr["editedat"]) . "</i></small><br />\n";
                    $body .= "\n";
                }
            }

            $quote = htmlspecialchars($arr["body"]);

            $postcount1 = DB::run("SELECT COUNT(forum_posts.userid) FROM forum_posts WHERE id=$posterid");

            while ($row = $postcount1->fetch(PDO::FETCH_LAZY)) {

                if ($privacylevel == "strong" && $_SESSION["control_panel"] != "yes") { //hide stats, but not from staff
                    $useruploaded = "---";
                    $userdownloaded = "---";
                    $userratio = "---";
                    $nposts = "-";
                    $tposts = "-";
                }
                /*
                print("<tr style='border: 1px solid black'   valign='top''><td  style='border: 1px solid black'  wi
                dth='150' align='left' class='comment-details'>
                <center><i>$title</i></center><br /><center>
                <img width='80' height='80' src='$avatar' alt='' /></center><br />
                Uploaded: $useruploaded<br />
                Downloaded: $userdownloaded<br />
                Posts: $forumposts<br />
                Ratio: $userratio<br />
                Location: $usercountry<br /><br /></td>");
                print("<td  style='border: 1px solid black'  class='comment'><br />$body<br />");
                 */
                ?>
        <div class="row border border-info">
        <div class="col-md-2 d-none d-sm-block border border-info">
                <center><i><?php echo $title; ?></i></center><br>
                <center><img width='80' height='80' src='<?php echo $avatar ?>' alt='' /></center><br>
                Uploaded: <?php echo $useruploaded; ?><br>
                Downloaded: <?php echo $userdownloaded; ?><br>
                Posts: <?php echo $forumposts; ?><br>
                Ratio: <?php echo $userratio; ?><br>
                Location: <?php echo $usercountry; ?><br>
        </div>
        <div class="col-md-10 border border-info"><br>
            <?php echo $body; ?>

                <?php
// attachments todo
$sql = DB::run("SELECT * FROM attachments WHERE content_id =?", [$postid]);
if ($sql->rowCount() != 0) {
    foreach ($sql as $row7) {
        print("<br>&nbsp;<b>$row7[filename]</b><br>");
        $extension = substr($row7['filename'], -3);
        if ($extension == 'zip') {
            $daimage = TORRENTDIR."/attachment/$row7[file_hash].data";
            if (file_exists($daimage)) {
                print(" <a class='btn btn-sm btn-success' href=\"" . URLROOT . "/download/attachment?id=$row7[id]&amp;hash=" . rawurlencode($row7["file_hash"]) . "\"><i class='fa fa-file-archive-o' aria-hidden='true'></i>Download</a><br>");
            } else {
                print("no zip<br>");
            }
        } else {
            $daimage = "uploads/thumbnail/$row7[file_hash].jpg";
            if (file_exists($daimage)) {
            

            ?>
                <!-- Trigger/Open The Model -->
                <img id="myBtn" src='<?php echo data_uri($daimage, $row7['filename']); ?>' height='110px' width='110px' border='0' alt=''  data-toggle="modal" data-target="#myModal-<?= $daimage; ?>">
                <!-- The Modal -->
                <div id="myModal-<?= $daimage; ?>" class="modal">
                  <!-- Modal content -->
                  <div class="modal-content">
                        <!-- The Close Button -->
                        <?php 
                        $switchimage =TORRENTDIR."/attachment/$row7[file_hash].data";
                        ?><button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><b>CLOSE</b></button><br><?php
                        echo $row7['filename']; ?><br>
                        <img src='<?php echo data_uri($switchimage, $row7['filename']); ?>' style="width:100%" alt=''>
                  </div>
                </div>
            <?php
            } else {
                print("<a href=\"" . URLROOT . "/download/attachment?id=$row7[id]&amp;hash=" . rawurlencode($row7["file_hash"]) . "\"><img src='" . URLROOT . "/thumb/$row7[file_hash].jpg' height='110px' width='110px' border='0' alt='' /></a><br>");
            }
        }
    }
}

                if (!$usersignature) {
                    print("<br /></td></tr>\n");
                } else {
                    print("<br /><hr /><br /><div class='f-sig' align='center'>$usersignature</div></td></tr>\n");
                }
            }
            ?>
        </div>
        </div>

            <div class="row navbarone">
            <div class="col-md-3 d-none d-sm-block">
            <a href='<?php echo URLROOT; ?>/profile?id=<?php echo $posterid; ?>'><img src='<?php echo URLROOT; ?>/assets/images/forum/icon_profile.png' border='0' alt='' /></a>
            <a href='<?php echo URLROOT; ?>/messages/create?id=<?php echo $posterid; ?>'><img src='<?php echo URLROOT; ?>/assets/images/forum/icon_pm.png' border='0' alt='' /></a>
            <a href='<?php echo URLROOT; ?>/report/forum?forumid=<?php echo $topicid ?>&amp;forumpost=<?php echo $postid ?>'><img src='<?php echo URLROOT; ?>/assets/images/forum/p_report.png' border='0' alt='" . Lang::T("FORUMS_REPORT_POST") . "' /></a>&nbsp;
            <a href='javascript:scroll(0,0);'><img src='<?php echo URLROOT; ?>/assets/images/forum/p_up.png'  alt='<?php echo Lang::T("FORUMS_GOTO_TOP_PAGE"); ?>' /></a>
            </div>
            <div class="col-md-9 d-none d-sm-block">
                <?php
// Hide Reply Mod
            if ($_SESSION["id"] !== $posterid) {
                // say thanks
                print("<a href='" . URLROOT . "/likes/likeforum?id=$topicid'><button class='btn btn-sm btn-success'>Say Thanks</button></a>&nbsp;");
            }

            //define buttons and who can use them
            if ($_SESSION["id"] == $posterid || $_SESSION["edit_forum"] == "yes" || $_SESSION["delete_forum"] == "yes") {
                print("<a href='" . URLROOT . "/forums/editpost&amp;postid=$postid'><img src='" . URLROOT . "/assets/images/forum/p_edit.png' border='0' alt='' /></a>&nbsp;");
            }
            if ($_SESSION["delete_forum"] == "yes") {
                print("<a href='" . URLROOT . "/forums/deletepost&amp;postid=$postid&amp;sure=0'><img src='" . URLROOT . "/assets/images/forum/p_delete.png' border='0' alt='' /></a>&nbsp;");
            }
            if (!$locked && $maypost) {
                print("<a href=\"javascript:SmileIT('[quote=$postername] $quote [/quote]', 'Form', 'body');\"><img src='" . URLROOT . "/assets/images/forum/p_quote.png' border='0' alt='' /></a>&nbsp;");
                print("<a href='#bottom'><img src='" . URLROOT . "/assets/images/forum/p_reply.png' alt='' /></a>");
            }
            ?>
            </div>
            </div><br>
<?php
//Post Bottom
            //print("<tr class='p-foot'><td width='150' align='center'><a href='" . URLROOT . "/profile?id=$posterid'><img src='" . URLROOT . "/assets/images/forum/icon_profile.png' border='0' alt='' /></a> <a href='" . URLROOT . "/messages/create?id=$posterid'><img src='" . URLROOT . "/assets/images/forum/icon_pm.png' border='0' alt='' /></a></td><td>");
            //print("<div style='float: left;'><a href='" . URLROOT . "/report/forum?forumid=$topicid&amp;forumpost=$postid'><img src='" . URLROOT . "/assets/images/forum/p_report.png' border='0' alt='" . Lang::T("FORUMS_REPORT_POST") . "' /></a>&nbsp;<a href='javascript:scroll(0,0);'><img src='" . URLROOT . "/assets/images/forum/p_up.png'  alt='" . Lang::T("FORUMS_GOTO_TOP_PAGE") . "' /></a></div><div align='right'>");

            //print("&nbsp;</div></td></tr></tbody><thead><tr></tr></thead></table></div>");
        }
        //-------- end posts table ---------//
        print($pagemenu);

        //quick reply
        if (!$locked && $_SESSION['loggedin'] == true) {
            //Style::begin("Reply", $newtopic = false);
            print("<fieldset class='download'><legend><b>" . Lang::T("FORUMS_POST_REPLY") . "</b></legend>");
            $newtopic = false;
            print("<a name='bottom'></a>");
            print("<form name='Form' method='post' action='" . URLROOT . "/forums/submittopic' enctype='multipart/form-data'>\n");
            if ($newtopic) {
                print("<input type='hidden' name='forumid' value='$id' />\n");
            } else {
                print("<input type='hidden' name='topicid' value='$topicid' />\n");
            }

            print("<table cellspacing='0' cellpadding='0' align='center'>");
            if ($newtopic) {
                print("<tr><td class='alt2'>" . Lang::T("FORUMS_SUBJECT") . "</td><td class='alt1' align='left' style='padding: 0px'><input type='text' size='100' maxlength='$maxsubjectlength' name='subject' style='border: 0px; height: 19px' /></td></tr>\n");
            }

            print("</table>");
///////////////////////////////////////////////////////////////////
            print("
            <div class='row justify-content-md-center'>
        <div class='col-md-8'>
            <textarea  id='example' style='height:300px;width:80%;' name='body' rows='13'></textarea>
            </div>
            </div>
            ");
            //textbbcode("Form", "body"); // todo


            echo 'Add attachment';
            echo '<center><input type="file" name="upfile[]" multiple></center><br>';
            //echo '<center><input type="file" name="upfile[]" multiple></center><br>';
            print("<center><br /><input type='image' src='" . URLROOT . "/assets/images/forum/button_reply.png' alt='' /></center>");
            print("</form>\n");
            //Style::end();
            print(" </fieldset>");
        } else {
            print("<img src='" . URLROOT . "/assets/images/forum/button_locked.png' alt='" . Lang::T("FORUMS_LOCKED") . "' /><br />");
        }
        //end quick reply

        if ($locked) {
            print(Lang::T("FORUMS_TOPIC_LOCKED") . "\n");
        } elseif (!$maypost) {
            print("<i>" . Lang::T("FORUMS_YOU_NOT_PERM_POST_FORUM") . "</i>\n");
        }

        //insert page numbers and quick jump

        // insert_quick_jump_menu($forumid);

        // MODERATOR OPTIONS
        if ($_SESSION["delete_forum"] == "yes" || $_SESSION["edit_forum"] == "yes") {
            print("<div class='f-border f-mod_options' align='center'><table width='100%' cellspacing='0'><tr class='f-title'><th><center>" . Lang::T("FORUMS_MOD_OPTIONS") . "</center></th></tr>\n");
            $res = DB::run("SELECT id,name,minclasswrite FROM forum_forums ORDER BY name");
            print("<tr><td class='ttable_col2'>\n");
            print("<form method='post' action='" . URLROOT . "/forums/renametopic'>\n");
            print("<input type='hidden' name='topicid' value='$topicid' />\n");
            print("<input type='hidden' name='returnto' value='forums/viewtopic&amp;topicid=$topicid' />\n");
            print("<div align='center'  style='padding:3px'>Rename topic: <input type='text' name='subject' size='60' maxlength='$maxsubjectlength' value='" . stripslashes(htmlspecialchars($subject)) . "' />\n");
            print("<input type='submit' value='Apply' />");
            print("</div></form>\n");
            print("<form method='post' action='" . URLROOT . "/forums/movetopic&amp;topicid=$topicid'>\n");
            print("<div align='center' style='padding:3px'>");
            print("Move this thread to: <select name='forumid'>");
            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                if ($arr["id"] != $forumid && $_SESSION["class"] >= $arr["minclasswrite"]) {
                    print("<option value='" . $arr["id"] . "'>" . $arr["name"] . "</option>\n");
                }
            }

            print("</select> <input type='submit' value='Apply' /></div></form>\n");
            print("<div align='center'>\n");
            if ($locked) {
                print(Lang::T("FORUMS_LOCKED") . ": <a href='" . URLROOT . "/forums/unlocktopic&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Unlock'><img src='" . URLROOT . "/assets/images/forum/topic_unlock.png' alt='UnLock Topic' /></a>\n");
            } else {
                print(Lang::T("FORUMS_LOCKED") . ": <a href='" . URLROOT . "/forums/locktopic&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Lock'><img src='" . URLROOT . "/assets/images/forum/topic_lock.png' alt='Lock Topic' /></a>\n");
            }

            print("Delete Entire Topic: <a href='" . URLROOT . "/forums/deletetopic&amp;topicid=$topicid&amp;sure=0' title='Delete'><img src='" . URLROOT . "/assets/images/forum/topic_delete.png' alt='Delete Topic' /></a>\n");
            if ($sticky) {
                print(Lang::T("FORUMS_STICKY") . ": <a href='" . URLROOT . "/forums/unsetsticky&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='UnStick'><img src='" . URLROOT . "/assets/images/forum/folder_sticky_new.png' alt='UnStick Topic' /></a>\n");
            } else {
                print(Lang::T("FORUMS_STICKY") . ": <a href='" . URLROOT . "/forums/setsticky&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Stick'><img src='" . URLROOT . "/assets/images/forum/folder_sticky.png' alt='Stick Topic' /></a>\n");
            }

            print("</div></td></tr></table></div>\n");

        }
        Style::end();

        Style::footer();
        die;
    }

    /**
     * Delete a Post.
     */
    public function deletepost()
    {
        $this->validForumUser();
        $postid = Input::get("postid");
        $sure = Input::get("sure");
        if ($_SESSION["delete_forum"] != "yes" || !$this->valid->validId($postid)) {
            Session::flash('info', Lang::T("FORUMS_DENIED"), URLROOT . "/forums");
        }
        // Get topic id
        $res = DB::run("SELECT topicid FROM forum_posts WHERE id=?", [$postid]);
        $arr = $res->fetch(PDO::FETCH_LAZY) or Session::flash('info', Lang::T("FORUMS_NOT_FOUND_POST"), URLROOT . "/forums");
        $topicid = $arr[0];
        // We can not delete the post if it is the only one of the topic
        $res = DB::run("SELECT COUNT(*) FROM forum_posts WHERE topicid=?", [$topicid]);
        $arr = $res->fetch(PDO::FETCH_LAZY);
        if ($arr[0] < 2) {
            Session::flash('info', "$topicid is the last post in topic", URLROOT . "/forums");
        }
        // Delete post
        DB::run("DELETE FROM forum_posts WHERE id=?", [$postid]);
		        // Delete attachment todo	
        $sql = DB::run("SELECT * FROM attachments WHERE content_id =?", [$postid]);	
        if ($sql->rowCount() != 0) {	
            foreach ($sql as $row7) {	
                //print("<br>&nbsp;<b>$row7[filename]</b><br>");	
                $daimage = TORRENTDIR."/attachment/$row7[file_hash].data";	
                if (file_exists($daimage)) {	
                    if (unlink($daimage)) {	
                        DB::run("DELETE FROM attachments WHERE content_id=?", [$postid]);	
                    }	
                }	
                $extension = substr($row7['filename'], -3);	
                if ($extension != 'zip') {	
                    $dathumb = "uploads/thumbnail/$row7[file_hash].jpg";	
                    if (!unlink($dathumb)) {	
                        Session::flash('info', "Could not remove thumbnail = $row7[file_hash].jpg", URLROOT . "/forums/viewtopic&topicid=$topicid");	
                    }	
                }	
            }	
        }
        // Update topic
        update_topic_last_post($topicid);
        Session::flash('info', "Post $topicid has been deleted", URLROOT . "/forums/viewtopic&topicid=$topicid");
        die;
    }

    /**
     * Delete a Topic.
     */
    public function deletetopic()
    {
        $this->validForumUser();
        $topicid = Input::get("topicid");
        if (!$this->valid->validId($topicid) || $_SESSION["delete_forum"] != "yes") {
            Session::flash('warning', Lang::T("FORUMS_DENIED"), 1);
        }
        $sure = Input::get("sure");
        if ($sure == "0") {
            Session::flash('warning', "Sanity check: You are about to delete a topic. Click <a href='" . URLROOT . "/forums/deletetopic&amp;topicid=$topicid?sure=1'>here</a> if you are sure.", URLROOT . "/forums");
        }
        $this->forumModel->deltopic($topicid);
        Session::flash('info', "Deleted topic", URLROOT . "/forums");
        die;
    }

    /**
     * Rename a Topic.
     */
    public function renametopic()
    {
        $this->validForumUser();
        if ($_SESSION["delete_forum"] != "yes" && $_SESSION["edit_forum"] != "yes") {
            Session::flash('warning', Lang::T("FORUMS_DENIED"), URLROOT . "/forums");
        }
        $topicid = Input::get('topicid');
        if (!$this->valid->validId($topicid)) {
            Session::flash('warning', Lang::T("FORUMS_DENIED"), URLROOT . "/forums");
        }
        $subject = Input::get('subject');
        if ($subject == '') {
            Session::flash('warning', Lang::T("FORUMS_YOU_MUST_ENTER_NEW_TITLE"), URLROOT . "/forums");
        }
        $subject = $subject;
        $this->forumModel->rename($subject, $topicid);
        $returnto = Input::get('returnto');
        if ($returnto) {
            header("Location: $returnto");
        }
        die;
    }

    /**
     * Move a Topic.
     */
    public function movetopic()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        if (!$this->valid->validId($forumid) || !$this->valid->validId($topicid) || $_SESSION["delete_forum"] != "yes" || $_SESSION["edit_forum"] != "yes") {
            Session::flash('warning', $topicid, 1);
        }
        // Make sure topic and forum is valid
        $res = DB::run("SELECT minclasswrite FROM forum_forums WHERE id=?", [$forumid]);
        if ($res->rowCount() != 1) {
            Session::flash('warning', Lang::T("FORUMS_NOT_FOUND"), URLROOT . "/forums");
        }
        $arr = $res->fetch(PDO::FETCH_LAZY);
        if ($_SESSION['class'] < $arr[0]) {
            Session::flash('warning', Lang::T("FORUMS_NOT_ALLOWED"), URLROOT . "/forums");
        }
        $res = DB::run("SELECT subject,forumid FROM forum_topics WHERE id=?", [$topicid]);
        if ($res->rowCount() != 1) {
            Session::flash('warning', Lang::T("FORUMS_NOT_FOUND_TOPIC"), URLROOT . "/forums");
        }
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if ($arr["forumid"] != $forumid) {
            DB::run("UPDATE forum_topics SET forumid=$forumid, moved='yes' WHERE id=$topicid");
        }
        Redirect::to(URLROOT . "/forums/viewforum&forumid=$forumid");
        die;
    }

    /**
     * Lock a Topic.
     */
    public function locktopic()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");
        if (!$this->valid->validId($topicid) || $_SESSION["delete_forum"] != "yes" || $_SESSION["edit_forum"] != "yes") {
            Session::flash('warning', Lang::T("FORUMS_DENIED"), URLROOT . "/forums");
        }
        $this->forumModel->lock($topicid, 'yes');
        Redirect::to(URLROOT . "/forums/viewforum&forumid=$forumid&page=$page");
        die;
    }

    /**
     * Unlock a Topic.
     */
    public function unlocktopic()
    {

        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");
        if (!$this->valid->validId($topicid) || $_SESSION["delete_forum"] != "yes" || $_SESSION["edit_forum"] != "yes") {
            Session::flash('warning', Lang::T("FORUMS_DENIED"), URLROOT . "/forums");
        }
        $this->forumModel->lock($topicid, 'no');
        Redirect::to(URLROOT . "/forums/viewforum&forumid=$forumid&page=$page");
        die;
    }

    /**
     * Set Topic Sticky.
     */
    public function setsticky()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");
        if (!$this->valid->validId($topicid) || ($_SESSION["delete_forum"] != "yes" && $_SESSION["edit_forum"] != "yes")) {
            Session::flash('warning', Lang::T("FORUMS_DENIED"), URLROOT . "/forums");
        }
        $this->forumModel->sticky($topicid, 'yes');
        Redirect::to(URLROOT . "/forums/viewforum&forumid=$forumid&page=$page");
        die;
    }

    /**
     * Unstick a Topic.
     */
    public function unsetsticky()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");
        if (!$this->valid->validId($topicid) || ($_SESSION["delete_forum"] != "yes" && $_SESSION["edit_forum"] != "yes")) {
            Session::flash('warning', Lang::T("FORUMS_DENIED"), URLROOT . "/forums");
        }
        $this->forumModel->sticky($topicid, 'no');
        Redirect::to(URLROOT . "/forums/viewforum&forumid=$forumid&page=$page");
        die;
    }

    public function user()
    {

        require_once APPROOT . "/helpers/bbcode_helper.php";
        $id = (int) ($_GET["id"] ?? 0);
        if (!isset($id) || !$id) {
            Session::flash('warning', Lang::T("ERROR"), URLROOT."/home");
        }

        //TORRENT
        $title = Lang::T("User Posts");
            
        $res = DB::run("SELECT 
            forum_posts.id, topicid, userid, forum_posts.added, body, 
            avatar, signature, username, title, class, uploaded, downloaded, privacy, donated 
            FROM forum_posts
            LEFT JOIN users 
            ON forum_posts.userid = users.id 
            WHERE userid = $id ORDER BY forum_posts.userid "); //$limit
        //$res = DB::run("SELECT * FROM comments WHERE user =?", [$id]);
            $row = $res->fetch(PDO::FETCH_LAZY);
            if (!$row) {
                Session::flash('warning', "User has not posted in forum", URLROOT."/home");
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='profile?id=" . $row['userid'] . "'>&nbsp;$row[username]</a>";


        Style::header(Lang::T("COMMENTS"));
        Style::begin($title);
        while ($row = $res->fetch(PDO::FETCH_LAZY)) {

            $postername = Users::coloredname($row["username"]);
            if ($postername == "") {
                $postername = Lang::T("DELUSER");
                $title = Lang::T("DELETED_ACCOUNT");
                $avatar = "";
                $usersignature = "";
                $userdownloaded = "";
                $useruploaded = "";
            } else {
                $privacylevel = $row["privacy"];
                $avatar = htmlspecialchars($row["avatar"]);
                $title = format_comment($row["title"]);
                $usersignature = stripslashes(format_comment($row["signature"]));
                $userdownloaded = mksize($row["downloaded"]);
                $useruploaded = mksize($row["uploaded"]);
            }
    
            if ($row["downloaded"] > 0) {
                $userratio = number_format($row["uploaded"] / $row["downloaded"], 2);
            } else {
                $userratio = "---";
            }
    
            if (!$avatar) {
                $avatar = URLROOT . "/assets/images/default_avatar.png";
            }
    
            $commenttext = format_comment($row["body"]);
    
            $edit = null;
            if ($_SESSION["edit_torrents"] == "yes" || $_SESSION["edit_news"] == "yes" || $_SESSION['id'] == $row['user']) {
                $edit = '[<a href="' . URLROOT . '/forums/editpost&amp;postid='.$row['id'].'">Edit</a>]&nbsp;';
            }
    
            $delete = null;
            if ($_SESSION["delete_torrents"] == "yes" || $_SESSION["delete_news"] == "yes") {
                $delete = '[<a href="' . URLROOT . '/forums/deletepost&amp;postid='.$row['id'].'&amp;sure=0">Delete</a>]&nbsp;';
            }
    
            print('<div class="container"><table class="table table-striped" style="border: 1px solid black" >');
            print('<thead><tr">');
            print('<th align="center" width="150"></th>');
            print('<th align="right">' . $edit . $delete . '[<a href="' . URLROOT . '/report/forum?forumid='. $row['topicid'] .'&amp;forumpost='. $row['id'] .'">Report</a>] Posted: ' . date("d-m-Y \\a\\t H:i:s", TimeDate::utc_to_tz_time($row["added"])) . '<a id="comment' . $row["id"] . '"></a></th>');
            print('</tr></thead>');
            print('<tr valign="top">');
            if ($_SESSION['edit_users'] == 'no' && $privacylevel == 'strong') {
                print('<td align="left" width="150"><center><a href="' . URLROOT . '/profile?id=' . $row['id'] . '"><b>' . $postername . '</b></a><br /><i>' . $title . '</i><br /><img width="80" height="80" src="' . $avatar . '" alt="" /><br /><br />Uploaded: ---<br />Downloaded: ---<br />Ratio: ---<br /><br /><a href="'.URLROOT.'/profile?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: DEFAULTTHEME) . '/forums/icon_profile.png" border="" alt="" /></a> <a href="'.URLROOT.'/messages/create?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: DEFAULTTHEME) . '/forums/icon_pm.png" border="0" alt="" /></a></center></td>');
            } else {
                print('<td align="left" width="150"><center><a href="' . URLROOT . '/profile?id=' . $row['id'] . '"><b>' . $postername . '</b></a><br /><i>' . $title . '</i><br /><img width="80" height="80" src="' . $avatar . '" alt="" /><br /><br />Uploaded: ' . $useruploaded . '<br />Downloaded: ' . $userdownloaded . '<br />Ratio: ' . $userratio . '<br /><br /><a href="'.URLROOT.'/profile?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: DEFAULTTHEME) . '/forums/icon_profile.png" border="0" alt="" /></a> <a href="/messages/create?id=' . $row["user"] . '"><img src="themes/' . ($_SESSION['stylesheet'] ?: DEFAULTTHEME) . '/forums/icon_pm.png" border="0" alt="" /></a></center></td>');
            }
    
            print('<td>' . $commenttext . '<hr />' . $usersignature . '</td>');
            print('</tr>');
            print('</table></div>');
        }
        Style::end();
        Style::footer();

    }

}