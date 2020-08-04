  <center>
  This page displays the options that you can redeem accumulated points against depending on the share of torrents. <font color=yellow><b><?php echo $data['usersbonus']; ?></font></b>
  </center>
  <br />
  <div class='table-responsive'> <table class='table table-striped'><thead><tr>
      <th>Option</th>
      <th>What is it?</th>
      <th>Points</th>
      <th>Exchange</th>
  </tr></thead>
  <?php foreach ($data['bonus'] as $row): ?>
  <form method="post" action="<?php echo TTURL; ?>/bonus">
  <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
  <tbody><tr>
      <td><?php echo htmlspecialchars($row->title); ?></td>
      <td><?php echo htmlspecialchars($row->descr); ?></td>
      <td><?php echo $row->cost; ?></td>
      <td><input type="submit" value="Exchange"></td>
  </tr></tbody>
  </form>
  <?php endforeach; ?>
  </table>
  <ul>
     <li>You recieve <font color=red><?php echo $data['configbonuspertime']; ?></font> points per <?php echo $data['configautoclean_interval']; ?> minutes the system registers you as a seeder by torrent.</li>
  </ul>
  <ul>
     <li><a href='<?php echo TTURL; ?>/user/profile?id=<?php echo $CURUSER["id"]; ?>'>Return to profile</a></li>
  </ul>
  </div>