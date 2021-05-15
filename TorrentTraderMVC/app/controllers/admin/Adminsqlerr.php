<?php
class Adminsqlerr extends Controller
{

    public function __construct()
    {
        Auth::user(); // should check admin here
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        if ($_POST['do'] == 'delete')
        {
            if (!@count($_POST['ids'])) show_error_msg(Lang::T("ERROR"), "Nothing Selected.", 1);
            $ids = array_map('intval', $_POST['ids']);
            $ids = implode(',', $ids);
            DB::run("DELETE FROM `sqlerr` WHERE `id` IN ($ids)");
            Redirect::autolink(URLROOT."/adminsqlerr", "Entries deleted.");
        }
        // Master pagination example
        $count = get_row_count('sqlerr');
        list($pagertop, $pagerbottom, $limit) = pager(2, $count, URLROOT.'/adminsqlerr?');
        $res = DB::run("SELECT * FROM `sqlerr` ORDER BY `time` DESC $limit");
        
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin('SQL Error');
        
        if ($count > 0): ?>
        <form id="sqlerr" method="post" action="<?php echo URLROOT; ?>/dmincp/sqlerr">
        <input type="hidden" name="do" value="delete" />
        <div class='table-responsive'> <table class='table table-striped'><thead>
        <tr><thead>
            <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);"</th>
            <th class="table_head">Message</th>
            <th class="table_head">Added</th>
        </tr></thead>
        <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
        <tr><tbody>
            <td class="table_col1"><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>" /></td>
            <td class="table_col2"><?php echo $row['txt']; ?></td>
            <td class="table_col1"><?php echo TimeDate::utc_to_tz($row['time']); ?></td>
        </tr><tbody>
        <?php endwhile; ?>
        </table>
        <center><input class="btn btn-sm btn-primary" type="submit" value="Delete" /></center>
        </div>
        </form>
        <?php 
        else:
          echo('<center><b>No Error logs found...</b></center>');
        endif;
              
        if ($count > 2) echo($pagerbottom);
        
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

}