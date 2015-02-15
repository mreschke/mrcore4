<?php
require_once Page::get_abs_base().'/model/adodb5/adodb.inc.php';
#require_once Page::get_abs_base().'/model/adodb5/adodb-exceptions.inc.php';

/*
 class ADODB
 Database connection string and simple database helpers
 mReschke 2010-08-05
*/
class ADODB {
    
    /*
     function connect() ADODB connection?
     Connects to the database
     mReschke 2010-08-05
    */
    public static function connect() {
        $db = NewADOConnection('mysql');
        $db->Connect(Config::DB_SERVER, Config::DB_USER, Config::DB_PASS, Config::DB_NAME);
        return $db;
    }
    
    /*
     function dbnow() string
     Returns the date and time in the correct format for this database
     mReschke 2010-08-06
    */
    public static function dbnow() {
        $now = date("Y-m-d H:i:s");
        return $now;
    }
    
    /*
     function dbclean($value) object
     Cleans and escapes string for database query
     mReschke 2011-01-02
    */
    public static function dbclean($value) {
        return mysql_real_escape_string($value);
    }

    /*
     function boolint($value)
     Convert boolean to integer
     mReschke 2012-10-28
    */
    public static function boolint($value) {
        if ($value) {
            return 1;
        } else {
            return 0;
        }
    }

}
