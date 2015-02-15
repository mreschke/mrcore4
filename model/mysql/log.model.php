<?php
require_once 'adodb.php';


/*
 class Tbl_log
 Database Layer for db.tbl_log
 mReschke 2010-09-10
*/
class Tbl_log {
    public $log_id;
    public $date;
    public $user_id;
    public $ip;
    public $url;
    public $agent;
    public $summary;
    public $detail;
    
    public static function insert_log($user_id, $ip, $url, $agent, $summary, $detail) {
        try {
            $db = ADODB::connect();
            $query = "
                INSERT INTO tbl_log
                    (date, user_id, ip, url, agent, summary, detail)
                VALUES (
                    '".ADODB::dbnow()."',
                    $user_id,
                    '$ip',
                    '$url',
                    '$agent',
                    '$summary',
                    '$detail'
                )
            ";
            $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }        
    }
    
}