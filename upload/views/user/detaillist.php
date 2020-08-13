<?php usermenu($id); ?>
          <div class="row">
            <div class="col-sm-2">
            <ul class="list-group">
            <li class="list-group-item"><a href="<?php echo TTURL; ?>/users/details?id=<?php echo $id; ?>"><?php echo 'Details';?></a></span></li>
            <li class="list-group-item"><a href="<?php echo TTURL; ?>/users/changepw?id=<?php echo $id; ?>"><?php echo 'Change Password';?></a></span></li>
                           <li class="list-group-item"><a href="<?php echo TTURL; ?>/users/signature?id=<?php echo $id; ?>"><?php echo 'Signature';?></a></span></li>
                <li class="list-group-item"><a href="<?php echo TTURL; ?>/users/avatar?id=<?php echo $id; ?>"><?php echo 'Avatar';?></a></span></li>
            </ul>
            </div>
            <div class="col-sm-8">