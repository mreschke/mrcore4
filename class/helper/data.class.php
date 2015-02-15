<?php
namespace Helper;

/*
 class \Helper\Data
 All Data helper work functions (non model)
 mReschke 2011-09-08
*/
class Data {
    
    /*
     function convert_rs_xml(RecordSet $rs, array $options) XML string
     Converts an ADODB $rs record set into XML (mainly used for my REST API)
     Resources: PEAR XML_Serializer Package http://pear.php.net/package/XML_Serializer/docs
     mReschke 2011-06-14
    */
    public static function convert_rs_xml($rs, $root_name, $tag_name) {
        try {
            require_once 'XML/Serializer.php'; //PEAR
            $options = array  (  
                XML_SERIALIZER_OPTION_INDENT => '    ',
                'addDecl' => false,
                'encoding' => "UTF-8",
                'rootName' => $root_name,
                'defaultTagName' => $tag_name,
                XML_SERIALIZER_OPTION_RETURN_RESULT => true  
            );            
            $serializer = new \XML_Serializer($options);
            return '<?xml version="1.0" encoding="UTF-8"?>'."\n".$serializer->serialize($rs);
            echo $serializer->serialize($rs);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        } 
    }
    
    /*
     function convert_rs_html(recordset array ignore_columns) string
     A generic function to convert a ADODB recordset into a simple HTML table
     mReschke 2011-05-22
    */
    public static function convert_rs_html($rs, $ignore_columns) {
        $html = '';
        $body = '';
        $header = '';
        $header_complete = false;
        while (!$rs->EOF) {
            if (!isset($keys)) $keys = array_keys($rs->fields); //column names
            for ($i = 1; $i < $rs->_numOfFields * 2; $i += 2) {
                // +=2 because ADODB fields contains a named ass array and int ass array, so double values one by name, one by number, I just want by name
                $col = $keys[$i];
                $data = $rs->fields[$col];
                
                if (!in_array($col, $ignore_columns)) {
                    //Add to $csv_header (only once per column)
                    if (!$header_complete) {
                        $header .= "<th>$col</th>";
                    }
                    
                    //Add Data
                    $rowdata .= "<td>$data</td>";
                }
            } // next col
            
            if (!$header_complete) {
                $header = "<tr>$header</tr>";
                $header_complete = true;
            }
            
            $body .= "<tr>$rowdata</tr>";
            $rowdata = '';
            
            $rs->MoveNext();
        } //next row
        
        if ($body != '') {
            $html = "
                <table id='report_table' class='datatable'>
                    <thead>$header</thead>
                    <tbody>$body</tbody>
                </table>
            ";
        }
        
        return $html;
    }
    
    /*
     function convert_rs_csv(recordset array ignore_columns) string
     A generic function to convert a ADODB recordset into a CSV
     mReschke 2011-05-07
    */
    public static function convert_rs_csv($rs, $ignore_columns) {
        $csv = '';
        $csv_header = '';
        $csv_header_complete = false;
        while (!$rs->EOF) {
            if (!isset($keys)) $keys = array_keys($rs->fields); //column names
            $row = array();
            for ($i = 1; $i < $rs->_numOfFields * 2; $i += 2) {
                // +=2 because ADODB fields contains a named ass array and int ass array, so double values one by name, one by number, I just want by name
                $col = $keys[$i];
                $data = $rs->fields[$col];
                
                if (!in_array($col, $ignore_columns)) {
                    //Add to $csv_header (only once per column)
                    if (!$csv_header_complete) {
                        $csv_header .= '"'.$col.'",';
                    }
                    
                    //Add Data
                    $data = preg_replace('"\""', "'", $data);
                    $csv .= '"'.$data.'",';                    
                }
            } // next col
            
            if (!$csv_header_complete) {
                $csv_header = substr($csv_header, 0, -1)."\n";
                $csv_header_complete = true;
            }
            
            $csv = substr($csv, 0, -1) . "\n";
            
            $rs->MoveNext();
        } //next row
        
        return $csv_header.$csv;
    }
    
