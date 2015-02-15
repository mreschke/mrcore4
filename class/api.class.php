<?php
namespace API;
/*
This is the API v1 interface for mRcore 4 written on 2013-11-24
*/

function snippet($name) {
	#Usage: eval(API::snippet('iam'));
	return "require_once '".\Config::FILES_DIR."/snippets/helper/$name.php';";
}

function load($name) {
	#Usage: eval(API::file('34/.sys/index.php'));
	return "require_once '".\Config::FILES_DIR."/$name';";
}


class v1 {

	public $config;
	public $user;
	public $view;
	public $page;
	public $topic;

	function __construct() {
		$this->user = new User_v1;
		$this->config = new Config_v1;
		$this->view = new View_v1;
		$this->page = new Page_v1;
		$this->topic = new Topic_v1;
	}

}


class User_v1 {

	public function __construct() {
		GLOBAL $info;
		$this->id = $info->tbl_user->user_id;
		$this->email = $info->tbl_user->email;
		$this->first = $info->tbl_user->first_name;
		$this->last = $info->tbl_user->last_name;
		$this->alias = $info->tbl_user->alias;
		$this->is_admin = $info->tbl_user->perm_admin;
		$this->is_authenticated = $info->is_authenticated;
		$this->perm_create = $info->tbl_user->pern_create;
		$this->perm_exec = $info->tbl_user->perm_exec;
		$this->perm_html = $info->tbl_user->perm_html;
		$this->global_topic = $info->tbl_user->global_topic_id;
		$this->user_topic = $info->tbl_user->user_topic_id;
	}

}


class Config_v1 {

	public function __construct() {
		$this->files_dir = \Config::FILES_DIR;
		$this->web_base_url = \Config::WEB_BASE_URL;
		$this->web_host = \Config::WEB_HOST;
		$this->web_base = \Config::WEB_BASE;
		$this->abs_base = \Config::ABS_BASE;
		$this->help_topic = \Config::WIKI_HELP_TOPIC_ID;
		$this->global_topic = \Config::GLOBAL_TOPIC;
		$this->userinfo_topic = \Config::USERINFO_TOPIC;
		$this->searchbox_topic = \Config::SEARCHBOX_TOPIC;
		$this->recent_max_title_len = \Config::RECENT_MAX_TITLE_LEN;
	}

}


class View_v1 {

	function appmode($value) {
		GLOBAL $view;
		if (isset($value)) {
			$view->viewmode_app = $value;
		} else {
			return $view->viewmode_app;
		}
	}

	function title($value) {
		GLOBAL $view;
		if (isset($value)) {
			$view->title = $value;
		} else {
			return $view->title;
		}
	}

	function menu($value) {
		GLOBAL $view;
		if (isset($value)) {
			$view->menu = $value;
		} else {
			return $view->menu;
		}
	}

	function form_attributes($value) {
		GLOBAL $view;
		if (isset($value)) {
			$view->form_attributes = $value;
		} else {
			return $view->form_attributes;
		}
	}

	function js($value, $append = true) {
		GLOBAL $view;
		if (isset($value)) {
			if ($append) {
				$view->js[] = $value;
			} else {
				$view->js = $value;
			}
		} else {
			return $view->js;	
		}
	}

	function remove_js($value) {
		\View::remove_js($value);
	}

	function css($value, $append = true) {
		GLOBAL $view;
		if (isset($value)) {
			if ($append) {
				$view->css[] = $value;
			} else {
				$view->css = $value;
			}
		} else {
			return $view->css;
		}
	}

	function css_print($value, $append = true) {
		GLOBAL $view;
		if (isset($value)) {
			if ($append) {
				$view->css_print[] = $value;
			} else {
				$view->css_print = $value;
			}
		} else {
			return $view->css_print;	
		}
	}

}


class Page_v1 {

	function redirect($page, $vars='') {
		return \Page::redirect($page, $vars);
	}

	function redirect_404() {
		return \Page::redirect_404();
	}

	function get_page() {
		return \Page::get_page();
	}

	function get_variables($as_string=false, $uri=null) {
		return \Page::get_variables($as_string, $uri);
	}

	function get_variable($pos, $uri=null) {
		return \Page::get_variable($pos, $uri);
	}

	function get_url($page='', $is_user_image=false) {
		return \Page::get_url($page, $is_user_image);
	}

}

Class Topic_v1 {

	public function __construct() {
		GLOBAL $topic;
		$this->id = $topic->tbl_post->topic_id;
		$this->post_id = $topic->tbl_post->post_id;
		$this->post_uuid = $topic->tbl_post->post_uuid;
		$this->title = $topic->tbl_post->title;
	}

}