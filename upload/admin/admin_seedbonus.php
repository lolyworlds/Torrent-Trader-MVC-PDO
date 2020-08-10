<?php
#======================================================================#
# Seedbonus Manager
#======================================================================#
if ($action == "seedbonus" && $do != "change")
{

   if (get_user_class() < 7)
      show_error_msg("STOP", "Area reserved for people entitled! and you do not have permission !!!");
    if ($do == "del")
    {
        if (!@count($_POST["ids"])) show_error_msg("Error", "select nothing.", 1);
        $ids = array_map("intval", $_POST["ids"]);
        $ids = implode(", ", $ids);
        
        DB::run("DELETE FROM `bonus` WHERE `id` IN ($ids)");
        autolink("admincp?action=seedbonus", "deleted entries");
    }

    $count = get_row_count("bonus");

    list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'admincp?action=seedbonus&amp;');
                                                                                                                                                                                                                                                                
    $res = DB::run("SELECT id, title, cost, value, descr, type FROM `bonus` ORDER BY `type` $limit");

	$title = T_("Seedbonus Manager");
    require 'views/admin/header.php';                                                      
    echo '<br>';
    begin_frame("Management of Seed Bonus");
    ?>

    <center>
This page displays all available options trade which users can exchange for seedbonus <?php echo number_format($count); ?>
    </center>
    <center>
<a href="admincp?action=seedbonus&amp;do=change&amp;id=null">Add</a> a new option?
    </center>
    <?php if ($count > 0): ?>
    <form id="seedbonus" method="post" action="<?php echo TTURL; ?>/admincp?action=seedbonus">
    <input type="hidden" name="do" value="del" />
    <div class='table-responsive'> <table class='table table-striped'><thead><tr>
        <th>Title</th>
        <th>Description</th>
        <th>Points</th>
        <th>Value</th>
        <th>Type</th>
        <th>Edit</th>
        <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr></thead>

    <?php while ($row = $res->fetch(PDO::FETCH_LAZY)):
	  //  while ($row = mysqli_fetch_object($res)):
  $row->value = ( $row->type == "traffic" ) ? mksize( $row->value ) : ( int ) $row->value;
  ?>
    <tbody><tr>
    <td><?php echo htmlspecialchars($row->title); ?></td>
    <td><?php echo htmlspecialchars($row->descr); ?></td>
    <td><?php echo $row->cost; ?></td>
    <td><?php echo $row->value; ?></td>
    <td><?php echo $row->type; ?></td>
    <td><a href="admincp?action=seedbonus&amp;do=change&amp;id=<?php echo $row->id; ?>">Edit</a></td>
    <td><input type="checkbox" name="ids[]" value="<?php echo $row->id; ?>" /></td>
    </tr></tbody>
    <?php endwhile; ?>

      </table>
  <ul>
        <li><input type="submit" value="Remove Selected" /></li>
        </ul>
        
    </form>
    </div>
    <?php
    endif;

    if ($count > 25) echo $pagerbottom;

    end_frame();
    require 'views/admin/footer.php';
}
        
if ($action == "seedbonus" && $do == "change")
{
    $row = null;
    if ( is_valid_id($_REQUEST['id']) )
    {
        $res = DB::run("SELECT id, title, cost, value, descr, type FROM `bonus` WHERE `id` = '$_REQUEST[id]'");
        $row = $res->fetch(PDO::FETCH_LAZY);
		//$row = mysqli_fetch_object($res);
    }

    if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
    {
        if ( empty($_POST['title']) or empty($_POST['descr']) or empty($_POST['type']) or !is_numeric($_POST['cost']) )
        {
                autolink($_SERVER['HTTP_REFERER'], "missing information.");
        }


$_POST["value"] = ( $_POST["type"] == "traffic" ) ? strtobytes( $_POST["value"] ) : ( int ) $_POST["value"];
        $var = array_map('sqlesc', $_POST);
        extract( $var );

        if ( $row == null )
        {
                DB::run("INSERT INTO `bonus` (`title`, `descr`, `cost`, `value`, `type`) VALUES ($title, $descr, $cost, $value, $type)");
        }
        else
        {
                DB::run("UPDATE `bonus` SET `title` = $title, `descr` = $descr, `cost` = $cost, `value` = $value, `type` = $type WHERE `id` = $id");
        }
                        
        autolink("admincp?action=seedbonus", "Updating the bonus seed.");
    }
        
	$title = T_("Seedbonus Manager");
    require 'views/admin/header.php';
    echo '<br>';
    begin_frame("Seedbonus Management");
    ?>

    <form method="post" action="<?php echo TTURL; ?>/admincp?action=seedbonus&amp;do=change">

    <?php if ($row != null): ?>
    <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
    <?php endif; ?>

    <div class='table-responsive'> <table class='table table-striped'><thead><tr>
        <td><b>Title:</b></td>
        <td><input type="text" name="title" value="<?php echo ( $row != null ? $row->title : null ); ?>" size="50" /></td>
    </tr>
    <tr>
        <td><b>Points:</b></td>
        <td><input type="text" name="cost" value="<?php echo ( $row != null ? $row->cost : null ); ?>" size="5" /></td>
    </tr>
    <tr>
        <td><b>Type:</b></td>
        <td>
        <select name="type">
        <?php foreach (array('invite', 'traffic','VIP', 'other', 'HnR') as $type): ?>
        <option value="<?php echo $type; ?>" <?php echo ( $row != null && $row->type == $type ? 'selected="selected"' : null ); ?>><?php echo $type; ?></option>
        <?php endforeach; ?>
        </select>
        </td>
    </tr>
    <tr>
        <td><b>Value:</b></td>
        <td><input type="text" name="value" value="<?php echo ( $row != null ? $row->value : null ); ?>" size="10" /></td>
    </tr>
    <tr>
        <td><b>Description:</b></td>
        <td><textarea name="descr" rows="5" cols="38"><?php echo ( $row != null ? $row->descr : null ); ?></textarea></td>
    </tr></tbody>
    </table>
    <ul>
        <input type="reset" value="Cancel" />
        <input type="submit" value="To send" />
    </ul>
    </form>
    </div>

    <?php
    end_frame();
    require 'views/admin/footer.php';
}