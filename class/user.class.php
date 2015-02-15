<?php
#eval(Page::load_model('user'));

$user = new User;


/*
 class User
 All user work functions (non model)
 mReschke 2010-09-22
*/
class User {
    public $tbl_user;
    public $tbl_users;
    public $user_id;
    public $edit = false;
    public $edit_admin = false;
    public $users;
    public $perm_groups;
    public $perm_group;
    public $view_compact = false;
    public $new_user = false;
    
    function __construct() {
        $this->tbl_user = new Tbl_user;
        $this->perm_group = new Tbl_perm;
    }
    
    /*
     function insert_recent($topic_id, $topic_name) null
     Inserts one topic into the $info->recent array
     $info->recent is a named array: array["topic_id"]["topic_title"];
     mReschke 2010-11-09
    */
    public static function insert_recent(Info $info, $topic_id, $topic_name) {
        //Remove Topic if already exists, and add it to end of array below
        if (array_key_exists("$topic_id", $info->recent)) {
            unset($info->recent["$topic_id"]);
        }

        //Shift One Element from array (remove first element)
        if (count($info->recent) >= Config::RECENT_MAX_TOPICS + 1) {
            #array_shift($info->recent); (converts the named key into int key, so not what I want)
            foreach ($info->recent as $id => $title) {
                unset($info->recent["$id"]);
                break;
            }
        }
        
        //Add topic to end of array
        $info->recent["$topic_id"] = $topic_name;
        
        //Update the Info Session
        Info::set_info($info);
        
        #var_dump($info->recent);
    }
}