<?php
Style::begin($data['title']);
?>
<center><form method='post' action='<?php echo URLROOT; ?>/torrents/deleteok?id=<?php echo $data['id']; ?>'>
<input type='hidden' name='torrentid' value='<?php echo $data['id']; ?>' />
<input type='hidden' name='torrentname' value='<?php echo htmlspecialchars($data["name"]); ?>' />
<b><?php echo Lang::T("REASON_FOR_DELETE"); ?></b><input type='text' size='30' name='delreason' />
&nbsp;<input type='submit' value='<?php echo Lang::T("DELETE_TORRENT"); ?>' /></form></center>
<?php
Style::end();