<?php
$search = new Search;

/*
 class Search
 All view work functions (non model)
 mReschke 2010-09-04
*/
class Search {
    public $query;      //Query String
    public $Query;      //Query Array or Words
    public $badges;     //Badge Names String
    public $badge_ids;  //Badge IDs String
    public $Badges;     //Badge Names Array
    public $Badge_ids;  //Badge IDs Array
    public $tags;       //Tag Names String
    public $tag_ids;    //Tag IDs String
    public $Tags;       //Tag Names Array
    public $Tag_ids;    //Tag IDs Array
    public $options;    //Options String
    public $Options;    //Options Array
    
    /*
     function convert_string_array
     Used for search.code.php URLs
     Takes a $item like 'LINUX,DEVELOPMENT' and create 3 things from it by REFERENCE
     1. $Item: an array of Names so (LINUX and DEVELOPMENT);
     2. $item_ids: A string of IDS (converting badge/tag names to ids) arrays, so '1,3'
     3. $Item_ids: an array of IDS
     $type can be 'badge' or 'tag'
     mReschke 2010-09-23
    */
    public static function convert_string_array($item, &$Item, &$item_ids, &$Item_ids, $type) {
        //Get Badges Array (Names)
        $splitchar = '';
        if (stristr($item, ";")) {
            $Item = explode(";", $item); //Names
            $item .= ";"; //Temp for eregi;
            $splitchar = ';';
        } else {
            $Item = explode(",", $item); //Names
            $item .= ","; //Temp for eregi
            $splitchar = ',';
        }
        
        //Get Badges Array & String (IDS)
        $Item_ids = array();
        $item_ids = $item;
        foreach ($Item as $name) {
            if ($type == 'badge') {
                $id = Tbl_badge::get_badge_id($name);    
            } elseif ($type == 'tag') {
                $id = Tbl_tag::get_tag_id($name);
            }
            $Item_ids[] = $id;
            #$item_ids = eregi_replace($name.$splitchar, $id.$splitchar, $item_ids);    
			$item_ids = preg_replace('"'.$name.$splitchar.'"', $id.$splitchar, $item_ids);
        }
        $item_ids = substr($item_ids, 0, -1);
        
    }
}
