<?php
require_once 'adodb.php';


/*
 class Tbl_badge
 Database Layer for db.Tbl_badge and db.tbl_badge_link
 mReschke 2010-08-24
*/
class Tbl_badge {
    public $badge_id;
    public $badge;
    public $image;
    public $default_topic_id;
    public $topic_count;
    public $selected;
    
    /*
     function get_badge($badge_id int) Tbl_badge
     Gets a single badge from db.Tbl_badge
     mReschke 2010-08-24
    */
    public static function get_badge($badge_id) {
        try {
            $db = ADODB::connect();
            $row = $db->GetRow("SELECT * FROM tbl_badge_item WHERE badge_id=$badge_id");
            $badge_item = new Tbl_badge;
            if (isset($row['badge_id'])) {
                $badge_item->badge_id = $row['badge_id'];
                $badge_item->badge= $row['badge'];
                $badge_item->image = $row['image'];
                $badge_item->default_topic_id = $row['default_topic_id'];
                $badge_item->topic_count = $row['topic_count'];
            }
            return $badge_item;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        } 
    }
    
    /*
     function get_badge_id($badge_name)
     Gets the badge_id from the badge name
     mReschke 2010-09-23
    */
    public static function get_badge_id($badge_name) {
        try {
            $db = ADODB::connect();
            $row = $db->GetRow("SELECT badge_id FROM tbl_badge_item WHERE badge='$badge_name'");
            $badge_id = 0;
            if (isset($row['badge_id'])) {
                $badge_id = $row['badge_id'];
            }
            return $badge_id;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        } 
    }
    
    /*
     function get_badges_array($topic_id)
     Gets simple array of badges (NAMES) in this topic_id
     mReschke 2010-09-09
    */
    public static function get_badges_array($topic_id) {
        try {
            $db = ADODB::connect();
            $query = "
                SELECT
                    badge
                FROM
                    tbl_badge_link bl
                    INNER JOIN tbl_badge_item bi on bl.badge_id = bi.badge_id
                WHERE
                    bl.topic_id=$topic_id
            ";
            $rs = $db->Execute($query);
            $badges = array();
            while (!$rs->EOF) {
                $badges[] = $rs->fields['badge'];
                $rs->MoveNext();
            }
            return $badges;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }   
    }
    

