<?php
//This is the only file that is included with every page
//Responsible for setting up the defaults (core) of the application
require_once substr($_SERVER['DOCUMENT_ROOT'], 0, strripos(substr($_SERVER['DOCUMENT_ROOT'], 0, -1), '/')).'/config/config.php';
require_once 'page.class.php';        //Page functions, used in master and most pages
eval(Page::load_class('helper/other'));     //Simple helper functions, used everywhere
eval(Page::load_model('user'));       //Tbl_user model (don't load class here for some reason??)
eval(Page::load_class('view'));       //View class (view variables...)
eval(Page::load_class('perm'));       //Permissions class
eval(Page::load_class('log'));        //Logging Class

//Set $Info variable from session, used in all pages as $Info->xxx
$info = new Info;
Info::get_info($info);
GLOBAL $info;

//Enable PHP Error Handler
set_error_handler("Info::php_error_handler");

//Translate GET vars into $view->vars
if (isset($_GET['simple']) || strtolower($_GET['viewmode']) == 'simple') {
    $view->viewmode_simple = true;
} elseif (isset($_GET['raw']) || strtolower($_GET['viewmode']) == 'raw') {
    $view->viewmode_raw = true;
}

//Main Info Class
class Info {
    public $tbl_user;
    public $user_id; //common item from tbl_user
    public $admin;   //common item from tbl_user\
    #public $perm_read;
    #public $perm_edit;
    #public $perm_comment;
    public $is_authenticated;
    public $perm_groups;
    public $read_view;
    public $recent;
    public $os;
    public $uuids; //session of accessible public UUID links by this user
    
    function __construct() {
        $this->tbl_user = new Tbl_user;
        $this->perm_read = false;
        $this->perm_edit = false;
        $this->perm_comment = false;
        $this->is_authenticated = false;
        $this->read_view = false;
        $this->recent = array();
        $this->os = '';
    }

    /*
    function __construct() {
        #$this->user = new CoreUser();
        #$this->page = new CorePage();
        /*$this->user_id = 1;
        $this->email = 'anonymous@nreschke.com';
        $this->first_name = 'anonymous';
        $this->last_name = 'anonymous';
        $this->title = ''
        $this->is_authenticated = false;
        $this->perm_admin = false;
        $this->perm_create = false;
        
        $this->Tbl_user = new Tbl_user;
        $this->Tbl_user = tbl_user::get_user("anonymous@mreschke.com");
        $this->perm_read = false;
        $this->perm_edit = false;
        $this->perm_comment = false;
    }
    */
    
    function __set($var, $val) {
        $this->$var = $val;
    }
    
    function __get($var) {
        if(isset($this->$var)) {
            return $this->$var;
        } elseif(method_exists($this, $var)) {
            return $this->$var();
        } else {
            throw new Exception("Property '$var' does not exist");
        }
    }
        
    public static function get_info(Info &$info) {
        session_name("core");
        session_start();
        if(!isset($_SESSION['core'])) {
            //Session lost, or first time at page, create session
            $info = new Info;
            $info->tbl_user = Tbl_user::get_user(Config::STATIC_ANONYMOUS_USER);
            $info->user_id = $info->tbl_user->user_id;
            $info->admin = $info->tbl_user->perm_admin;
            $info->perm_read = false;
            $info->perm_edit = false;
            $info->perm_comment = false;
            $info->perm_groups = Tbl_perm::get_perm_groups_array($info->tbl_user->user_id);
            $info->os = \Helper\Other::get_os();
            $_SESSION['core'] = $info;
        } else {
            $info = $_SESSION['core'];    
        }
    }
    
    public static function set_info($core) {
        if($core == "") {
            unset($_SESSION['core']);
        } else {
            $_SESSION['core'] = $core;    
        }
    }
    
    public static function send_error_email($info, $view) {
        //Should be called from master.code.php
        //becuase it is the very last page to be compiled, so all errors would already be generated into $view->debug_error array
        if (isset($view->debug_error) && Config::EMAIL_ERROR != '') {
            $css = "<style>
                #title {
                    font-weight: bold;
                    font-size: 18px;
                    color: red;
                    margin-bottom: 5px;
                }
                .info {
                    font-size: 14px;
                    color: black;
                }
                .header {
                    color: red;
                    font-weight: bold;
                    font-size: 16px;
                }
                .loc {
                    font-size: 14px;
                    color: blue;
                }
                .data {
                    font-size: 14px;
                    color: black;
                    margin-top: 5px;
                    margin-bottom: 20px;
                    margin-left: 10px;
                }
            </style>";
            $body = "<div id='title'>Errors Found on ".date("m/d/Y H:i:s")." for user ".$info->tbl_user->first_name." ".$info->tbl_user->last_name."</div>
            <div class='info'>URL: ".Page::get_url()."</div>
            <div class='info'>DB_NAME: ".Config::DB_NAME."</div>
            <hr />";
            
            for ($i=0; $i < count($view->debug_error['header']); $i++) {
                $body .= "<div class='header'>".$view->debug_error['header'][$i]."</div>";
                $body .= "<div class='loc'>".$view->debug_error['location'][$i]."</div>";
                $body .= "<div class='data'>".$view->debug_error['data'][$i]."</div>";
            }
            
            $subject = 'Errors Found '.Config::APP_NAME.' v'.Config::APP_VERSION;
            $body = "<html><head>$css</head><body>$body</body></html>";
            $to = Config::EMAIL_ERROR;
            
            //Email Report
            exec("/nwq/admin/bin/email '$to' '$subject' '$body'", $blankreturn);
        }
    }    
    
    /*
     function error_handler($errno, $errstr, $errfile, $errline)
     Main PHP error handler function
     mReschke 2010-09-09
    */    
    function php_error_handler($errno, $errstr, $errfile, $errline ) {
        global $view;
        $error_level = Config::ERROR_REPORTING();
        if(in_array($errno, $error_level)) {
            $view->add_debug_error("Error", "$errfile line $errline", $errstr);
            #echo "$errstr<br />$errfile $errline<hr />";
        }
    }
    
    /*
     function exception_handler($e)
     Custom try..catch exception handler
     try {
         //Example Usage
     } catch (exception $e) {
         Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
     }                 
     mReschke 2011-04-19
    */
    function exception_handler($e, $file, $class, $function, $line) {
        global $view;
        $view->add_debug_error("$class.$function Error", "$file line $line", $e);
    }        
}



