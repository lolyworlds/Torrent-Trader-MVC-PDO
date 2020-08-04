<?php
class Rating extends Controller
{
    public function __construct()
    {
        $this->groupsModel = $this->model('Groups');
    }

    public function index()
    {
        dbconn();
        global $site_config, $CURUSER, $pdo;
        loggedinonly();
		$id = (int) $_GET["id"];
        
        if (!is_valid_id($id)) {
            show_error_msg(T_("ERROR"), T_("THATS_NOT_A_VALID_ID"), 1);
        }

        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        
        
        //take rating
        if ($_GET["takerating"] == 'yes') {
            $rating = (int) $_POST['rating'];

            if ($rating <= 0 || $rating > 5) {
                show_error_msg(T_("RATING_ERROR"), T_("INVAILD_RATING"), 1);
            }

            $res = $pdo->run("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rating, '" . get_date_time() . "')");

            if (!$res) {
                if ($res->errorCode() == 1062) {
                    show_error_msg(T_("RATING_ERROR"), T_("YOU_ALREADY_RATED_TORRENT"), 1);
                } else {
                    show_error_msg(T_("RATING_ERROR"), T_("A_UNKNOWN_ERROR_CONTACT_STAFF"), 1);
                }

            }

            $pdo->run("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
            show_error_msg(T_("RATING_SUCCESS"), T_("RATING_THANK") . "<br /><br /><a href='$site_config[SITEURL]/torrents/read?id=$id'>" . T_("BACK_TO_TORRENT") . "</a>");
        }

        
        stdhead(T_("Torrents"));
        begin_frame(T_("Torrents"));
      // $srating IS RATING VARIABLE
      $srating = "";
      $srating .= "<table class='f-border' cellspacing=\"1\" cellpadding=\"4\" width='100%'><tr><td class='f-title' width='60'><b>" . T_("RATINGS") . ":</b></td><td class='f-title' valign='middle'>";
      if (!isset($row["rating"])) {
          $srating .= "Not Yet Rated";
      } else {
          $rpic = ratingpic($row["rating"]);
          if (!isset($rpic)) {
              $srating .= "invalid?";
          } else {
              $srating .= "$rpic (" . $row["rating"] . " " . T_("OUT_OF") . " 5) " . $row["numratings"] . " " . T_("USERS_HAVE_RATED");
          }

      }
      $srating .= "\n";
      if (!isset($CURUSER)) {
          $srating .= "(<a href='$site_config[SITEURL]/account/login'>Log in</a> to rate it)";
      } else {
          $ratings = array(
              5 => T_("COOL"),
              4 => T_("PRETTY_GOOD"),
              3 => T_("DECENT"),
              2 => T_("PRETTY_BAD"),
              1 => T_("SUCKS"),
          );
          //if (!$owned || $moderator) {
          $xres = DB::run("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
          $xrow = $xres->fetch(PDO::FETCH_ASSOC);
          if ($xrow) {
              $srating .= "<br /><i>(" . T_("YOU_RATED") . " \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")</i>";
          } else {
              $srating .= "<form style=\"display:inline;\" method=\"post\" action=\"torrents/read?id=$id&amp;takerating=yes\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
              $srating .= "<select name=\"rating\">\n";
              $srating .= "<option value=\"0\">(" . T_("ADD_RATING") . ")</option>\n";
              foreach ($ratings as $k => $v) {
                  $srating .= "<option value=\"$k\">$k - $v</option>\n";
              }
              $srating .= "</select>\n";
              $srating .= "<input type=\"submit\" value=\"" . T_("VOTE") . "\" />";
              $srating .= "</form>\n";
          }
          //}
      }
      $srating .= "</td></tr></table>";

      print("<center>" . $srating . "</center>"); // rating

      //END DEFINE RATING VARIABLE

      echo "<br />";




      echo "<br /><br />";
        end_frame();

        stdfoot();
    }
}