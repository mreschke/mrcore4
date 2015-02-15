<?php
eval(Page::load_class('datatables')); //This is a builtin mRcore4 Class
require_once 'common.php';

/*
 helper_mssql
 MSSQL connection and query helpers
 mReschke 2012-10-29
*/
class helper_mssql {
	public $server;
	public $user;
	public $pass;
	public $db;
	public $handle;
	public $query;
	public $sp;
	public $Params;
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
		$this->builder->db_type = 'mssql';
	}

	public function connect($server=\Snippets\Config::MSSQL_DB_SERVER, $db=\Snippets\Config::MSSQL_DB_NAME, $user=\Snippets\Config::MSSQL_DB_USER, $pass=\Snippets\Config::MSSQL_DB_PASS) {
		$this->server = $server;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
		$this->handle = mssql_connect($this->server, $this->user, $this->pass) or die("Couldn't connect to SQL Server on ".$this->server);
		mssql_select_db($this->db, $this->handle) or die("Couldn't open database ".$this->db);
	}

	public function disconnect() {
		mssql_free_result($this->result);
		mssql_close($this->handle);
		$this->query = null;
		$this->sp = null;
		$this->Params = null;
		$this->result = null;
		$this->handle = null;
		$this->row_count = null;
		$this->field_count = null;
	}

	public function execute() {
		if (isset($this->query)) {
			//Execute SQL Query
			#mssql_query("SET ANSI_NULLS ON") or die("Error: ".mssql_get_last_message());    #Fixes a multi server UNION error
			#mssql_query("SET ANSI_WARNINGS ON") or die("Error: ".mssql_get_last_message()); #Fixes a multi server UNION error
			#$this->result = mssql_query($this->query) or die("Error: ".mssql_get_last_message());
			mssql_query("SET ANSI_NULLS ON");    #Fixes a multi server UNION error
			mssql_query("SET ANSI_WARNINGS ON"); #Fixes a multi server UNION error
			$this->result = mssql_query($this->query) or die(mssql_get_last_message());
			#if ($this->result == false) {
			#	echo "<div style='color: red;font-weight: bold'>".mssql_get_last_message()."</div><div style='color:red'>".$this->query."</div>";
			#} 
			$this->row_count = mssql_num_rows($this->result);
			$this->field_count = mssql_num_fields($this->result);
		
		} elseif (isset($this->sp)) {
			//Execute SQL Procedure
			$statement = mssql_init($this->sp) or die ("Failed to initialize procedure ".$this->sp);
			foreach ($this->Params as $param) {
				mssql_bind($statement, '@'.$param->name, $param->value, $param->type);
			}
			if (!$this->result = mssql_execute($statement)) {
				print "Could not execute stored procedure, invalid paramaters?";
			}
			$this->row_count = mssql_num_rows($this->result);
			$this->field_count = mssql_num_fields($this->result);
		
		} elseif (isset($this->builder->from)) {
			//Execute a advanced query builder object
			$common = new helper_common;
			if ($common->get_url_action() == 'ajax') {
				if ($_GET['key'] == '8feb8b9265a3878fcb204a591dcb91a5') {
  				
	  				//Take query builder plus all filters/sorts and
	  				//get an object that has everything, query + total records query
	  				$this->builder->expand();
	 				#echo $this->builder->query;

	  				//Execute the Total Rows Query
	  				$this->result = mssql_query($this->builder->query_total);
	  				$this->builder->total_count = $this->get_scalar();

	  				//Execute the Total Filtered Rows Query
	  				$this->result = mssql_query($this->builder->query_total_filtered);
	  				$this->builder->total_filtered_count = $this->get_scalar();

	  				//Execute the query
	  				$this->result = mssql_query($this->builder->query);
					$this->row_count = mssql_num_rows($this->result);
					$this->field_count = mssql_num_fields($this->result);

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
	        $row = mssql_fetch_assoc($this->result);
	        $line = array();
	        $f=0;
	        foreach($row as $colname => $data) {
	            if ($this->builder->columns[$f]->visible) {
	                $field = mssql_fetch_field($this->result, $f);
	                //Field Types
	                if ($field->type == 'unknown') {
	                    $data = mssql_guid_string($data);
	                } elseif ($field->type == 'bit') {
	                    $data = ($data == 1 ? 'true' : 'false');
	                }
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
		  echo "<th>".$col->display."</th>";
		}
		echo "</tr></thead><tbody></tbody>";
		echo "<tfoot><tr>";
		for ($i=0; $i < count($this->builder->columns); $i++) {
			echo "<th><input type='text' name='search_$i' class='search_init' /></th>";
		}
		echo "</tr></tfoot></table>";
	}

	public function get_scalar() {
		$row = mssql_fetch_row($this->result);
		return $row[0];
	}

	public function isnull($value, $default, $auto_inclose_quotes=true) {
		$ret = $default;
		if (isset($value)) $ret = $value;
		if ($auto_inclose_quotes && !is_numeric($ret) && strtolower($ret) != 'null') {
			$ret = "'".$ret."'";	
		}
		return $ret;
	}

	public function to_assoc($keyfield, $valuefield) {
		$ret = array();
		while ($row = mssql_fetch_array($this->result)) {
			$ret[$row[$keyfield]] = $row[$valuefield];
		}
		return $ret;
	}

	public function output_table() {
		mssql_data_seek($this->result, 0);
		if ($this->capture_output) {
			//Capture output as return variable instead of printing to screen
			ob_start();
		}
		echo "<table class=".($this->datatables ? "'datatable'" : "'table_table'")." ".($this->table_style ? 'style=\''.$this->table_style.'\'' : '')." border='0'>";
		echo "<thead>";
			for ($f = 0; $f <= $this->field_count-1; $f++) {
				$field = mssql_fetch_field($this->result, $f);
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
				$row = mssql_fetch_row($this->result);
				if ($r % 2) {
					echo "<tr class='table_tr_even'>";	
				} else {
					echo "<tr class='table_tr_odd'>";
				}
				for ($f = 0; $f <= $this->field_count-1; $f++) {
					$field = mssql_fetch_field($this->result, $f);
					//Field Types
					if ($field->type == 'unknown') {
						$row[$f] = mssql_guid_string($row[$f]);
					} elseif ($field->type == 'bit') {
						$row[$f] = ($row[$f] == 1 ? 'true' : 'false');
					}
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
		mssql_data_seek($this->result, 0);
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
			$field = mssql_fetch_field($this->result, $f);
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
			$row = mssql_fetch_row($this->result);
			for ($f = 0; $f <= $this->field_count-1; $f++) {
				$field = mssql_fetch_field($this->result, $f);
				//Field Types
				if ($field->type == 'unknown') {
					$row[$f] = mssql_guid_string($row[$f]);
				} elseif ($field->type == 'bit') {
					$row[$f] = ($row[$f] == 1 ? 'true' : 'false');
				}
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


	function escape($data) {
		#http://stackoverflow.com/questions/574805/how-to-escape-strings-in-sql-server-using-php
		#This one removes or escapes all harmful characters
        if ( !isset($data) or empty($data) ) return '';
        if ( is_numeric($data) ) return $data;

        $non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ( $non_displayables as $regex )
            $data = preg_replace( $regex, '', $data );
        $data = str_replace("'", "''", $data );
        return $data;
    }


	function escape2($data) {
		#http://stackoverflow.com/questions/574805/how-to-escape-strings-in-sql-server-using-php
		#This one encodes the data as a hex bytestring
		#Downside of this is the query values us not human readable anymore
	    if(is_numeric($data))
	        return $data;
	    $unpacked = unpack('H*hex', $data);
	    return '0x' . $unpacked['hex'];
	}
}


class mssql_param {
	public $name;
	public $value;
	public $type;

	function __construct($name, $value, $type) {
		$this->name = $name;
		$this->value = $value;
		$this->type = $type;
	}
}
