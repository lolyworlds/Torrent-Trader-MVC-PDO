<?php
  class Forums extends Controller {
    
    public function __construct(){
       
    }
    
    public function index(){
require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();



$action = strip_tags($_REQUEST["action"]);
    
if (!$CURUSER && ($action == "newtopic" || $action == "post")) 
    showerror(T_("FORUM_ERROR"), T_("FORUM_NO_ID"));

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";


//Global variables
$postsperpage = 20;
$maxsubjectlength = 50;


///////////////////////////////////////////////////////// Action: DEFAULT ACTION (VIEW FORUMS)
if (isset($_GET["catchup"]))
	catch_up();

///////////////////////////////////////////////////////// Action: SHOW MAIN FORUM INDEX
$forums_res = DB::run("SELECT forumcats.id AS fcid, forumcats.name AS fcname, forum_forums.* FROM forum_forums LEFT JOIN forumcats ON forumcats.id = forum_forums.category ORDER BY forumcats.sort, forum_forums.sort, forum_forums.name");

stdhead("Forums");
begin_frame("Forum Home");
forumheader("Index");
latestforumposts();

print("<div class='f-border f-forums'><table width='100%' cellspacing='0'>");// MAIN LAYOUT

print("<tr class='f-title'><th align='left' colspan='2'>Forum</th><th width='37' align='right'>Topics</th><th width='47' align='right'>Posts</th><th align='right' width='180'>Last post</th></tr>\n");// head of forum index
  
if ($forums_res->rowCount() == 0)
    print("<tr class='f-cat'><td colspan='5' align='center'>No Forum Categories</td></tr>\n");  
  
$fcid = 0;
 
while ($forums_arr = $forums_res->fetch(PDO::FETCH_ASSOC)){
	
    if (get_user_class() < $forums_arr["minclassread"] && $forums_arr["guest_read"] == "no")
        continue;
        
    if ($forums_arr['fcid'] != $fcid) {// add forum cat headers
		print("<tr class='f-cat'><td colspan='5' align='center'>".htmlspecialchars($forums_arr['fcname'])."</td></tr>\n");

		$fcid = $forums_arr['fcid'];
	}

    $forumid = 0 + $forums_arr["id"];

    $forumname = htmlspecialchars($forums_arr["name"]);

    $forumdescription = htmlspecialchars($forums_arr["description"]);
    $postcount = number_format(get_row_count("forum_posts", "WHERE topicid IN (SELECT id FROM forum_topics WHERE forumid=$forumid)"));
    $topiccount = number_format(get_row_count("forum_topics", "WHERE forumid = $forumid"));


    // Find last post ID
    $lastpostid = get_forum_last_post($forumid);

    // Get last post info
    $post_res = DB::run("SELECT added,topicid,userid FROM forum_posts WHERE id=$lastpostid");
    if ($post_res->rowCount() == 1) {
		$post_arr = $post_res->fetch(PDO::FETCH_ASSOC) or showerror(T_("ERROR"), "Bad forum last_post");
		$lastposterid = $post_arr["userid"];
		$lastpostdate = utc_to_tz($post_arr["added"]);
		$lasttopicid = $post_arr["topicid"];
		$user_res = DB::run("SELECT username FROM users WHERE id=$lastposterid");
		$user_arr = $user_res->fetch(PDO::FETCH_ASSOC);
		$lastposter = class_user($user_arr["username"]);
		$topic_res = DB::run("SELECT subject FROM forum_topics WHERE id=$lasttopicid");
		$topic_arr = $topic_res->fetch(PDO::FETCH_ASSOC);
		$lasttopic = stripslashes(htmlspecialchars($topic_arr['subject']));
		
		//cut last topic
		$latestleng = 10;

		$lastpost = "<small><a href='$site_config[SITEURL]/forums/viewtopic&amp;topicid=$lasttopicid&amp;page=last#last'>" . CutName($lasttopic, $latestleng) . "</a> by <a href='$site_config[SITEURL]/accountdetails?id=$lastposterid'>$lastposter</a><br />$lastpostdate</small>";


		if ($CURUSER) {
            $a = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=$CURUSER[id] AND topicid=$lasttopicid")->fetch();
		}

		//define the images for new posts or not on index
		if ($a && $a[0] == $lastpostid)
			$img = "folder";
		else
		$img = "folder_new";
    }else{
		$lastpost = "<span class='small'>No Posts</span>";
		$img = "folder";
    }
	//following line is each forums display
    print("<tr class='f-row'><td class='f-img'><img src='". $themedir ."$img.png' alt='' /></td><td align='left' width='100%' class='alt1'><a href='$site_config[SITEURL]/forums/viewforum&amp;forumid=$forumid'><b>$forumname</b></a><br />\n" .
    "<small>- $forumdescription</small></td><td class='alt2' align='center' width='40'>$topiccount</td><td class='alt3' align='center' width='40'>$postcount</td>" .
    "<td class='alt2' align='right' width='110'><small style='white-space: nowrap'>$lastpost</small></td></tr>\n");
}
print("</table></div>");
//forum Key
print("<table cellspacing='0' cellpadding='3'><tr valign='middle'>\n");
print("<td><img src='". $themedir ."folder_new.png' style='margin: 5px' alt='' /></td><td>New posts</td>\n");
print("<td><img src='". $themedir ."folder.png' style='margin: 5px' alt='' /></td><td>No New posts</td>\n");
print("<td><img src='". $themedir ."folder_locked.png' style='margin: 5px' alt='' /></td><td>".T_("FORUMS_LOCKED")." topic</td>\n");
print("<td><img src='". $themedir ."folder_sticky.png' style='margin: 5px' alt='' /></td><td>".T_("FORUMS_STICKY")." topic</td>\n");
print("</tr></table>\n");

//Top posters
$r = DB::run("SELECT users.id, users.username, COUNT(forum_posts.userid) as num FROM forum_posts LEFT JOIN users ON users.id = forum_posts.userid GROUP BY userid ORDER BY num DESC LIMIT 10");
forumpostertable($r);

//topic count and post counts
$postcount = number_format(get_row_count("forum_posts"));
$topiccount = number_format(get_row_count("forum_topics"));
print("<br /><center>Our members have made " . $postcount . " posts in  " . $topiccount . " topics</center><br />");

insert_quick_jump_menu();
end_frame();
stdfoot();

}else{//HEY IF FORUMS ARE OFF, SHOW THIS...
    showerror("Notice", "Unfortunatley the forums are not currently available.");
}
}


    public function newtopic(){
require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
    $forumid = $_GET["forumid"];
    if (!is_valid_id($forumid))
    showerror(T_("FORUM_ERROR"), "No Forum ID $forumid");

    stdhead("New topic");
    begin_frame("New topic");

	forumheader("Compose New Thread");

    insert_compose_frame($forumid);
    end_frame();
    stdfoot();
    die;
}
	}
	
	
	    public function search(){
require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}

	stdhead("Forum Search");
	begin_frame("Search Forum");
	forumheader("Search Forums");
			
	$keywords = trim($_GET["keywords"]);
	
	if ($keywords != ""){
		print("<p>Search Phrase: <b>" . htmlspecialchars($keywords) . "</b></p>\n");
		$maxresults = 50;
		$ekeywords = $keywords;

        $res = "SELECT forum_posts.topicid, forum_posts.userid, forum_posts.id, forum_posts.added,
                MATCH ( forum_posts.body ) AGAINST ( ". $ekeywords ." ) AS relevancy
                FROM forum_posts
                WHERE MATCH ( forum_posts.body ) AGAINST ( ". $ekeywords ." IN BOOLEAN MODE )
                ORDER BY relevancy DESC";
        
		$res = DB::run($res);
		// search and display results...
		$num = $res->rowCount();

		if ($num > $maxresults) {
			$num = $maxresults;
			print("<p>Found more than $maxresults posts; displaying first $num.</p>\n");
		}
		
		if ($num == 0)
			print("<p><b>Sorry, nothing found!</b></p>");
		else {
			print("<p><center><div class='f-border f-srch_results'><table width='100%' cellspacing='0'>\n");
			print("<tr class='f-title'><th>Post ID</th><th align='left'>Topic</th><th align='left'>Forum</th><th align='left'>Posted by</th></tr>\n");

			for ($i = 0; $i < $num; ++$i){
				$post = $res->fetch(PDO::FETCH_ASSOC);

				$res2 = DB::run("SELECT forumid, subject FROM forum_topics WHERE id=$post[topicid]");
				$topic = $res2->fetch(PDO::FETCH_ASSOC);

				$res2 = DB::run("SELECT name,minclassread, guest_read FROM forum_forums WHERE id=$topic[forumid]");
				$forum = $res2->fetch(PDO::FETCH_ASSOC);

				if ($forum["name"] == "" || ($forum["minclassread"] > $CURUSER["class"] && $forum["guest_read"] == "no"))
					continue;
				
				$res2 = DB::run("SELECT username FROM users WHERE id=$post[userid]");
				$user = $res2->fetch(PDO::FETCH_ASSOC);
				if ($user["username"] == "")
					$user["username"] = "Deluser";
				print("<tr class='f-row'><td>$post[id]</td><td align='left'><a href='$site_config[SITEURL]/forums/viewtopic&amp;topicid=$post[topicid]#post$post[id]'><b>" . htmlspecialchars($topic["subject"]) . "</b></a></td><td align='left'><a href='$site_config[SITEURL]/forums/viewforum&amp;forumid=$topic[forumid]'><b>" . htmlspecialchars($forum["name"]) . "</b></a></td><td align='left'><a href='$site_config[SITEURL]/accountdetails?id=$post[userid]'><b>$user[username]</b></a><br />at ".utc_to_tz($post["added"])."</td></tr>\n");
			}
			print("</table></div></center></p>\n");
			print("<p><b>Search again</b></p>\n");
		}
	}

	print("<center><form method='get' action='?'>\n");
	print("<input type='hidden' name='action' value='search' />\n");
	print("<table cellspacing='0' cellpadding='5'>\n");
	print("<tr><td valign='bottom' align='right'>Search For: </td><td align='left'><input type='text' size='40' name='keywords' /><br /></td></tr>\n");
	print("<tr><td colspan='2' align='center'><input type='submit' value='Search' /></td></tr>\n");
	print("</table>\n</form></center>\n");
	end_frame();
	stdfoot();
	die;
	}


	    public function viewunread(){
require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
	$userid = $CURUSER['id'];
	$maxresults = 25;
	$res = DB::run("SELECT id, forumid, subject, lastpost FROM forum_topics ORDER BY lastpost");
    stdhead();
	begin_frame("Topics with unread posts");
	forumheader("New Topics");

    $n = 0;
    $uc = get_user_class();
    while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
      $topicid = $arr['id'];
      $forumid = $arr['forumid'];

      //---- Check if post is read
	  if ($CURUSER) {
		$a = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=$userid AND topicid=$topicid")->fetch();
	  }
      if ($a && $a[0] == $arr['lastpost'])
        continue;

      //---- Check access & get forum name
      $a = DB::run("SELECT name, minclassread, guest_read FROM forum_forums WHERE id=$forumid")->fetch();
      if ($uc < $a['minclassread'] && $a["guest_read"] == "no")
        continue;
      ++$n;
      if ($n > $maxresults)
        break;
      $forumname = $a['name'];
      if ($n == 1) {
        print("<div class='f-border f-unread'><table width='100%' cellspacing='0'>\n");
        print("<tr class='f-title'><th align='left'>Topic</th><th align='left' colspan='2'>Forum</th></tr>\n");
      }
      print("<tr class='f-row'><td class='f-img' valign='middle'>" .
       "<img src='". $themedir ."folder_unlocked_new.png' style='margin: 5px' alt='' /></td><td class='alt1'>" .
       "<a href='$site_config[SITEURL]/forums/viewtopic&amp;topicid=$topicid&amp;page=last#last'><b>" . stripslashes(htmlspecialchars($arr["subject"])) ."</b></a></td><td class='alt2' align='left'><a href='$site_config[SITEURL]/forums/viewforum&amp;forumid=$forumid'><b>$forumname</b></a></td></tr>\n");
    }
    if ($n > 0) {
      print("</table></div><br />\n");
      if ($n > $maxresults)
        print("<p>More than $maxresults items found, displaying first $maxresults.</p>\n");
      print("<center><a href='$site_config[SITEURL]/forums?catchup'><b>Mark All Forums Read.</b></a></center><br />\n");
    }
    else
      print("<b>Nothing found</b>");
	 end_frame();
    stdfoot();
    die;
}
	
	
		    public function viewforum(){
require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
//Global variables
$postsperpage = 20;
$maxsubjectlength = 50;	
	$forumid = $_GET["forumid"];
	if (!is_valid_id($forumid))
        showerror(T_("ERROR"), T_("FORUMS_DENIED"));
        $page = $_GET["page"];
        $userid = $CURUSER["id"];

    //------ Get forum name
    $res = DB::run("SELECT name, minclassread, guest_read FROM forum_forums WHERE id=?", [$forumid]);
    $arr = $res->fetch(PDO::FETCH_ASSOC);
    $forumname = $arr["name"];
    if (!$forumname || get_user_class() < $arr["minclassread"] && $arr["guest_read"] == "no")
		showerror(T_("ERROR"), T_("FORUMS_NOT_PERMIT"));

    //------ Get topic count
    $perpage = 20;
    $res = DB::run("SELECT COUNT(*) FROM forum_topics WHERE forumid=$forumid");
    $arr = $res->fetch(PDO::FETCH_LAZY);
    $num = $arr[0];
    
//    $num = DB::run("SELECT COUNT(*) FROM forum_topics WHERE forumid=$forumid")->fetchColumn();
    
    if ($page == 0)
      $page = 1;
    $first = ($page * $perpage) - $perpage + 1;
    $last = $first + $perpage - 1;
    if ($last > $num)
      $last = $num;
    $pages = floor($num / $perpage);
    if ($perpage * $pages < $num)
      ++$pages;

    //------ Build menu
    $menu = "<p align='center'><b>\n";
    $lastspace = false;
    for ($i = 1; $i <= $pages; ++$i) {
      if ($i == $page)
        $menu .= "<span class='next-prev'>$i</span>\n";
      elseif ($i > 3 && ($i < $pages - 2) && ($page - $i > 3 || $i - $page > 3)) {
    	if ($lastspace)
          continue;
   	    $menu .= "... \n";
    	$lastspace = true;
      }
      else {
        $menu .= "<a href='$site_config[SITEURL]/forums/viewforum&amp;forumid=$forumid&amp;page=$i'>$i</a>\n";
        $lastspace = false;
      }
      if ($i < $pages)
        $menu .= "</b>|<b>\n";
    }
    $menu .= "<br />\n";
    if ($page == 1)
      $menu .= "<span class='next-prev'>&lt;&lt; Prev</span>";
    else
      $menu .= "<a href='$site_config[SITEURL]/forums/viewforum&amp;forumid=$forumid&amp;page=" . ($page - 1) . "'>&lt;&lt; Prev</a>";
    $menu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if ($last == $num)
      $menu .= "<span class='next-prev'>Next &gt;&gt;</span>";
    else
      $menu .= "<a href='$site_config[SITEURL]/forums/viewforum&amp;forumid=$forumid&page=" . ($page + 1) . "'>Next &gt;&gt;</a>";
    $menu .= "</b></p>\n";
    $offset = $first - 1;

    //------ Get topics data and display category
    $topicsres = DB::run("SELECT * FROM forum_topics WHERE forumid=$forumid ORDER BY sticky, lastpost DESC LIMIT $offset,$perpage")->fetchAll();

    stdhead("Forum : $forumname");
  //  $numtopics = $topicsres->fetch(PDO::FETCH_LAZY);
    begin_frame("$forumname");
	forumheader("<a href='$site_config[SITEURL]/forums/viewforum&amp;forumid=$forumid'>$forumname</a>");
	
	if ($CURUSER)
		print ("<table cellpadding='0' cellspacing='5' width='100%'><tr><td><div align='right'><a href='$site_config[SITEURL]/forums/newtopic&amp;forumid=$forumid'><img src='". $themedir. "button_new_post.png' alt='' /></a></div></td></tr></table>");

    if ($topicsres > 0) {
	print("<div class='f-border f-sub_forum'> <table width='100%' cellspacing='0'>");

	print("<tr class='f-title'><th align='left' colspan='2' width='100%'>Topic</th><th>Replies</th><th>Views</th><th>Author</th><th align='right'>Last post</th>\n");
		if ($CURUSER["edit_forum"] == "yes" || $CURUSER["delete_forum"] == "yes")
			print("<th>Moderator</th>");
      print("</tr>\n");
      foreach ($topicsres as $topicarr) {
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
			$tpages = floor($posts / $postsperpage);
			if ($tpages * $postsperpage != $posts)
			  ++$tpages;
			if ($tpages > 1) {
			  $topicpages = " (<img src='". $site_config['SITEURL'] ."/images/forum/multipage.png' alt='' />";
			  for ($i = 1; $i <= $tpages; ++$i)
				$topicpages .= " <a href='$site_config[SITEURL]/forums/viewtopic&amp;topicid=$topicid&amp;page=$i'>$i</a>";
			  $topicpages .= ")";
        }
        else
          $topicpages = "";

        //---- Get userID and date of last post
        $res = DB::run("SELECT * FROM forum_posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $lppostid = $arr["id"];
        $lpuserid = ( int ) $arr["userid"];
        $lpadded = utc_to_tz($arr["added"]);

        //------ Get name of last poster
        if ($lpuserid > 0) {
        $res = DB::run("SELECT * FROM users WHERE id=$lpuserid");
        if ($res->rowCount() == 1) {
          $arr = $res->fetch(PDO::FETCH_ASSOC);
          $lpusername = "<a href='$site_config[SITEURL]/accountdetails?id=$lpuserid'>".class_user($arr['username'])."</a>";
        }
        else
          $lpusername = "Deluser";
        }
        else
          $lpusername = "Deluser";

        //------ Get author
        if ($topic_userid > 0) {
        $res = DB::run("SELECT username FROM users WHERE id=$topic_userid");
        if ($res->rowCount() == 1) {
          $arr = $res->fetch(PDO::FETCH_ASSOC);
          $lpauthor = "<a href='$site_config[SITEURL]/accountdetails?id=$topic_userid'>".class_user($arr['username'])."</a>";
        }
        else
          $lpauthor = "Deluser";
        }
        else
          $lpauthor = "Deluser";

		// Topic Views
		$viewsq = DB::run("SELECT views FROM forum_topics WHERE id=$topicid");
		$viewsa = $viewsq->fetch(PDO::FETCH_LAZY);
		$views = $viewsa[0];
		// End

        //---- Print row
		if ($CURUSER) {
			$r = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=$userid AND topicid=$topicid");
			$a = $r->fetch(PDO::FETCH_LAZY);
		}
        $new = !$a || $lppostid > $a[0];
        $topicpic = ($locked ? ($new ? "folder_locked_new" : "folder_locked") : ($new ? "folder_new" : "folder"));
        $subject = ($sticky ? "<b>".T_("FORUMS_STICKY").": </b>" : "") . "<a href='$site_config[SITEURL]/forums/viewtopic&amp;topicid=$topicid'><b>" .
        encodehtml(stripslashes($topicarr["subject"])) . "</b></a>$topicpages";
        print("<tr class='f-row'><td class='f-img' valign='middle'><img src='". $themedir ."$topicpic.png' alt='' />" .
         "</td><td class='alt1' align='left' width='100%'>\n" .
         "$subject</td><td class='alt2' align='center'>$replies</td>\n" .
		 "<td class='alt3' align='center'>$views</td>\n" .
         "<td class='alt2' align='center'>$lpauthor</td>\n" .
         "<td class='alt3' align='right'><span class='small'>by&nbsp;$lpusername<br /><span style='white-space: nowrap'>$lpadded</span></span></td>\n");
	     if ($CURUSER["edit_forum"] == "yes" || $CURUSER["delete_forum"] == "yes") {
			  print("<td class='alt2' align='center'><span style='white-space: nowrap'>\n");
			if ($locked)
				print("<a href='$site_config[SITEURL]/forums/unlocktopic&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Unlock'><img src='". $themedir ."topic_unlock.png' alt='UnLock Topic' /></a>\n");
			else
				print("<a href='$site_config[SITEURL]/forums/locktopic&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Lock'><img src='". $themedir ."topic_lock.png' alt='Lock Topic' /></a>\n");
				print("<a href='$site_config[SITEURL]/forums/deletetopic&amp;topicid=$topicid&amp;sure=0' title='Delete'><img src='". $themedir ."topic_delete.png' alt='Delete Topic' /></a>\n");
			if ($sticky)
			   print("<a href='$site_config[SITEURL]/forums/unsetsticky&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='UnStick'><img src='". $themedir ."folder_sticky_new.png' alt='Unstick Topic' /></a>\n");
			else
			   print("<a href='$site_config[SITEURL]/forums/setsticky&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Stick'><img src='". $themedir ."folder_sticky.png' alt='Stick Topic' /></a>\n");
			  print("</span></td>\n");
        }
        print("</tr>\n");
      } // while
   //   end_table();
   print("</table></div>");
      print($menu);
    } // if
    else
      print("<p align='center'>No topics found</p>\n");
    print("<table cellspacing='5' cellpadding='0'><tr valign='middle'>\n");
    print("<td><img src='". $themedir ."folder_new.png' style='margin-right: 5px' alt='' /></td><td >New posts</td>\n");
	 print("<td><img src='". $themedir ."folder.png' style='margin-left: 10px; margin-right: 5px' alt='' />" .
     "</td><td>No New posts</td>\n");
    print("<td><img src='".$site_config['SITEURL']."/". $themedir ."folder_locked.png' style='margin-left: 10px; margin-right: 5px' alt='' />" .
     "</td><td>".T_("FORUMS_LOCKED")." topic</td></tr></table>\n");
    $arr = get_forum_access_levels($forumid) or die;
    $maypost = get_user_class() >= $arr["write"];
    if (!$maypost)
		print("<p><i>".T_("FORUMS_YOU_NOT_PERM_POST_FORUM")."</i></p>\n");
    print("<table cellspacing='0' cellpadding='0'><tr>\n");

    if ($maypost)
		print("<td><a href='$site_config[SITEURL]/forums/newtopic&amp;forumid=$forumid'><img src='" . $themedir . "button_new_post.png' alt='' /></a></td>\n");
    print("</tr></table>\n");
    insert_quick_jump_menu($forumid);
    end_frame();
    stdfoot();
    die;
}	

public function setsticky(){	
require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
   $forumid = $_GET["forumid"];
   $topicid = $_GET["topicid"];
   $page = $_GET["page"];
   if (!is_valid_id($topicid) || ($CURUSER["delete_forum"] != "yes" && $CURUSER["edit_forum"] != "yes"))
        showerror(T_("ERROR"), T_("FORUMS_DENIED"));
        DB::run("UPDATE forum_topics SET sticky='yes' WHERE id=$topicid");
        header("Location: ".TTURL."/forums/viewforum&forumid=$forumid&page=$page");
   die;
}	


public function reply(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
	$topicid = $_GET["topicid"];
	if (!is_valid_id($topicid))
    showerror(T_("FORUM_ERROR"), sprintf(T_("FORUMS_NO_ID_FORUM"), $topicid));
	stdhead(T_("FORUMS_POST_REPLY"));
	begin_frame(T_("FORUMS_POST_REPLY"));
	insert_compose_frame($topicid, false);
	end_frame();
	stdfoot();
	die;
}

public function editpost(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
	$postid = $_GET["postid"];
	if (!is_valid_id($postid))
        showerror(T_("ERROR"), T_("FORUMS_DENIED"));
    $res = DB::run("SELECT * FROM forum_posts WHERE id=?", [$postid]);
	if ($res->rowCount() != 1)
		showerror(T_("ERROR"), sprintf(T_("FORUMS_NO_ID_POST"), $postid));
	$arr = $res->fetch(PDO::FETCH_ASSOC);
    if ($CURUSER["id"] != $arr["userid"] && $CURUSER["delete_forum"] != "yes" && $CURUSER["edit_forum"] != "yes")
		showerror(T_("ERROR"), T_("FORUMS_DENIED"));

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$body = $_POST['body'];
			if ($body == "")
				showerror(T_("ERROR"), "Body cannot be empty!");
		$body = $body;
		$editedat = get_date_time();
        DB::run("UPDATE forum_posts SET body=?, editedat=?, editedby=? WHERE id=?", [$body, $editedat, $CURUSER['id'], $postid]);
		$returnto = $_POST["returnto"];
			if ($returnto != "")
				header("Location: $returnto");
			else
				showerror(T_("SUCCESS"), "Post was edited successfully.");
	}

    stdhead();

    begin_frame(T_("FORUMS_EDIT_POST"));
    print("<form name='Form' method='post' action='forums/editpost&amp;postid=$postid'>\n");
    print("<input type='hidden' name='returnto' value='" . htmlspecialchars($_SERVER["HTTP_REFERER"]) . "' />\n");
    print("<center><table  cellspacing='0' cellpadding='5'>\n");
    print("<tr><td colspan='2'>\n");
    textbbcode("Form", "body", htmlspecialchars($arr["body"]));
    print("</td></tr>");

	
    print("<tr><td align='center' colspan='2'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");
    print("</table></center>\n");
    print("</form>\n");
    end_frame();
    stdfoot();
    die;
}

