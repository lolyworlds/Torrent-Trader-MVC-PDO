<?php

//setup the forum head
function forumheader($location)
{
  global $site_config;
    echo "<div>
    <img src='$site_config[SITEURL]/images/forum/help.png'  alt='' />&nbsp;<a href='$site_config[SITEURL]/faq'>" . T_("FORUM_FAQ") . "</a>&nbsp; &nbsp;&nbsp;
    <img src='$site_config[SITEURL]/images/forum/search.png' alt='' />&nbsp;<a href='$site_config[SITEURL]/forums/search'>" . T_("SEARCH") . "</a>&nbsp; &nbsp;
    <b>" . T_("FORUM_CONTROL") . "</b> 
    &middot; <a href='$site_config[SITEURL]/forums/viewunread'>" . T_("FORUM_NEW_POSTS") . "</a> 
    &middot; <a href='$site_config[SITEURL]/forums?catchup'>" . T_("FORUM_MARK_READ") . "</a>
   </div>
    <br />";
    print("<div>" . T_("YOU_ARE_IN") . ": &nbsp;<a href='$site_config[SITEURL]/forums'>" . T_("FORUMS") . "</a> <b style='vertical-align:middle'>/ $location</b></div>");
}

// Mark all forums as read
function catch_up()
{
    global $CURUSER, $pdo;

    if (!$CURUSER) {
        return;
    }

    $userid = $CURUSER["id"];
    $res = $pdo->run("SELECT id, lastpost FROM forum_topics");
    while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
        $topicid = $arr["id"];
        $postid = $arr["lastpost"];
        $r = $pdo->run("SELECT id,lastpostread FROM forum_readposts WHERE userid=? and topicid=?", [$userid, $topicid]);
        if ($r->rowCount() == 0) {
            $pdo->run("INSERT INTO forum_readposts (userid, topicid, lastpostread) VALUES(?, ?, ?), [$userid, $topicid, $postid]");
        } else {
            $a = $r->fetch(PDO::FETCH_ASSOC);
            if ($a["lastpostread"] < $postid) {
                $pdo->run("UPDATE forum_readposts SET lastpostread=$postid WHERE id=?", [$a["id"]]);
            }

        }
    }
}

// Returns the minimum read/write class levels of a forum
function get_forum_access_levels($forumid)
{
    global $pdo;
    $res = $pdo->run("SELECT minclassread, minclasswrite FROM forum_forums WHERE id=?", [$forumid]);
    if ($res->rowCount() != 1) {
        return false;
    }

    $arr = $res->fetch(PDO::FETCH_ASSOC);
    return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"]);
}

// Returns the forum ID of a topic, or false on error
function get_topic_forum($topicid)
{
    global $pdo;
    $res = $pdo->run("SELECT forumid FROM forum_topics WHERE id=?", [$topicid]);
    if ($res->rowCount() != 1) {
        return false;
    }

    $arr = $res->fetch(PDO::FETCH_LAZY);
    return $arr[0];
}

// Returns the ID of the last post of a forum
function update_topic_last_post($topicid)
{
    global $pdo;
    $res = DB::run("SELECT id FROM forum_posts WHERE topicid=? ORDER BY id DESC LIMIT 1", [$topicid]);
    $arr = $res->fetch(PDO::FETCH_LAZY) or showerror(T_("FORUM_ERROR"), "No post found");
    $postid = $arr[0];
    $pdo->run("UPDATE forum_topics SET lastpost=? WHERE id=?", [$postid, $topicid]);
}

function get_forum_last_post($forumid)
{
    global $pdo;
    $res = $pdo->run("SELECT lastpost FROM forum_topics WHERE forumid=? ORDER BY lastpost DESC LIMIT 1", [$forumid]);
    $arr = $res->fetch(PDO::FETCH_LAZY);
    $postid = $arr[0];
    if ($postid) {
        return $postid;
    } else {
        return 0;
    }

}

