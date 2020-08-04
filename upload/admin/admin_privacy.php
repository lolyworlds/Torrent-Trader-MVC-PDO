<?php
#======================================================================#
#  Strong Privacy Users - Added by djhowarth (01-12-2011) 
#======================================================================#
if ($action == "privacylevel")
{
    $where = array();
    
    switch ( $_GET['type'] )
    {
        case 'low': 
              $where[] = "privacy = 'low'";    break;
        case 'normal':
              $where[] = "privacy = 'normal'"; break;
        case 'strong':                         
              $where[] = "privacy = 'strong'"; break;
        default:
              break;
    }
    
    $where[] = "enabled = 'yes'";
    $where[] = "status = 'confirmed'";
    
    $where = implode(' AND ', $where);
    
    $count = get_row_count("users", "WHERE $where");
    // $count = DB::run("SELECT COUNT(*) FROM users WHERE $where")->fetchColumn();

    list($pagertop, $pagerbottom, $limit) = pager(25, $count, htmlspecialchars($_SERVER['REQUEST_URI'] . '&'));  
                                                                     
    $res = DB::run("SELECT id, username, class, email, ip, added, last_access FROM users WHERE $where ORDER BY username DESC $limit");
    
    stdhead("Privacy Level");
    adminnavmenu();
    
    begin_frame("Privacy Level");
    ?>
    
    <center>
    This page displays all users which are enabled, confirmed grouped by their privacy level.
    </center>

    <br />
    <table align="right">
    <tr>
        <td valign="top">
        <form id='sort' action=''>
        <b>Privacy Level:</b>
        <select name="type" onchange="window.location='<?php echo TTURL; ?>/admincp?action=privacylevel&type='+this.options[this.selectedIndex].value">
        <option value="">Any</option>
        <option value="low" <?php echo ($_GET['type'] == "low" ? " selected='selected'" : ""); ?>>Low</option>
        <option value="normal" <?php echo ($_GET['type'] == "normal" ? " selected='selected'" : ""); ?>>Normal</option>
        <option value="strong" <?php echo ($_GET['type'] == "strong" ? " selected='selected'" : ""); ?>>Strong</option>
        </select>
        </form>     
    </td>
    </tr>
    </table>
    <br />
    <br />
    
    <?php if ($count > 0): ?>
    <br />
    <div class='table-responsive'><table class='table table-striped'>
    <thead><tr>
        <th>Username</th>
        <th><?php echo T_("CLASS");?></th>
        <th>E-mail</th>
        <th>IP</th>
        <th>Added</th>
        <th>Last Visited</th>  
    </tr></thead>
    <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
    <tbody><tr>
        <td><a href="<?php echo TTURL; ?>/users/profile?id=<?php echo $row["id"]; ?>"><?php echo class_user_colour($row["username"]); ?></a></td>
        <td><?php echo get_user_class_name($row["class"]); ?></td>
        <td><?php echo $row["email"]; ?></td>
        <td><?php echo $row["ip"]; ?></td>
        <td><?php echo utc_to_tz($row["added"]); ?></td>
        <td><?php echo utc_to_tz($row["last_access"]); ?></td> 
    </tr>
    <?php endwhile; ?>
    <tbody></table></div>         
    <?php else: ?>
    <center><b>Nothing Found...</b></center>
    <?php  
    endif;
    
    if ($count > 25) echo $pagerbottom;
    
    end_frame();
    stdfoot(); 
}