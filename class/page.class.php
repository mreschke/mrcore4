<?php

/*
 class Page
 All page work functions (non model)
 mReschke 2010-08-04
*/
class Page {
    
    /*
     function redirect($page, $vars='') null
     Redirects to the given page with optional page $_GET variables
     mReschke 2010-08-04
    */
    public static function redirect($page, $vars='') {
        #$page = strtolower($page);
        
        if (stristr($page, '/redirect/')) {
            $page .= '/?ref='.Page::get_url();
            
        }
        if(strchr($page, '.php')) {
            $page = substr($page, 0, -4);
        }
        if(Config::USE_PAGE_EXTENSIONS) {
            #echo "use: $page";
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: $page.php$vars");
        } else {
            #echo "no use: $page";
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: $page$vars");
        }
        exit();
    }

    /*
     function redirect_404()
     mReschke 2013-09-30
    */
    public static function redirect_404() {
        //Path not found, return 404
        header("HTTP/1.0 404 Not Found");
        ?>
        <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
        <html><head>
        <title>404 Not Found</title>
        <link rel='shortcut icon' href='/favicon.ico' />
        </head><body>
        <h1>Not Found</h1>
        <p>The requested URL <? echo $_SERVER['REQUEST_URI'] ?> was not found on this server.</p>
        </body></html>
        <?
        #echo 'Directory or File Not Found: '.$files->fullfile;
        exit();
    }

    /*
     function get_page() string
     Gets the current page of the current URL
     Example: /article.php or /admin/users.php (or /article, /admin/users if no extensions)
     mReschke 2010-08-04
    */
    public static function get_page() {
        $uri = (substr(@$_SERVER['REQUEST_URI'], 1));
        $found_subfolder = Page::is_subfolder($uri);
            
        if ($found_subfolder) {
            //URI has a folder in it, parse accordingly
            //Example: http://host/admin/edit/user/1 (admin is actually a folder, while edit is a .php file in web/admin/)
            
            #$uri = substr($uri, strlen($found_subfolder)+1);
            $params = explode('/', $uri);
            #$page = '/'.$params[0].'/'.$params[1];
            $page = '';
            for ($i=0; $i < count(explode('/', $found_subfolder)); $i++) {
                $page .= '/'.$params[$i];
            }
            
        } else {
            //URI has no folder in it, parse accordingly
            //Example: http://host/article/1 (there is no folder, article is a .php file)
            
            $params = explode('/', $uri);
            $page = '/'.$params[0];
            
        }
        
        //Remove ?
        if (stristr($page, "?")) {
            $tmp = explode("?", $page);
            $page = $tmp[0];
        }
        return $page;
    }
    
    /*
     function is_subfolder($uri) string of subfolder found name
     Checks if the URI contains a subfolder
     mReschke 2010-09-23
    */
    private static function is_subfolder($uri) {
        //Must remove any ? from URI before checking subfolders, cause if we have ..redirect/denied/?ref=http://domain.com/admin/users
        //Then the 'admin' subfolder would be found, which is not what we want, so remove any ?... first
        if (stristr($uri, "?")) {
            $uri = explode("?", $uri);
            $uri = $uri[0];
        }
        $subfolders = Config::WEB_SUBFOLDERS();

        $found_subfolder = '';
        //Dont look for subfolders if on search page (but NOT rest API search page), they could search for 'admin' and would break
        $look_for_subfolders = true;
        if (stristr($uri, 'rest/')) {
            $look_for_subfolders = true;
        } elseif (stristr($uri, 'search/') || stristr($uri, 'net/') || stristr($uri, 'profile/')) {
            $look_for_subfolders = false;
        }
        
        if ($look_for_subfolders) {
            foreach ($subfolders as $subfolder) {
                #if (stristr($uri, $subfolder)) {
                if (substr($uri, 0, strlen($subfolder)) == $subfolder) {
                    $found_subfolder = $subfolder;
                    break;
                }
            }
        }
        return $found_subfolder;
    }
    
