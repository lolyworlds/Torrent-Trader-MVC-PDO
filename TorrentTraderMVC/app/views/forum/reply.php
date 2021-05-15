<?php
Style::header(Lang::T("FORUMS_POST_REPLY"));
Style::begin(Lang::T("FORUMS_POST_REPLY"));
insert_compose_frame($data['topicid'], false);
Style::end();
Style::footer();