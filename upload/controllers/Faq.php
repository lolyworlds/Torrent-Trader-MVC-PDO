<?php
  class Faq extends Controller {
    
    public function __construct(){
         $this->faqModel = $this->model('Faqs');
    }
    
    public function index(){
dbconn();
global $config, $pdo;
stdhead(T_("FAQ"));

$faq_categ = null;

$res = $this->faqModel->getFaqByCat();
// $res = DB::run("SELECT `id`, `question`, `flag` FROM `faq` WHERE `type`=? ORDER BY `order` ASC", ['categ']);
while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
 $faq_categ[$arr['id']]['title'] = $arr['question'];
 $faq_categ[$arr['id']]['flag'] = $arr['flag'];
}

$res = $this->faqModel->getFaqByType();
while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
 $faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
 $faq_categ[$arr['categ']]['items'][$arr['id']]['answer'] = $arr['answer'];
 $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
}

if (isset($faq_categ)) {
// gather orphaned items
 foreach ($faq_categ as $id => $temp) {
  if (!array_key_exists("title", $faq_categ[$id])) {
   foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
    $faq_orphaned[$id2]['question'] = $faq_categ[$id]['items'][$id2]['question'];
	$faq_orphaned[$id2]['answer'] = $faq_categ[$id]['items'][$id2]['answer'];
    $faq_orphaned[$id2]['flag'] = $faq_categ[$id]['items'][$id2]['flag'];
    unset($faq_categ[$id]);
   }
  }
 }
 
 begin_frame(T_("CONTENTS"));
  print("<a name='top'></a>"); 
 foreach ($faq_categ as $id => $temp) {
  if ($faq_categ[$id]['flag'] == "1") {
   //print("<ul>\n<li><a href=\"#". $id ."\"><b>". $faq_categ[$id]['title'] ."</b></a>\n<ul>\n");
   
   print("<ul style='list-style: none'>\n<li><a href=\"#section". $id ."\"><b>". stripslashes($faq_categ[$id]['title']) ."</b></a>\n<ul style='list-style: none'>\n");
   if (array_key_exists("items", $faq_categ[$id])) {
    foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
	 if ($faq_categ[$id]['items'][$id2]['flag'] == "1") print("<li><a href=\"#section". $id2 ."\">". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</a></li>\n");
	 elseif ($faq_categ[$id]['items'][$id2]['flag'] == "2") print("<li><a href=\"#section". $id2 ."\">". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</a> <img src=\"".$config["SITEURL"]."/images/faq/updated.png\" alt=\"Updated\" width=\"46\" height=\"13\" align=\"bottom\" /></li>\n");
	 elseif ($faq_categ[$id]['items'][$id2]['flag'] == "3") print("<li><a href=\"#section". $id2 ."\">". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</a> <img src=\"".$config["SITEURL"]."/images/faq/new.png\" alt=\"New\" width=\"25\" height=\"12\" align=\"bottom\" /></li>\n");
    }
   }
   print("</ul>\n</li>\n</ul>\n<br />\n");
  }
 }
 end_frame();

 foreach ($faq_categ as $id => $temp) {
  if ($faq_categ[$id]['flag'] == "1") {
   $frame = $faq_categ[$id]['title'] ." - <a href=\"#top\">Top</a>";
   begin_frame($frame);
   print("<a id=\"section". $id ."\"></a>\n");
   if (array_key_exists("items", $faq_categ[$id])) {
    foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
	 if ($faq_categ[$id]['items'][$id2]['flag'] != "0") {
      print("<br />\n<b>". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</b><a id=\"section". $id2 ."\"></a>\n<br />\n");
      print("<br />\n". stripslashes($faq_categ[$id]['items'][$id2]['answer']) ."\n<br /><br />\n");
	 }
    }
   }
   end_frame();
  }
 }

}


stdfoot();
}