    /*
     function get_badges($user_id, $perm_admin, $topic_id=0) array of Tbl_badge
     Gets all badges from db.Tbl_badge or only badges in this topic_id
     $user_id is used for the badge total topic_count, it only counts topics YOU have access too
     If $perm_admin, dont bother counting topics by permission, just use the topic_count column
     mReschke 2010-08-24
    */
    public static function get_badges(Info $info, $topic_id=0) {
        try {
            //NOTE: If you are super admin, dont bother joining in perm tables to get accurate topic_count by permission
            //Since you are super, you can read ALL articles, so just use the topic_count column in tbl_badge_item
            $db = ADODB::connect();
            if ($topic_id > 0) {
                //Get badges in this topoic

                //NOTICE: this is no wrong (the tbl_badge_item.topic_count column) kinda, because some topis are hidden
                //So Badge SITE may say 20 items, but 18 are hidden, kind of misleading
                if ($info->admin) {
                    //This does not take into account permissions on topic_count, becuase Im admin
                    $query = "
                        SELECT
                            bi.*
                        FROM
                            tbl_badge_link bl
                            INNER JOIN tbl_badge_item bi on bl.badge_id = bi.badge_id
                        WHERE
                            bl.topic_id=$topic_id
                        ORDER BY
                            bi.badge ASC
                    ";
                } else {
                    //This uses a true topic count, taking into account your permissions
                    $query = "
                        SELECT
                            mainbi.badge_id,
                            mainbi.badge,
                            mainbi.image,
                            (
                                SELECT count(DISTINCT t.topic_id) FROM tbl_topic t
                                LEFT OUTER JOIN tbl_badge_link bl on t.topic_id = bl.topic_id
                                LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id
                                LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id
                                WHERE t.deleted=0 AND t.hidden=0 AND bl.badge_id = mainbi.badge_id AND ((pl.perm_id = ".Config::STATIC_PERM_READ." AND pgl.user_id = ".$info->user_id.") or t.created_by = ".$info->user_id.")
                            ) as topic_count
                        FROM
                            tbl_badge_link mainbl
                            INNER JOIN tbl_badge_item mainbi on mainbl.badge_id = mainbi.badge_id
                        WHERE
                            mainbl.topic_id = $topic_id
                        ORDER BY
                            badge
                    ";
                }
            } else {
                //Get all badges
                if ($info->admin) {
                    //This does not take into account permissions on topic_count, becuase Im admin
                    $query =  "SELECT * FROM tbl_badge_item ORDER BY badge ASC";
                } else {
                    //This is a true topic count, taking into account your permissions
                    //IF you can read the article, it counts as one
                    $query = "
                        SELECT
                            mainbi.badge_id,
                            mainbi.badge,
                            mainbi.image,
                            (
                                SELECT count(DISTINCT t.topic_id) FROM tbl_topic t
                                LEFT OUTER JOIN tbl_badge_link bl on t.topic_id = bl.topic_id
                                LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id
                                LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id
                                WHERE t.deleted=0 AND t.hidden = 0 AND bl.badge_id = mainbi.badge_id AND ((pl.perm_id = ".Config::STATIC_PERM_READ." AND pgl.user_id = $info->user_id) or t.created_by = $info->user_id)
                            ) as topic_count
                        FROM
                            tbl_badge_item mainbi
                        WHERE
                            topic_count IS NOT NULL
                        ORDER BY
                            mainbi.badge ASC
                    ";                                    
                }
            }

            if ($info->admin) {
                //Admins just use the topic_count column, which does NOT reflect hidden topics.
                //So if admin, subtract all hidden topics of this badge from the total count
                #$subquery = "SELECT"??????????????????????
            }
            $rs = $db->Execute($query);
            $badge_items = array();
            while (!$rs->EOF) {
                #print $rs->fields[0].' '.$rs->fields['first_name'].'<BR>';
                $badge_item = new Tbl_badge;
                $badge_item->badge_id = $rs->fields['badge_id'];
                $badge_item->badge = $rs->fields['badge'];
                $badge_item->image = $rs->fields['image'];
                $badge_item->default_topic_id = $rs->fields['default_topic_id'];
                $badge_item->topic_count = $rs->fields['topic_count'];
                $badge_items[] = $badge_item; //add this badge_item to array of Tbl_badge
                $rs->MoveNext();
            }
            return $badge_items;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }      
    }
    
