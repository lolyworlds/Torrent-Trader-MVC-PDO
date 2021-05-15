<?php
Style::begin(Lang::T("Topics with unread posts"));
forumheader('New Topics');

while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    $topicid = $arr['id'];
    $forumid = $arr['forumid'];
    // Check if post is read
    if ($_SESSION['loggedin'] == true) {
        $a = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=? AND topicid=?", [$_SESSION['id'], $topicid])->fetch();
    }
    if ($a && $a[0] == $arr['lastpost']) {
        continue;
    }
    // Check access & get forum name
    $a = DB::run("SELECT name, minclassread, guest_read FROM forum_forums WHERE id=$forumid")->fetch();
    if ($_SESSION["class"] < $a['minclassread'] && $a["guest_read"] == "no") {
        continue;
    }
    ++$data['n'];
    if ($data['n'] > 25) {
        break;
    }
    $forumname = $a['name'];
        if ($data['n'] == 1) {
        ?>
        <div class='table'><table class='table table-striped' style='border: 1px solid black' >
        <thead>
        <tr><th></th><th align='left'>Topic</th><th align='left' colspan='2'>Forum</th></tr>
        </thead><tbody>
        <?php } ?>
        <tr style='border: 1px solid black' ><td  style='border: 1px solid black'  valign='middle'>
            <img src='<?php echo URLROOT ?>/assets/images/forum/folder_unlocked_new.png' style='margin: 5px' alt='' /></td>
            <td  style='border: 1px solid black'  class='alt1'>
            <a href='<?php echo URLROOT ?>/forums/viewtopic&amp;topicid=<?php echo $topicid ?>&amp;page=last#last'><b><?php echo stripslashes(htmlspecialchars($arr["subject"])) ?></b></a></td>
            <td style='border: 1px solid black' class='alt2' align='left'><a href='<?php echo URLROOT ?>/forums/viewforum&amp;forumid=<?php echo $forumid ?>'><b><?php echo $forumname ?></b></a></td></tr>
    <?php }
if ($data['n'] > 0) {
    print("</tbody></table></div>\n");
    if ($n > $data['n']) {
        print("<p>More than 25 items found, displaying first 25.</p>\n");
    }

    print("<center><a href='" . URLROOT . "/forums?catchup'><b>Mark All Forums Read.</b></a></center>\n");
} else {
    print("<b>Nothing found</b>");
}
Style::end();