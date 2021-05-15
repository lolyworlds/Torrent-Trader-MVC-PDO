<?php
class Logout extends Controller
{

    public function index()
    { 
        Cookie::destroyAll();
        Redirect::to(URLROOT."/login");
    }
}