//Top forum posts
function forumpostertable($res)
{
    print("<br /><div>");
    ?>
      <font><?php echo T_("FORUM_RANK"); ?></font>
      <font><?php echo T_("FORUM_USER"); ?></font>
      <font><?php echo T_("FORUM_POST"); ?></font>
      <br>
    <?php
    global $site_config, $pdo;
    $num = 0;
    while ($a = $res->fetch(PDO::FETCH_ASSOC)) {
        ++$num;
        print("$num &nbsp; <a href='".$site_config['SITEURL']."/users/profile?id=$a[id]'><b>$a[username]</b></a> $a[num]");
    }

    if ($num == 0) {
        print("<b>No Forum Posters</b>");
    }

    print("</div>");
}

// Inserts a quick jump menu
function insert_quick_jump_menu($currentforum = 0)
{
    global $CURUSER, $site_config, $pdo;
    print("<div style='text-align:right'><form method='get' action='?' name='jump'>\n");
    print("<input type='hidden' name='action' value='".$site_config['SITEURL']."/forums/viewforum' />\n");
    $res = $pdo->run("SELECT * FROM forum_forums ORDER BY name");

    if ($res->rowCount() > 0) {
        print(T_("FORUM_JUMP") . ": ");
        print("<select class='styled' name='forumid' onchange='if(this.options[this.selectedIndex].value != -1){ forms[jump].submit() }'>\n");

        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            if (get_user_class() >= $arr["minclassread"] || (!$CURUSER && $arr["guest_read"] == "yes")) {
                print("<option value='" . $arr["id"] . "'" . ($currentforum == $arr["id"] ? " selected='selected'>" : ">") . $arr["name"] . "</option>\n");
            }

        }

        print("</select>\n");
        print("<button type='submit' class='btn btn-sm btn-primary'>" . T_("GO") . "</button>\n");
    }

    // print("<input type='submit' value='Go!'>\n");
    print("</form>\n</div>");
}