public function post(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
	$forumid = $_POST["forumid"];
	$topicid = $_POST["topicid"];

	if (!is_valid_id($forumid) && !is_valid_id($topicid))
		    showerror(T_("FORUM_ERROR"), "w00t");
	$newtopic = $forumid > 0;
	$subject = $_POST["subject"];
	if ($newtopic) {
		if (!$subject)
			showerror(T_("ERROR"), "You must enter a subject.");
		$subject = trim($subject);
		//if (!$subject)
			//showerror(T_("ERROR"), "You must enter a subject.");
		//showerror(T_("ERROR"), "Subject is limited to $maxsubjectlength characters.");
	}else{
      $forumid = get_topic_forum($topicid) or showerror(T_("FORUM_ERROR"),"Bad topic ID");
	}

    ////// Make sure sure user has write access in forum
	$arr = get_forum_access_levels($forumid) or showerror(T_("FORUM_ERROR"),"Bad forum ID");
	if (get_user_class() < $arr["write"])
		showerror(T_("FORUM_ERROR"),T_("FORUMS_NOT_PERMIT"));
	$body = trim($_POST["body"]);
	if (!$body)
		showerror(T_("ERROR"), "No body text.");
	$userid = $CURUSER["id"];

	if ($newtopic) { //Create topic
		$subject = $subject;
        DB::run("INSERT INTO forum_topics (userid, forumid, subject) VALUES(?,?,?)", [$userid, $forumid, $subject]);
		$topicid = DB::lastInsertId()or showerror(T_("FORUM_ERROR"),"Topics id n/a");

	}else{
		//Make sure topic exists and is unlocked
		$res = DB::run("SELECT * FROM forum_topics WHERE id=?", [$topicid]);
		$arr = $res->fetch(PDO::FETCH_ASSOC) or showerror(T_("FORUM_ERROR"),"Topic id n/a");
		if ($arr["locked"] == 'yes')
        showerror(T_("FORUM_ERROR"),"Topic locked");
		//Get forum ID
		$forumid = $arr["forumid"];
    }

    //Insert the new post
    $added = "'" . get_date_time() . "'";
    $body = $body;
    DB::run("INSERT INTO forum_posts (topicid, userid, added, body) VALUES(?, ?, ?, ?)", [$topicid, $userid, get_date_time(), $body]);
    $postid = DB::lastInsertId() or showerror(T_("FORUM_ERROR"),"Post id n/a");

    //Update topic last post
    update_topic_last_post($topicid);

    //All done, redirect user to the post
    $headerstr = "Location: ".TTURL."/forums/viewtopic&topicid=$topicid&page=last";
    if ($newtopic)
		header($headerstr);
    else
		header("$headerstr#post$postid");
    die;
}


