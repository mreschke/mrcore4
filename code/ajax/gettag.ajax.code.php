<?php
mysql_pconnect(Config::DB_SERVER, Config::DB_USER, Config::DB_PASS) or die("Could not connect");
mysql_select_db(Config::DB_NAME) or die("Could not select database");
 
$query = sprintf("SELECT tag_id as id, tag as name from tbl_tag_item WHERE tag LIKE '%%%s%%' ORDER BY tag DESC LIMIT 10", mysql_real_escape_string($_GET["q"]));
$arr = array();
$rs = mysql_query($query);

while($obj = mysql_fetch_object($rs))
{
    $arr[] = $obj;
}

echo json_encode($arr);