<?php
eval(Page::load_class('topic'));
$topic->tbl_post = Tbl_post::get_topic($info, Config::SEARCHBOX_TOPIC);
$wiki = Parser::parse_wiki($info, $topic, $topic->tbl_post->body, false);