public function viewtopic(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
//Global variables
$postsperpage = 20;
$maxsubjectlength = 50;
	$topicid = $_GET["topicid"];
	$page = $_GET["page"];

	if (!is_valid_id($topicid))
        showerror(T_("FORUM_ERROR"),"Topic Not Valid");
	$userid = $CURUSER["id"];

    //------ Get topic info
    $res = DB::run("SELECT * FROM forum_topics WHERE id=?", [$topicid]);
    $arr = $res->fetch(PDO::FETCH_ASSOC) or showerror(T_("FORUM_ERROR"), "Topic not found");
    $locked = ($arr["locked"] == 'yes');
    $subject = stripslashes($arr["subject"]);
	$sticky = $arr["sticky"] == "yes";
    $forumid = $arr["forumid"];
	
	// Check if user has access to this forum
	$res2 = DB::run("SELECT minclassread, guest_read FROM forum_forums WHERE id=?", [$forumid]);
    $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
    if (!$arr2 || get_user_class() < $arr2["minclassread"] && $arr2["guest_read"] == "no")
        show_error_msg("Error: Access Denied","You do not have access to the forum this topic is in.");

	// Update Topic Views
	$viewsq = DB::run("SELECT views FROM forum_topics WHERE id=$topicid");
	$viewsa = $viewsq->fetch(PDO::FETCH_LAZY);
	$views = $viewsa[0];
	$new_views = $views+1;
	$uviews = DB::run("UPDATE forum_topics SET views = $new_views WHERE id=$topicid");
	// End

    //------ Get forum
    $res = DB::run("SELECT * FROM forum_forums WHERE id=?", [$forumid]);
    $arr = $res->fetch(PDO::FETCH_ASSOC) or showerror(T_("FORUM_ERROR"), "Forum is empty");
    $forum = stripslashes($arr["name"]);

    //------ Get post count
    $res = DB::run("SELECT COUNT(*) FROM forum_posts WHERE topicid=?", [$topicid]);
    $arr = $res->fetch(PDO::FETCH_LAZY);
    $postcount = $arr[0];         

    //------ Make page menu
    $pagemenu = "<br /><small>\n";
    $perpage = $postsperpage;
    $pages = floor($postcount / $perpage);
    if ($pages * $perpage < $postcount)
		++$pages;
    if ($page == "last")
		$page = $pages;
    else {
		if($page < 1)
			$page = 1;
		elseif ($page > $pages)
			$page = $pages;
    }
    $offset = max( 0, ( $page * $perpage ) - $perpage );  
    
	//
    if ($page == 1)
      $pagemenu .= "<b>&lt;&lt; Prev</b>";
    else
      $pagemenu .= "<a href='$site_config[SITEURL]/forums/viewtopic&amp;topicid=$topicid&amp;page=" . ($page - 1) . "'><b>&lt;&lt; Prev</b></a>";
	//
	$pagemenu .= "&nbsp;&nbsp;";
	    for ($i = 1; $i <= $pages; ++$i) {
      if ($i == $page)
        $pagemenu .= "<b>$i</b>\n";
      else
        $pagemenu .= "<a href='$site_config[SITEURL]/forums/viewtopic&amp;topicid=$topicid&amp;page=$i'><b>$i</b></a>\n";
    }
	//
    $pagemenu .= "&nbsp;&nbsp;";
    if ($page == $pages)
      $pagemenu .= "<b>Next &gt;&gt;</b><br /><br />\n";
    else
      $pagemenu .= "<a href='$site_config[SITEURL]/forums/viewtopic&amp;topicid=$topicid&amp;page=" . ($page + 1) . "'><b>Next &gt;&gt;</b></a><br /><br />\n";
    $pagemenu .= "</small>";
      
//Get topic posts
    $res = DB::run("SELECT * FROM forum_posts WHERE topicid=$topicid ORDER BY id LIMIT $offset,$perpage");
    stdhead("View Topic: $subject");
    begin_frame("$forum &gt; $subject");
	forumheader("<a href='$site_config[SITEURL]/forums/viewforum&amp;forumid=$forumid'>$forum</a> <b style='font-size:16px; vertical-align:middle'>/</b> $subject");
	
	print ("<div style='padding: 6px'>");
	
	$levels = get_forum_access_levels($forumid) or die;
	if (get_user_class() >= $levels["write"])
		$maypost = true;
	else
		$maypost = false;
	
	if (!$locked && $maypost){
		print ("<div align='right'><a href='#bottom'><img src='". $themedir ."button_reply.png' border='0' alt='' /></a></div>");
	}else{
		print ("<div align='right'><img src='" . $themedir . "button_locked.png'  alt='".T_("FORUMS_LOCKED")."' /></div>");
	}
	print ("</div>");

//------ Print table of posts
    $pc = $res->rowCount();
    $pn = 0;
	if ($CURUSER) {
	    $r = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=? AND topicid=?", [$CURUSER['id'], $topicid]);
	    $a = $r->fetch(PDO::FETCH_LAZY);
	    $lpr = $a[0];
	    if (!$lpr)
            DB::run("INSERT INTO forum_readposts (userid, topicid) VALUES($userid, $topicid)");
	}
	
    while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
		++$pn;
		$postid = $arr["id"];
		$posterid = $arr["userid"];
		$added = utc_to_tz($arr["added"])."(" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

		//---- Get poster details
		$res4 = DB::run("SELECT COUNT(*) FROM forum_posts WHERE userid=?", [$posterid]);
		$arr33 = $res4->fetch(PDO::FETCH_LAZY);
		$forumposts = $arr33[0];

		$res2 = DB::run("SELECT * FROM users WHERE id=?", [$posterid]);
		$arr2 = $res2->fetch(PDO::FETCH_ASSOC);
		$postername = class_user($arr2["username"]);

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
			}else{
				$avatar = htmlspecialchars($arr2["avatar"]);
				$userdownloaded = mksize($arr2["downloaded"]);
				$useruploaded = mksize($arr2["uploaded"]);
				$privacylevel = $arr2["privacy"];
				$usersignature = stripslashes(format_comment($arr2["signature"]));
					if ($arr2["downloaded"] > 0) {
						$userratio = number_format($arr2["uploaded"] / $arr2["downloaded"], 2);
					}else
						if ($arr2["uploaded"] > 0)
							$userratio = "Inf.";
						else
							$userratio = "---";
        
					if(!$arr2["country"]){
						$usercountry = "unknown";
					}else{
						$res4 = DB::run("SELECT name,flagpic FROM countries WHERE id=? LIMIT 1", [$arr2['country']]);
						$arr4 = $res4->fetch(PDO::FETCH_ASSOC);
						$usercountry = $arr4["name"];
					}

				$title = format_comment($arr2["title"]);
				$donated = $arr2['donated'];
				$by = "<a href='$site_config[SITEURL]/accountdetails?id=$posterid'>$postername</a>" . ($donated > 0 ? "<img src='".$site_config['SITEURL']."/images/star.png' alt='Donated' />" : "") . "";
			}

		 if (!$avatar)
            $avatar = $site_config['SITEURL']."/images/default_avatar.png";
      # print("<a name=$postid>\n");
        print("<a id='post$postid'></a>");
        if ($pn == $pc) {
            print("<a name='last'></a>\n");
            if ($postid > $lpr && $CURUSER)
                DB::run("UPDATE forum_readposts SET lastpostread=$postid WHERE userid=? AND topicid=?", [$userid, $topicid]);
        }