public function actions(){
dbconn();
global $config;
loggedinonly();

if (!$_SESSION['loggedin']  == true || $_SESSION["control_panel"]!="yes"){
 show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}

// ACTION: reorder - reorder sections and items
if ($_GET["action"] == "reorder") {
foreach($_POST['order'] as $id => $position) DB::run("UPDATE `faq` SET `order`='$position' WHERE id='$id'");
header("Refresh: 0; url=".TTURL."/faq/manage"); 
}

// ACTION: edit - edit a section or item
elseif ($_GET["action"] == "edit" && is_valid_id($_GET['id'])) {
stdhead(T_("FAQ_MANAGEMENT"));
begin_frame();
print("<h1 align=\"center\">Edit Section or Item</h1>");

$res = DB::run("SELECT * FROM `faq` WHERE `id`='$_GET[id]' LIMIT 1");
while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
$arr['question'] = stripslashes(htmlspecialchars($arr['question']));
$arr['answer'] = stripslashes(htmlspecialchars($arr['answer']));
if ($arr['type'] == "item") {
 print("<form method=\"post\" action=\"$config[SITEURL]/faq/actions?action=edititem\">");
 print("<table border=\"0\" class=\"table_table\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
 print("<tr><td class='table_col1'>ID:</td><td class='table_col1'>$arr[id] <input type=\"hidden\" name=\"id\" value=\"$arr[id]\" /></td></tr>\n");
 print("<tr><td class='table_col2'>Question:</td><td class='table_col2'><input style=\"width: 300px;\" type=\"text\" name=\"question\" value=\"$arr[question]\" /></td></tr>\n");
 print("<tr><td class='table_col1' style=\"vertical-align: top;\">Answer:</td><td class='table_col1'><textarea rows='3' cols='35' name=\"answer\">$arr[answer]</textarea></td></tr>\n");
 if ($arr['flag'] == "0") print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\" selected=\"selected\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
 elseif ($arr['flag'] == "2") print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\" selected=\"selected\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
 elseif ($arr['flag'] == "3") print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\" selected=\"selected\">New</option></select></td></tr>");
 else print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
 print("<tr><td class='table_col1'>Category:</td><td class='table_col1'><select style=\"width: 300px;\" name=\"categ\">");
 $res2 = DB::run("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
 while ($arr2 = $res2->fetch(PDO::FETCH_BOTH)) {
  $selected = ($arr2['id'] == $arr['categ']) ? " selected=\"selected\"" : "";
  print("<option value=\"$arr2[id]\"". $selected .">$arr2[question]</option>");
 }
 print("</select></td></tr>\n");
 print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Edit\" style=\"width: 60px;\" /></td></tr>\n");
 print("</table></form>");
}
elseif ($arr['type'] == "categ") {
 print("<form method=\"post\" action=\"$config[SITEURL]/faq/actions?action=editsect\">");
 print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
 print("<tr><td class='table_col1'>ID:</td><td class='table_col1'>$arr[id] <input type=\"hidden\" name=\"id\" value=\"$arr[id]\" /></td></tr>\n");
 print("<tr><td class='table_col2'>Title:</td><td class='table_col2'><input style=\"width: 300px;\" type=\"text\" name=\"title\" value=\"$arr[question]\" /></td></tr>\n");
 if ($arr['flag'] == "0") print("<tr><td class='table_col1'>Status:</td><td class='table_col1'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\" selected=\"selected\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option></select></td></tr>");
 else print("<tr><td class='table_col1'>Status:</td><td class='table_col1'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option></select></td></tr>");
 print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Edit\" style=\"width: 60px;\" /></td></tr>\n");
 print("</table></form>");
}
}

end_frame();
stdfoot();
}

// subACTION: edititem - edit an item
elseif ($_GET['action'] == "edititem" && is_valid_id($_POST['id']) && $_POST['question'] != NULL && $_POST['answer'] != NULL && is_valid_int($_POST['flag']) && is_valid_id($_POST['categ'])) {
$question = $_POST['question'];
$answer = $_POST['answer'];
DB::run("UPDATE `faq` SET `question`=?, `answer`=?, `flag`=?, `categ`=? WHERE id=?", [$question, $answer, $_POST['flag'], $_POST['categ'], $_POST['id']]);
header("Refresh: 0; url=".TTURL."/faq/manage"); 
}

