<?php
require_once 'adodb.php';


/*
 class Tbl_user
 Database Layer for db.tbl_user and db.tbl_user_stat
 mReschke 2010-08-06
*/
class Tbl_user {
    public $user_id;
    public $email;
    public $first_name;
    public $last_name;
    public $title;
    public $alias;
    public $signature;
    public $description;
    #public $password;
    public $avatar;
    public $created_by;
    public $created_on;
    public $updated_on;
    public $last_login_on;
    public $disabled;
    public $perm_create;
    public $perm_admin;
    public $perm_exec;
    public $perm_html;
    public $global_topic_id;
    public $user_topic_id;
    public $topic_count;
    public $comment_count;

    
    /*
     function get_user($email_or_id string or integer) Tbl_user
     Gets a user from db.tbl_user into Tbl_user by email address or user_id
     Actually, $email_or_id can be email, ID or alias
     mReschke 2010-08-06
    */
    public static function get_user($email_or_id) {
        try {
            $db = ADODB::connect();
                $query = "
                    SELECT
                        u.*,
                        us.topic_count, us.comment_count
                    FROM
                        tbl_user u
                        LEFT OUTER JOIN tbl_user_stat us on u.user_id = us.user_id
                    WHERE ";
            if (is_numeric($email_or_id)) {
                $query .= "u.user_id='$email_or_id'";
            } elseif (stristr($email_or_id, "@")) {
                $query .= "u.email='$email_or_id'";
            } else {
                $query .= "u.alias='$email_or_id'";
            }
            $row = $db->GetRow($query);
            $user = new Tbl_user;
            if (isset($row['user_id'])) {
                $user->user_id = $row['user_id'];
                $user->email = $row['email'];
                $user->first_name = $row['first_name'];
                $user->last_name = $row['last_name'];
                $user->title = $row['title'];
                $user->alias = $row['alias'];
                $user->signature = $row['signature'];
                $user->description = $row['description'];
                #$user->password = $row['password'];
                $user->avatar = $row['avatar'];
                $user->created_by = $row['created_by'];
                $user->created_on = $row['created_on'];
                $user->updated_on = $row['updated_on'];
                $user->last_login_on = $row['last_login_on'];
                $user->disabled = $row['disabled'];
                $user->perm_create = $row['perm_create'];
                $user->perm_admin = $row['perm_admin'];
                $user->perm_exec = $row['perm_exec'];
                $user->perm_html = $row['perm_html'];
                $user->global_topic_id = $row['global_topic_id'];
                $user->user_topic_id = $row['user_topic_id'];
                $user->topic_count = $row['topic_count'];
                $user->comment_count = $row['comment_count'];
            }
            return $user;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }        
        
    }
    
    /*
     function get_user_password($user_id) string
     Gets a users password from db.tbl_user
     This is separate that get_user because I don't want the password field in the get_user return
     mReschke 2011-06-14
    */
    public static function get_user_password($user_id) {
        try {
            $db = ADODB::connect();
            $query = "SELECT password FROM tbl_user WHERE user_id = $user_id";
            $row = $db->GetRow($query);
            $password = '';
            if (isset($row['password'])) {
                $password = $row['password'];
            }
            return $password;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }        
        
    }    
    
    /*
     function get_users() array Tbl_user
     Gets all users
     mReschke 2010-09-22
    */
    public function get_users() {
        try {
            $db = ADODB::connect();
            $query = "
                SELECT
                    u.*,
                    us.topic_count, us.comment_count
                FROM
                    tbl_user u
                    LEFT OUTER JOIN tbl_user_stat us on u.user_id = us.user_id
                ORDER BY alias
            ";
            $rs = $db->Execute($query);
            $users = array();
            while (!$rs->EOF) {
                $user = new Tbl_user;
                $user->user_id = $rs->fields['user_id'];
                $user->email = $rs->fields['email'];
                $user->first_name = $rs->fields['first_name'];
                $user->last_name = $rs->fields['last_name'];
                $user->title = $rs->fields['title'];
                $user->alias = $rs->fields['alias'];
                $user->signature = $rs->fields['signature'];
                $user->description = $rs->fields['description'];
                #$user->password = $rs->fields['password'];
                $user->avatar = $rs->fields['avatar'];
                $user->created_by = $rs->fields['created_by'];
                $user->created_on = $rs->fields['created_on'];
                $user->updated_on = $rs->fields['updated_on'];
                $user->last_login_on = $rs->fields['last_login_on'];
                $user->disabled = $rs->fields['disabled'];
                $user->perm_create = $rs->fields['perm_create'];
                $user->perm_admin = $rs->fields['perm_admin'];
                $user->perm_exec = $rs->fields['perm_exec'];
                $user->perm_html = $rs->fields['perm_html'];
                $user->global_topic_id = $rs->fields['global_topic_id'];
                $user->user_topic_id = $rs->fields['user_topic_id'];
                $user->topic_count = $rs->fields['topic_count'];
                $user->comment_count = $rs->fields['comment_count'];
                $users[] = $user;
                $rs->MoveNext();
            }
            return $users;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }           
    }
    