//working here
//Post Top

		print("<div class='f-border f-post'><table width='100%' cellspacing='0'><tr class='p-title'><th width='150'>$by</th><th align='left'><small>Posted at $added </small></th></tr>");

//Post Middle

		$body = stripslashes(format_comment($arr["body"]));

		if (is_valid_id($arr['editedby'])) {
			$res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr['editedby']]);

			if ($res2->rowCount() == 1) {
				$arr2 = $res2->fetch(PDO::FETCH_ASSOC);
				//edited by comment out if needed
				$body .= "<br /><br /><small><i>Last edited by <a href='$site_config[SITEURL]/accountdetails?id=$arr[editedby]'>$arr2[username]</b></a> on ".utc_to_tz($arr["editedat"])."</i></small><br />\n";
				$body .= "\n";
			}
		}

		$quote = htmlspecialchars($arr["body"]);

		$postcount1 = DB::run("SELECT COUNT(forum_posts.userid) FROM forum_posts WHERE id=$posterid") or forumsqlerr();

		while($row = $postcount1->fetch(PDO::FETCH_LAZY)) {

			if  ($privacylevel == "strong" && $CURUSER["control_panel"] != "yes"){//hide stats, but not from staff
				$useruploaded = "---";
				$userdownloaded = "---";
				$userratio = "---";
				$nposts = "-";
				$tposts = "-";
			}
			print ("<tr valign='top''><td width='150' align='left' class='comment-details'><center><i>$title</i></center><br /><center><img width='80' height='80' src='$avatar' alt='' /></center><br />Uploaded: $useruploaded<br />Downloaded: $userdownloaded<br />Posts: $forumposts<br /><br />Ratio: $userratio<br />Location: $usercountry<br /><br /></td>");

			print ("<td class='comment'><br />$body<br />");

			if (!$usersignature){
				print("<br /></td></tr>\n");
			}else{
				print("<br /><hr /><br /><div class='f-sig' align='center'>$usersignature</div></td></tr>\n");
			}
		}