// Inserts a compose frame
function insert_compose_frame($id, $newtopic = true)
{
    global $maxsubjectlength, $site_config, $pdo;

    if ($newtopic) {
        $res = $pdo->run("SELECT name FROM forum_forums WHERE id=$id");
        $arr = $res->fetch(PDO::FETCH_ASSOC) or showerror(T_("FORUM_ERROR"), T_("FORUM_BAD_FORUM_ID"));
        $forumname = stripslashes($arr["name"]);

        print("<p align='center'><b>" . T_("FORUM_NEW_TOPIC") . " <a href='".$site_config['SITEURL']."/forums/viewforum&amp;forumid=$id'>$forumname</a></b></p>\n");
    } else {
        $res = $pdo->run("SELECT * FROM forum_topics WHERE id=$id");
        $arr = $res->fetch(PDO::FETCH_ASSOC) or showerror(T_("FORUM_ERROR"), T_("FORUMS_NOT_FOUND_TOPIC"));
        $subject = stripslashes($arr["subject"]);
        print("<p align='center'>" . T_("FORUM_REPLY_TOPIC") . ": <a href='".$site_config['SITEURL']."/forums/viewtopic&amp;topicid=$id'>$subject</a></p>");
    }

    # Language Marker #
    print("<p align='center'>" . T_("FORUM_RULES") . "\n");
    print("<br />" . T_("FORUM_RULES2") . "<br /></p>\n");

    #begin_frame("Compose Message", true);
    print("<div class=table>");
    print("<center><b>Compose Message</b></center>");
    print("<form name='Form' method='post' action='$site_config[SITEURL]/forums/post'>\n");
    if ($newtopic) {
        print("<table class='table'>
        <tr>
        <td align='center'><strong>Subject:</strong>
        <input type='text' maxlength='$maxsubjectlength' name='subject' /></td></tr>");
        print("<input type='hidden' name='forumid' value='$id' />\n");
    } else {
        print("<input type='hidden' name='topicid' value='$id' />\n");
    }

    if ($newtopic) {
        print("<tr><td align='center'>");
        textbbcode("Form", "body");
        print("</td></tr><tr><td align='center'><br /><button type='submit' class='btn btn-sm btn-primary'>" . T_("SUBMIT") . "</button></td></tr></table>
			");

    }
    print("</center>");
    print("</form>\n");
    print("</div>");
    #end_frame();

    insert_quick_jump_menu();
}

//LASTEST FORUM POSTS
function latestforumposts()
{
    global $pdo, $site_config;
    
    print("<div><table style='border: 1px solid black;'>
    <thead><tr>
        <th>Latest Topic Title</th>
        <th>Replies</th>
        <th>Views</th>
        <th>Author</th>
        <th>Last Post</th>
    </tr></thead>");

/// HERE GOES THE QUERY TO RETRIEVE DATA FROM THE DATABASE AND WE START LOOPING ///
    $for = $pdo->run("SELECT * FROM forum_topics ORDER BY lastpost DESC LIMIT 5");

    if ($for->rowCount() == 0) {
        print("<tr><td class='alt1' align='center' colspan='5'><b>No Latest Topics</b></td></tr>");
    }

    while ($topicarr = $for->fetch(PDO::FETCH_ASSOC)) {
// Set minclass
        $res = $pdo->run("SELECT name,minclassread,guest_read FROM forum_forums WHERE id=$topicarr[forumid]");
        $forum = $res->fetch(PDO::FETCH_ASSOC);

        if ($forum && get_user_class() >= $forum["minclassread"] || $forum["guest_read"] == "yes") {
            $forumname = "<a href='".$site_config['SITEURL']."/forums/viewforum&amp;forumid=$topicarr[forumid]'><b>" . htmlspecialchars($forum["name"]) . "</b></a>";

            $topicid = $topicarr["id"];
            $topic_title = stripslashes($topicarr["subject"]);
            $topic_userid = $topicarr["userid"];
// Topic Views
            $views = $topicarr["views"];
// End

/// GETTING TOTAL NUMBER OF POSTS ///
            $res = $pdo->run("SELECT COUNT(*) FROM forum_posts WHERE topicid=?", [$topicid]);
            $arr = $res->fetch(PDO::FETCH_LAZY);
            $posts = $arr[0];
            $replies = max(0, $posts - 1);

/// GETTING USERID AND DATE OF LAST POST ///
            $res = $pdo->run("SELECT * FROM forum_posts WHERE topicid=? ORDER BY id DESC LIMIT 1", [$topicid]);
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            $postid = 0 + $arr["id"];
            $userid = 0 + $arr["userid"];
            $added = utc_to_tz($arr["added"]);

/// GET NAME OF LAST POSTER ///
            $res = $pdo->run("SELECT id, username FROM users WHERE id=$userid");
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $username = "<a href='".$site_config['SITEURL']."/users/profile?id=$userid'>" . class_user_colour($arr['username']) . "</a>";
            } else {
                $username = "Unknown[$topic_userid]";
            }

/// GET NAME OF THE AUTHOR ///
            $res = $pdo->run("SELECT username FROM users WHERE id=?", [$topic_userid]);
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $author = "<a href='".$site_config['SITEURL']."/users/profile?id=$topic_userid'>" . class_user_colour($arr['username']) . "</a>";
            } else {
                $author = "Unknown[$topic_userid]";
            }

/// GETTING THE LAST INFO AND MAKE THE TABLE ROWS ///
            $r = $pdo->run("SELECT lastpostread FROM forum_readposts WHERE userid=$userid AND topicid=$topicid");
            $a = $r->fetch(PDO::FETCH_LAZY);
            $new = !$a || $postid > $a[0];
            $subject = "<a href='".$site_config['SITEURL']."/forums/viewtopic&amp;topicid=$topicid'><b>" . stripslashes(encodehtml($topicarr["subject"])) . "</b></a>";

            print("<tr style='border: 1px solid black;'>
                 <td class='f-img' width='100%'>$subject</td>" .
                "<td style='plainborder' align='center'>$replies</td>" .
                "<td style='border: 1px solid black' align='center'>$views</td>" .
                "<td style='border: 1px solid black' align='center'>$author</td>" .
                "<td style='border: 1px solid black'  align='right'><font size=1>$subject</font><small>&nbsp;by&nbsp;$username<br /></small><small style='white-space: nowrap'>$added</small></td>");

            print("</tr>");
        } // while
    }
    print("</table></div><br />");
} // end function