    /*
     function get_users_in_group($perm_group_id) array($user_id => $alias)
     Gets users in this perm group
     Returns named array key=$user_id, value=$alias
     mReschke 2010-09-23
    */
    public static function get_users_in_group($perm_group_id) {
        try {
            $db = ADODB::connect();
            $query = "
                SELECT
                    u.user_id, u.alias
                FROM
                    tbl_user u
                    INNER JOIN tbl_perm_group_link pgl on u.user_id = pgl.user_id
                WHERE
                    pgl.perm_group_id = $perm_group_id
                ORDER BY alias
            ";
            $rs = $db->Execute($query);
            $users = array();
            while (!$rs->EOF) {
                $users[$rs->fields['user_id']] = $rs->fields['alias'];
                $rs->MoveNext();
            }
            return $users;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        } 
    }
        

    /*
     function get_users_array($only_disabled=true)
     Gets an array of all enabled users (unless $only_disabled=false, then all users)
     Named array, key is user alias, value is user_id
     mReschke 2010-09-15
    */
    public static function get_users_array($only_disabled=true) {
        try {
            $db = ADODB::connect();
            if ($only_disabled) {
                $query = "SELECT user_id,alias FROM tbl_user WHERE disabled=0";    
            } else {
                $query = "SELECT user_id,alias FROM tbl_user";
            }
            
            $rs = $db->Execute($query);
            $users = array();
            while (!$rs->EOF) {
                $users[$rs->fields['alias']] = $rs->fields['user_id'];
                $rs->MoveNext();
            }
            return $users;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }        
    }
    
