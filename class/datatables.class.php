<?php
#require_once '../model/mysql/adodb.php';

/*
 class Datatables
 mReschke 2013-09-23
*/
class Datatables {
    public $db_type;
    public $debug;
    public $columns;
    public $from;
    public $where;
    public $order;
    public $limit;
  
    public $search;
    public $search_col;
    public $sort_col;
    public $sort_col_dir;
    public $limit_start;
    public $limit_len;
    public $query;
    public $query_total;
    public $query_total_filtered;
    public $total_count;
    public $total_filtered_count;
    
    public function __construct() {
        $this->db_type = 'mysql';
        $ths->debug = false;
        $this->from = '';
        $this->where = '';
        $this->order = '';
        $this->limit = '';
        $this->columns = array();
        $this->search_col = array();
        $this->sort_col = null;
        $this->sort_col_dir = array();
        $this->limit_start = null;
        $this->limit_len = -1;
    }

    public function add_column($display, $column, $datatype='string', $visible=true) {
        $col = new DatatablesColumn();
        $col->display = $display;
        $col->column = $column;
        $col->datatype = $datatype;
        $col->visible = $visible;
        $this->columns[] = $col;
    }

    public function expand() {
        //Prepare Global Search
        //Master search text is $_GET['sSearch'];
        $this->search = $_GET['sSearch'];
        
        //Prepare SQL Where (Column Search)
        //$_GET['iColumns'] is count if visible columns
        //Each column search is $_GET['sSearch_x'] where x is column number, starting at 0
        for ($i=0; $i < intval($_GET['iColumns']); $i++) {
            if ($_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '') {
                //Datatables column is searchable, and is being searched
                $this->search_col[$i] = $_GET['sSearch_'.$i];
            }
        }
        
        //Prepare SQL Sorting
        //First sorted column is $_GET['iSortCol_x'] (where x is the first column sorted, starts at 0);
        //$_GET['sSortDir_0']=asc or desc is first column direction
        if (isset($_GET['iSortCol_0'])) {
            for ($i=0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_'.intval($_GET['iSortCol_'.$i])] == "true") {
                    //Datatables column is sortable, and is set to sort
                    $this->sort_col[] = intval($_GET['iSortCol_'.$i]);
                    $this->sort_col_dir[] = $_GET['sSortDir_'.$i];
                }
            }
        }

        //Prepare SQL Limits
        //Current page is $_GET['iDisplayStart'] which is page-1 * page size
        //Page length is $_GET['iDisplayLength']
        if (isset($_GET['iDisplayStart'])) $this->limit_start = $_GET['iDisplayStart'];
        if (isset($_GET['iDisplayLength'])) $this->limit_len = $_GET['iDisplayLength'];

        //SQL Total Query
        //Here because $this->where has NOT had any filtered added yet
        if ($this->where != '') $this->where = "WHERE ".$this->where;
        $this->query_total = "SELECT COUNT(*) FROM ".$this->from." ".$this->where;
        if ($this->debug) echo "query_total: ".$this->query_total."\r\n\r\n";

        //Global Search Filtering
        #\Helper\Datatables::datatables_global_filter($this->where, $search, $aColumns, $all_cols);
        if ($this->search != "") {
            $this->search = strtolower(mysql_real_escape_string($this->search));
            $this->where .= ($this->where=='') ? "WHERE (" : " AND (";
            for ($i=0; $i < count($this->columns); $i++) {
                #$datatype = $all_cols[$i]->datatype;
                //Only global filter on string or date columns, integers and bools are pointless
                #if (($datatype == 'string' || $datatype == 'date') || $ignore_datatypes) {
                    $col = $this->columns[$i]->column;
                    #if (stristr($col, " as ")) $col = substr($col, strripos($col, " as ")+4);
                    if (stristr($col, " as "))  $col = substr($col, 0, strripos($col, " as "));
                    if ($col == 'null') {
                        //Nothing, don't Global Filter on these columns
                    } else {
                        $this->where .= $col." LIKE '%".mysql_real_escape_string($this->search)."%' OR ";
                    }
                #}
            }
            $this->where = substr_replace($this->where, "", -3).')';
        }

