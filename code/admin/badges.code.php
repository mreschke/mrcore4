<?php
eval(Page::load_class('badge'));
eval(Page::load_class('helper/file'));
eval(Page::load_class('helper/image'));

//Security, admin only
if (!$info->admin) Page::redirect(Page::get_url('redirect').'/denied');

//Recognize Events
$event_save=$event_edit=$event_delete=$event_new=false;
if ($_POST['__EVENTTARGET'] == 'btn_save') {
    $event_save = true;
} elseif ($_POST['__EVENTTARGET'] == 'btn_delete') {
    $event_delete = true;
}

if (Page::get_variable(0) == 'edit') {
    $event_edit = true;
    $badge_id = Page::get_variable(1);
} elseif (Page::get_variable(0) == 'new') {
    $event_new = true;
}

 
$view->css[] = Page::get_url('admin/badges.css');
$view->title = 'Badge Management';
$view->header = "
    <table width='100%'><tr>
    <td>".$view->title."</td>
    <td align='right'>
    <a href='".Page::get_url('admin/badges').'/new'."' class='master_bar_link'>New Badge</a>
    </td>
    </tr></table>
";


$view->menu_left_parts_end = false;
$view->menu_left_parts[] = 'menu/admin';

//Get all Badges
$badge->tbl_badges_all = Tbl_badge::get_badges($info);


//Event Actions
if ($event_save) {
    if (trim($_POST['txt_badge']) != '') {
        # && trim($_POST['txt_topic_count']) != ''
        $default_topic_id = ($_POST['txt_default_topic_id']) ? $_POST['txt_default_topic_id'] : 0;
        
        if ($badge_id > 0) {
            //Update Badge
            $success = Tbl_badge::update_badge($badge_id, $_POST['txt_badge'], $_POST['txt_image'], $default_topic_id, $_POST['txt_topic_count']);
            
        } else {
            //Insert Badge
            #$group_id = Tbl_perm::insert_perm_group($_POST['txt_group'], $_POST['txt_group_description']);
            if ($_POST['txt_topic_count'] == '') $topic_count = 0;
            if (is_numeric($topic_count)) {
                $badge_id = Tbl_badge::insert_badge($_POST['txt_badge'], $_POST['txt_image'], $default_topic_id, $topic_count);
            } else {
                $badge_id = -1;
            }
        }
        if ($badge_id == -1 || $success == -1) {
            //Failed, already exists
            $view->error = 'Badge already exists by that name or topic_count invalid';
        } else {
            //Insert/Update Success
            if ($success) $badge_id = $success;
            $badge = $badge->tbl_badge->badge;
            
            //Upload Avatar
            if ($_FILES['uploadedfile']['name'] != '') {
                //Delete any previous avatars
                \Helper\File::unlink_wildcards(Page::get_abs_base().'/web/image/', 'badge'.$badge_id.'.*');
                
                $filename = basename( $_FILES['uploadedfile']['name']);
                $ext = strtolower(substr($filename, strripos($filename, ".")+1));
                $newfilename = 'badge'.$badge_id.'.'.$ext;
                $newfullfile = Page::get_abs_base().'/web/image/'.$newfilename;
                if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $newfullfile)) {
                    //Upload Success
                    
                    \Helper\Image::resize_image($newfullfile, 40,0,true);
                    
                    //Add new badge image to tbl_badge_item
                    Tbl_badge::update_badge_image($badge_id, $newfilename);
                }
            }            
            Page::redirect(Page::get_url('admin/badges'));
        }
        
    } else {
        $view->error = 'Please enter a badge name';
    }
} elseif ($event_delete) {
    if ($badge_id > 0) {
        Tbl_badge::delete_badge($badge_id);
        Page::redirect(Page::get_url('admin/badges'));
    }
}