//Post Bottom

	print("<tr class='p-foot'><td width='150' align='center'><a href='$site_config[SITEURL]/accountdetails?id=$posterid'><img src='".$themedir."icon_profile.png' border='0' alt='' /></a> <a href='$site_config[SITEURL]/mailbox?compose&amp;id=$posterid'><img src='".$themedir."icon_pm.png' border='0' alt='' /></a></td><td>");

	print ("<div style='float: left;'><a href='$site_config[SITEURL]/report?forumid=$topicid&amp;forumpost=$postid'><img src='".$themedir."p_report.png' border='0' alt='".T_("FORUMS_REPORT_POST")."' /></a>&nbsp;<a href='javascript:scroll(0,0);'><img src='".$themedir."p_up.png'  alt='".T_("FORUMS_GOTO_TOP_PAGE")."' /></a></div><div align='right'>");
	
	//define buttons and who can use them
	if ($CURUSER["id"] == $posterid || $CURUSER["edit_forum"] == "yes" || $CURUSER["delete_forum"] == "yes"){
		print ("<a href='$site_config[SITEURL]/forums/editpost&amp;postid=$postid'><img src='".$themedir."p_edit.png' border='0' alt='' /></a>&nbsp;");
	}
	if ($CURUSER["delete_forum"] == "yes"){
		print ("<a href='$site_config[SITEURL]/forums/deletepost&amp;postid=$postid&amp;sure=0'><img src='".$themedir."p_delete.png' border='0' alt='' /></a>&nbsp;");
	}
	if (!$locked && $maypost) {
		print ("<a href=\"javascript:SmileIT('[quote=$postername] $quote [/quote]', 'Form', 'body');\"><img src='".$themedir."p_quote.png' border='0' alt='' /></a>&nbsp;");
		print ("<a href='#bottom'><img src='".$themedir."p_reply.png' alt='' /></a>");
	}
		print("&nbsp;</div></td></tr></table></div>");
	}
