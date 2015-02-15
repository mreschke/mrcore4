<?php
eval(Page::load_class('tag'));
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
    $tag_id = Page::get_variable(1);
} elseif (Page::get_variable(0) == 'new') {
    $event_new = true;
}

 
$view->css[] = Page::get_url('admin/tags.css');
$view->title = 'Tag Management';
$view->header = "
    <table width='100%'><tr>
    <td>".$view->title."</td>
    <td align='right'>
    <a href='".Page::get_url('admin/tags').'/new'."' class='master_bar_link'>New Tag</a>
    </td>
    </tr></table>
";


$view->menu_left_parts_end = false;
$view->menu_left_parts[] = 'menu/admin';

//Get all tags
$tag->tbl_tags_all = Tbl_tag::get_tags($info);


//Event Actions
if ($event_save) {
    if (trim($_POST['txt_tag']) != '') {
        $default_topic_id = ($_POST['txt_default_topic_id']) ? $_POST['txt_default_topic_id'] : 0;

        if ($tag_id > 0) {
            //Update tag
            $success = Tbl_tag::update_tag($tag_id, $_POST['txt_tag'], $_POST['txt_image'], $default_topic_id, $_POST['txt_topic_count']);
        } else {
            //Insert tag
            #$group_id = Tbl_perm::insert_perm_group($_POST['txt_group'], $_POST['txt_group_description']);
            $tag_id = Tbl_tag::insert_tag_item($_POST['txt_tag'], $_POST['txt_image'], $default_topic_id);
        }
        if ($tag_id == -1 || $success == -1) {
            //Failed, already exists
            #$view->error = 'tag already exists by that name or topic_count invalid';
        } else {
            //Insert/Update Success
            if ($success) $tag_id = $success;
            $tag = $tag->tbl_tag->tag;
            
            //Upload Avatar
            if ($_FILES['uploadedfile']['name'] != '') {
                //Delete any previous avatars
                \Helper\File::unlink_wildcards(Page::get_abs_base().'/web/image/', 'tag'.$tag_id.'.*');
                
                $filename = basename( $_FILES['uploadedfile']['name']);
                $ext = strtolower(substr($filename, strripos($filename, ".")+1));
                $newfilename = 'tag'.$tag_id.'.'.$ext;
                $newfullfile = Page::get_abs_base().'/web/image/'.$newfilename;
                if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $newfullfile)) {
                    //Upload Success
                    
                    \Helper\Image::resize_image($newfullfile, 40,0,true);
                    
                    //Add new tag image to tbl_tag_item
                    Tbl_tag::update_tag_image($tag_id, $newfilename);
                }
            }
            Page::redirect(Page::get_url('admin/tags'));
        }
        
    } else {
        $view->error = 'Please enter a tag name';
    }
} elseif ($event_delete) {
    if ($tag_id > 0) {
        Tbl_tag::delete_tag($tag_id);
        Page::redirect(Page::get_url('admin/tags'));
    }
}
