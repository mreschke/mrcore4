<?php
eval(Page::load_class('perm'));

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
    $group_id = Page::get_variable(1);
} elseif (Page::get_variable(0) == 'new') {
    $event_new = true;
} elseif (Page::get_variable(0) == 'detail') {
    $view_detail = true;
}

$view->css[] = Page::get_url('admin/groups.css');
$view->title = 'Group Management';
$view->header = "
    <table width='100%'><tr>
    <td>".$view->title."</td>
    <td align='right'>
    <a href='".Page::get_url('admin/groups').'/new'."' class='master_bar_link'>New Group</a> |";
    if ($view_detail) {
        $view->header .= "
        <a href='".Page::get_url('admin/groups')."' class='master_bar_link'>Summary View</a>
        <span class='master_bar_link_disabled'>Detailed View</span>";
    } else {
        $view->header .= "
        <span class='master_bar_link_disabled'>Summary View</span>
        <a href='".Page::get_url('admin/groups').'/detail'."' class='master_bar_link'>Detailed View</a>";
    }
    $view->header .= "</td>
    </tr></table>";



$view->menu_left_parts_end = false;
$view->menu_left_parts[] = 'menu/admin';

//Get all Groups
$perm->tbl_perm_groups = Tbl_perm::get_groups();


//Event Actions
if ($event_save) {
    if (trim($_POST['txt_group']) != '' && trim($_POST['txt_group_description']) != '') {    
        if ($group_id > 0) {
            //Update Group
            $success = Tbl_perm::update_perm_group($group_id, $_POST['txt_group'], $_POST['txt_group_description']);
        } else {
            //Insert Group
            $group_id = Tbl_perm::insert_perm_group($_POST['txt_group'], $_POST['txt_group_description']);
        }
        if ($group_id == -1 || $success == -1) {
            //Failed, already exists
            $view->error = 'Group already exists by that name';
        } else {
            //Insert/Update Success
            Page::redirect(Page::get_url('admin/groups'));    
        }
        
    } else {
        $view->error = 'Must enter a group name and group description';
    }
} elseif ($event_delete) {
    if ($group_id > 0) {
        Tbl_perm::delete_perm_group($group_id);
        Page::redirect(Page::get_url('admin/groups'));
    }
}