// subACTION: editsect - edit a section
elseif ($_GET['action'] == "editsect" && is_valid_id($_POST['id']) && $_POST['title'] != NULL && is_valid_int($_POST['flag'])) {
$title = $_POST['title'];
DB::run("UPDATE `faq` SET `question`=?, `answer`=?, `flag`=?, `categ`=? WHERE id=?", [$title, $_POST['flag'], '', 0, $_POST['id']]);
header("Refresh: 0; url=".TTURL."/faq/manage"); 
}

// ACTION: delete - delete a section or item
elseif ($_GET['action'] == "delete" && isset($_GET['id'])) {
if ($_GET['confirm'] == "yes") {
DB::run("DELETE FROM `faq` WHERE `id`=? LIMIT 1", [$_GET['id']]);
header("Refresh: 0; url=".TTURL."/faq/manage"); 
}
else {
stdhead(T_("FAQ_MANAGEMENT"));
begin_frame();
print("<h1 align=\"center\">Confirmation required</h1>");
print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n<tr><td align=\"center\">Please click <a href=\"$config[SITEURL]/faq/actions?action=delete&amp;id=$_GET[id]&amp;confirm=yes\">here</a> to confirm.</td></tr>\n</table>\n");
end_frame();
stdfoot();
}
}

// ACTION: additem - add a new item
elseif ($_GET['action'] == "additem" && $_GET['inid']) {
stdhead(T_("FAQ_MANAGEMENT"));
begin_frame();
print("<h1 align=\"center\">Add Item</h1>");
print("<form method=\"post\" action=\"$config[SITEURL]/faq/actions?action=addnewitem\">");
print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
print("<tr><td class='table_col1'>Question:</td><td class='table_col1'><input style=\"width: 300px;\" type=\"text\" name=\"question\" value=\"\" /></td></tr>\n");
print("<tr><td class='table_col2' style=\"vertical-align: top;\">Answer:</td><td class='table_col2'><textarea rows='3' cols='35' name=\"answer\"></textarea></td></tr>\n");
print("<tr><td class='table_col1'>Status:</td><td class='table_col1'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\" selected=\"selected\">New</option></select></td></tr>");
print("<tr><td class='table_col2'>Category:</td><td class='table_col2'><select style=\"width: 300px;\" name=\"categ\">");
$res = DB::run("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
$selected = ($arr['id'] == $_GET['inid']) ? " selected=\"selected\"" : "";
print("<option value=\"$arr[id]\"". $selected .">$arr[question]</option>");
}
print("</select></td></tr>\n");
print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Add\" style=\"width: 60px;\" /></td></tr>\n");
print("</table></form>");
end_frame();
stdfoot();
}

// ACTION: addsection - add a new section
elseif ($_GET['action'] == "addsection") {
stdhead(T_("FAQ_MANAGEMENT"));
begin_frame();
print("<h1 align=\"center\">Add Section</h1>");
print("<form method=\"post\" action=\"$config[SITEURL]/faq/actions?action=addnewsect\">");
print("<table border=\"0\" class=\"table_table\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
print("<tr><td class='table_col1'>Title:</td><td class='table_col1'><input style=\"width: 300px;\" type=\"text\" name=\"title\" value=\"\" /></td></tr>\n");
print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option></select></td></tr>");
print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Add\" style=\"width: 60px;\" /></td></tr>\n");
print("</table></form>");
end_frame();
stdfoot();
}

