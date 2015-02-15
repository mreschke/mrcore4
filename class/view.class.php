<?php
$view = new View;
GLOBAL $view;


/*
 class view
 All view work functions (non model)
 mReschke 2010-08-28
*/
class View {
    public $css;
    public $css_print;
    public $js;
    public $title;
    /*public $header;*/
    /*public $header_include_recent;*/
    public $onload;
    public $onload_disabled;
    /*public $menu_left_hidden;
    public $menu_right_hidden;
    public $menu_left_parts;
    public $menu_left_parts_end = false;
    public $menu_right_parts;*/
    public $meta_keywords;
    public $meta_description;
    public $error;
    public $error_close;
    public $error_close_hide;
    public $message;
    public $search_query;
    public $debug;
    public $debug_error;
    public $viewmode_simple;
    public $viewmode_raw;
    public $viewmode_app;
    public $menu;
    public $form_attributes;
    
    //Increment manually to force clear cache on all clients (this appended to img, css and js files, will not clear cache of any images defined in the css itself :()
    public $cache_version = 2;
    
    public static function add_debug($header, $location, $data) {
        GLOBAL $view;
        $view->debug['header'][] = $header;
        $view->debug['location'][] = $location;
        $view->debug['data'][] = $data;
    }
    
    public static function add_debug_error($header, $location, $data) {
        GLOBAL $view;
        $view->debug_error['header'][] = $header;
        $view->debug_error['location'][] = $location;
        $view->debug_error['data'][] = $data;        
    }

    /*
     function remove_js($js)
     Removes one javascript file from $view->js by regular expression
     mReschie 2013-09-23
    */
    public static function remove_js($js) {
        GLOBAL $view;
        for ($i=0; $i < count($view->js); $i++) {
            if (preg_match("'$js'", $view->js[$i])) {
                unset($view->js[$i]);
                $view->js = array_values($view->js);
                break;
            }
        }
    }

    /*
     function remove_css($css)
     Removes one css file from $view->css by regular expression
     mReschie 2013-09-23
    */
    public static function remove_css($css) {
        GLOBAL $view;
        for ($i=0; $i < count($view->css); $i++) {
            if (preg_match("'$css'", $view->css[$i])) {
                unset($view->css[$i]);
                $view->css = array_values($view->css);
                break;
            }
        }
    }
}
