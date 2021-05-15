<?php
Style::begin(sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($data["username"])));
usermenu($data['id']);
if ($data['count']) {
    print($pagertop);
    torrenttable($data['res']);
    print($pagerbottom);
} else {
    print("<br><br><center><b>" . Lang::T("UPLOADED_TORRENTS_ERROR") . "</b></center><br />");
}
Style::end();