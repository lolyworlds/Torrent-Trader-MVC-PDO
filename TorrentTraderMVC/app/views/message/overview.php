<?php
Style::begin(Lang::T("Messages"));
?>
<?php include APPROOT.'/views/message/messagenavbar.php'; ?>
<div class="container"><table class="table"><thead>
<tr><th class="table_head" align="center"><b><i><?php echo  Lang::T("OVERVIEW_INFO"); ?></i></b></th>
</tr></thead>
<tbody>
    <tr>
        <td align="center"><!--<a href="<?php echo URLROOT; ?>/messages/inbox">--><?php echo  Lang::T("INBOX"); ?> :</a>
        &nbsp;[<font color=green><?php echo $data['inbox']; ?></font>] <?php echo Lang::N("", $data['inbox']); ?> (<font color=red><?php echo $data['unread'], Lang::T("UNREAD"); ?></font>)</td>
    </tr>
    <tr>
        <td align="center" width="105%"><!--<a href="<?php echo URLROOT; ?>/messages/outbox">--><?php echo  Lang::T("OUTBOX"); ?> :</a>
        &nbsp;<?php echo $data['outbox'], Lang::N("", $data['outbox']); ?></td>
    </tr>
    <tr>
        <td align="center" width="25%"><!--<a href="<?php echo URLROOT; ?>/messages/draft">--><?php echo  Lang::T("DRAFT"); ?> :</a>
        &nbsp;<?php echo $data['draft'], Lang::N("", $data['draft']); ?></td>
    </tr>
    <tr>
        <td align="center" width="25%"><!--<a href="<?php echo URLROOT; ?>/messages/templates">--><?php echo  Lang::T("TEMPLATES"); ?> :</a>
        &nbsp;<?php echo $data['template'], Lang::N("", $data['template']); ?></td>
    </tr>
</tbody></table><br></div>
<?php
Style::end();
?>