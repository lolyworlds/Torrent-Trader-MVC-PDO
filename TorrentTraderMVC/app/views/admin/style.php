<?php
Style::begin(Lang::T("THEME_MANAGEMENT"));
            echo "<center><a href=" . URLROOT . "admintheme?do=add'>" . Lang::T("THEME_ADD") . "</a><!-- - <b>" . Lang::T("THEME_CLICK_A_THEME_TO_EDIT") . "</b>--></center>";
            echo "<center>" . Lang::T("THEME_CURRENT") . ":<form id='deltheme' method='post' action=" . URLROOT . "admintheme?do=del'></center><table class='table table-striped table-bordered table-hover'>
        <thead>" .
            "<tr><th>ID</th><th>" . Lang::T("NAME") . "</th><th>" . Lang::T("THEME_FOLDER_NAME") . "</th><th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th></tr></thead<tbody>";
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                if (!is_dir("themes/$row[uri]")) {
                    $row['uri'] .= " <b>- " . Lang::T("THEME_DIR_DONT_EXIST") . "</b>";
                }
                echo "<tr><td class='table_col1' align='center'>$row[id]</td><td class='table_col2' align='center'>$row[name]</td><td class='table_col1' align='center'>$row[uri]</td><td class='table_col2' align='center'><input name='ids[]' type='checkbox' value='$row[id]' /></td></tr>";
            }
            echo "</tbody></table><center><input type='submit' value='" . Lang::T("SELECTED_DELETE") . "' /><center></form><br>";
            echo "<p>" . Lang::T("THEME_IN_THEMES_BUT_NOT_IN_DB") . "</p><form id='addtheme' action='admintheme?do=add2' method='post'><table class='table table-striped table-bordered table-hover'><thead>" .
            "<tr><th>" . Lang::T("NAME") . "</th><t>" . Lang::T("THEME_FOLDER_NAME") . "</th><th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th></tr></thead><tbody>";
            $dh = opendir("assets/css");
            $i = 0;
            while (($file = readdir($dh)) !== false) {
                if ($file == "." || $file == ".." || !is_dir("themes/$file")) {
                    continue;
                }
                if (is_file("themes/$file/header.php")) {
                    $res = DB::run("SELECT id FROM stylesheets WHERE uri = '$file' ");
                    if ($res->rowCount() == 0) {
                        echo "<tr><td class='table_col1' align='center'><input type='text' name='add[$i][name]' value='$file' /></td>
						<td class='table_col2' align='center'>$file<input type='hidden' name='add[$i][uri]' value='$file' /></td>
						<td class='table_col1' align='center'><input type='checkbox' name='add[$i][add]' value='1' /></td></tr>";
                        $i++;
                    }
                }
            }
            if (!$i) {
                echo "<tr><td class='table_col1' align='center' colspan='3'>" . Lang::T("THEME_NOTHING_TO_SHOW") . "</td></tr>";
            }
            echo "</tbody></table><p align='center'>" . ($i ? "<input type='submit' value='" . Lang::T("SELECTED_ADD") . "' />" : "") . "</p></form>";
            Style::end();