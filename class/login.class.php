<?php

/*
 class Login
 All login/security work functions (non model)
 mReschke 2010-08-06
*/
class Login {
    
    public static function validate($username, $password) {
        if (trim($username) <> '' and trim($password) <> '') {
            $user = new Tbl_user;
            $user = Tbl_user::get_user($username);
            $database_password = Tbl_user::get_user_password($user->user_id);
            if (((stristr($username, "@") && $user->email == $username) || $user->alias == $username) && ($database_password == $password) && $user->disabled == false) {
                //Authentication Success!
                $info = new Info;
                $info->tbl_user = $user;
                $info->user_id = $user->user_id;
                $info->admin = $user->perm_admin;
                $info->is_authenticated = true;
                $info->os = \Helper\Other::get_os();
                
                //Update Last Login Date
                $info->tbl_user->last_login_on = Tbl_user::update_last_login($info->tbl_user->user_id);
                
                //Update users permission groups
                $info->perm_groups = Tbl_perm::get_perm_groups_array($info->tbl_user->user_id);
                
                //Update the Info Session
                Info::set_info($info);
                
                //Write Log
                Log::write($info, "Login", "Login Success");

                return $info;
            } else {
                //Validation Failed
                return null;
            }
        } else {
            return null;
        }
    }
    
}