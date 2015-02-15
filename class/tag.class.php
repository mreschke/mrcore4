<?php
eval(Page::load_model('tag'));
$tag = new Tag;

/*
 class Tag
 All badge work functions (non model)
 mReschke 2010-08-28
*/
class Tag {
    public $tbl_tag;
    public $tbl_tags_all;
    public $tbl_tags_all_selected;
    public $tbl_tags_intopic;
    public $tbl_tags_related;
        
    function __construct() {
        $this->tbl_tag = new Tbl_tag;

    }

    /*
     function clean_tag($tag)
     Remove invalid characters from tag name
     mReschke 2012-11-10
    */
    public static function clean_tag($tag) {
        $tag = strtolower(trim($tag));
        $tag = \Helper\File::clean_filename($tag);
        $tag = preg_replace('"-"', '', $tag);
        $tag = preg_replace('"_"', '', $tag);
        $tag = preg_replace('"\."', '', $tag);
        return $tag;
    }

}
