<?php
require_once 'config.php';
$common = new helper_common;

/*
 helper_common
 Common variables and function to all helpers
 mReschke 2012-11-19
*/
class helper_common {
	public $servers_sql = array('DYNA-SQL', 'DYNA-SQL2', 'DYNA-SQL3', 'DYNA-SQL4', 'DYNA-SQL5', 'DYNA-SQL6', 'DYNA-SQL7');
	public $servers_sql2 = array('DYNA-SQL', 'DYNA-SQL2', 'DYNA-SQL3', 'DYNA-SQL4', 'DYNA-SQL5', 'DYNA-SQL6'); //excludes one-offs like QMA's SQL7
	public $servers_web = array('DYNA-WEB', 'DYNA-WEB2', 'DYNA-WEB3', 'DYNA-WEB4', 'DYNA-WEB5', 'DYNA-WEB6');
	public $servers_app = array('DYNA-APP', 'DYNA-APP2', 'DYNA-APP3');
	public $tmp_dir;
	public $tmp_url;

	public function __construct() {
		$this->tmp_dir = Config::ABS_BASE.'/web/tmp/';
		$this->tmp_url = Config::WEB_BASE_URL.'/tmp/';
	}

	/*
	 array_insert($array, $position, $value) array
	 Inserts a $value into an $array at $position
	 mReschke 2013-01-24
	*/
	public function array_insert($array, $position, $value) {
		array_splice($array, $position, 0, $value);
		return $array;
	}

	/*
	 Execute a command and return the resulting output as a string
	 mReschke 2013-07-10
	*/
	public function execute($cmd, $delimeter="<br />") {
		exec($cmd, $ret);
		return implode($delimeter, $ret);
	}

	/*
	 Get the server number (integer) from the server name
	 mReschke 2013-08-19
	*/
	public function get_server_num($server) {
		$last = substr($server, -1);
		if (is_numeric($last)) {
			return $last;
		} else {
			return 1;
		}
	}

	/*
	 function get_url_action(array $actions=null)
	 Parses this topics url to get action
	 mReschke 2013-09-24
	*/
	public function get_url_action() {
		$url = Page::get_variables();
		$c = count($url);
		$action = 'list';
		if ($c == 2) {
			if ($url[1] == 'ajax') $action = 'ajax';
		} elseif ($c == 3) {
			if ($url[2] == 'create') {
				$action = 'create';
			} elseif ($url[2] == 'ajax') {
				$action = 'ajax';
			} else {
				$action = 'view';
			}
		} elseif ($c == 4) {
			$action = $url[3];
		}
		return $action;
	}

	/*
	 function get_url_action_id(array $actions=null)
	 Parses this topics url to get action id
	 mReschke 2013-09-24
	*/
	public function get_url_action_id() {
		$url = Page::get_variables();
		$c = count($url);
		$id = '';
		if ($c >= 3 && $url[2] != 'create' && $url[2] != 'ajax') {
			$id = $url[2];	
		} 
		return $id;
	}
}

