<?php

require_once "a_start_function.php";

// Insertion of Smilies function
function insert_smilies_frame()
{
    global $config;
    // Summary Table of Emotions
    $smilies = array
        (
        ":)" => "smile.png",
        ":(" => "sad.png",
        ";)" => "wink.png",
        ":P" => "razz.png",
        ":D" => "grin.png",
        ":|" => "plain.png",
        ":O" => "suprise.png",
        ":?" => "confused.png",
        "8)" => "glasses.png",
        "8o" => "eek.png",
        "B)" => "cool.png",
        ":-)" => "smile-big.png",
        ":-(" => "crying.png",
        ":-*" => "kiss.png",
        "O:-D" => "angel.png",
        ":-@" => "devilish.png",
        ":o)" => "monkey.png",
        ":help" => "help.png",
        ":love" => "love.png",
        ":warn" => "warn.png",
        ":bomb" => "bomb.png",
        ":idea" => "idea.png",
        ":bad" => "bad.png",
        ":!" => "important.png",
        "brb" => "brb.png",
        ":gigg" => "giggle.png",
        ":rofl" => "roflmao.png",
        ":slep" => "sleep.png",
        ":thum" => "thumbsup.png",
        ":0_0" => "zpo.png",
        ":poop" => "poop.png",
        ":spechles" => "speechless.png",
        ":unsure" => "unsure.png",
        ":mad" => "mad.png",
        ":roll" => "rolleyes.png",
        ":sick" => "sick.png",
        ":crylol" => "crylaugh.png",
        ":confos" => "confound.png",
        ":fire" => "fire.png",
    );
    echo "<table><tr><td>Type...</td><td>To make a...</td></tr>";
    foreach ($smilies as $code => $url) {
        echo "<tr><td>$code</td><td><a href=\"javascript:window.opener.SmileIT('$code', '" . cleanstr($_GET["form"]) . "', '" . htmlspecialchars($_GET["text"]) . "')\"><img src=\"$config[SITEURL]/images/smilies/$url\" alt=\"$code\" title=\"$code\" border=\"0\"></a></td></tr>";
    }
    echo "</table>";
}

if (isset($_GET['action']) == "display") {
    insert_smilies_frame();
}