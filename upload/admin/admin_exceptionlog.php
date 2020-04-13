<?php
#======================================================================#
#  SQL Error Log - Added by djhowarth (23-12-2011)
#======================================================================#
  if ($action == 'sqlerr')
  {
      if ($_POST['do'] == 'delete')
      {
          if (!@count($_POST['ids'])) show_error_msg(T_("ERROR"), "Nothing Selected.", 1);
          $ids = array_map('intval', $_POST['ids']);
          $ids = implode(',', $ids);
          
          DB::run("DELETE FROM `sqlerr` WHERE `id` IN ($ids)");
          autolink("/admincp?action=sqlerr", "Entries deleted.");
      }
      
      
      $count = get_row_count('sqlerr');
      
      list($pagertop, $pagerbottom, $limit) = pager(25, $count, '/admincp?action=sqlerr&amp;');
      
      $res = DB::run("SELECT * FROM `sqlerr` ORDER BY `time` DESC $limit");
      
      stdhead('SQL Error');
      navmenu();
      
      begin_frame('SQL Error');
      
      if ($count > 0): ?>
      <form id="sqlerr" method="post" action="/admincp?action=sqlerr">
      <input type="hidden" name="do" value="delete" />
      <table cellpadding="5" class="table_table" width="100%">
      <tr>
          <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);"</th>
          <th class="table_head">Message</th>
          <th class="table_head">Added</th>
      </tr>
      <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
      <tr>
          <td class="table_col1"><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>" /></td>
          <td class="table_col2"><?php echo $row['txt']; ?></td>
          <td class="table_col1"><?php echo utc_to_tz($row['time']); ?></td>
      </tr>
      <?php endwhile; ?>
      <tr>
          <td align="right" colspan="3">
          <input type="submit" value="Delete" />
          </td>
      </tr>
      </table>
      </form>
      <?php 
      else:
        echo('<center><b>No Error logs found...</b></center>');
      endif;
            
      if ($count > 25) echo($pagerbottom);
      
      end_frame();
      stdfoot();
  }