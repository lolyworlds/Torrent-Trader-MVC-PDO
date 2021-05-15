<?php
Style::begin(Lang::T("Forum Home"));
forumheader('index');
latestforumposts();
// MAIN LAYOUT 
?>
<div class="row">
<div class="col-lg-12">
<div class="wrapper wrapper-content animated fadeInRight">
<?php
$fcid = 0;
while ($forums_arr = $data['mainquery']->fetch(PDO::FETCH_ASSOC)) {
    if ($_SESSION['class'] < $forums_arr["minclassread"] && $forums_arr["guest_read"] == "no") {
        continue;
    }
    if ($forums_arr['fcid'] != $fcid) {
?>
        <div class="row navbarone">
        <div class="col-md-8">
        <?php echo htmlspecialchars($forums_arr['fcname']); ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
            Topics
        </div>
        <div class="col-md-1 d-none d-sm-block">
            Posts
        </div>
        <div class="col-md-2 d-none d-sm-block">
            Last post
        </div>
        </div>
    <?php
    $fcid = $forums_arr['fcid'];
    }
    $forumid = 0 + $forums_arr["id"];
    $forumname = htmlspecialchars($forums_arr["name"]);
    $forumdescription = htmlspecialchars($forums_arr["description"]);
    $postcount = number_format(get_row_count("forum_posts", "WHERE topicid IN (SELECT id FROM forum_topics WHERE forumid=$forumid)"));
    $topiccount = number_format(get_row_count("forum_topics", "WHERE forumid = $forumid"));
    $lastpostid = get_forum_last_post($forumid);
    // Get last post info in a array return img & lastpost
    $detail = lastpostdetails($lastpostid); ?>

        <div class="row border border-info">
        <div class="col-md-8">
        <img src='<?php echo URLROOT; ?>/assets/images/forum/<?php echo $detail['img']; ?>.png'>&nbsp;

        <a href='<?php echo URLROOT; ?>/forums/viewforum&amp;forumid=<?php echo $forumid; ?>'><b><?php echo $forumname; ?></b></a><br>
        <small>- <?php echo $forumdescription; ?></small>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $topiccount; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $postcount; ?>
        </div>
        <div class="col-md-2 d-none d-sm-block">
        <?php echo $detail['lastpost']; ?>
        </div>
        </div>

    <?php } ?>
</div></div></div>

    <table cellspacing='0' cellpadding='3'><tr valign='middle'>
    <td><img src='<?php echo URLROOT; ?>/assets/images/forum/folder_new.png' style='margin: 5px' alt='' /></td><td>New posts</td>
    <td><img src='<?php echo URLROOT; ?>/assets/images/forum/folder.png' style='margin: 5px' alt='' /></td><td>No New posts</td>;
    <td><img src='<?php echo URLROOT; ?>/assets/images/forum/folder_locked.png' style='margin: 5px' alt='' /></td><td><?php echo Lang::T("FORUMS_LOCKED"); ?> topic</td>
    <td><img src='<?php echo URLROOT; ?>/assets/images/forum/folder_sticky.png' style='margin: 5px' alt='' /></td><td><?php echo Lang::T("FORUMS_STICKY"); ?> topic</td>
    </tr></table>
    <center>Our members have made <?php echo $data[' $postcount']; ?> posts in  <?php echo $data['topiccount']; ?> topics</center>
<?php 
insert_quick_jump_menu();
Style::end();