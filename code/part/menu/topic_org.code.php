<?php
eval(Page::load_class('badge'));

//Get Topic Badges
if (!isset($badge->tbl_badges_intopic)) {
    //Get all badges in this topic
    $badge->tbl_badges_intopic = Tbl_badge::get_badges($info, $topic->topic_id);
}
    
//Get Topic Tags
if (!isset($tag->tbl_tags_intopic)) {
    //Get all tags in this topic
    $tag->tbl_tags_intopic = Tbl_tag::get_tags($info, $topic->topic_id);
}

//Get Topic Perm Groups and Shorts
$perm->tbl_perm_groups_short_display = Tbl_perm::get_perm_groups_short_display($topic->topic_id);