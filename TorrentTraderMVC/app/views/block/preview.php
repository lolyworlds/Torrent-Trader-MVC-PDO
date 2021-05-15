<?php
Style::header(Lang::T("_BLC_MAN_"));
        echo "<a name=\"" . $data['name'] . "\"></a>";
        Style::begin(Lang::T("_BLC_PREVIEW_"));
        echo "<br /><center><b>" . Lang::T("_BLC_USE_SITE_SET_") . "</b></center><hr />";
        echo "<table border=\"0\" width=\"180\" align=\"center\"><tr><td>";
        include APPROOT . "/views/blocks/" . $data['name'] . "_block.php";
        echo "</td></tr></table><hr />";
        echo "<center><a href=\"javascript: self.close();\">" . Lang::T("_CLS_WIN_") . "</a></center>";
        Style::end();
        Style::footer();