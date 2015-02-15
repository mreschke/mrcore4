<?php
eval(Page::load_class('topic'));
eval(Page::load_class('badge'));

//Global Events
Log::log_page_hit($info);

//Unlock last edited post
if ($info->is_authenticated && !$skip_unlock && !$_GET['skip_unlock']) {
    Topic::unlock_last_edited_post($info);
}

//Recognize Events
$event_search = false;
if (isset($_POST['btn_search'])) {
    $event_search = true;
}

//Event Actions
if ($event_search) {
    $search_text = $_POST['txt_search'];
    if (trim($search_text) != '') {
        if (substr($search_text,0,2) == '> ') {
            //Goto or Create new Article
        } elseif (substr($search_text,0,3) == '>g ') {
            //Search google
            $search_text = trim(substr($search_text,3));
            Page::redirect(Page::get_url('net').'/http://www.google.com/search?q='.$search_text);
        } elseif (substr($search_text,0,4) == '>go ') {
            //Goto Website
            $search_text = trim(substr($search_text,4));
            if (strtolower(substr($search_text,0, 7)) != 'http://') {
                if (strtolower(substr($search_text,0,8)) != 'https://') {
                    $search_text = 'http://'.$search_text;
                }
            }
            Page::redirect(Page::get_url('net').'/'.$search_text);
        }
    }
    
}

//Adjust view settings by $_GET variable
/*
if (isset($_GET['viewmode']) || isset($_GET['viewmodeonce'])) {
    if ($_GET['viewmode'] == 'full') {
        $info->read_view = false;
    } elseif ($_GET['viewmode'] == 'simple') {
        $info->read_view = true;
    } elseif ($_GET['viewmodeonce'] == 'full') {
        $read_view_once = false;
    } elseif ($_GET['viewmodeonce'] == 'simple') {
        $read_view_once = true;
    }
    if (isset($_GET['viewmode'])) {
        //Update the Info Session
        Info::set_info($info); 
    }
}
*/


//Add the master.css to the beginning of the $pageStylesheet array
$view->css = array_pad($view->css, -(count($view->css) + 1), Page::get_url('datatables.css')); #1st
if (isset($view->css)) {
    $view->css = array_pad($view->css, -(count($view->css) + 1), Page::get_url('master_dialog.css')); #2nd
    $view->css = array_pad($view->css, -(count($view->css) + 1), Page::get_url('master.css')); #1st
} else {
    $view->css[] = Page::get_url('master.css');
    $view->css[] = Page::get_url('master_dialog.css');
}


if (isset($view->css_print)) {
    $view->css_print = array_pad($view->css_print, -(count($view->css_print) + 1), Page::get_url('master_print.css'));    
} else {
    $view->css_print[] = Page::get_url('master_print.css');
}

//Add simple style sheet override if in simple view mode
#if ($info->read_view || $read_view_once) {
#    $view->css[] = Page::get_url('master_simple.css');
#}

//Add these javascript files to the end of the JS array
$view->js[] = Page::get_url('master.js');

//Add these javascript files to the beginning of the JS array (order matters for jquery)
$view->js = array_pad($view->js, -(count($view->js) + 1), Page::get_url("jquery.min.js")); #1st


//Set Default Center Header & Page Title
/*
if (!isset($view->header) && isset($view->title)) {
    $view->header = $view->title;
} elseif (isset($view->header) && !isset($view->title)) {
    $view->title = $view->header;
} elseif (!isset($view->header) && !isset($view->title)) {
    $view->title = Page::get_url();
    $view->header = $view->title;
}
*/

#header("Cache-Control: max-age=3600, must-revalidate");


//Set Default Focus Form
#if (!$view->onload_disabled) {
#    if (!isset($view->onload)) {
#        $view->onload = "onload=\"document.getElementById('master_search_text').focus();return true;\"";
#    } else {
#        $view->onload = "onload=\"".$view->onload.";return true;\"";
#    }
#}
//Set Generic body onload
#$view->onload = "onload=\"body_onload();\"";

//Set left menu items (if enabled)
/*
if (!$view->menu_left_hidden) {
    if (isset($view->menu_left_parts)) {
        //Add the menu/badges to the beginning (or end) of the array (this always goes first)
        if ($view->menu_left_parts_end) {
            //Add default menus to END of current menus array
            $view->menu_left_parts[] = 'menu/badges';
            if ($info->is_authenticated) $view->menu_left_parts[] = 'menu/my';
        } else {
            //Add default menus to BEGINNING of current menus array
            if ($info->is_authenticated) $view->menu_left_parts = array_pad($view->menu_left_parts, -(count($view->menu_left_parts) + 1), 'menu/my');
            $view->menu_left_parts = array_pad($view->menu_left_parts, -(count($view->menu_left_parts) + 1), 'menu/badges');
        }

    } else {
        $view->menu_left_parts[] = 'menu/badges';
        if ($info->is_authenticated) {
            $view->menu_left_parts[] = 'menu/my';
        }
    }
}
*/

//Add in More Debug Info
if (Config::DEBUG) View::add_debug("View", Page::get_page(), \Helper\Other::print_obj($view));
if (Config::DEBUG) View::add_debug("Info", "core.class", \Helper\Other::print_obj($info));


//Email any Errors
Info::send_error_email($info, $view);


