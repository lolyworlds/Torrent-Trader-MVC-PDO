<?php
Style::begin(Lang::T("Forum Home"));
forumheader($data['forumname']);
// MAIN LAYOUT
if ($_SESSION['loggedin'] == true) {
?>
    <table class='table table-striped'><tr><td>
        <div align='right'><a href='<?php echo URLROOT; ?>/forums/newtopic&amp;forumid=<?php echo $data['forumid']; ?>'><img src='<?php echo URLROOT; ?>/assets/images/forum/button_new_post.png' alt='' /></a></div>
    </td></tr></table>
<?php }
if ($data['topicsres'] > 0) {
    ?>
    <div class="row">
    <div class="col-lg-12">
    <div class="wrapper wrapper-content animated fadeInRight">

    <div class="row navbarone">
    <div class="col-md-1">
    Read
    </div>
    <div class="col-md-5">
    Topic
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Replies
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Views
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Author
    </div>
    <div class="col-md-2 d-none d-sm-block">
    Last Post
    </div>
<?php
    if ($_SESSION["edit_forum"] == "yes" || $_SESSION["delete_forum"] == "yes") {
        ?>
    <div class="col-md-1 d-none d-sm-block">
    Moderate
    </div>
        <?php
    }
    print("</div>");

    
    foreach ($data['topicsres'] as $topicarr) {
        $topicid = $topicarr["id"];
        $topic_userid = $topicarr["userid"];
        $locked = $topicarr["locked"] == "yes";
        $moved = $topicarr["moved"] == "yes";
        $sticky = $topicarr["sticky"] == "yes";
        //---- Get reply count
        $res = DB::run("SELECT COUNT(*) FROM forum_posts WHERE topicid=$topicid");
        $arr = $res->fetch(PDO::FETCH_LAZY);
        $posts = $arr[0];
        $replies = max(0, $posts - 1);
        //---- Get userID and date of last post
        $res = DB::run("SELECT * FROM forum_posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $lppostid = $arr["id"];
        $lpuserid = (int) $arr["userid"];
        $lpadded = TimeDate::utc_to_tz($arr["added"]);
        //------ Get name of last poster
        if ($lpuserid > 0) {
            $res = DB::run("SELECT * FROM users WHERE id=$lpuserid");
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $lpusername = "<a href='" . URLROOT . "/profile?id=$lpuserid'>" . Users::coloredname($arr['username']) . "</a>";
            } else {
                $lpusername = "Deluser";
            }
        } else {
            $lpusername = "Deluser";
        }
        //------ Get author
        if ($topic_userid > 0) {
            $res = DB::run("SELECT username FROM users WHERE id=$topic_userid");
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $lpauthor = "<a href='" . URLROOT . "/profile?id=$topic_userid'>" . Users::coloredname($arr['username']) . "</a>";
            } else {
                $lpauthor = "Deluser";
            }
        } else {
            $lpauthor = "Deluser";
        }
        // Topic Views
        $viewsq = DB::run("SELECT views FROM forum_topics WHERE id=$topicid");
        $viewsa = $viewsq->fetch(PDO::FETCH_LAZY);
        $views = $viewsa[0];
        // End
        //---- Print row
        if ($_SESSION) {
            $r = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=$_SESSION[id] AND topicid=$topicid");
            $a = $r->fetch(PDO::FETCH_LAZY);
        }
        $new = !$a || $lppostid > $a[0];
        $topicpic = ($locked ? ($new ? "folder_locked_new" : "folder_locked") : ($new ? "folder_new" : "folder"));
        $subject = ($sticky ? "<b>" . Lang::T("FORUMS_STICKY") . ": </b>" : "") . "<a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$topicid'><b>" .
        encodehtml(stripslashes($topicarr["subject"])) . "</b></a>$topicpages";
        ?>
        <div class="row border border-info">
        <div class="col-md-1 d-none d-sm-block">
        <img src='<?php echo URLROOT; ?>/assets/images/forum/<?php echo $topicpic ?>.png' alt='' />
        </div>
        <div class="col-md-5">
        <?php echo $subject; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $replies; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $views; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $lpauthor; ?>
        </div>
        <div class="col-md-2">
        <span class='small'>by&nbsp;<?php echo $lpusername; ?><br /><span style='white-space: nowrap'><?php echo $lpadded; ?></span></span>
        </div>
            <?php
    
                if ($_SESSION["edit_forum"] == "yes" || $_SESSION["delete_forum"] == "yes") {
                print("<div class='col-md-1 d-none d-sm-block'>");
                if ($locked) {
                    print("<a href='" . URLROOT . "/forums/unlocktopic&amp;forumid=$data[forumid]&amp;topicid=$topicid&amp;page=$page' title='Unlock'><img src='" . URLROOT . "/assets/images/forum/topic_unlock.png' alt='UnLock Topic' /></a>\n");
                } else {
                    print("<a href='" . URLROOT . "/forums/locktopic&amp;forumid=$data[forumid]&amp;topicid=$topicid&amp;page=$page' title='Lock'><img src='" . URLROOT . "/assets/images/forum/topic_lock.png' alt='Lock Topic' /></a>\n");
                }
    
                print("<a href='" . URLROOT . "/forums/deletetopic&amp;topicid=$topicid&amp;sure=0' title='Delete'><img src='" . URLROOT . "/assets/images/forum/topic_delete.png' alt='Delete Topic' /></a>\n");
                if ($sticky) {
                    print("<a href='" . URLROOT . "/forums/unsetsticky&amp;forumid=$data[forumid]&amp;topicid=$topicid&amp;page=$page' title='UnStick'><img src='" . URLROOT . "/assets/images/forum/older_sticky_new.png' alt='Unstick Topic' /></a>\n");
                } else {
                    print("<a href='" . URLROOT . "/forums/setsticky&amp;forumid=$data[forumid]&amp;topicid=$topicid&amp;page=$page' title='Stick'><img src='" . URLROOT . "/assets/images/forum/folder_sticky.png' alt='Stick Topic' /></a>\n");
                }
    
                print("</div>");
            }
        print("</div>");
    }

    print("</div></div></div>");
    print ($data['pagerbottom']);
} else {
    print("<p align='center'>No topics found</p>\n");
}

print("<table cellspacing='5' cellpadding='0'><tr valign='middle'>\n");
print("<td><img src='" . URLROOT . "/assets/images/forum/folder_new.png' style='margin-right: 5px' alt='' /></td><td >New posts</td>\n");
print("<td><img src='" . URLROOT . "/assets/images/forum/folder.png' style='margin-left: 10px; margin-right: 5px' alt='' />" .
    "</td><td>No New posts</td>\n");
print("<td><img src='" . URLROOT . "/image/forum/folder_locked.png' style='margin-left: 10px; margin-right: 5px' alt='' />" .
    "</td><td>" . Lang::T("FORUMS_LOCKED") . " topic</td></tr></tbody></table>\n");
print("<table cellspacing='0' cellpadding='0'><tr>\n");
print("</tr></table>\n");
insert_quick_jump_menu($data['forumid']);
Style::end();