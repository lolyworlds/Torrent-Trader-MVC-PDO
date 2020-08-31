<a href='<?php echo TTURL; ?>/torrents/read?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Back</button></a>
        <a href='<?php echo TTURL; ?>/rating/index?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Rating</button></a>
        <a href='<?php echo TTURL; ?>/torrents/edit?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Edit</button></a>
        <a href='<?php echo TTURL; ?>/comments/torrent?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Comments</button></a>
        <a href='<?php echo TTURL; ?>/torrents/torrentfilelist?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Files</button></a>
        <?php if ($row["external"] != 'yes') {?>
        <a href='<?php echo TTURL; ?>/peers/peerlist?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Peers</button></a>
        <?php }?>
        <?php if ($config["imdb"] == 'yes') {?>
        <a href='<?php echo TTURL; ?>/imdbs?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">IMDB</button></a>
        <?php }?>
        <?php if ($row["external"] == 'yes') {?>
        <a href='<?php echo TTURL; ?>/torrents/torrenttrackerlist?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm btn-primary">Trackers</button></a>
        <?php }