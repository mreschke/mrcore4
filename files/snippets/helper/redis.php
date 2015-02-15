<?php
require_once 'common.php';
require_once __DIR__.'/../vendor/predis/autoload.php';



/*
 helper_redis
 Redis helper
 mReschke 2013-04-17
*/
class helper_redis {
	public $server;
	public $port;
	public $db;
	public $query;

	public function __construct($server=null, $db=0, $port=6379) {
		$this->server = $server;
		$this->db = $db;
		$this->port = $port;
		if (isset($server)) $this->connect($server, $db, $port);
	}

	public function connect($server='xenstore.dynatronsoftware.com', $db=0, $port=6379) {
		$this->server = $server;
		$this->db = $db;
		$this->port = $port;
		$this->query = new Predis\Client(array(
			'scheme' => 'tcp',
			'host' => $this->server,
			'port' => $this->port,
			'database' => $this->db,
		));
	}

	public function disconnect() {
		$this->query = null;
	}
}