    /*
     function get_variables($as_string=false, $uri=null) array
     Gets only the page $_GET variables in the current URL
     IF $as_string=true then returns a string of variables with the / (not / at end unless in url)
     if $uri is set then use that uri string instead of REQUEST_URI
     Does not get any /'s
     Example Return: $params[0]=topic, $params[1]=1
     mReschke 2010-08-04
    */
    public static function get_variables($as_string=false, $uri=null) {
        if (!isset($uri)) $uri = (substr(@$_SERVER['REQUEST_URI'], 1));

        $found_subfolder = Page::is_subfolder($uri);

        //Remove any $_GET from uri first
        if (!stristr($uri, 'net/')) {
            if (stristr($uri, "?")) {
                $uri = explode("?", $uri);
                $uri = $uri[0];
            }
        }

        #$found_subfolder = 'topi';

        if ($found_subfolder) {
            //URI has a folder in it, parse accordingly
            //Example: http://host/admin/edit/user/1 (admin is actually a folder, while edit is a .php file in web/admin/)
            $uri = substr($uri, strlen($found_subfolder)+1);
            $params = explode('/', $uri);
        } else {
            //URI has no folder in it, parse accordingly
            //Example: http://host/article/1 (there is no folder, article is a .php file)
            $params = explode('/', $uri);
        }
        
        //Take off first item in array (which is not a param, its the .php file)
        $params = array_reverse($params);
        array_pop($params);
        $params = array_reverse($params);
        //Remove empty items
        if (!$as_string) {
            for ($i=0; $i < count($params); $i++) {
                if (trim($params[$i]) == '') {
                    unset($params[$i]);
                }
            }
        }
        
        
        if ($as_string) {
            $p = '';
            foreach ($params as $param) {
                $p .= $param.'/';
            }
            $params = substr($p,0,-1);
        }
        return $params;
    }
    
    /*
     function get_variable($pos) string
     Gets one variable from the current URI, by array position
     mReschke 2010-08-25
    */
    public static function get_variable($pos, $uri=null) {
        $params = Page::get_variables(false, $uri);
        if (isset($params[$pos])) {
            return $params[$pos];    
        } else {
            return null;
        }
        
    }
    
    /*
     function get_url($page='', $is_user_image=false) string
     Gets the absolute web URL for a page in the web/theme/xxx/ folder
     If $is_user_image=true then looks at web/image/ instead (looks for user images instead of themes)
     If it IS a user_image, you must include the extension, only .php files do not require an extension
     If $page='', then gets the current URL
     Used for images, style sheets and javascript (anything publically accessible via url)
     $page example (article.css or admin/users.css), no / at beginning return http://hosname/theme/xxx/article.css
     mReschke 2010-08-24
    */
    public static function get_url($page='', $is_user_image=false) {
        #GLOBAL $view;
        if ($page == '') {
            $pageURL = 'http';
            
            #Does not work if you have SSL termination at loadbalancer, use Config::WEB_BASE_URL instead
            #if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            if (preg_match("'https'i", Config::WEB_BASE_URL)) {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if (@$_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }
            $ret = $pageURL;
        } else {

            //A note on query string caching (?v=xxx)
            //http://code.google.com/speed/page-speed/docs/caching.html
            //Recommendations
            //Don't include a query string in the URL for static resources.
            //Most proxies, most notably Squid up through version 3.0, do not cache resources with a "?" in their URL even if a Cache-control: public header is present in the response.
            //To enable proxy caching for these resources, remove query strings from references to static resources, and instead encode the parameters into the file names themselves.
            //So I guess I must change the filename whenever I make major edits.
            if (Config::CACHE_VERSION) {
                $cache_version = "?v=".Config::CACHE_VERSION;
            }
            if ($is_user_image) {
                //Getting a user uploaded image url, from web/images/
                $ret = Config::WEB_BASE_IMAGE_URL.Config::WEB_BASE."image/$page$cache_version";
            } else {
                //Getting a web url, from web/xxx
                $pl = strtolower($page);
                if (!stristr($page, ".") || stristr($pl, ".ajax") || stristr($pl, ".php") || stristr($pl, ".html") || stristr($pl, ".htm")) {
                    //Looking for no extension or .htm, .html, .php, so NOT in web/theme/xxx/ directory
                    $ret = Config::WEB_BASE_URL.Config::WEB_BASE.$page;
                    if (Config::USE_PAGE_EXTENSIONS) $ret .= '.php';
                } else {
                    //Looking for other (css,js,jpg,gif,png...) so look IN web/theme/xxx/ directory
                    if (preg_match("/\.css/i", $page)) {
                        //Load css from WEB_BASE_CSS_URL (just parallel optimization here)
                        $ret = Config::WEB_BASE_CSS_URL.Config::WEB_BASE.'theme';
                        if (file_exists(Config::ABS_BASE."/web/theme/".Config::THEME."/css/$page")) {
                            $ret = "$ret/".Config::THEME."/css/$page$cache_version";
                        } else {
                            $ret = "$ret/default/css/$page$cache_version";
                        }
                        
                    } elseif (preg_match("/\.js/i", $page)) {
                        //Loading JS
                        $ret = Config::WEB_BASE_JS_URL.Config::WEB_BASE.'theme';
                        if (file_exists(Config::ABS_BASE."/web/theme/".Config::THEME."/js/$page")) {
                            $ret = "$ret/".Config::THEME."/js/$page$cache_version";
                        } else {
                            $ret = "$ret/default/js/$page$cache_version";
                        }
                    } else {
                        //Loading Image from WEB_BASE_IMAGE_URL (just parallel optimization here)
                        $ret = Config::WEB_BASE_IMAGE_URL.Config::WEB_BASE.'theme';
                        if (file_exists(Config::ABS_BASE."/web/theme/".Config::THEME."/images/$page")) {
                            $ret = "$ret/".Config::THEME."/images/$page$cache_version";
                        } else {
                            $ret = "$ret/default/images/$page$cache_version";
                        }
                    }                    
                }
            }
        }
        return $ret;
    }
    
    
    /*
     function load_view($view='', $require_once = true) string
     Includes with require_once the given view
     If no view is given, parses the current URL (which would be like /admin/user.php) and loads the matching view
     returns the views filesystem relative path
     $view is only the view name, not the .view.php
     Defaults to require_once, but sometimes include is needed instead for multiple instances
     Example: Page::get_view('article');
     mReschke 2010-08-24
    */
    public static function load_view($view='', $require_once = true) {
        if ($view == '') {
            $view = Page::get_page(); #/admin/user.php
            $view = substr($view, 1); //removes the beginning /
            if (stristr(strtolower($view), ".php")) {
                $view = substr($view, 0, -4);
            }
            //left with admin/user which will load nicely below
        }
        if ($view == '') $view = 'topic'; //Default document (so would think http://mreschke.com is http://mreschke.com/topic)

        $ret = Page::get_abs_base()."/view";
        if (file_exists("$ret/default/$view.view.php")) {
            $ret = "$ret/default/$view.view.php";
        } else {
            $ret = "$ret/".Config::THEME."/$view.view.php";
        }
        
        if ($require_once) {
            $ret = "require_once '$ret';";
        } else {
            $ret = "include '$ret';";
        }
        
        return $ret;
    }
    
