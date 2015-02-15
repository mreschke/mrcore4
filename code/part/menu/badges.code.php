<?php
eval(Page::load_class('badge'));

$left_right = "left";

if (!isset($badge->tbl_badges_all)) {
    //Get all badges
    $badge->tbl_badges_all = Tbl_badge::get_badges($info);
}