    /*
     function get_related_badges($user_id, $perm_admin, $badges, $tags) array of tbl_badge_item
     Gets all tags that are linked to the topics that are linked to $badges & $tags
     $badges and $tags arrays of IDS
     Complicated, but used for the menu, when you click a badge/tag, it searches all topics with that badge/tag
     And the menu shows all badges/tags in the topics listed below the main badge/tag you clicked, hard to explain, but awesome!
     mReschke 2010-09-04
    */
    public static function get_related_badges(Info $info, $badges, $tags) {
        try {
            //NOTE: If you are super admin, dont bother joining in perm tables to get accurate topic_count by permission
            //Since you are super, you can read ALL articles, so just use the topic_count column in tbl_badge_item
            $badges_exist = false;
            $tags_exist = false;
            if ($badges[0]  != '' && $badges[0] != '*') $badges_exist = true;
            if ($tags[0]  != '' && $tags[0] != '*') $tags_exist = true;
            if ($badges_exist) {
                $db = ADODB::connect();
                if ($info->admin) {
                    //This does not take into account permissions on topic_count, becuase Im admin
                    $query = "
                        SELECT
                            DISTINCT bi.*
                        FROM
                            tbl_badge_link bl
                            LEFT OUTER JOIN tbl_badge_item bi on bl.badge_id = bi.badge_id
                        WHERE ";
                        if ($badges_exist) {
                            $query .= "
                            bl.topic_id IN (
                                SELECT
                                    bl.topic_id
                                FROM
                                    tbl_badge_link bl
                                WHERE
                                    bl.badge_id IN (".implode(",", $badges).") 
                                GROUP BY
                                    bl.topic_id
                                HAVING 
                                    count(DISTINCT bl.badge_id) = ".count($badges)."
                            ) ";
                        }
                        if ($tags_exist) {
                            if ($badges_exist) $query .= "AND ";
                            $query .= "
                            bl.topic_id IN (
                                SELECT
                                    tl.topic_id
                                FROM
                                    tbl_tag_link tl
                                WHERE
                                    tl.tag_id IN (".implode(",", $tags).") 
                                GROUP BY
                                    tl.topic_id
                                HAVING 
                                    count(DISTINCT tl.tag_id) = ".count($tags)."
                            ) ";
                        }
                        if ($badges_exist) {
                            $query .= "
                            AND
                            bl.badge_id NOT IN (".implode(",", $badges).") ";
                        }
                        $query .= "ORDER BY bi.badge";
                } else {
                    //This uses a true topic count, taking into account your permissions
                    //Holy crap, this ones crazy!
                    $query = "
                        SELECT  DISTINCT
                            mainbi.badge_id,
                            mainbi.badge,
                            mainbi.image ";
                            /* I took counts off because this is total count, not filtered count
                            (
                                SELECT count(DISTINCT t.topic_id) FROM
                                tbl_topic t LEFT OUTER JOIN tbl_badge_link bl on t.topic_id = bl.topic_id
                                LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id
                                LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id
                                WHERE bl.badge_id = mainbi.badge_id AND ((pl.perm_id = ".Config::STATIC_PERM_READ." AND pgl.user_id = $user_id) or t.created_by = $user_id)
                            ) as topic_count*/
                        $query .= "FROM
                            tbl_badge_link mainbl
                            INNER JOIN tbl_badge_item mainbi on mainbl.badge_id = mainbi.badge_id
                        WHERE ";
                        if ($badges_exist) {
                            $query .= "
                            mainbl.topic_id IN (
                                SELECT
                                    bl.topic_id
                                FROM 
                                    tbl_topic t
                                    LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id and pl.perm_id = ".Config::STATIC_PERM_READ."
                                    LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id
                                    LEFT OUTER JOIN tbl_badge_link bl on t.topic_id = bl.topic_id
                                WHERE
                                    (pgl.user_id = $info->user_id OR t.created_by = $info->user_id) AND
                                    bl.badge_id IN (".implode(",", $badges).")
                                GROUP BY
                                    bl.topic_id
                                HAVING
                                    count(DISTINCT bl.badge_id) >= ".count($badges)."
                            )";
                        }
                        if ($tags_exist) {
                            if ($badges_exist) $query .= "AND ";
                            $query .="
                            mainbl.topic_id IN (
                                SELECT
                                    tl.topic_id
                                FROM 
                                    tbl_topic t
                                    LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id and pl.perm_id = ".Config::STATIC_PERM_READ."
                                    LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id
                                    LEFT OUTER JOIN tbl_tag_link tl on t.topic_id = tl.topic_id
                                WHERE
                                    (pgl.user_id = $info->user_id OR t.created_by = $info->user_id) AND
                                    tl.tag_id IN (".implode(",", $tags).")
                                GROUP BY
                                    tl.topic_id
                                HAVING
                                    count(DISTINCT tl.tag_id) >= ".count($tags)."
                            )";
                        }
                        if ($badges_exist) {
                            $query .="
                            AND
                            mainbl.badge_id NOT IN (".implode(",", $badges).") ";
                        }
                        $query .= "ORDER BY mainbi.badge";
                }
                #echo "<br /><br />$query<br /><br />";
                $rs = $db->Execute($query);
                $badge_items = array();
                while (!$rs->EOF) {
                    #print $rs->fields[0].' '.$rs->fields['first_name'].'<BR>';
                    $badge_item = new Tbl_badge;
                    $badge_item->badge_id = $rs->fields['badge_id'];
                    $badge_item->badge = $rs->fields['badge'];
                    $badge_item->image = $rs->fields['image'];
                    #$badge_item->default_topic_id = $rs->fields['default_topic_id']; //not needed in results
                    #$badge_item->topic_count = $rs->fields['topic_count']; //not needed in results
                    $badge_items[] = $badge_item; //add this badge_item to array of Tbl_badge
                    $rs->MoveNext();
                }
                return $badge_items;
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }        
    }
    
    
    
    /*
     function get_badges_with_links($topic_id) array of Tbl_badge
     Gets all badges, with a flag column (selected) set if badge is linked to given $topic_id
     mReschke 2010-08-26
    */
    public static function get_badges_with_links($topic_id) {
        try {
            $db = ADODB::connect();
            if ($topic_id == '') $topic_id = 0;
            //Get badges in this topoic
            $rs = $db->Execute("
                SELECT
                    bi.*,
                    CASE WHEN bl.topic_id IS NULL THEN 0 ELSE 1 END as selected
                FROM
                    tbl_badge_item bi
                    LEFT OUTER JOIN tbl_badge_link bl on bi.badge_id = bl.badge_id AND bl.topic_id=$topic_id
                ORDER BY
                    selected DESC, bi.badge ASC
            ");
            $badge_items = array();
            while (!$rs->EOF) {
                $badge_item = new Tbl_badge;
                $badge_item->badge_id = $rs->fields['badge_id'];
                $badge_item->badge = $rs->fields['badge'];
                $badge_item->image = $rs->fields['image'];
                $badge_item->topic_count = $rs->fields['topic_count'];
                $badge_item->selected = $rs->fields['selected'];
                $badge_items[] = $badge_item; //add this badge_item to array of Tbl_badge
                $rs->MoveNext();
            }
            return $badge_items;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }        
    }
    
    /*
     function delete_badge_links($topic_id) null
     Deletes ALL badges from this topic (and subtracts from topic_count)
     mReschke 2010-08-29
    */
    public static function delete_badge_links($topic_id) {
        try {
            $db = ADODB::connect();
            
            //Subtract topic_count for each badge
            $query = "
                UPDATE tbl_badge_item SET topic_count = topic_count - 1 WHERE badge_id IN (
                    SELECT badge_id FROM tbl_badge_link WHERE topic_id = $topic_id
                )
            ";
            $rs = $db->Execute($query);
            
            //Delete all badge links for this topic
            $query = "DELETE FROM tbl_badge_link WHERE topic_id=$topic_id";
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    
    /*
     function insert_badge_link($topic_id, $badge_id) null
     Inserts one topic/badge link into tbl_badge_link (and adds to topic_count)
     mReschke 2010-08-29
    */
    public static function insert_badge_link($topic_id, $badge_id) {
        try {
            if ($topic_id > 0 && $badge_id > 0) {
                //Insert topic/badge link
                $db = ADODB::connect();
                $query = "INSERT INTO tbl_badge_link (topic_id, badge_id) VALUES ($topic_id, $badge_id)";
                $rs = $db->Execute($query);
                
                //Add one to topic_count
                Tbl_badge::update_topic_count($badge_id, 1);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    /*
     function update_topic_count($badge_id, $num)
     Updates this badges topic_count ($num can be 1 or -1)
     mReschke 2010-09-09
    */
    public static function update_topic_count($badge_id, $num) {
        try {
            $db = ADODB::connect();
            $query = "UPDATE tbl_badge_item SET topic_count = topic_count + $num WHERE badge_id = $badge_id";
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    /*
     function update_badge($badge_id, $badge_name, $image, $default_topic_id, $topic_count) badge_id or -1 if exists
     Updates one badge_item
     Returns the same badge_id, unless you tried to update to a badge name that already exists, if so, returns -1
     mReschke 2010-09-23
    */
    public static function update_badge($badge_id, $badge_name, $image, $default_topic_id, $topic_count) {
        try {
            if ($badge_id > 0) {
                $db = ADODB::connect();

                //Clean Badge Name
                $badge_name = ADODB::dbclean(Badge::clean_badge($badge_name));

                //Check if group exists (when cant change grou name to existing users)
                $query = "SELECT badge_id FROM tbl_badge_item WHERE badge='$badge_name' AND badge_id <> ".ADODB::dbclean($badge_id);
                $row = $db->GetRow($query);
                if (isset($row['badge_id'])) {
                    //Error: Tried to update to a badge name that already exists
                    return -1;
                }
                
                if (!$default_topic_id || $default_topic_id == 0) $default_topic_id = 'null';
                
                $query = "
                    UPDATE tbl_badge_item SET
                        badge='$badge_name',
                        image='".ADODB::dbclean($image)."',
                        default_topic_id=".ADODB::dbclean($default_topic_id);
                    if ($topic_count) {
                        $query .= ",topic_count=".ADODB::dbclean($topic_count);
                    }
                    $query .= " WHERE
                        badge_id = $badge_id
                ";
                $rs = $db->Execute($query);
                return $badge_id;
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }  
    }
    
    /*
     function update_badge_image($badge_id, $image) null
     Updates the badge image filename
     mReschke 2010-09-23
    */
    public static function update_badge_image($badge_id, $image) {
        try {
            if ($badge_id > 0 && $image != '') {
                $db = ADODB::connect();
                $query = "UPDATE tbl_badge_item SET image='".ADODB::dbclean($image)."' WHERE badge_id=$badge_id";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    
    /*
     function insert_badge($badge_name, $image, $default_topic_id, $topic_count) new badge_id or -1 if exists
     Inserts one badge
     mReschke 2010-09-23
    */
    public static function insert_badge($badge_name, $image, $default_topic_id, $topic_count)  {
        try {
            $db = ADODB::connect();

            //Clean Badge Name
            $badge_name = ADODB::dbclean(Badge::clean_badge($badge_name));

            //Check if exists
            $count = 0;
            $query = "SELECT count(*) as cnt FROM tbl_badge_item WHERE badge='$badge_name'";
            $row = $db->GetRow($query);
            if (isset($row['cnt'])) {
                $count = $row['cnt'];
            }
            
            if (!$default_topic_id || $default_topic_id == 0) $default_topic_id = 'null';
            
            if ($count == 0) {
                $query = "
                    INSERT INTO tbl_badge_item
                    (badge, image, default_topic_id, topic_count)
                    VALUES (
                        '$badge_name',
                        '".ADODB::dbclean($image)."',
                        ".ADODB::dbclean($default_topic_id).",
                        ".ADODB::dbclean($topic_count)."
                    )
                ";
                $rs = $db->Execute($query);
                $badge_id = $db->Insert_ID();
            } else {
                $badge_id = -1; //Badge already exists
            }
            return $badge_id;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }  
    }
    
    /*
     function delete_badge($badge_id)
     Deletes one badge (and badge image), will cascade delete all badge links
     mReschke 2010-09-26
    */
    public static function delete_badge($badge_id) {
        try {
            if ($badge_id > 0) {
                $tbl_badge = Tbl_badge::get_badge($badge_id);

                $db = ADODB::connect();
                $query = "DELETE FROM tbl_badge_item WHERE badge_id=$badge_id";
                $rs = $db->Execute($query);

                //Delete Image
                if ($tbl_badge->image) {
                    if (file_exists(Page::get_abs_base().'/web/image/'.$tbl_badge->image)) {
                        unlink(Page::get_abs_base().'/web/image/'.$tbl_badge->image);
                    }
                }

            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
}