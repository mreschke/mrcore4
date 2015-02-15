<?
require_once 'adodb.php';


/*
 class Tbl_perm_item
 Database Layer for db.tbl_perm_item and db.tbl_perm_link
 mReschke 2010-08-26
*/
class Tbl_perm {
    public $perm;
    public $short;
    public $description;
    public $perm_group_id;
    public $group;
    public $group_description;
    public $selected;
    
    
    
    /*
     function get_perm_groups_array($user_id) array of perm groups
     Gets an array of perm group IDs linked to this $user_id
     This should be saved once into the $info session under $perm_groups
     mReschke 2010-09-02
    */
    public static function get_perm_groups_array($user_id) {
        try {
            $db = ADODB::connect();
            $query = "
                SELECT
                    DISTINCT pi.perm_group_id, pi.group
                FROM
                    tbl_perm_group_link pl
                    INNER JOIN tbl_perm_group_item pi on pl.perm_group_id = pi.perm_group_id
                WHERE
                    pl.user_id = $user_id
            ";
            $rs = $db->Execute($query);
            $perm_groups = array();
            while (!$rs->EOF) {
                $perm_groups[$rs->fields['group']] = $rs->fields['perm_group_id'];
                $rs->MoveNext();
            }
            return $perm_groups;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                
    }
    
    /*
     function get_groups($user_id=0) array tbl_perm_group_item
     Gets all perm groups
     If $user_id > 0 then show all perm groups with a link flag if group is linked to $user_id
     mReschke 2010-09-22
    */
    public static function get_groups($user_id=0) {
        try {
            $db = ADODB::connect();
            if ($user_id > 0) {
                $query = "
                    SELECT
                        pgi.*,
                        (
                            SELECT user_id
                            FROM tbl_perm_group_link pgl
                            WHERE pgl.perm_group_id = pgi.perm_group_id ANd user_id = $user_id
                        ) as selected
                    FROM
                        tbl_perm_group_item pgi
                ";
            } else {
                $query = "
                    SELECT
                        pgi.*
                    FROM
                        tbl_perm_group_item pgi
                ";            
            }
            $rs = $db->Execute($query);
            $groups = array();
            while (!$rs->EOF) {
                $group = new Tbl_perm;
                $group->perm_group_id = $rs->fields['perm_group_id'];
                $group->group = $rs->fields['group'];
                $group->group_description = $rs->fields['description'];
                if ($user_id > 0) $group->selected = $rs->fields['selected'];
                $groups[] = $group;
                $rs->MoveNext();
            }
            return $groups;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }      
    }

    /*
     function get_perm_groups_short_display($topic_id)
     Gets a concat version of groupname/permshort,permshort... for this topic
     Used for display in topic_org and search results
     Result is 2 column array, group and short
     mReschke 2010-09-30
    */
    public static function get_perm_groups_short_display($topic_id) {
        try {
            $db = ADODB::connect();
            if ($topic_id > 0) {
                $query = "
                    SELECT
                        pgi.group,
                        group_concat(left(pi.short,1) separator ',') as short
                    FROM
                        tbl_perm_link pl
                        INNER JOIN tbl_perm_item pi on pl.perm_id = pi.perm_id
                        INNER JOIN tbl_perm_group_item pgi on pl.perm_group_id = pgi.perm_group_id
                    WHERE
                        pl.topic_id = $topic_id
                    GROUP BY pgi.group
                    ORDER BY pgi.group
                ";
            }
            $rs = $db->Execute($query);
            $groups = array();
            while (!$rs->EOF) {
                $group = new Tbl_perm;
                $group->group = $rs->fields['group'];
                $group->short = $rs->fields['short'];
                $groups[] = $group;
                $rs->MoveNext();
            }
            return $groups;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    /*
     function update_perm_group($perm_group_id, $group_name, $group_description) group_id or -1 if exists
     Updates one perm_group_item
     Returns the same group_id, unless you tried to update to a group name that already exists, if so, returns -1
     mReschke 2010-09-23
    */
    public static function update_perm_group($perm_group_id, $group_name, $group_description) {
        try {
            if ($perm_group_id > 0) {
                $db = ADODB::connect();

                //Check if group exists (when cant change grou name to existing users)
                $query = "SELECT perm_group_id FROM tbl_perm_group_item WHERE `group`='".ADODB::dbclean($group_name)."' AND perm_group_id <> ".ADODB::dbclean($perm_group_id);
                $row = $db->GetRow($query);
                if (isset($row['perm_group_id'])) {
                    //Error: Tried to update to a group name that already exists
                    return -1;
                }
                
                $query = "
                    UPDATE tbl_perm_group_item SET
                        `group`='".ADODB::dbclean($group_name)."',
                        `description`='".ADODB::dbclean($group_description)."'
                    WHERE
                        perm_group_id = $perm_group_id
                ";
                $rs = $db->Execute($query);
                return $perm_group_id;
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }  
    }
    
    /*
     function insert_perm_group($group_name, $group_description) new perm_group_id or -1 if exists
     Inserts one perm_group_item
     mReschke 2010-09-23
    */
    public static function insert_perm_group($group_name, $group_description)  {
        try {
            $db = ADODB::connect();
            
            //Check if exists
            $count = 0;
            $query = "SELECT count(*) as cnt FROM tbl_perm_group_item WHERE `group`='".mysql_escape_string($group_name)."'";
            $row = $db->GetRow($query);
            if (isset($row['cnt'])) {
                $count = $row['cnt'];
            }
            
            if ($count == 0) {
                $query = "
                    INSERT INTO tbl_perm_group_item
                    (`group`,`description`)
                    VALUES (
                        '".ADODB::dbclean($group_name)."',
                        '".ADODB::dbclean($group_description)."'
                    )
                ";
                $rs = $db->Execute($query);
                $perm_group_id = $db->Insert_ID();
            } else {
                $perm_group_id = -1; //Group already exists
            }
            return $perm_group_id;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }   
    }    
    
    /*
     function delete_perm_group($perm_group_id) null
     Deletes one perm_group_item
     mReschke 2010-09-23
    */
    public static function delete_perm_group($perm_group_id) {
        try {
            $db = ADODB::connect();
            if ($perm_group_id > 0) {
                $query = "DELETE FROM tbl_perm_group_item WHERE perm_group_id=".ADODB::dbclean($perm_group_id);
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }  
    }
    
    /*
     function delete_perm_links($topic_id, array $perm_group_ids) null
     delete all perm_links for this topic AND array of $perm_group_ids
     mReschke 2010-09-08
    */
    public static function delete_perm_links($topic_id, $perm_group_ids) {
        try {
            if ($topic_id > 0 && count($perm_group_ids) > 0) {
                $db = ADODB::connect();
                $query = "DELETE FROM tbl_perm_link WHERE topic_id=$topic_id AND perm_group_id IN (".implode(",", $perm_group_ids).")";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }   
    }
    
    /*
     function delete_all_perm_links($topic_id, array $perm_group_ids) null
     delete all perm_links for this topic (makes it completely private)
     mReschke 2013-10-12
    */
    public static function delete_all_perm_links($topic_id) {
        try {
            if ($topic_id > 0) {
                $db = ADODB::connect();
                $query = "DELETE FROM tbl_perm_link WHERE topic_id=$topic_id";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }   
    }

    /*
     function delete_perm_group_links($user_id) null
     Delete all perm_group_links for this $user_id
     mReschke 2010-09-22
    */
    public static function delete_perm_group_links($user_id) {
        try {
            if ($user_id > 0) {
                $db = ADODB::connect();
                $query = "DELETE FROM tbl_perm_group_link WHERE user_id=$user_id";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }   
    }
    
    /*
     function insert_perm_link($topic_id, $perm_group_id, $perm_id)
     insert one perm_link into tbl_perm_link
     mReschke 2010-09-08
    */
    public static function insert_perm_link($topic_id, $perm_group_id, $perm_id) {
        try {
            if ($topic_id > 0 && $perm_group_id > 0 && $perm_id > 0) {
                $db = ADODB::connect();
                $query = "
                    INSERT INTO tbl_perm_link
                    (topic_id, perm_group_id, perm_id)
                    VALUES (
                        ".ADODB::dbclean($topic_id).",
                        ".ADODB::dbclean($perm_group_id).",
                        ".ADODB::dbclean($perm_id)."
                    )
                ";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }       
    }
    
    /*
     function insert_perm_group_link($user_id, $perm_group_id) null
     Insert one perm_group_link into tbl_perm_group_link for this $user_id/$perm_group_id
     mReschke 2010-09-22
    */
    public static function insert_perm_group_link($user_id, $perm_group_id) {
        try {
            if ($user_id > 0 && $perm_group_id) {
                $db = ADODB::connect();
                $query = "
                    INSERT INTO tbl_perm_group_link
                    (user_id, perm_group_id)
                    VALUES (
                        ".ADODB::dbclean($user_id).",
                        ".ADODB::dbclean($perm_group_id)."
                    )
                ";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }    
    }
    
    
    /*
     function get_permissions($info_class, $topic_id) array of perm shorts
     Gets an array of perm shorts that this user has on this topic
     If user is admin, always shows all shorts (so full perms)
     mReschke 2010-09-02
    */
    public static function get_permissions(Info $info, $topic_id) {
        try {
            $db = ADODB::connect();
            if ($info->admin || $topic_id == 0) {
                //Admin gets all perms
                $query = "SELECT short FROM tbl_perm_item ORDER BY perm_id";
            } else {
                $query = "
                    SELECT
                        DISTINCT pi.short
                    FROM
                        tbl_perm_link pl
                        INNER JOIN tbl_perm_item pi on pl.perm_id = pi.perm_id
                    WHERE
                        pl.topic_id = $topic_id
                        AND perm_group_id in (".implode(",",$info->perm_groups).")
                    ORDER BY
                        pi.perm_id
                ";
            }
            $perms = array();
            if ($query) {
                $rs = $db->Execute($query);
                while (!$rs->EOF) {
                    $perms[] = $rs->fields['short'];
                    $rs->MoveNext();
                }
            }
            return $perms;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }       
    }

}

class Tbl_perm_custom_all_selected {
    public $perm_group_id;
    public $group_name;
    public $group_description;
    public $perm_id;
    public $short;
    public $perm_description;
    public $selected;
    
    /*
     function get_perms_with_links($topic_id, $user_id) array of Tbl_perm_item
     Get permissions/groups has accesss too with a flag column (selected) set if perm is linked to given $topic_id
     mReschke 2010-08-26
    */
    public static function get_groups_and_perms_with_links($topic_id, $user_id) {
        try {
            $db = ADODB::connect();
            if ($topic_id == '') $topic_id = 0;
            //Get ALL groups, and ALL perms, with a selected flag if perm/group linked to current $topic_id
            //Very tricky procedure, becuase it lists each perm once for each group
            //So I join perm_group_id on itself
            $rs = $db->Execute("
                SELECT
                    pgi.perm_group_id,
                    pgi.group as group_name,
                    pgi.description as group_description,
                    pi.perm_id,
                    pi.short,
                    pi.description as perm_description,
                    CASE WHEN pl.topic_id IS NULL THEN 0 ELSE 1 END as selected
                FROM
                    tbl_perm_group_item pgi
                    INNER JOIN tbl_perm_group_link pgl on pgi.perm_group_id = pgl.perm_group_id                    
                    LEFT OUTER JOIN tbl_perm_item pi on pgi.perm_group_id = pgi.perm_group_id
                    LEFT OUTER JOIN tbl_perm_link pl on pi.perm_id = pl.perm_id
                        AND pgi.perm_group_id = pl.perm_group_id
                        AND pl.topic_id=$topic_id
                WHERE
                    pgl.user_id = $user_id
                ORDER BY
                    pgi.perm_group_id, pi.perm_id
            ");
            $perm_items = array();
            while (!$rs->EOF) {
                $perm_item = new Tbl_perm;
                $perm_item->perm_group_id = $rs->fields['perm_group_id'];
                $perm_item->group_name = $rs->fields['group_name'];
                $perm_item->group_description = $rs->fields['group_description'];
                $perm_item->perm_id = $rs->fields['perm_id'];
                $perm_item->short = $rs->fields['short'];
                $perm_item->perm_description = $rs->fields['perm_description'];
                $perm_item->selected = $rs->fields['selected'];
                $perm_items[] = $perm_item; //add this item to array of items
                $rs->MoveNext();
            }
            return $perm_items;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }      
    }
}