//-------- end posts table ---------//
	print($pagemenu);

	//quick reply
	if (!$locked && $CURUSER){
	//begin_frame("Reply", $newtopic = false);
	print ("<fieldset class='download'><legend><b>".T_("FORUMS_POST_REPLY")."</b></legend>");
	$newtopic = false;
	print("<a name='bottom'></a>");
    print("<form name='Form' method='post' action='$site_config[SITEURL]/forums/post'>\n");
    if ($newtopic)
		print("<input type='hidden' name='forumid' value='$id' />\n");
    else
		print("<input type='hidden' name='topicid' value='$topicid' />\n");

    print("<table cellspacing='0' cellpadding='0' align='center'>");
    if ($newtopic)
		print("<tr><td class='alt2'>".T_("FORUMS_SUBJECT")."</td><td class='alt1' align='left' style='padding: 0px'><input type='text' size='100' maxlength='$maxsubjectlength' name='subject' style='border: 0px; height: 19px' /></td></tr>\n");

	echo "<tr><td align='center' colspan='3'>";
	textbbcode("Form", "body");
	echo "</td></tr>\n";
    print("<tr><td colspan='3' align='center'><br /><input type='image' src='". $themedir ."button_reply.png' alt='' /></td></tr>\n");
    print("</table></form>\n");
	//end_frame();
	print (" </fieldset>");
	}else{
	print ("<img src='".$themedir."button_locked.png' alt='".T_("FORUMS_LOCKED")."' /><br />");
	}
	//end quick reply

	if ($locked)
		print(T_("FORUMS_TOPIC_LOCKED")."\n");
	elseif (!$maypost)
		print("<i>".T_("FORUMS_YOU_NOT_PERM_POST_FORUM")."</i>\n");
    //insert page numbers and quick jump

   // insert_quick_jump_menu($forumid);

	// MODERATOR OPTIONS
     if ($CURUSER["delete_forum"] == "yes" || $CURUSER["edit_forum"] == "yes") {
      print("<br /><div class='f-border f-mod_options' align='center'><table width='100%' cellspacing='0'><tr class='f-title'><th>".T_("FORUMS_MOD_OPTIONS")."</th></tr>\n");
     $res = DB::run("SELECT id,name,minclasswrite FROM forum_forums ORDER BY name");
      print("<tr><td class='ttable_col2'>\n");
      print("<form method='post' action='/forums/renametopic'>\n");
      print("<input type='hidden' name='topicid' value='$topicid' />\n");
      print("<input type='hidden' name='returnto' value='/forums/viewtopic&amp;topicid=$topicid' />\n");
	  print("<div align='center'  style='padding:3px'>Rename topic: <input type='text' name='subject' size='60' maxlength='$maxsubjectlength' value='" . stripslashes(htmlspecialchars($subject)) . "' />\n");
      print("<input type='submit' value='Apply' />");
      print("</div></form>\n");
      print("<form method='post' action='$site_config[SITEURL]/forums/movetopic&amp;topicid=$topicid'>\n");
      print("<div align='center' style='padding:3px'>");
      print("Move this thread to: <select name='forumid'>");
      while ($arr = $res->fetch(PDO::FETCH_ASSOC))
        if ($arr["id"] != $forumid && get_user_class() >= $arr["minclasswrite"])
          print("<option value='" . $arr["id"] . "'>" . $arr["name"] . "</option>\n");
      print("</select> <input type='submit' value='Apply' /></div></form>\n");
 print("<div align='center'>\n");
			if ($locked)
				print(T_("FORUMS_LOCKED").": <a href='$site_config[SITEURL]/forums/unlocktopic&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Unlock'><img src='". $themedir ."topic_unlock.png' alt='UnLock Topic' /></a>\n");
			else
				print(T_("FORUMS_LOCKED").": <a href='$site_config[SITEURL]/forums/locktopic&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Lock'><img src='". $themedir ."topic_lock.png' alt='Lock Topic' /></a>\n");
			print("Delete Entire Topic: <a href='$site_config[SITEURL]/forums/deletetopic&amp;topicid=$topicid&amp;sure=0' title='Delete'><img src='". $themedir ."topic_delete.png' alt='Delete Topic' /></a>\n");
			if ($sticky)
			   print(T_("FORUMS_STICKY").": <a href='$site_config[SITEURL]/forums/unsetsticky&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='UnStick'><img src='". $themedir ."folder_sticky_new.png' alt='UnStick Topic' /></a>\n");
			else
			   print(T_("FORUMS_STICKY").": <a href='$site_config[SITEURL]/forums/setsticky&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Stick'><img src='". $themedir ."folder_sticky.png' alt='Stick Topic' /></a>\n");
			print("</div><br /></td></tr></table></div>\n");

    }
    end_frame();

    stdfoot();
    die;
}
///////////////////////////////////////////////////////// Action: DELETE TOPIC
public function deletetopic(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
	$topicid = $_GET["topicid"];
	if (!is_valid_id($topicid) || $CURUSER["delete_forum"] != "yes")
        showerror(T_("ERROR"), T_("FORUMS_DENIED"));
	
	$sure = $_GET["sure"];
	if ($sure == "0") 
		showerror(T_("FORUMS_DEL_TOPIC"), sprintf(T_("FORUMS_DEL_TOPIC_SANITY_CHK"), $topicid));

	DB::run("DELETE FROM forum_topics WHERE id=?", [$topicid]);
	DB::run("DELETE FROM forum_posts WHERE topicid=?", [$topicid]);
    DB::run("DELETE FROM forum_readposts WHERE topicid=?", [$topicid]);
	header("Location: ".TTURL."/forums");
	die;
}

