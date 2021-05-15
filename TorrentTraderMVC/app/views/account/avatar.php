<?php
Style::begin("Upload");
print("<b>" . Lang::T("AVATAR_UPLOAD") . ":</b> &nbsp;
<form action='".URLROOT."/account/avatar?id=".$data['id']."' method='post' enctype='multipart/form-data'>
<input type='file' name='upfile'>
<button type='submit' class='btn btn-sm btn-primary' value='" . Lang::T("SUBMIT") . "' />Submit</button>
</form><br />");
Style::end();