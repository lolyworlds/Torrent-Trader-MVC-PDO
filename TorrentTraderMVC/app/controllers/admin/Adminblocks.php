<?php
class Adminblocks extends Controller
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
        $title = Lang::T("_BLC_MAN_");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();

        Style::begin(Lang::T("BLC_VIEW"));

        $enabled = DB::run("SELECT named, name, description, position, sort FROM blocks WHERE enabled=1 ORDER BY position, sort");
        $disabled = DB::run("SELECT named, name, description, position, sort FROM blocks WHERE enabled=0 ORDER BY position, sort");

        print("<table align=\"center\" width=\"70%\"><tr><td>");
        print("<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">" .
            "<tr>" .
            "<th class=\"table_head\"><center>" . Lang::T("_BLC_ENABLED_") . "</center></th>" .
            "</tr>" .
            "</table>" .

            "<table class='table table-striped table-bordered table-hover'>
        <thead>" .
            "<tr>" .
            "<th class=\"table_head\">" . Lang::T("NAME") . "</th>" .
            "<th class=\"table_head\">Description</th>" .
            "<th class=\"table_head\">Position</th>" .
            "<th class=\"table_head\">Sort<br />Order</th>" .
            "<th class=\"table_head\">Preview</th>" .
            "</tr></thead><tbody>");
        while ($blocks = $enabled->fetch(PDO::FETCH_LAZY)) {
            if (!$setclass) {
                $class = "table_col2";
                $setclass = true;} else {
                $class = "table_col1";
                $setclass = false;}

            print("<tr>" .
                "<td class=\"$class\" valign=\"top\">" . $blocks["named"] . "</td>" .
                "<td class=\"$class\">" . $blocks["description"] . "</td>" .
                "<td class=\"$class\" align=\"center\">" . $blocks["position"] . "</td>" .
                "<td class=\"$class\" align=\"center\">" . $blocks["sort"] . "</td>" .
                "<td class=\"$class\" align=\"center\">[<a href=\"".URLROOT."/blocks/preview?name=" . $blocks["name"] . "#" . $blocks["name"] . "\" target=\"_blank\">preview</a>]</td>" .
                "</tr>");
        }
        print("<tr><td colspan=\"5\" align=\"center\" class=\"table_head\"><form action='".URLROOT."/blocks/edit'><input type='submit' value='Edit' /></form></td></tr>");
        print("</tbody></table>");
        print("</td></tr></table>");

        print("<hr />");
        $setclass = false;
        print("<table align=\"center\" width=\"600\"><tr><td>");
        print("<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">" .
            "<tr>" .
            "<th class=\"table_head\"><center>Disabled Blocks</center></th>" .
            "</tr>" .
            "</table>" .
            "<table class='table'>
        <thead>" .
            "<tr>" .
            "<th class=\"table_head\">" . Lang::T("NAME") . "</th>" .
            "<th class=\"table_head\">Description</th>" .
            "<th class=\"table_head\">Position</th>" .
            "<th class=\"table_head\">Sort<br />Order</th>" .
            "<th class=\"table_head\">Preview</th>" .
            "</tr></thead><tbody>");
        while ($blocks = $disabled->fetch(PDO::FETCH_LAZY)) {
            if (!$setclass) {
                $class = "table_col2";
                $setclass = true;} else {
                $class = "table_col1";
                $setclass = false;}

            print("<tr>" .
                "<td class='$class' valign=\"top\">" . $blocks["named"] . "</td>" .
                "<td class='$class'>" . $blocks["description"] . "</td>" .
                "<td class='$class' align=\"center\">" . $blocks["position"] . "</td>" .
                "<td class='$class' align=\"center\">" . $blocks["sort"] . "</td>" .
                "<td class='$class' align=\"center\">[<a href=\"".URLROOT."/blocks/preview?name=" . $blocks["name"] . "#" . $blocks["name"] . "\" target=\"_blank\">preview</a>]</td>" .
                "</tr>");
        }
        print("<tr><td colspan=\"5\" align=\"center\" valign=\"bottom\" class=\"table_head\"><form action='".URLROOT."/blocks/upload'><input type='submit' value='Upload new Block' /></form></td></tr>");
        print("</tbody></table>");
        print("</td></tr></table>");
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }
}