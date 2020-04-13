<?php
#======================================================================#
#  Simple User Search - Updated by djhowarth (21-11-2011) 
#======================================================================#
if ($action == "users")
{
    if ($CURUSER['delete_users'] == 'no' || $CURUSER['delete_torrents'] == 'no')
        autolink("/admincp", "You do not have permission to be here.");
    
    if ($do == "del") 
    {
        if (!@count($_POST["users"])) show_error_msg(T_("ERROR"), "Nothing Selected.", 1);

        $ids = array_map("intval", $_POST["users"]);
        $ids = implode(", ", $ids);

        $res = DB::run("SELECT `id`, `username` FROM `users` WHERE `id` IN ($ids)");
        while ($row = $res->fetch(PDO::FETCH_LAZY))
        {
            write_log("Account '$row[1]' (ID: $row[0]) was deleted by $CURUSER[username]");  
            deleteaccount($row[0]); 
        }
        
        if ($_POST['inc']) 
        {
            $res = DB::run("SELECT `id`, `name` FROM `torrents` WHERE `owner` IN ($ids)");
            while ($row = $res->fetch(PDO::FETCH_LAZY))
            {
                write_log("Torrent '$row[1]' (ID: $row[0]) was deleted by $CURUSER[username]");    
                deletetorrent($row["id"]);
            }  
        } 

        autolink("/admincp?action=users", "Entries Deleted");
    }
    
    $where = null;
    
    if ( !empty( $_GET['search'] ) )
    {
          $search = sqlesc('%' . $_GET['search'] . '%');
        
          $where  = "AND username LIKE " . $search . " OR email LIKE " . $search . "
                     OR ip LIKE " . $search;
    }

    $count = get_row_count("users", "WHERE enabled = 'yes' AND status = 'confirmed' $where");
    
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, '/admincp?action=users&amp;');  
                                                                     
    $res = DB::run("SELECT id, username, class, email, ip, added, last_access FROM users WHERE enabled = 'yes' AND status = 'confirmed' $where ORDER BY username DESC $limit");
    
    stdhead(T_("USERS_SEARCH_SIMPLE"));
    navmenu();
    
    begin_frame(T_("USERS_SEARCH_SIMPLE"));
    ?>
    
    <center>
    This page displays all users which are enabled and confirmed. You can search for users and results will be returned
    matched against there username, e-mail and ip. You can also choose to delete them. If no results are shown please try
    redefining your search.
    
    <br />
    <form method="get" action="/admincp">
    <input type="hidden" name="action" value="users" />
    Search: <input type="text" name="search" size="30" value="<?php echo htmlspecialchars( $_GET['search'] ); ?>" />
    <input type="submit" value="Search" />
    </form>
    </center>

    <?php if ($count > 0): ?>
    <br />
    <form id="usersearch" method="post" action="/admincp?action=users">
    <input type="hidden" name="do" value="del" />
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head"><?php echo T_("CLASS");?></th>
        <th class="table_head">E-mail</th>
        <th class="table_head">IP</th>
        <th class="table_head">Added</th>
        <th class="table_head">Last Visited</th>  
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="/accountdetails?id=<?php echo $row["id"]; ?>"><?php echo class_user($row["username"]); ?></a></td>
        <td class="table_col2" align="center"><?php echo get_user_class_name($row["class"]); ?></td>
        <td class="table_col1" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo utc_to_tz($row["last_access"]); ?></td> 
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td class="table_head" colspan="7" align="center">
        <input type="submit" name="inc" value="Delete (inc. torrents)" />
        <input type="submit" value="Delete" />
        </td>
    </tr>
    </table>         
    </form>
    <?php 
    endif;
    
    if ($count > 25) echo $pagerbottom;
    
    end_frame();
    stdfoot(); 
}
