<?php
        $arr = $data['res']->fetch(PDO::FETCH_ASSOC);
        Style::begin("Edit Request");
        print("<a href=".URLROOT."/request><button  class='btn btn-sm btn-success'>All Request</button></a>&nbsp;
        <a href=".URLROOT."/request?requestorid=$_SESSION[id]><button  class='btn btn-sm btn-success'>View my requests</button></a>");
        ?>
        <div><center>
        <form name="form" action="takereqedit&id=<?php echo $arr['id']; ?>" method="post">
        <input type="hidden" name="filledby" value="<?php echo $arr['filledby']; ?>" />
        <label for="cat">Change Cat id</label>
        <input type="text" name="cat" value="<?php echo $arr['id']; ?>" id="cat"><br>
        <label for="request">Request Tilte</label>
        <input type="text" name="request" value="<?php echo $arr['request']; ?>" id="request"><br>
        <label for="descr">Description</label>
        <input type="text" name="descr" value="<?php echo $arr['descr']; ?>" id="descr"><br>
        <label for="filled">Url To Torrent</label>
        <input type="text" name="filled" value="<?php echo $arr['filled']; ?>" id="filled"><br>
        <input type="submit" value="Update">
        </form>
        </center></div>
        <?php
        Style::end();