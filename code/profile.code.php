<?php
eval(Page::load_class('user'));
eval(Page::load_class('helper/file'));
eval(Page::load_class('helper/image'));

//Permissions
#if (!$info->is_authenticated) {
#    Page::redirect(Page::get_url('redirect').'/denied/'.$topic->topic_id);        
#}

//Recognize Events
$event_save=$event_new=false;
if ($_POST['__EVENTTARGET'] == 'btn_save') {
#if (isset($_POST['btn_save'])) {
    $event_save = true;
}
if (Page::get_variable(0) == 'new') {
    $event_new = true;
    $user->new_user = true;
}

if (!$event_new) {
    $user->tbl_user = Tbl_user::get_user(Page::get_variable(0));
    $user->user_id = $user->tbl_user->user_id;
    #var_dump($user->tbl_user);
    
    if ($user->tbl_user->user_id <= 0) {
        Page::redirect(Page::get_url('redirect').'/error');
    }
    
    /*$view->title = $user->tbl_user->alias.' User Profile';
    $view->header = "
            <table width='100%'><tr>
            <td>".$view->title."</td>
            <td align='right'>";
    */      
    //Set Edit Permission and Header
    if (Page::get_variable(1) == 'edit') {
        if (($info->user_id == $user->user_id && $user->user_id != Config::STATIC_ANONYMOUS_USER) || $info->admin) {
            if ($info->admin || $user->user_id = $info->user_id) $user->edit = true;
            if ($info->admin) $user->edit_admin = true;
            
            //Get array of enabled users for created/updated by select boxes
            $user->users = Tbl_user::get_users_array(false);
        } else {
            //Permission denied to edit
            Page::redirect(Page::get_url('redirect').'/denied');
        }
        
    } else {
        if (($info->user_id == $user->user_id && $user->user_id != Config::STATIC_ANONYMOUS_USER) || $info->admin) {
            /*$view->header .= "<a href='".Page::get_url('profile')."/".$user->user_id."/edit' class='m_bar_link'>Edit</a>";
            if ($info->admin) $view->header .= " | ";*/
        }
    }
    if ($info->admin) {
        /*$view->header .= "<a href='".Page::get_url('admin/users')."' class='m_bar_link'>Manage Users</a>";*/
    }
    /*$view->header .= "</td></tr></table>";*/


} else {
    //Creating New
    if ($info->admin) {
        /*$view->title = 'New User Profile';*/
        
        $user->edit = true;
        $user->edit_admin = true;
        
        //Fill new tbl_user with defaults
        $user->tbl_user = new Tbl_user;
        $user->tbl_user->avatar = Tbl_user::get_user(Config::STATIC_ANONYMOUS_USER)->avatar;
        $user->tbl_user->created_by = $info->user_id;
        $user->tbl_user->created_on = ADODB::dbnow();
        $user->tbl_user->updated_on = ADODB::dbnow();
        $user->tbl_user->topic_count = 0;
        $user->tbl_user->comment_count = 0;
        
        //Get array of enabled users for created/updated by select boxes
        $user->users = Tbl_user::get_users_array(false);
        
    } else {
        Page::redirect(Page::get_url('redirect').'/denied');
    }
    
    
}



$view->css[] = Page::get_url('profile.css');
$view->js[] = Page::get_url('profile.js');

//Set left menu items
if ($info->admin) {
    #$view->menu_left_parts_end = false;
    #$view->menu_left_parts[] = 'menu/admin';
}
#$view->menu_left_parts[] = 'menu/sites';


//Set right menu items for this topic page
#$view->menu_right_parts[] = 'menu/topic_about';
#$view->menu_right_parts[] = 'menu/topic_org';
#$view->menu_right_parts[] = 'menu/related';
#$view->menu_right_parts[] = 'menu/files';


//Get Perm Groups with Links
$user->perm_groups = Tbl_perm::get_groups($user->user_id);


