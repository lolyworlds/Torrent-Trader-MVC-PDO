<?php
Style::begin(Lang::T("New topic"));
forumheader('Compose New Thread');
require_once APPROOT."/helpers/bbcode_helper.php";
insert_compose_frame($data['id']);
Style::end();