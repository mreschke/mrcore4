<?php
eval(Page::load_class('datatables')); //This is a builtin mRcore4 Class
require_once 'common.php';

/*
 helper_mysql
 MSSQL connection and query helpers
 mReschke 2012-10-31 (halloween oooh spooky)
*/
class helper_mysql {
	public $server;
	public $port;
	public $user;
	public $pass;
	public $db;
	public $mysqli;
	public $query;
	public $builder;
	public $result;
	public $row_count;
	public $field_count;
	public $datatables;
	public $capture_output;
	public $table_style;
	public $columns;
	public $columns_output;

	public function __construct($server=null,$db=null) {
		$this->datatables = true;
		$this->capture_output = false;
		if (isset($server) && isset($db)) $this->connect($server, $db);
		$this->builder = new \Datatables;
		$this->builder->db_type = 'mysql';
	}

	public function connect($server=\Snippets\Config::MYSQL_DB_SERVER, $db=\Snippets\Config::MYSQL_DB_NAME, $user=\Snippets\Config::MYSQL_DB_USER, $pass=\Snippets\Config::MYSQL_DB_PASS, $port=\Snippets\Config::MYSQL_DB_PORT) {
		$this->server = $server;
		$this->port = $port;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
		$this->mysqli = new mysqli($this->server, $this->user, $this->pass, $this->db, $this->port);
		if (mysqli_connect_error()) {
			die ("Couldn't connect to MySQL Server on ".$this->server.".<br />Error: ".mysqli_connect_error());
		}
		#echo $this->mysqli->host_info; #Test for success
	}

	public function disconnect() {
		#$this->result->close();
		$this->mysqli->close();
	}

	public function execute() {
		if (isset($this->query)) {
			//Execute a simple query string
			$this->result = $this->mysqli->query($this->query);
			$this->row_count = $this->result->num_rows;
			$this->field_count = $this->result->field_count;
		
		} elseif (isset($this->builder->from)) {
			//Execute a advanced query builder object
			$common = new helper_common;
			if ($common->get_url_action() == 'ajax') {
				if ($_GET['key'] == '8feb8b9265a3878fcb204a591dcb91a5') {
	  				//Take query builder plus all filters/sorts and
	  				//get an object that has everything, query + total records query
	  				$this->builder->expand();

	  				//Execute the Total Rows Query
	  				$this->result = $this->mysqli->query($this->builder->query_total);
	  				$this->builder->total_count = $this->get_scalar();

	  				//Execute the Total Filtered Rows Query
	  				$this->result = $this->mysqli->query($this->builder->query_total_filtered);
	  				$this->builder->total_filtered_count = $this->get_scalar();

	  				//Execute the query
	  				$this->result = $this->mysqli->query($this->builder->query);
					$this->row_count = $this->result->num_rows;
					$this->field_count = $this->result->field_count;

					//Output Datatables Ajax
					echo json_encode($this->get_datatables_output());
				}
  				exit();

			} else {
				$this->print_datatables_ajax_template();
			}
		}
	}

