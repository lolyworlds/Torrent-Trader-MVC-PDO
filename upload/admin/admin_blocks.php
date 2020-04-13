<?php

///////////////// BLOCKS MANAGEMENT /////////////
if ($action=="blocks" && $do=="view") {
    stdhead(T_("_BLC_MAN_"));

    navmenu();

    begin_frame(T_("BLC_VIEW"));

    $enabled = DB::run("SELECT named, name, description, position, sort FROM blocks WHERE enabled=1 ORDER BY position, sort");
    $disabled = DB::run("SELECT named, name, description, position, sort FROM blocks WHERE enabled=0 ORDER BY position, sort");
    
    print("<table align=\"center\" width=\"600\"><tr><td>");
    print("<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<th class=\"table_head\">".T_("_BLC_ENABLED_")."</th>".
            "</tr>".
        "</table><br />".
        "<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<th class=\"table_head\">".T_("NAME")."</th>".
                "<th class=\"table_head\">Description</th>".
                "<th class=\"table_head\">Position</th>".
                "<th class=\"table_head\">Sort<br />Order</th>".
                "<th class=\"table_head\">Preview</th>".
            "</tr>");
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
                        "<td class=\"$class\" align=\"center\">[<a href=\"blocks-edit.php?preview=true&amp;name=".$blocks["name"]."#".$blocks["name"]."\" target=\"_blank\">preview</a>]</td>".
                    "</tr>");
        }
    print("<tr><td colspan=\"5\" align=\"center\" class=\"table_head\"><form action='blocks-edit.php'><input type='submit' value='Edit' /></form></td></tr>");
    print("</table>");
    print("</td></tr></table>");    
    
    print("<hr />");
    $setclass=false;
    print("<table align=\"center\" width=\"600\"><tr><td>");
    print("<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<th class=\"table_head\">Disabled Blocks</th>".
            "</tr>".
        "</table><br />".
        "<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<th class=\"table_head\">".T_("NAME")."</th>".
                "<th class=\"table_head\">Description</th>".
                "<th class=\"table_head\">Position</th>".
                "<th class=\"table_head\">Sort<br />Order</th>".
                "<th class=\"table_head\">Preview</th>".
            "</tr>");
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
                        "<td class='$class' align=\"center\">[<a href=\"blocks-edit.php?preview=true&amp;name=".$blocks["name"]."#".$blocks["name"]."\" target=\"_blank\">preview</a>]</td>".
                    "</tr>");
        }
    print("<tr><td colspan=\"5\" align=\"center\" valign=\"bottom\" class=\"table_head\"><form action='blocks-edit.php'><input type='submit' value='Edit' /></form></td></tr>");
    print("</table>");
    print("</td></tr></table>");    
    end_frame();
    stdfoot();    
}

