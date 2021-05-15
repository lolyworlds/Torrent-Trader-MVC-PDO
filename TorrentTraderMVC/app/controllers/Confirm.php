<?php
class Confirm extends Controller
{

    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->pdo = new Database();
        $this->valid = new Validation();
    }

    // Confirm by email (siteconfig - first contact)
    public function index()
    {
        
        $id = (int) Input::get("id");
        $md5 = Input::get("secret");
        if (!$id || !$md5) {
            Redirect::autolink(URLROOT . "/home", Lang::T("INVALID_ID"));
        }
        $row = $this->pdo->run("SELECT `password`, `secret`, `status` FROM `users` WHERE `id` =?", [$id])->fetch();
        if (!$row) {
            $mgs = sprintf(Lang::T("CONFIRM_EXPIRE"), SIGNUPTIMEOUT / 86400);
            Redirect::autolink(URLROOT . "/home", $mgs);
        }
        if ($row->status != "pending") {
            Redirect::autolink(URLROOT . "/home", 'Pending');
            die;
        }
        if ($md5 != $row->secret) {
            Redirect::autolink(URLROOT . "/home", Lang::T("SIGNUP_ACTIVATE_LINK"));
        }
        $secret = mksecret();
        $upd = $this->pdo->run("UPDATE `users` SET `secret` =?, `status` =? WHERE `id` =? AND `secret` =? AND `status` =?", [$secret, 'confirmed', $id, $row->secret, 'pending']);
        if (!$upd) {
            Redirect::autolink(URLROOT . "/home", Lang::T("SIGNUP_UNABLE"));
        }
        Session::flash('info', Lang::T("ACCOUNT_ACTIVATED"), URLROOT."/login");
    }

    // user confirm email reset (reset own email)
    public function email()
    {
        
        $id = (int) Input::get("id");
        $md5 = Input::get("secret");
        $email = Input::get("email");
        if (!$id || !$md5 || !$email) {
            Redirect::autolink(URLROOT . "/home",  Lang::T("MISSING_FORM_DATA"));
        }
        $row = $this->pdo->run("SELECT `editsecret` FROM `users` WHERE `enabled` =? AND `status` =? AND `editsecret` !=?  AND `id` =?", ['yes', 'confirmed', '', $id])->fetch();
        if (!$row) {
            Redirect::autolink(URLROOT . "/home",  Lang::T("NOTHING_FOUND"));
        }
        $sec = $row->editsecret;
        if ($md5 != $sec) {
            Redirect::autolink(URLROOT . "/home",  Lang::T("NOTHING_FOUND"));
        }
        $this->pdo->run("UPDATE `users` SET `editsecret` =?, `email` =? WHERE `id` =? AND `editsecret` =?", ['', $email, $id, $row->editsecret]);
        Redirect::autolink(URLROOT . "/home", 'Success');
    }

}