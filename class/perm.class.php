<?php
eval(Page::load_model('perm'));
$perm = new Perm;

/*
 class Per,
 All perm work functions (non model)
 mReschke 2010-08-29
*/
class Perm {
    public $tbl_perm;
    public $tbl_perm_selected;
    public $tbl_perms_all_selected;
    public $tbl_perm_group;
    public $tbl_perm_groups;
    public $tbl_perm_group_users;
    public $tbl_perm_groups_short_display;
    
    public function __construct() {
        $this->tbl_perm = new Tbl_perm;
        $this->tbl_perm_selected = new Tbl_perm_custom_all_selected;
        $this->tbl_perm_groups = new Tbl_perm;
    }
    


}