///////////////////////////////////////////////////////// Action: RENAME TOPIC
public function renametopic(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
	if ($CURUSER["delete_forum"] != "yes" && $CURUSER["edit_forum"] != "yes")
        showerror(T_("ERROR"), T_("FORUMS_DENIED"));
  	$topicid = $_POST['topicid'];
  	if (!is_valid_id($topicid))
        showerror(T_("ERROR"), T_("FORUMS_DENIED"));
  	$subject = $_POST['subject'];
 	if ($subject == '')
		showerror(T_("ERROR"), T_("FORUMS_YOU_MUST_ENTER_NEW_TITLE"));
  	$subject = $subject;
    DB::run("UPDATE forum_topics SET subject=$subject WHERE id=$topicid");
  	$returnto = $_POST['returnto'];
  	if ($returnto)
		header("Location: $returnto");
  	die;
}

public function movetopic(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
    $forumid = $_POST["forumid"];
    $topicid = $_GET["topicid"];
    if (!is_valid_id($forumid) || !is_valid_id($topicid) || $CURUSER["delete_forum"] != "yes" || $CURUSER["edit_forum"] != "yes")
         showerror(T_("FORUM_ERROR"), sprintf(T_("FORUMS_NO_ID_FORUM"),$forumid,$topicid));

    // Make sure topic and forum is valid
    $res = DB::run("SELECT minclasswrite FROM forum_forums WHERE id=?", [$forumid]);
    if ($res->rowCount() != 1)
      showerror(T_("ERROR"), T_("FORUMS_NOT_FOUND"));
    $arr = $res->fetch(PDO::FETCH_LAZY);
    if (get_user_class() < $arr[0])
    showerror(T_("FORUM_ERROR"), T_("FORUMS_NOT_ALLOWED"));
    $res = DB::run("SELECT subject,forumid FROM forum_topics WHERE id=?", [$topicid]);
    if ($res->rowCount() != 1)
      showerror(T_("ERROR"), T_("FORUMS_NOT_FOUND_TOPIC"));
    $arr = $res->fetch(PDO::FETCH_ASSOC);
    if ($arr["forumid"] != $forumid)
        DB::run("UPDATE forum_topics SET forumid=$forumid, moved='yes' WHERE id=$topicid");

    // Redirect to forum page
    header("Location: ".TTURL."/forums/viewforum&forumid=$forumid");
    die;
}

