<a href='<?php echo URLROOT; ?>/torrents/read?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Back</button></a>
<a href='<?php echo URLROOT; ?>/torrents/edit?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Edit</button></a>
<a href='<?php echo URLROOT; ?>/comments?type=torrent&amp;id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Comments</button></a>
<a href='<?php echo URLROOT; ?>/torrents/torrentfilelist?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Files</button></a>

<?php if ($row["external"] != 'yes') {?>
     <a href='<?php echo URLROOT; ?>/peers/peerlist?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Peers</button></a>
<?php }?>

<?php if ($row["external"] == 'yes') {?>
     <a href='<?php echo URLROOT; ?>/torrents/torrenttrackerlist?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-primary">Trackers</button></a>
<?php } ?>
<br><br>