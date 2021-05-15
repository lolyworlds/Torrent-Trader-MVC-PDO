<?php
class Faq extends Controller
{
    public function __construct()
    {
        Auth::user();
        $this->faqModel = $this->model('Faqs');
        $this->valid = new Validation();
    }

    public function index()
    {
        Style::header(Lang::T("FAQ"));
        $faq_categ = null;
        $res = $this->faqModel->getFaqByCat();
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
            $data = [
                'faq_categ' => $faq_categ,
            ];
            $this->view('faq/index', $data);
            Style::footer();
        }
    }


    public function manage()
    {
        if (!$_SESSION['loggedin'] == true || $_SESSION["control_panel"] != "yes") {
            show_error_msg(Lang::T("ERROR"), Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
        }

        Style::header(Lang::T("FAQ_MANAGEMENT"));

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

            $data = [
                'faq_categ' => $faq_categ,
            ];
            $this->view('faq/manage', $data);
        }
        Style::footer();
    }

    public function reorder()
    {
            foreach ($_POST['order'] as $id => $position) {
                DB::run("UPDATE `faq` SET `order`='$position' WHERE id='$id'");
            }
            Redirect::to(URLROOT . "/faq/manage");
    }


    public function delete()
    {
        if ($_GET['confirm'] == "yes") {
            DB::run("DELETE FROM `faq` WHERE `id`=? LIMIT 1", [$_GET['id']]);
            Redirect::to(URLROOT . "/faq/manage");
        } else {
            Style::header(Lang::T("FAQ_MANAGEMENT"));
            Style::begin();
            print("<h1 align=\"center\">Confirmation required</h1>");
            print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n<tr><td align=\"center\">Please click <a href=\"" . URLROOT . "/faq/delete?id=$_GET[id]&amp;confirm=yes\">here</a> to confirm.</td></tr>\n</table>\n");
            Style::end();
            Style::footer();
        }
    }

    public function newsection()
    {
        if ($_POST['action'] == "addnewsect" && $_POST['title'] != null && $this->valid->validInt($_POST['flag'])) {
            $title = sqlesc($_POST['title']);
            $res = DB::run("SELECT MAX(`order`) FROM `faq` WHERE `type`='categ'");
            while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
                $order = $arr[0] + 1;
            }
            DB::run("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES ('categ', $title, '', '$_POST[flag]', '0', '$order')");
            Redirect::to(URLROOT . "/faq/manage");
        }

        Style::header(Lang::T("FAQ_MANAGEMENT"));
        Style::begin();
        print("<h1 align=\"center\">Add Section</h1>");
        print("<form method=\"post\" action=\"" . URLROOT . "/faq/newsection\">");
        print("<input type=hidden name=action value=addnewsect>");
        print("<table border=\"0\" class=\"table_table\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
        print("<tr><td class='table_col1'>Title:</td><td class='table_col1'><input style=\"width: 300px;\" type=\"text\" name=\"title\" value=\"\" /></td></tr>\n");
        print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option></select></td></tr>");
        print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Add\" style=\"width: 60px;\" /></td></tr>\n");
        print("</table></form>");
        Style::end();
        Style::footer();
    }


    //elseif ($_GET['action'] == "additem" && $_GET['inid']) {
        public function additem()
        {
            Style::header(Lang::T("FAQ_MANAGEMENT"));
            // subACTION: addnewitem - add a new item to the db
            if ($_POST['question'] != null && $_POST['answer'] != null && $this->valid->validInt($_POST['flag']) && $this->valid->validInt($_POST['categ'])) {
                $question = sqlesc($_POST['question']);
                $answer = sqlesc($_POST['answer']);
                $res = DB::run("SELECT MAX(`order`) FROM `faq` WHERE `type`='item' AND `categ`='$_POST[categ]'");
                while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
                    $order = $arr[0] + 1;
                }
    
                DB::run("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES ('item', $question, $answer, '$_POST[flag]', '$_POST[categ]', '$order')");
                Redirect::to(URLROOT . "/faq/manage");
            }
            Style::begin();
            print("<h1 align=\"center\">Add Item</h1>");
            print("<form method='post' action='" . URLROOT . "/faq/additem'>");
            print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
            print("<tr><td class='table_col1'>Question:</td><td class='table_col1'><input style=\"width: 300px;\" type=\"text\" name=\"question\" value=\"\" /></td></tr>\n");
            print("<tr><td class='table_col2' style=\"vertical-align: top;\">Answer:</td><td class='table_col2'><textarea rows='3' cols='35' name=\"answer\"></textarea></td></tr>\n");
            print("<tr><td class='table_col1'>Status:</td><td class='table_col1'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\" selected=\"selected\">New</option></select></td></tr>");
            print("<tr><td class='table_col2'>Category:</td><td class='table_col2'><select style=\"width: 300px;\" name=\"categ\">");
            $res = DB::run("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
            while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
                $selected = ($arr['id'] == $_GET['inid']) ? " selected=\"selected\"" : "";
                print("<option value=\"$arr[id]\"" . $selected . ">$arr[question]</option>");
            }
            print("</select></td></tr>\n");
            print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Add\" style=\"width: 60px;\" /></td></tr>\n");
            print("</table></form>");
            Style::end();
            Style::footer();
        }
    

    public function edit()
    {
        if (!$_SESSION['loggedin'] == true || $_SESSION["control_panel"] != "yes") {
            show_error_msg(Lang::T("ERROR"), Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
        }
        // subACTION: edititem - edit an item
        if ($_GET['action'] == "edititem" && $this->valid->validId($_POST['id']) && $_POST['question'] != null && $_POST['answer'] != null && $this->valid->validInt($_POST['flag']) && $this->valid->validId($_POST['categ'])) {
            $question = $_POST['question'];
            $answer = $_POST['answer'];
            DB::run("UPDATE `faq` SET `question`=?, `answer`=?, `flag`=?, `categ`=? WHERE id=?", [$question, $answer, $_POST['flag'], $_POST['categ'], $_POST['id']]);
            Redirect::to(URLROOT . "/faq/manage");
        }
        
                // subACTION: editsect - edit a section
        if ($_GET['action'] == "editsect" && $this->valid->validId($_POST['id']) && $_POST['title'] != null && $this->valid->validInt($_POST['flag'])) {
            $title = $_POST['title'];
            DB::run("UPDATE `faq` SET `question`=?, `answer`=?, `flag`=?, `categ`=? WHERE id=?", [$title, $_POST['flag'], '', 0, $_POST['id']]);
            Redirect::to(URLROOT . "/faq/manage");
        }

        $res = DB::run("SELECT * FROM `faq` WHERE `id`='$_GET[id]' LIMIT 1");
        $res2 = DB::run("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");

        //if ($_GET["action"] == "edit" && $this->valid->validId($_GET['id'])) {
            Style::header(Lang::T("FAQ_MANAGEMENT"));
            ;
            $data = [
                'res' => $res,
                'res2' => $res2,
            ];
            $this->view('faq/edit', $data);

            Style::footer();
        //}
    }


}
