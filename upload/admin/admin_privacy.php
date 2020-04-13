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
    navmenu();
    
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
        <select name="type" onchange="window.location='admincp.php?action=privacylevel&type='+this.options[this.selectedIndex].value">
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
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head"><?php echo T_("CLASS");?></th>
        <th class="table_head">E-mail</th>
        <th class="table_head">IP</th>
        <th class="table_head">Added</th>
        <th class="table_head">Last Visited</th>  
    </tr>
    <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="account-details.php?id=<?php echo $row["id"]; ?>"><?php echo $row["username"]; ?></a></td>
        <td class="table_col2" align="center"><?php echo get_user_class_name($row["class"]); ?></td>
        <td class="table_col1" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo utc_to_tz($row["last_access"]); ?></td> 
    </tr>
    <?php endwhile; ?>
    </table>         
    <?php else: ?>
    <center><b>Nothing Found...</b></center>
    <?php  
    endif;
    
    if ($count > 25) echo $pagerbottom;
    
    end_frame();
    stdfoot(); 
}