    /*
     function update_user_avatar($user_id, $avatar) null
     Updates the existing users avatar (avatar is filename only)
     mReschke 2010-09-22
    */
    public static function update_user_avatar($user_id, $avatar) {
        try {
            $db = ADODB::connect();
            $query = "UPDATE tbl_user SET avatar='$avatar' WHERE user_id=$user_id";
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                
    }

    /*
     function insert_user(Tbl_user $tbl_user) int
     Inserts a new user into db.tbl_user
     Returns new user ID or -1 if user already exists
     mReschke 2010-08-06
    */
    public static function insert_user(Tbl_user $tbl_user) {
        try {
            $db = ADODB::connect();
            
            //Check if user does not exist by email AND alias
            $query = "SELECT count(user_id) as cnt FROM tbl_user WHERE email='".$tbl_user->email."' OR alias='".$tbl_user->alias."'";
            $row = $db->GetRow($query);
            if (isset($row['cnt'])) {
                $count = $row['cnt'];
            }

            if ($tbl_user->global_topic_id == '') $tbl_user->global_topic_id = 0;
            if ($tbl_user->user_topic_id == '') $tbl_user->user_topic_id = 0;
            
            if ($count == 0) {
                
                $query = "
                    INSERT INTO tbl_user (
                        email,first_name,last_name,title,alias,signature,description,password,
                        avatar,created_by,created_on,updated_on,last_login_on,
                        disabled,perm_create,perm_admin,perm_exec,perm_html,global_topic_id,user_topic_id
                    ) VALUES (
                        '".ADODB::dbclean(trim($tbl_user->email))."',
                        '".ADODB::dbclean(trim($tbl_user->first_name))."',
                        '".ADODB::dbclean(trim($tbl_user->last_name))."',
                        '".ADODB::dbclean(trim($tbl_user->title))."',
                        '".ADODB::dbclean(trim($tbl_user->alias))."',
                        '".ADODB::dbclean(trim($tbl_user->signature))."',
                        '".ADODB::dbclean(trim($tbl_user->description))."',
                        '".ADODB::dbclean(trim($tbl_user->password))."',
                        '".ADODB::dbclean(trim($tbl_user->avatar))."',
                        ".ADODB::dbclean($tbl_user->created_by).",
                        '".ADODB::dbclean(trim($tbl_user->created_on))."',
                        '".ADODB::dbclean(trim($tbl_user->updated_on))."',
                        '1900-01-01 12:00:00',
                        ".ADODB::dbclean($tbl_user->disabled).",
                        ".ADODB::dbclean($tbl_user->perm_create).",
                        ".ADODB::dbclean($tbl_user->perm_admin).",
                        ".ADODB::dbclean($tbl_user->perm_exec).",
                        ".ADODB::dbclean($tbl_user->perm_html).",
                        ".ADODB::dbclean($tbl_user->global_topic_id).",
                        ".ADODB::dbclean($tbl_user->user_topic_id)."
                    )
                ";
                echo $query;
                $rs = $db->Execute($query);
                $user_id = $db->Insert_ID();
                
                //Update user stats
                $query = "INSERT INTO tbl_user_stat (user_id, topic_count, comment_count) VALUES (
                    $user_id,
                    ".ADODB::dbclean($tbl_user->topic_count).",
                    ".ADODB::dbclean($tbl_user->comment_count)."
                    )";
                $rs = $db->Execute($query);

                return $user_id;
            } else {
                return -1;
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }        
    }

    /*
     function update_user(Tbl_user $tbl_user) user_id or -1 if exist
     Update one user
     Returns the same user_id, unless you tried to update to a email/alias that already exists, if so, returns -1
     mReschke 2010-09-22
    */
    public static function update_user(Tbl_user $tbl_user) {
        try {
            $db = ADODB::connect();
            
            //Check if user exists (when cant change email/alias to existing users)
            $query = "SELECT user_id FROM tbl_user WHERE user_id <> ".$tbl_user->user_id." AND (email='".$tbl_user->email."' OR alias='".$tbl_user->alias."')";
            $row = $db->GetRow($query);
            if (isset($row['user_id'])) {
                //Error: Tried to update to a email/alias that already exists
                return -1;
            }

            if ($tbl_user->global_topic_id == '') $tbl_user->global_topic_id = 0;
            if ($tbl_user->user_topic_id == '') $tbl_user->user_topic_id = 0;
            
            $query = "
                UPDATE tbl_user SET
                    email='".ADODB::dbclean(trim($tbl_user->email))."',
                    first_name='".ADODB::dbclean(trim($tbl_user->first_name))."',
                    last_name='".ADODB::dbclean(trim($tbl_user->last_name))."',
                    title='".ADODB::dbclean(trim($tbl_user->title))."',
                    alias='".ADODB::dbclean(trim($tbl_user->alias))."',
                    signature='".ADODB::dbclean(trim($tbl_user->signature))."',
                    description='".ADODB::dbclean(trim($tbl_user->description))."',
                    password='".ADODB::dbclean(trim($tbl_user->password))."',
                    avatar='".ADODB::dbclean(trim($tbl_user->avatar))."',
                    created_by=".ADODB::dbclean(trim($tbl_user->created_by)).",
                    created_on='".ADODB::dbclean(trim($tbl_user->created_on))."',
                    updated_on='".ADODB::dbnow()."',
                    disabled=".ADODB::dbclean($tbl_user->disabled).",
                    perm_create=".ADODB::dbclean($tbl_user->perm_create).",
                    perm_admin=".ADODB::dbclean($tbl_user->perm_admin).",
                    perm_exec=".ADODB::dbclean($tbl_user->perm_exec).",
                    perm_html=".ADODB::dbclean($tbl_user->perm_html).",
                    global_topic_id=".ADODB::dbclean($tbl_user->global_topic_id).",
                    user_topic_id=".ADODB::dbclean($tbl_user->user_topic_id)."
                WHERE
                    user_id = ".$tbl_user->user_id
            ;
            $rs = $db->Execute($query);    
            
            //Update user stats
            $query = "UPDATE tbl_user_stat SET
                topic_count=".ADODB::dbclean($tbl_user->topic_count).",
                comment_count=".ADODB::dbclean($tbl_user->comment_count)."
                WHERE user_id=".$tbl_user->user_id;
            $rs = $db->Execute($query);
            
            return $tbl_user->user_id;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }        
    }
    
    /*
     function update_last_login($user_id) now
     update users last login date
     mReschke 2010-08-06
    */
    public static function update_last_login($user_id) {
        $now = ADODB::dbnow();
        try {
            $query = "
                UPDATE tbl_user SET
                    last_login_on='$now'
                WHERE
                    user_id = ".$user_id
            ;
            $db = ADODB::connect();
            $rs = $db->Execute($query);
            return $now;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }        
    }
    
    /*
     function update_topic_count($user_id, $num) null
     Updates users total topic creation count ($num can be 1 or -1)
     mReschke 2010-09-01
    */
    public static function update_topic_count($user_id, $num) {
        try {
            $query = "INSERT INTO tbl_user_stat VALUES ($user_id,1,0) ON DUPLICATE KEY UPDATE topic_count=topic_count + $num";
            $db = ADODB::connect();
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                
    }
    
    /*
     function update_comment_count($user_id, $num) null
     Updates users total comment creation count ($num can be 1 or -1)
     mReschke 2010-09-01
    */
    public static function update_comment_count($user_id, $num) {
        try {
            $query = "INSERT INTO tbl_user_stat VALUES ($user_id,0,1) ON DUPLICATE KEY UPDATE comment_count=comment_count + $num";
            $db = ADODB::connect();
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                
    }    
}
