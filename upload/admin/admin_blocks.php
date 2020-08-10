<?php

///////////////// BLOCKS MANAGEMENT /////////////
if ($action=="blocks" && $do=="view") {
    $title = T_("_BLC_MAN_");
	require 'views/admin/header.php';
    adminnavmenu();

    begin_frame(T_("BLC_VIEW"));

    $enabled = DB::run("SELECT named, name, description, position, sort FROM blocks WHERE enabled=1 ORDER BY position, sort");
    $disabled = DB::run("SELECT named, name, description, position, sort FROM blocks WHERE enabled=0 ORDER BY position, sort");
    
    print("<table align=\"center\" width=\"70%\"><tr><td>");
    print("<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<th class=\"table_head\"><center>".T_("_BLC_ENABLED_")."</center></th>".
            "</tr>".
        "</table>".

        "<table class='table table-striped table-bordered table-hover'>
        <thead>".
            "<tr>".
                "<th class=\"table_head\">".T_("NAME")."</th>".
                "<th class=\"table_head\">Description</th>".
                "<th class=\"table_head\">Position</th>".
                "<th class=\"table_head\">Sort<br />Order</th>".
                "<th class=\"table_head\">Preview</th>".
            "</tr></thead><tbody>");
        while($blocks = $enabled->fetch(PDO::FETCH_LAZY)){
        if(!$setclass){
            $class="table_col2";$setclass=true;}
        else{
            $class="table_col1";$setclass=false;}
    
            print("<tr>".
                        "<td class=\"$class\" valign=\"top\">".$blocks["named"]."</td>".
                        "<td class=\"$class\">".$blocks["description"]."</td>".
                        "<td class=\"$class\" align=\"center\">".$blocks["position"]."</td>".
                        "<td class=\"$class\" align=\"center\">".$blocks["sort"]."</td>".
                        "<td class=\"$class\" align=\"center\">[<a href=\"blocks/edit?preview=true&amp;name=".$blocks["name"]."#".$blocks["name"]."\" target=\"_blank\">preview</a>]</td>".
                    "</tr>");
        }
    print("<tr><td colspan=\"5\" align=\"center\" class=\"table_head\"><form action='blocks/edit'><input type='submit' value='Edit' /></form></td></tr>");
    print("</tbody></table>");
    print("</td></tr></table>");    
    
    print("<hr />");
    $setclass=false;
    print("<table align=\"center\" width=\"600\"><tr><td>");
    print("<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<th class=\"table_head\"><center>Disabled Blocks</center></th>".
            "</tr>".
        "</table>".
        "<table class='table'>
        <thead>".
            "<tr>".
                "<th class=\"table_head\">".T_("NAME")."</th>".
                "<th class=\"table_head\">Description</th>".
                "<th class=\"table_head\">Position</th>".
                "<th class=\"table_head\">Sort<br />Order</th>".
                "<th class=\"table_head\">Preview</th>".
            "</tr></thead><tbody>");
        while($blocks = $disabled->fetch(PDO::FETCH_LAZY)){
        if(!$setclass){
            $class="table_col2";$setclass=true;}
        else{
            $class="table_col1";$setclass=false;}
    
            print("<tr>".
                        "<td class='$class' valign=\"top\">".$blocks["named"]."</td>".
                        "<td class='$class'>".$blocks["description"]."</td>".
                        "<td class='$class' align=\"center\">".$blocks["position"]."</td>".
                        "<td class='$class' align=\"center\">".$blocks["sort"]."</td>".
                        "<td class='$class' align=\"center\">[<a href=\"blocks/edit?preview=true&amp;name=".$blocks["name"]."#".$blocks["name"]."\" target=\"_blank\">preview</a>]</td>".
                    "</tr>");
        }
    print("<tr><td colspan=\"5\" align=\"center\" valign=\"bottom\" class=\"table_head\"><form action='blocks/edit'><input type='submit' value='Edit' /></form></td></tr>");
    print("</tbody></table>");
    print("</td></tr></table>");    
    end_frame();
    require 'views/admin/footer.php';    
}