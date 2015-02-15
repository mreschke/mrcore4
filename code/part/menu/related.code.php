<?php
eval(Page::load_class('badge'));

//Get Related Topics
if (!isset($topic->tbl_topics_related)) {
    //Get all related topics, ordered by weight!
    $topic->tbl_topics_related = Tbl_topic::get_related_topics($info->tbl_user->user_id, $info->tbl_user->perm_admin, $topic->topic_id);
}
