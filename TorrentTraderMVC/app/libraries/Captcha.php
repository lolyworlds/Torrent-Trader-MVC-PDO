<?php

class Captcha
{
    private $key;
    private $secret;

    public function __construct()
    {
        $this->key = CAPTCHA_KEY;
        $this->secret = CAPTCHA_SECRET;
    }

    public function response($captcha)
    {
        if (CAPTCHA_ON) {
            if (!$captcha) {
                Session::flash('info', Lang::T('<b>Please check the the captcha form.</b>'), URLROOT . "/login");
            }
            $secret = CAPTCHA_SECRET;
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $this->secret . '&response=' . $captcha);
            $responseData = json_decode($verifyResponse);
            if ($responseData->success) {
                // its successfull so let it go
            }
        }
    }

    public function html()
    {
        if (CAPTCHA_ON) {
            return print("<div class='g-recaptcha' data-theme='light' data-sitekey='" . $this->key . "'></div>");
        }
    }

}