// subACTION: addnewitem - add a new item to the db
elseif ($_GET['action'] == "addnewitem" && $_POST['question'] != NULL && $_POST['answer'] != NULL && is_valid_int($_POST['flag']) && is_valid_int($_POST['categ'])) {
$question = sqlesc($_POST['question']);
$answer = sqlesc($_POST['answer']);
$res = DB::run("SELECT MAX(`order`) FROM `faq` WHERE `type`='item' AND `categ`='$_POST[categ]'");
while ($arr = $res->fetch(PDO::FETCH_BOTH))
   $order = $arr[0] + 1;
   DB::run("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES ('item', $question, $answer, '$_POST[flag]', '$_POST[categ]', '$order')");
   header("Refresh: 0; url=".TTURL."/faq/manage");
}

// subACTION: addnewsect - add a new section to the db
elseif ($_GET['action'] == "addnewsect" && $_POST['title'] != NULL && is_valid_int($_POST['flag'])) {
$title = sqlesc($_POST['title']);
$res = DB::run("SELECT MAX(`order`) FROM `faq` WHERE `type`='categ'");
while ($arr = $res->fetch(PDO::FETCH_BOTH))
   $order = $arr[0] + 1;
   DB::run("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES ('categ', $title, '', '$_POST[flag]', '0', '$order')");
   header("Refresh: 0; url=".TTURL."/faq/manage");
}


else header("Refresh: 0; url=".TTURL."/faq/manage");
}


public function manage(){

dbconn(false);
global $config;
loggedinonly();

if (!$_SESSION['loggedin']  == true || $_SESSION["control_panel"]!="yes"){
show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}

stdhead(T_("FAQ_MANAGEMENT"));
begin_frame(T_("FAQ_MANAGEMENT"));

// make the array that has all the faq in a nice structured
$res = DB::run("SELECT `id`, `question`, `flag`, `order` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
while ($arr = $res->fetch(PDO::FETCH_LAZY)) {
$faq_categ[$arr['id']]['title'] = $arr['question'];
$faq_categ[$arr['id']]['flag'] = $arr['flag'];
$faq_categ[$arr['id']]['order'] = $arr['order'];
}

$res = DB::run("SELECT `id`, `question`, `flag`, `categ`, `order` FROM `faq` WHERE `type`='item' ORDER BY `order` ASC");
while ($arr = $res->fetch(PDO::FETCH_LAZY)) {
$faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
$faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
$faq_categ[$arr['categ']]['items'][$arr['id']]['order'] = $arr['order'];
}

if (isset($faq_categ)) {
// gather orphaned items
foreach ($faq_categ as $id => $temp) {
if (!array_key_exists("title", $faq_categ[$id])) {
 foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
  $faq_orphaned[$id2]['question'] = $faq_categ[$id]['items'][$id2]['question'];
  $faq_orphaned[$id2]['flag'] = $faq_categ[$id]['items'][$id2]['flag'];
  unset($faq_categ[$id]);
 }
}
}

// print the faq table
print("<form method=\"post\" action=\"$config[SITEURL]/faq/actions?action=reorder\">");

foreach ($faq_categ as $id => $temp) {
print("<br />\n<table class='table table-striped table-bordered table-hover'><thead>\n");
print("<tr><th class=\"table_head\" colspan=\"2\">Position</th><th class=\"table_head\">Section/Item ".T_("TITLE").": </th><th class=\"table_head\">Status</th><th class=\"table_head\">Actions</th></tr></thead><tbody>\n");

print("<tr><td class=\"table_col1\" align=\"center\" width=\"40px\"><select name=\"order[". $id ."]\">");
for ($n=1; $n <= count($faq_categ); $n++) {
 $sel = ($n == $faq_categ[$id]['order']) ? " selected=\"selected\"" : "";
 print("<option value=\"$n\"". $sel .">". $n ."</option>");
}
$status = ($faq_categ[$id]['flag'] == "0") ? "<font color=\"red\">Hidden</font>" : "Normal";
print("</select></td><td class=\"table_col2\" align=\"center\" width=\"40px\">&nbsp;</td><td class=\"table_col1\"><b>". stripslashes($faq_categ[$id]['title']) ."</b></td><td class=\"ttable_col2\" align=\"center\" width=\"60px\">". $status ."</td><td class=\"ttable_col1\" align=\"center\" width=\"60px\"><a href=\"$config[SITEURL]/faq/actions?action=edit&amp;id=". $id ."\">edit</a> <a href=\"$config[SITEURL]/faq/actions?action=delete&amp;id=". $id ."\">delete</a></td></tr>\n");

if (array_key_exists("items", $faq_categ[$id])) {
 foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
  print("<tr><td class=\"ttable_col1\" align=\"center\" width=\"40px\">&nbsp;</td><td class=\"table_col2\" align=\"center\" width=\"40px\"><select name=\"order[". $id2 ."]\">");
  for ($n=1; $n <= count($faq_categ[$id]['items']); $n++) {
   $sel = ($n == $faq_categ[$id]['items'][$id2]['order']) ? " selected=\"selected\"" : "";
   print("<option value=\"$n\"". $sel .">". $n ."</option>");
  }
  if ($faq_categ[$id]['items'][$id2]['flag'] == "0") $status = "<font color=\"#ff0000\">Hidden</font>";
  elseif ($faq_categ[$id]['items'][$id2]['flag'] == "2") $status = "<font color=\"#0000FF\">Updated</font>";
  elseif ($faq_categ[$id]['items'][$id2]['flag'] == "3") $status = "<font color=\"#008000\">New</font>";
  else $status = "Normal";
  print("</select></td><td class=\"ttable_col1\">". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</td><td class=\"table_col2\" align=\"center\" width=\"60px\">". $status ."</td><td class=\"ttable_col1\" align=\"center\" width=\"60px\"><a href=\"$config[SITEURL]/faq/actions?action=edit&amp;id=". $id2 ."\">edit</a> <a href=\"$config[SITEURL]/faq/actions?action=delete&amp;id=". $id2 ."\">delete</a></td></tr>\n");
 }
}

print("<tr><td colspan=\"5\" align=\"center\"><a href=\"$config[SITEURL]/faq/actions?action=additem&amp;inid=". $id ."\">Add new item</a></td></tr>\n");
print("</tbody></table>\n");
}
}

