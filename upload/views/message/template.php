<?php
usermenu($_SESSION["id"]);
include 'views/message/messagenavbar.php';
?><br>
            <div><center>
            <form name="form" action="create" method="post">

                <label for="subject">Subject:</label>&nbsp;
                <input type="text" name="subject" size="60" placeholder="Subject" id="subject">
                <?php require_once "helpers/bbcode_helper.php";
print textbbcode("form", "body", "$body");?><br>
        <input type="submit" name="Create" value="template">
            </form>
            </center></div>