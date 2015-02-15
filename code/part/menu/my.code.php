<?php
eval(Page::load_class('topic'));

$topic->unread_count = Tbl_topic::get_unread_count($info);