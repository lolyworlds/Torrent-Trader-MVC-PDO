<center><a href='<?php echo URLROOT; ?>/adminpolls/pollsadd'>Add New Poll</a>&nbsp;/&nbsp;
<a href='<?php echo URLROOT; ?>/adminpolls/pollsresults'>View Poll Results</a></center>
<b>Polls</b> (Top poll is current)<br />
<div class='border border-warning'>
<?php while ($row = $data['query']->fetch(PDO::FETCH_ASSOC)) { ?>
   <a href='<?php echo URLROOT; ?>/adminpolls/pollsadd?subact=edit&amp;pollid=<?php echo $row['id']; ?>'>
   <?php echo stripslashes($row['question']); ?></a> - 
   <?php echo TimeDate::utc_to_tz($row['added']); ?> - 
   <a href='<?php echo URLROOT; ?>/adminpolls/pollsdelete?id=<?php echo $row['id']; ?>'>Delete</a><br />
<?php } ?>
</div>