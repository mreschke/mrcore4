<?php

// Load up our Core Class
require_once('../class/core.class.php');

// Define our Router Analyzer
$page = strtolower(Page::get_page()); 
$route = function($url = null) use ($page) {
	if (isset($url)) {
		$url = strtolower($url);
		#if (preg_match("'$url'", $page)) {
		if ($page == $url) {
			return true;
		} else {
			$url = $url.'.php';
			if ($page == $url) {
				return true;
			}
		}
	} else {
		return $page;
	}
};


// Define our Default Routes
$defaults = array(
	'/edit', '/files', '/login', '/net', '/profile', '/redirect', '/search',
	'/admin/badges', '/admin/groups', '/admin/indexer', '/admin/tags', '/admin/users',
);


// Router
if (in_array($route(), $defaults)) {
	//Default Route
	eval(Page::load_code());
	eval(Page::load_view('master'));

} elseif ($route('/') || $route('/topic')) {
	//Load our Snippets Directory Composer Autoloader
	require_once Config::FILES_DIR.'/snippets/.sys/vendor/autoload.php';
	eval(Page::load_code());
	
	if ($view->viewmode_simple) {
		//Simple view mode means to hide mrcore layout (header, search, avatar, menus...)
		//Wiki is still parsed and CSS/Javascript is still present though no <html><body>...tags are present
		//This should just be the wiki content and should look and function like normal because of the css/js
		//Simple mode does include site/user global topics but NOT comments.
		eval(Page::load_code('master'));
		$view->css[] = Page::get_url('master_simple.css');

		if(isset($view->css)) {
		    foreach($view->css as $css) {
		        echo "    <link rel='stylesheet' type='text/css' href='$css' />\r\n";
		    }
		}
		if(isset($view->css_print)) {
		    foreach($view->css_print as $css) {
		        echo "    <link rel='stylesheet' type='text/css' media='print' href='$css' />\r\n";
		    }
		}
		if(isset($view->js)) {
		    foreach($view->js as $js) {
		        echo "    <script language='javascript' src='$js' type='text/javascript'></script>\r\n";
		    }
		}
		
		echo "<div id='tbwiki_simple'><div><div><div><div><div><div>";
		Parser::parse_wiki($info, $topic, $topic->tbl_post->body);
		echo "</div></div></div></div></div></div></div>";

	} elseif ($view->viewmode_raw) {
		//Raw view mode means to hide all mrcore layout AND exclude all CSS/Javascript
		//So this should look real ugly and any javascript items like header expand/collapse will NOT work
		//This is so I can cause topics produce simple text ajax jason strings if I need too!
		//Raw mode does NOT include site/user global topics or comments, just the requested topics HTML
		Parser::parse_wiki($info, $topic, $topic->tbl_post->body, true, array('paragraph'));

	} else {
		//Default view, load all theme layouts and full site
		eval(Page::load_code('master'));
	    $wiki = Parser::parse_wiki($info, $topic, $topic->tbl_post->body, false);
		eval(Page::load_view('master'));
	}

} elseif ($route('/ajax/files.ajax')) {
	eval(Page::load_code());
	eval(Page::load_view());

} elseif ($route('/redirect')) {
	eval(Page::load_code('redirect'));
	eval(Page::load_view('master'));

} elseif ($route('/rest/v1/topic')) {
	eval(Page::load_code());

} elseif ($route('/ajax/search.ajax')) {
	if ($_POST['key'] == 'c44dbb976265f6d75756475bb7cbdee5') {
		eval(Page::load_code());
		eval(Page::load_view());
	} else {
		Page::redirect_404();
	}

} elseif ($route('/ajax/userinfo.ajax')) {
	if ($_POST['key'] == 'c44dbb976265f6d75756475bb7cbdee5') {
		eval(Page::load_code());
		eval(Page::load_view());
	} else {
		Page::redirect_404();
	}

} elseif ($route('/ajax/getbadge.ajax')) {
	eval(Page::load_code());

} elseif ($route('/ajax/gettag.ajax')) {
	eval(Page::load_code());

} else {
	//Check MySQL for custom route
	if ($route('/about')) {
		echo "About page";

	} else {
		Page::redirect_404();
	}
}
