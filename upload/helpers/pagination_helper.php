<?php
function pager($rpp, $count, $href, $opts = array())
{
    $pages = ceil($count / $rpp);

    if (!$opts["lastpagedefault"]) {
        $pagedefault = 0;
    } else {
        $pagedefault = floor(($count - 1) / $rpp);
        if ($pagedefault < 0) {
            $pagedefault = 0;
        }

    }

    if (isset($_GET["page"])) {
        $page = (int) $_GET["page"];
        if ($page < 0) {
            $page = $pagedefault;
        }

    } else {
        $page = $pagedefault;
    }

    $pager = "";

    $mp = $pages - 1;
    $as = "<b>&lt;&lt;&nbsp;" . T_("PREVIOUS") . "</b>";
    if ($page >= 1) {
        $pager .= "<a href=\"{$href}page=" . ($page - 1) . "\">";
        $pager .= $as;
        $pager .= "</a>";
    } else {
        $pager .= $as;
    }

    $pager .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    $as = "<b>" . T_("NEXT") . "&nbsp;&gt;&gt;</b>";
    if ($page < $mp && $mp >= 0) {
        $pager .= "<a href=\"{$href}page=" . ($page + 1) . "\">";
        $pager .= $as;
        $pager .= "</a>";
    } else {
        $pager .= $as;
    }

    if ($count) {
        $pagerarr = array();
        $dotted = 0;
        $dotspace = 3;
        $dotend = $pages - $dotspace;
        $curdotend = $page - $dotspace;
        $curdotstart = $page + $dotspace;
        for ($i = 0; $i < $pages; $i++) {
            if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
                if (!$dotted) {
                    $pagerarr[] = "...";
                }

                $dotted = 1;
                continue;
            }
            $dotted = 0;
            $start = $i * $rpp + 1;
            $end = $start + $rpp - 1;
            if ($end > $count) {
                $end = $count;
            }

            $text = "$start&nbsp;-&nbsp;$end";
            if ($i != $page) {
                $pagerarr[] = "<a href=\"{$href}page=$i\"><b>$text</b></a>";
            } else {
                $pagerarr[] = "<b>$text</b>";
            }

        }
        $pagerstr = join(" | ", $pagerarr);
        $pagertop = "<p align=\"center\">$pager<br />$pagerstr</p>\n";
        $pagerbottom = "<p align=\"center\">$pagerstr<br />$pager</p>\n";
    } else {
        $pagertop = "<p align=\"center\">$pager</p>\n";
        $pagerbottom = $pagertop;
    }

    $start = $page * $rpp;

    return array($pagertop, $pagerbottom, "LIMIT $start,$rpp");
}

function pagination($query, $per_page = 10, $page = 1, $url = '?')
{

    global $pdo;
    $query = "SELECT COUNT(*) as `num` FROM {$query}";
    $row = $pdo->run($query)->fetch();
    $total = $row['num'];
    $adjacents = "2";

    $prevlabel = "Prev";
    $nextlabel = "Next";
    $lastlabel = "Last";

    $page = ($page == 0 ? 1 : $page);
    $start = ($page - 1) * $per_page;

    $prev = $page - 1;
    $next = $page + 1;

    $lastpage = ceil($total / $per_page);

    $lpm1 = $lastpage - 1; // //last page minus 1

    $pagination = "";
    if ($lastpage > 1) {
        $pagination .= "<nav aria-label='Page navigation example'><ul class='pagination'>";
        //     $pagination .= "<li class='page-item'><a class='page-link' href='#'>{$page}</a></li>";

        if ($page > 1) {
            $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page={$prev}'>{$prevlabel}</a></li>";
        }

        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page) {
                    $pagination .= "<li class='page-item'><a class='page-link' >{$counter}</a></li>";
                } else {
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";
                }

            }

        } elseif ($lastpage > 5 + ($adjacents * 2)) {

            if ($page < 1 + ($adjacents * 2)) {

                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<li class='page-item'><a class='page-link'>{$counter}</a></li>";
                    } else {
                        $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";
                    }

                }
                $pagination .= "<li class='page-link'>...</li>";
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page={$lastpage}'>{$lastpage}</a></li>";

            } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page=1'>1</a></li>";
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page=2'>2</a></li>";
                $pagination .= "<li class='page-link'>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<li class='page-item'><a class='page-link>{$counter}</a></li>";
                    } else {
                        $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";
                    }

                }
                $pagination .= "<li class='page-link'>..</li>";
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page={$lastpage}'>{$lastpage}</a></li>";

            } else {

                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page=1'>1</a></li>";
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page=2'>2</a></li>";
                $pagination .= "<li class='page-link'>..</li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<li class='page-item'><a class='page-link'>{$counter}</a></li>";
                    } else {
                        $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";
                    }

                }
            }
        }

        if ($page < $counter - 1) {
            $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page={$next}'>{$nextlabel}</a></li>";
            $pagination .= "<li class='page-item'><a class='page-link' href='{$url}page=$lastpage'>{$lastlabel}</a></li>";
        }

        $pagination .= "</ul></nav>";
    }

    return $pagination;
}