	public function get_datatables_output() {
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $this->builder->total_count,
            "iTotalDisplayRecords" => $this->builder->total_filtered_count,
            "aaData" => array()
        );
        for ($r = 0; $r <= $this->row_count-1; $r++) {
            $row = $this->result->fetch_array(MYSQLI_ASSOC);
            $line = array();
            $f=0;
            foreach($row as $colname => $data) {
				if ($this->builder->columns[$f]->visible) {
	                foreach($this->columns_output as $custcolname => $custcoldata) {
	                    if (strtolower($custcolname) == strtolower($colname)) {
	                        $data = $custcoldata;
	                        foreach ($row as $regexcolname => $regexdata) {
	                            $data = preg_replace("'%$regexcolname%'", $row[$regexcolname], $data);
	                        }
	                        break;
	                    }
	                }
	                $line[] = $data;
	            }
	            $f++;
            }
            $output['aaData'][] = $line;

        }
        return $output;
	}

	private function print_datatables_ajax_template() {
		$api = new API\v1;
		$api->view->remove_js('datatables.js');
		$api->view->js($api->page->get_url('datatables_ajax.js'));

		echo "<table class='datatable' ".($this->table_style ? 'style=\''.$this->table_style.'\'' : '').">";
		echo "<thead><tr>";
		foreach($this->builder->columns as $col) {
			if ($col->visible) {
		  		echo "<th>".$col->display."</th>";
		  	}
		}
		echo "</tr></thead><tbody></tbody>";
		echo "<tfoot><tr>";
		for ($i=0; $i < count($this->builder->columns); $i++) {
			if ($this->builder->columns[$i]->visible) {
				echo "<th><input type='text' name='search_$i' class='search_init' /></th>";
			}
		}
		echo "</tr></tfoot></table>";
	}

	public function get_scalar() {
		$row = $this->result->fetch_row();
		return $row[0];
	}

	public function isnull($value, $default) {
		$ret = $default;
		if (isset($value)) $ret = $value;
		if (!is_numeric($ret) && strtolower($ret) != 'null') $ret = "'".$ret."'";
		return $ret;
	}

	public function to_assoc($keyfield, $valuefield) {
		$ret = array();
		while ($row = mysqli_fetch_array($this->result)) {
			$ret[$row[$keyfield]] = $row[$valuefield];
		}
		return $ret;
	}

	public function output_table() {
		mysqli_data_seek($this->result, 0);
		if ($this->capture_output) {
			//Capture output as return variable instead of printing to screen
			ob_start();
		}
		echo "<table class=".($this->datatables ? "'datatable'" : "'table_table'")." ".($this->table_style ? 'style=\''.$this->table_style.'\'' : '')." border='0'>";
		echo "<thead>";
			for ($f = 0; $f <= $this->field_count-1; $f++) {
				#$field = mssql_fetch_field($this->result, $f);
				$field = $this->result->fetch_field();
				$show = true;
				if (isset($this->columns)) {
					//Only show columns if in list of visible columns
					if (!in_array(strtolower($field->name), array_map('strtolower', $this->columns))) $show = false;
				}
				if ($show) echo "<th>".$field->name."</th>";
			}
		echo "</thead>";
		echo "<tbody>";
			for ($r = 0; $r <= $this->row_count-1; $r++) {
				$row = $this->result->fetch_row();
				if ($r % 2) {
					echo "<tr class='table_tr_even'>";	
				} else {
					echo "<tr class='table_tr_odd'>";
				}
				for ($f = 0; $f <= $this->field_count-1; $f++) {
					$field = $this->result->fetch_field();
					//Field Types
					#if ($field->type == 'unknown') {
					#	$row[$f] = mssql_guid_string($row[$f]);
					#} elseif ($field->type == 'bit') {
					#	$row[$f] = ($row[$f] == 1 ? 'true' : 'false');
					#}
					$output = $row[$f];
					$show = true;
					if (isset($this->columns)) {
						//Only show columns if in list of visible columns
						if (!in_array(strtolower($field->name), array_map('strtolower', $this->columns))) $show = false;
					}
					foreach($this->columns_output as $key=>$value) {
						if (strtolower($key) == strtolower($field->name)) {
							$output = preg_replace('"%data%"', $output, $value);
							break;
						}
					}
					if ($show) echo "<td>$output</td>";
					
				}
				echo "</tr>";
			}
		echo "</tbody>";
		echo "</table>";
		if ($this->capture_output) {
			$return = ob_get_contents();
            ob_end_clean();
            return $return;
		}
	}

	public function output_csv($filename, $save_path = null) {
		mysqli_data_seek($this->result, 0);
		$common = new helper_common;
		if (isset($save_path)) {
			$tmp_dir = $save_path;
		} else {
			$tmp_dir = $common->tmp_dir;	
		}
		if (!is_dir($tmp_dir)) exec('mkdir -p '.$tmp_dir);
		$filename = preg_replace("'\.csv'i", '', $filename)."_".date("Y-m-d-H-m-s").".csv";
		$fp = fopen($tmp_dir.$filename, 'w');

		// Write Headers
		$headers = array();

		for ($f = 0; $f <= $this->field_count-1; $f++) {
			$field = $this->result->fetch_field();
			$show = true;
			if (isset($this->columns)) {
				//Only show columns if in list of visible columns
				if (!in_array(strtolower($field->name), array_map('strtolower', $this->columns))) $show = false;
			}
			if ($show) $headers[] = $field->name;
		}		
		fputcsv($fp, $headers);

		$data = array();
		for ($r = 0; $r <= $this->row_count-1; $r++) {
			$row = $this->result->fetch_row();
			for ($f = 0; $f <= $this->field_count-1; $f++) {
				$field = $this->result->fetch_field();
				$output = $row[$f];
				$show = true;
				if (isset($this->columns)) {
					//Only show columns if in list of visible columns
					if (!in_array(strtolower($field->name), array_map('strtolower', $this->columns))) $show = false;
				}
				foreach($this->columns_output as $key=>$value) {
					if (strtolower($key) == strtolower($field->name)) {
						$output = preg_replace('"%data%"', $output, $value);
						break;
					}
				}
				if ($show) $data[] = $output;
			}
			fputcsv($fp, $data);
			$data = array();
		}
		fclose($fp);

		if (!isset($save_path)) {
			//Download File
			echo "<META HTTP-EQUIV='Refresh' Content='0; URL=".$common->tmp_url.$filename."'>";
		}
	}
}