    /*
     function convert_rs_xls(recordset, workbook, worksheet) null
     A generic function to convert a ADODB recordset into a Excel XLS
     mReschke 2011-05-07
    */    
    public static function convert_rs_xls($rs, &$workbook, &$worksheet) {
        //Set Formats
        $format_bold =& $workbook->addFormat();
        $format_bold->setBold();

        //Freeze Panes
        $worksheet->freezePanes(array(1,0)); //Freeze header
        #$worksheet->freezePanes(array(1, 1)); //Freeze header and first column

        $header_complete = false;
        while (!$rs->EOF) {
            if (!isset($keys)) $keys = array_keys($rs->fields); //column names
            $row = array();
            $col_num = 0;
            for ($i = 1; $i < $rs->_numOfFields * 2; $i += 2) {
                // +=2 because ADODB fields contains a named ass array and int ass array, so double values one by name, one by number, I just want by name
                $col = $keys[$i];
                $data = $rs->fields[$col];
                
                //Add Header data
                if (!$header_complete) {
                    $worksheet->write(0, $col_num, $col, $format_bold);
                }
                
                //Write Data
                $worksheet->write($rs->_currentRow+1, $col_num, $data);
                $col_num ++;
            } //next col
            
            if (!$header_complete) $header_complete = true; //So only true for first row loop
            
            $rs->MoveNext();
        } //next row
        
    }
    
    /*
     function convert_datatables_get_to_var(...)
     Helps my datatables ajax functions map all the $_GET vars into regular vars/arrays to be used by my model get_datatables_sql functions
     mReschke 2011-04-10
    */
    public static function convert_datatables_get_to_var(&$search, &$search_col, &$sort_col, &$sort_col_dir, &$limit_start, &$limit_len) {
        //Notice all parameters are referenced, so they are the return
        //All datatables ajaxed $_GET variables should be accessible here
        
        /*
        Uses $_GET variables to pass information
        $_GET['iColumns'] is count if visible columns
        $_GET['iSortingColumns'] number of columns being sorted
        Current page is $_GET['iDisplayStart'] which is page-1 * page size
        Page length is $_GET['iDisplayLength']
        Master search text is $_GET['sSearch'];
        Each column search is $_GET['sSearch_x'] where x is column number, starting at 0
        First sorted column is $_GET['iSortCol_0'] (=x where x is the first column sorted, starts at 0);
          $_GET['sSortDir_0']=asc or desc is first column direction
        Second sorted column is $_GET['iSortCol_1'] (=x where x is the column number starting at 0)
          ...
        $_GET['iSortCol_0'] = 1;
        */        
        
        //Prepare SQL Where (Global Search)
        $search = $_GET['sSearch'];
        
        //Prepare SQL Where (Column Search)
        $search_col = null; //associative arrray [num] = value
        for ($i=0; $i < intval($_GET['iColumns']); $i++) {
            if ($_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '') {
                //Datatables column is searchable, and is being searched
                $search_col[$i] = $_GET['sSearch_'.$i];
            }
        }
        
        //Prepare SQL Sorting
        //NOTE: this $sort_col is an ID, in order as they appear on the grid, 0, 1, 2...NOT the same as my $col class db_id field
        $sort_col = array(); //order as appears on grid, not same as $col->col_id
        $sort_col_dir = array(); //direction of coresponding $sort_col_num column (ASC, DESC)
        if (isset($_GET['iSortCol_0'])) {
            for ($i=0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_'.intval($_GET['iSortCol_'.$i])] == "true") {
                    //Datatables column is sortable, and is set to sort
                    $sort_col[] = intval($_GET['iSortCol_'.$i]);
                    $sort_col_dir[] = $_GET['sSortDir_'.$i];
                }
            }
        }

        //Prepare SQL Limits
        $limit_start = null;
        if (isset($_GET['iDisplayStart'])) {
            $limit_start = $_GET['iDisplayStart'];
        }
        
        $limit_len = -1; //-1=no limit (show all records)
        if (isset($_GET['iDisplayLength'])) {
            $limit_len = $_GET['iDisplayLength'];
        }        
        
    }
    
    

}