//Event Actions
if ($event_save) {
    
    //Javascript has validated all input, go ahead and save
    $user->tbl_user->first_name = split(" ", $_POST['txt_name']);
    $user->tbl_user->last_name = $user->tbl_user->first_name[1];
    $user->tbl_user->first_name = $user->tbl_user->first_name[0];
    if (isset($_POST['lst_created_by'])) $user->tbl_user->created_by = $_POST['lst_created_by']; //optional admin override
    $user->tbl_user->title = $_POST['txt_title'];
    if (isset($_POST['txt_created_on'])) $user->tbl_user->created_on = $_POST['txt_created_on']; //optional admin override
    $user->tbl_user->email = $_POST['txt_email'];
    $user->tbl_user->alias = $_POST['txt_alias'];
    if (isset($_POST['chk_disabled'])) $user->tbl_user->disabled = 1; else $user->tbl_user->disabled = 0;
    $user->tbl_user->description = $_POST['txt_description'];
    $user->tbl_user->global_topic_id = $_POST['txt_global_topic_id'];
    $user->tbl_user->user_topic_id = $_POST['txt_user_topic_id'];
    if (trim($_POST['txt_password']) != '') {
        $user->tbl_user->password = $_POST['txt_password'];
    } else $user->tbl_user->password = Tbl_user::get_user_password($user->tbl_user->user_id);
    if (isset($_POST['txt_topic_count'])) $user->tbl_user->topic_count = $_POST['txt_topic_count'];
    if (isset($_POST['txt_comment_count'])) $user->tbl_user->comment_count = $_POST['txt_comment_count'];
    if (isset($_POST['chk_admin'])) $user->tbl_user->perm_admin = 1; else $user->tbl_user->perm_admin = 0;
    if (isset($_POST['chk_create'])) $user->tbl_user->perm_create = 1; else $user->tbl_user->perm_create = 0;
    if (isset($_POST['chk_exec'])) $user->tbl_user->perm_exec = 1; else $user->tbl_user->perm_exec = 0;
    if (isset($_POST['chk_html'])) $user->tbl_user->perm_html = 1; else $user->tbl_user->perm_html = 0;
    
    if ($event_new) {
        //Insert new User
        $user_id = Tbl_user::insert_user($user->tbl_user);
    } else {
        //Update tbl_user
        $user_id = Tbl_user::update_user($user->tbl_user);
    }
    if ($user_id == -1) {
        $view->error = "User already exists by that alias or email, please try again";
        #$user->user_id = 0;
    } else {
        $user->user_id = $user_id;
    }
    
    //Update users perm_group linkage
    //Only admins can edit users perms
    if ($user->user_id > 0 && $user_id != -1) {
        if ($info->admin) {
            Tbl_perm::delete_perm_group_links($user->user_id);
    
            //Add all checked checkbox permission groups to database
            //Checkbox values are the perm_group_id;
            while (list($key, $value) = each($_POST)) {
                if (substr($key, 0, 10) == 'chk_group_') {
                    Tbl_perm::insert_perm_group_link($user->user_id, $value);
                }
            }
        }
        
        //Upload Avatar
        if ($_FILES['uploadedfile']['name'] != '') {
            //Delete any previous avatars
            \Helper\File::unlink_wildcards(Page::get_abs_base().'/web/image/', 'avatar_user'.$user->user_id.'.*');
            
            $filename = basename( $_FILES['uploadedfile']['name']);
            $ext = strtolower(substr($filename, strripos($filename, ".")+1));
            $newfilename = 'avatar_user'.$user->user_id.'.'.$ext;
            $newfullfile = Page::get_abs_base().'/web/image/'.$newfilename;
            if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $newfullfile)) {
                //Upload Success
                
                //Resize Image (see http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php)
                #$image = new ResizeImage();
                #$image->load($newfullfile);
                #$image->resizeToWidth(80);
                #$image->resize(80,80);
                #$image->save($newfullfile);
                #smart_resize_image($newfullfile, 80, 0, true, $newfillfule, true);
                
                \Helper\Image::resize_image($newfullfile, 80,0,true);
                
                //Add avatar to tbl_user
                Tbl_user::update_user_avatar($user->user_id, $newfilename);
            }
        }
        
        Page::redirect(Page::get_url('profile').'/'.$user->user_id);
    }

}






