<?php
eval(Page::load_class('user'));

//Security, admin only
if (!$info->admin) Page::redirect(Page::get_url('redirect').'/denied');

$view->css[] = Page::get_url('admin/users.css');
$view->title = 'User Management';
$view->header = "
    <table width='100%'><tr>
    <td>".$view->title."</td>
    <td align='right'>
    <a href='".Page::get_url('profile').'/new'."' class='master_bar_link'>New User</a> | 
    ";
    if (Page::get_variable(0)=='compact') {
        $user->view_compact = true;
        $view->header .= "
            <a href='".Page::get_url('admin/users')."' class='master_bar_link'>Expanded View</a>
            <span class='master_bar_link_disabled'>Compact View</span>
        ";
    } else {
        $view->header .= "
            <span class='master_bar_link_disabled'>Expanded View</span>
            <a href='".Page::get_url('admin/users').'/compact'."' class='master_bar_link'>Compact View</a>
        ";
    }
    $view->header .= "
    </td>
    </tr></table>
";



$view->menu_left_parts_end = false;
$view->menu_left_parts[] = 'menu/admin';

//Get all users
$user->tbl_users = Tbl_user::get_users();