public function locktopic(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}
	$forumid = $_GET["forumid"];
	$topicid = $_GET["topicid"];
	$page = $_GET["page"];
	if (!is_valid_id($topicid) || $CURUSER["delete_forum"] != "yes" || $CURUSER["edit_forum"] != "yes")
        showerror(T_("ERROR"), T_("FORUMS_DENIED"));
        DB::run("UPDATE forum_topics SET locked='yes' WHERE id=$topicid");
        header("Location: ".TTURL."/forums/viewforum&forumid=$forumid&page=$page");
	die;
}

public function deletepost(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}

	$postid = $_GET["postid"];
	$sure = $_GET["sure"];
	if ($CURUSER["delete_forum"] != "yes" || !is_valid_id($postid))
        showerror(T_("ERROR"), T_("FORUMS_DENIED"));

    //SURE?
	if ($sure == "0") {
		showerror(T_("FORUMS_DEL_POST"), sprintf(T_("FORUMS_DEL_POST_SANITY_CHK"), $postid));
    }

	//------- Get topic id
    $res = DB::run("SELECT topicid FROM forum_posts WHERE id=?", [$postid]);
    $arr = $res->fetch(PDO::FETCH_LAZY) or showerror(T_("ERROR"), T_("FORUMS_NOT_FOUND_POST"));
    $topicid = $arr[0];

    //------- We can not delete the post if it is the only one of the topic
    $res = DB::run("SELECT COUNT(*) FROM forum_posts WHERE topicid=?", [$topicid]);
    $arr =$res->fetch(PDO::FETCH_LAZY);
    if ($arr[0] < 2)
		showerror(T_("ERROR"), sprintf(T_("FORUMS_DEL_POST_ONLY_POST"), $topicid));

    //------- Delete post
    DB::run("DELETE FROM forum_posts WHERE id=?", [$postid]);

    //------- Update topic
    update_topic_last_post($topicid);
    header("Location: ".TTURL."/forums/viewtopic&topicid=$topicid");
    die;
}

    
 public function unlocktopic(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();


if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}   

    $forumid = $_GET["forumid"];
    $topicid = $_GET["topicid"];
    $page = $_GET["page"];
    if (!is_valid_id($topicid) || $CURUSER["delete_forum"] != "yes" || $CURUSER["edit_forum"] != "yes")
        showerror(T_("ERROR"), T_("FORUMS_DENIED"));
        DB::run("UPDATE forum_topics SET locked='no' WHERE id=$topicid");
        header("Location: ".TTURL."/forums/viewforum&forumid=$forumid&page=$page");
    die;
}

///////////////////////////////////////////////////////// Action: STICK TOPIC


public function unsetsticky(){
    require_once("helpers/bbcode_helper.php");
dbconn();
global $site_config, $CURUSER, $THEME;
if (!$site_config["FORUMS_GUESTREAD"])
	loggedinonly();

if ($CURUSER["forumbanned"] == "yes" || $CURUSER["view_forum"] == "no")
    showerror(T_("FORUM_BANNED"), T_("FORUM_BANNED"));

//Here we decide if the forums is on or off
if ($site_config["FORUMS"]) {
$themedir = $site_config['SITEURL']."/views/themes/".$THEME."/forums/";
}   
   $forumid = $_GET["forumid"];
   $topicid = $_GET["topicid"];
   $page = $_GET["page"];
   if (!is_valid_id($topicid) || ($CURUSER["delete_forum"] != "yes" && $CURUSER["edit_forum"] != "yes"))
        showerror(T_("ERROR"), T_("FORUMS_DENIED"));
        DB::run("UPDATE forum_topics SET sticky='no' WHERE id=$topicid");
        header("Location: ".TTURL."/forums/viewforum&forumid=$forumid&page=$page");
   die;
}
    
  }