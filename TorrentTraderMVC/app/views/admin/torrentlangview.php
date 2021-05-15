 <?php Style::begin(Lang::T("TORRENT_LANGUAGES"));
        echo "<center><a href='" . URLROOT . "/admintorrentlang/torrentlangsadd'><b>Add New Language</b></a></center>";
        print("<i>Please note that language image is optional</i><br />");
        echo ("<center><table class='table table-striped table-bordered table-hover'><thead><tr>");
        echo ("<th width='10' class='table_head'><b>Sort</b></th><th class='table_head'><b>" . Lang::T("NAME") . "</b></th><th class='table_head'><b>Image</b></th><th width='30' class='table_head'></th></tr></thead><tbody>");
        while ($row = $data['sql']->fetch(PDO::FETCH_LAZY)) {
            $id = $row['id'];
            $name = $row['name'];
            $priority = $row['sort_index'];
            print("<tr><td class='table_col1' align='center'>$priority</td><td class='table_col2'>$name</td><td class='table_col1' width='50' align='center'>");
            if (isset($row["image"]) && $row["image"] != "") {
                print("<img border=\"0\" src=\"" . URLROOT . "/assets/images/languages/" . $row["image"] . "\" alt=\"" . $row["name"] . "\" />");
            } else {
                print("-");
            }
            print("</td><td class='table_col1'><a href=" . URLROOT . "/admintorrentlang/torrentlangsedit?id=$id'>[EDIT]</a> <a href=" . URLROOT . "/admintorrentlang/torrentlangsdelete?id=$id'>[DELETE]</a></td></tr>");
        }
        echo ("</tbody></table></center>");
        Style::end();