    /*
     function load_part($part, $require_once = true) string
     Gets the eval string to use to load up the partial view
     Defaults to require_once, but sometimes include is needed instead for multiple instances
     Example: $view_right_items[] = Page::load_part('menu/topic_about');
       Then to run the eval string, use <?php eval(Page::load_part('menu/topic_about')) ?>
     mReschke 2010-08-28
    */
    public static function load_part($part, $require_once=true) {
        if (!$require_once) $require_once = 0;
        $part = "eval(Page::load_view('part/$part', $require_once));";
        return $part;
    }
    
    /*
     function load_code($code) string
     Includes with require_once the given code
     If no code is given, parse URL to get code
     returns the codes filesystem relative path
     $code is only the code name, not the .code.php
     Example: Page::get_code('page');
     mReschke 2010-08-24
    */
    public static function load_code($code='') {
        if ($code == '') {
            $code = Page::get_page(); #/admin/user.php
            $code = substr($code, 1); //removes the beginning /
            if (stristr(strtolower($code), ".php")) {
                $code = substr($code, 0, -4);
            }
            //left with admin/user which will load nicely below
        }
        if ($code == '') $code = 'topic'; //Default document (so would think http://mreschke.com is http://mreschke.com/topic)
        $code = "require_once '".Page::get_abs_base()."/code/$code.code.php';";
        return $code;
    }

    /*
     function load_class($class) string
     Includes with require_once the given class
     returns the classes filesystem relative path
     $class is only the class name, not the .class.php
     Example: Page::get_class('helper');
     mReschke 2010-08-24
    */
    public static function load_class($class, $append_dotclass=true) {
        if ($append_dotclass) {
            $class = "require_once '".Page::get_abs_base()."/class/$class.class.php';";
        } else {
            $class = "require_once '".Page::get_abs_base()."/class/$class';";
        }
        return $class;
    }

    /*
     function load_model($model) string
     Includes with require_once the given model
     returns the models filesystem relative path
     $model is only the model name, not the .model.php
     Example: Page::get_model('user_pref');
     mReschke 2010-08-24
    */
    public static function load_model($model) {
        $model = "require_once '".Page::get_abs_base()."/model/".Config::DB_TYPE."/$model.model.php';";
        return $model;
    }
    
    /*
     function get_abs_base() string
     Gets the absolute filesystem path to the base project
     Example: Page::get_abs_base() returns /srv/http/mrcore4/src
     No / at end
     mReschke 2010-08-24
    */
    public static function get_abs_base() {
        #This does work, but why not use config file
        #$abs = substr($_SERVER['DOCUMENT_ROOT'], 0, strripos(substr($_SERVER['DOCUMENT_ROOT'], 0, -1), '/'));
        $abs = Config::ABS_BASE;
        return $abs;
    }
    
    
}
