<?php
eval(Page::load_model('log'));        //Log Model (page click logging, etc...)

/*
 class Log
 All log work functions (non model)
 mReschke 2010-09-10
*/
class Log {
    
    /*
     function log_page_hit(Info $info)
     mReschke 2010-09-10
    */
    public static function log_page_hit(Info $info) {
        
        //I only log page hits for none admin, I don't care what I do :)
        if (!$info->tbl_user->perm_admin) {
            $user_id = $info->tbl_user->user_id;
            $ip = $_SERVER['REMOTE_ADDR'];
            $url = Page::get_url();
            $agent = $_SERVER['HTTP_USER_AGENT'];
            $summary = 'Page Request';
            $detail = 'Page Request';
            
            Tbl_log::insert_log($user_id, $ip, $url, $agent, $summary, $detail);
        }
    }
    
    public static function write(Info $info, $summary, $detail) {
        //Only log stuff for none admin
        //actually it would be good to see on login success, if anyone logs in as me :) besides me
        #if (!$info->tbl_user->perm_admin) {
            Tbl_log::insert_log($info->tbl_user->user_id, $_SERVER['REMOTE_ADDR'], Page::get_url(), $_SERVER['HTTP_USER_AGENT'], $summary, $detail);
        #}
    }
    
}

