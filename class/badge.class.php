<?php
eval(Page::load_model('badge'));
$badge = new Badge;

/*
 class Badge
 All badge work functions (non model)
 mReschke 2010-08-28
*/
class Badge {
    public $tbl_badge;
    public $tbl_badges_all;
    public $tbl_badges_all_selected;
    public $tbl_badges_intopic;
    public $tbl_badges_related;
    
    
    function __construct() {
        $this->tbl_badge = new Tbl_badge;
    }

    /*
     function clean_badge($tag)
     Remove invalid characters from badge name
     mReschke 2012-11-10
    */
    public static function clean_badge($badge) {
        $badge = strtoupper(trim($badge));
        $badge = \Helper\File::clean_filename($badge);
        $badge = preg_replace('"-"', '', $badge);
        $badge = preg_replace('"_"', '', $badge);
        $badge = preg_replace('"\."', '', $badge);
        return $badge;
    }

    
}