        //Individual Column Search Filtering
        if (isset($this->search_col)) {
            foreach ($this->search_col as $key => $val) {
                $val = strtolower(mysql_real_escape_string($val));
                $c = -1;
                foreach ($this->columns as $column) {
                    if ($column->visible) $c++;
                    if ($c == intval($key)) break;
                }
                $col = $column->column;
                $datatype = $column->datatype;




                #$col = $this->columns[intval($key)]->column;
                #$datatype = $this->columns[intval($key)]->datatype;
                
                // Parser && (AND) and || (OR) multiple statements
                $matches = array($val);
                if (preg_match("/\&\&/", $val)) {
                    $matches = explode("&&", $val);
                } elseif (preg_match("/\|\|/", $val)) {
                    $matchor = true;
                    $matches = explode("||", $val);
                }
                
                foreach ($matches as $val) {
                    $val = trim($val);
                    if (stristr($col, " as ")) {
                        $col_name = substr($col, strripos($col, " as ")+4);
                        $col = substr($col, 0, strrpos($col, " as "));
                    }
                    if ($matchor) {
                        $this->where .= ($this->where=='') ? "WHERE " : " OR ";
                    } else {
                        $this->where .= ($this->where=='') ? "WHERE " : " AND ";
                    }
                    
                    $yes = array('y','yes','1','e','s','ye','es','t','true');
                    $no = array('n','no','o','-1','f','false');
                    $na = array('na','n/a','0');
                    
                    if ($val == '!') {
                        $this->where .= "($col = '' or $col is NULL) ";
                    } elseif ($val == '=') {
                        $this->where .= "($col <> '' AND $col is NOT NULL) ";
                    } else {
                        if ($datatype == 'nullbool') {
                            //Searching on nullbool (Yes/No/NA)
                            #if ($col_name == 'accredited' || $col == 'd.reviewed' || $col == 'd.memo_recorded' || $col == 'd.approved') {
                            if (in_array($val, $yes)) {
                                $this->where .= "$col = 1 ";
                            } elseif (in_array($val, $no)) {
                                $this->where .= "$col = -1 ";
                            } elseif (in_array($val, $na)) {
                                $this->where .= "$col = 0 ";
                            } else {
                                $this->where .= "$col LIKE '%$val%' ";    
                            }
                        } elseif ($datatype == 'bool') {
                            //Searching on bool (Yes/No) Column
                            #} elseif ($col == 'd.land_owner' || $col == 'd.land_manager' || $col == 'd.investor_land' || $col == 'd.investor_community' || $col == 'd.tge' || $col == 'd.other') {
                            if (in_array($val, $yes)) {
                                $this->where .= "$col = 1 ";
                            } elseif (in_array($val, $no)) {
                                $this->where .= "$col = 0 ";
                            } else {
                                $this->where .= "$col LIKE '%$val%' ";    
                            }                        
                        } else {
                            if (in_array(substr($val, 0, 2), array('<=', '>=', '!='))) {
                                $condit = substr($val, 0, 2);
                                $val = substr($val, 2);
                                if ($datatype == 'string' || $datatype == 'date') $val = "'$val'";
                            } elseif (in_array(substr($val, 0, 1), array('<', '>', '='))) {
                                $condit = substr($val, 0, 1);
                                $val = substr($val, 1);
                                if ($datatype == 'string' || $datatype == 'date') $val = "'$val'";
                            } else {
                                if (substr($val, 0, 1) == '!') {
                                    $condit = 'NOT LIKE';
                                    $val = "'%".substr($val, 1)."%'";
                                } else {
                                    $condit = 'LIKE';
                                    $val = "'%$val%'";
                                }
                                
                            }                            
                            $this->where .= "$col $condit $val ";
                        }                    
                    }                    
                }
            }
        }
        
        //Ordering
        if (count($this->sort_col) > 0) {
            $this->order = "ORDER BY  ";
            for ($i=0 ; $i < count($this->sort_col); $i++) {
                $col = $this->columns[$this->sort_col[$i]]->column;
                if (stristr($col, " as ")) $col = substr($col, strripos($col, " as ")+4);
                $this->order .= $col." ".mysql_real_escape_string($this->sort_col_dir[$i]) .", ";
            }
            $this->order = substr_replace($this->order, "", -2);
            if ($this->order == "ORDER BY") $this->order = "";
        } else {
            if ($this->order != '') $this->order = "ORDER BY ".$this->order;
        }

        //Paging (Limit)
        if (isset($this->limit_start) && $this->limit_len != '-1') {
            if ($this->db_type == 'mysql') {
                $this->limit = "LIMIT ".mysql_real_escape_string($this->limit_start).", ".mysql_real_escape_string($this->limit_len);
            } elseif ($this->db_type == 'mssql') {
                //FIXME, MSSQL paging!
                $this->limit = "OFFSET ".mysql_real_escape_string($this->limit_start)." ROWS FETCH NEXT ".mysql_real_escape_string($this->limit_len)." ROWS ONLY;";
                //OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY;
            }
        }

        //SQL Query Builder
        $cols = array(); foreach ($this->columns as $col) $cols[] = $col->column;
        $this->query = "
            SELECT ".str_replace(" , ", " ", implode(", ", $cols))."
            FROM ".$this->from."
            ".$this->where."
            ".$this->order."
            ".$this->limit;
        if ($this->debug) echo "query: ".$this->query."\r\n\r\n";

        //SQL Total Filtered Query
        //Here because $this->where has had all filtered added
        $this->query_total_filtered = "SELECT COUNT(*) FROM ".$this->from." ".$this->where;
        if ($this->debug) echo "query_total_filtered: ".$this->query_total_filtered."\r\n\r\n";
    }

}


class DatatablesColumn {
    public $display;  //display name
    public $column;   //column (mytbl.mycol or advanced subselects...)
    public $datatype; //string, nullbool, bool, date
    public $visible;  //will always be in SQL query, but may or may not be on the output grid
}