// print the orphaned items table
if (isset($faq_orphaned)) {
print("<br />\n<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n");
print("<tr><td align=\"center\" colspan=\"3\"><b style=\"color: #ff0000\">Orphaned Items</b></td>\n");
print("<tr><td  align=\"left\">Item ".T_("TITLE").": </td><td  align=\"center\">Status</td><td  align=\"center\">Actions</td></tr>\n");
foreach ($faq_orphaned as $id => $temp) {
if ($faq_orphaned[$id]['flag'] == "0") $status = "<font color=\"#ff0000\">Hidden</font>";
elseif ($faq_orphaned[$id]['flag'] == "2") $status = "<font color=\"#0000FF\">Updated</font>";
elseif ($faq_orphaned[$id]['flag'] == "3") $status = "<font color=\"#008000\">New</font>";
else $status = "Normal";
print("<tr><td>". stripslashes($faq_orphaned[$id]['question']) ."</td><td align=\"center\" width=\"60px\">". $status ."</td><td align=\"center\" width=\"60px\"><a href=\"/aq/actions?action=edit&amp;id=". $id ."\">edit</a> <a href=\"$config[SITEURL]/faq/actions?action=delete&amp;id=". $id ."\">delete</a></td></tr>\n");
}
print("</table>\n");
}

print("<br />\n<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n<tr><td align=\"center\"><a href=\"$config[SITEURL]/faq/actions?action=addsection\">Add new section</a></td></tr>\n</table>\n");
print("<p align=\"center\"><input type=\"submit\" name=\"reorder\" value=\"Reorder\" /></p>\n");
print("</form>\n");
print("When the position numbers don't reflect the position in the table, it means the order id is bigger than the total number of sections/items and you should check all the order id's in the table and click \"reorder\"\n");
echo $pagerbottom;

end_frame();
stdfoot();
}
  }