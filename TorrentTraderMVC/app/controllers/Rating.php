<?php
class Rating extends Controller
{
    public function __construct()
    {
        Auth::user();
        $this->valid = new Validation();
    }

    public function index()
    { 
        $pdo = new Database();  
		$id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            show_error_msg(Lang::T("ERROR"), Lang::T("THATS_NOT_A_VALID_ID"), 1);
        }
        if ($_GET["takerating"] == 'yes') {
            $rating = (int) $_POST['rating'];
            if ($rating <= 0 || $rating > 5) {
                show_error_msg(Lang::T("RATING_ERROR"), Lang::T("INVAILD_RATING"), 1);
            }
            $res = $pdo->run("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $_SESSION["id"] . ", $rating, '" . TimeDate::get_date_time() . "')");
            if (!$res) {
                if ($res->errorCode() == 1062) {
                    show_error_msg(Lang::T("RATING_ERROR"), Lang::T("YOU_ALREADY_RATED_TORRENT"), 1);
                } else {
                    show_error_msg(Lang::T("RATING_ERROR"), Lang::T("A_UNKNOWN_ERROR_CONTACT_STAFF"), 1);
                }
            }
            $pdo->run("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
            show_error_msg(Lang::T("RATING_SUCCESS"), Lang::T("RATING_THANK") . "<br /><br /><a href='".URLROOT."/torrents/read?id=$id'>" . Lang::T("BACK_TO_TORRENT") . "</a>");
        }
    }
}