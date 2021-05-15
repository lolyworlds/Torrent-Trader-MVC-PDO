<div class="card">
<div class="card-header">
    <?php echo Lang::T("Search Forums"); ?>
</div>
<div class="card-body">
<div>
    <img src='<?php echo URLROOT; ?>/assets/images/forum/help.png'  alt='' />&nbsp;
    <a href='<?php echo URLROOT; ?>/faq'><?php echo Lang::T("FORUM_FAQ"); ?></a>&nbsp; &nbsp;&nbsp;
    <img src='<?php echo URLROOT; ?>/assets/images/forum/search.png' alt='' />&nbsp;
    <a href='<?php echo URLROOT; ?>/forums/search'><?php echo Lang::T("SEARCH"); ?></a>&nbsp; &nbsp;
    <b><?php echo Lang::T("FORUM_CONTROL"); ?></b>
     &middot; <a href='<?php echo URLROOT; ?>/forums/viewunread'><?php echo Lang::T("FORUM_NEW_POSTS"); ?></a>
     &middot; <a href='<?php echo URLROOT; ?>/forums?catchup'><?php echo Lang::T("FORUM_MARK_READ"); ?></a>
</div><br>
<div><?php echo Lang::T("YOU_ARE_IN"); ?>: &nbsp;<a href='<?php echo URLROOT; ?>/forums'><?php echo Lang::T("FORUMS"); ?></a> <b style='vertical-align:middle'>/ Search Forums</b></div>    
 <?php 
        $keywords = trim($_GET["keywords"]);
        if ($keywords != "") {
            print("<p>Search Phrase: <b>" . htmlspecialchars($keywords) . "</b></p>\n");
            $maxresults = 50;
            $ekeywords = $keywords;
            $res = "SELECT forum_posts.topicid, forum_posts.userid, forum_posts.id, forum_posts.added,
                MATCH ( forum_posts.body ) AGAINST ( " . $ekeywords . " ) AS relevancy
                FROM forum_posts
                WHERE MATCH ( forum_posts.body ) AGAINST ( " . $ekeywords . " IN BOOLEAN MODE )
                ORDER BY relevancy DESC";
            $res = DB::run($res);
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
<center><form method='get' action='?'>
<input type='hidden' name='action' value='search' />
<table cellspacing='0' cellpadding='5'>
<tr><td valign='bottom' align='right'>Search For: </td><td align='left'><input type='text' size='40' name='keywords' /><br /></td></tr>
<tr><td colspan='2' align='center'><input type='submit' value='Search' /></td></tr>
</table></form></center>
</div>
</div>