<?php
require_once 'adodb.php';


/*
 class Tbl_tag
 Database Layer for db.Tbl_tag an ddb.tbl_tag_link
 mReschke 2010-08-24
*/
class Tbl_tag {
    public $tag_id;
    public $tag;
    public $image;
    public $default_topic_id;
    public $topic_count;
    public $selected;
    
    /*
     function get_tag($name_or_id int or string) Tbl_tag
     Gets a single tag from db.Tbl_tag
     mReschke 2010-08-24
    */
    public static function get_tag($name_or_id) {
        try {
            $db = ADODB::connect();
            if (is_numeric($name_or_id)) {
                $row = $db->GetRow("SELECT * FROM tbl_tag_item WHERE tag_id=$name_or_id");
            } else {
                $row = $db->GetRow("SELECT * FROM tbl_tag_item WHERE tag='$name_or_id'");
            }            
            $tag_item = new Tbl_tag;
            if (isset($row['tag_id'])) {
                $tag_item->tag_id = $row['tag_id'];
                $tag_item->tag = $row['tag'];
                $tag_item->image = $row['image'];
                $tag_item->default_topic_id = $row['default_topic_id'];
                $tag_item->topic_count = $row['topic_count'];
            }
            return $tag_item;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }      
    }    
    
    /*
     function get_tag_id($tag_name)
     Gets the tag_id from the tag name
     mReschke 2010-09-23
    */
    public static function get_tag_id($tag_name) {
        try {
            $db = ADODB::connect();
            $row = $db->GetRow("SELECT tag_id FROM tbl_tag_item WHERE tag='$tag_name'");
            $tag_id = 0;
            if (isset($row['tag_id'])) {
                $tag_id = $row['tag_id'];
            }
            return $tag_id;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    
    /*
     function get_tags_array($topic_id)
     Gets simple array of tags (NAMES) in this topic_id
     mReschke 2010-09-09
    */
    public static function get_tags_array($topic_id) {
        try {
            $db = ADODB::connect();
            $query = "
                SELECT
                    tag
                FROM
                    tbl_tag_link tl
                    INNER JOIN tbl_tag_item ti on tl.tag_id = ti.tag_id
                WHERE
                    tl.topic_id=$topic_id
            ";
            $rs = $db->Execute($query);
            $tags = array();
            while (!$rs->EOF) {
                $tags[] = $rs->fields['tag'];
                $rs->MoveNext();
            }
            return $tags;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }    

    /*
     function get_tags($user_id, $topic_id=0) array of Tbl_tag
     Gets all tags from db.Tbl_tag
     If $topic_id > 0 then get all tags in this topic_id
     $user_id is used for the tag total topic_count, it only counts topics YOU have access too
     If $info->admin, dont bother counting topics by permission, just use the topic_count column
     mReschke 2010-08-24
    */
    public static function get_tags(Info $info, $topic_id=0) {
        try {
            //NOTE: If you are super admin, dont bother joining in perm tables to get accurate topic_count by permission
            //Since you are super, you can read ALL articles, so just use the topic_count column in tbl_badge_item            
            $db = ADODB::connect();
            if ($topic_id > 0) {
                //Get tags in this topoic
                if ($info->admin) {
                    //This does not take into account permissions on topic_count, becuase Im admin
                    $query = "
                        SELECT
                            ti.*
                        FROM
                            tbl_tag_link tl
                            INNER JOIN tbl_tag_item ti on tl.tag_id = ti.tag_id
                        WHERE
                            tl.topic_id=$topic_id
                        ORDER BY
                            ti.tag
                    ";
                } else {
                    //This uses a true topic count, taking into account your permissions
                    $query = "
                        SELECT
                            mainti.tag_id,
                            mainti.tag,
                            mainti.image,
                            mainti.default_topic_id,
                            (
                                SELECT count(DISTINCT t.topic_id) FROM
                                tbl_topic t LEFT OUTER JOIN tbl_tag_link tl on t.topic_id = tl.topic_id
                                LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id
                                LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id
                                WHERE t.deleted=0 AND tl.tag_id = mainti.tag_id AND ((pl.perm_id = ".Config::STATIC_PERM_READ." AND pgl.user_id = $info->user_id) or t.created_by = $info->user_id)
                            ) as topic_count
                        FROM
                            tbl_tag_link maintl
                            INNER JOIN tbl_tag_item mainti on maintl.tag_id = mainti.tag_id
                        WHERE
                            maintl.topic_id = $topic_id
                        ORDER BY
                            mainti.tag
                    ";                
                }
            } else {
                //Get all tags
                if ($info->admin) {
                    //This does not take into account permissions on topic_count, becuase Im admin
                    $query =  "SELECT * FROM tbl_tag_item ORDER BY tag";
                } else {
                    //This is a true topic count, taking into account your permissions
                    $query = "
                        SELECT
                            mainti.tag_id,
                            mainti.tag,
                            (
                                SELECT count(DISTINCT t.topic_id) FROM
                                tbl_topic t LEFT OUTER JOIN tbl_tag_link tl on t.topic_id = tl.topic_id
                                LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id
                                LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id
                                WHERE t.deleted=0 AND tl.tag_id = mainti.tag_id AND ((pl.perm_id = ".Config::STATIC_PERM_READ." AND pgl.user_id = $info->user_id) or t.created_by = $info->user_id)
                            ) as topic_count
                        FROM
                            tbl_tag_item mainti
                        ORDER BY
                            mainti.tag
                    ";
                }
            }
            $rs = $db->Execute($query);
            $tag_items = array();
            while (!$rs->EOF) {
                #print $rs->fields[0].' '.$rs->fields['first_name'].'<BR>';
                $tag_item = new Tbl_tag;
                $tag_item->tag_id = $rs->fields['tag_id'];
                $tag_item->tag = $rs->fields['tag'];
                $tag_item->image = $rs->fields['image'];
                $tag_item->default_topic_id = $rs->fields['default_topic_id'];
                $tag_item->topic_count = $rs->fields['topic_count'];
                $tag_items[] = $tag_item; //add this tag_item to array of Tbl_tag
                $rs->MoveNext();
            }
            return $tag_items;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }     
    }
    
    /*
    /*
     function get_related_tags($user_id, $badges, $tags) array of tbl_tag_item
     Gets all tags that are linked to the topics that are linked to $badges & $tags
     $badges and $tags arrays of IDS
     Complicated, but used for the menu, when you click a badge/tag, it searches all topics with that badge/tag
     And the menu shows all badges/tags in the topics listed below the main badge/tag you clicked, hard to explain, but awesome!
     mReschke 2010-09-04
    */
    public static function get_related_tags(Info $info, $badges, $tags) {
        try {
            //NOTE: If you are super admin, dont bother joining in perm tables to get accurate topic_count by permission
            //Since you are super, you can read ALL articles, so just use the topic_count column in tbl_badge_item
            $badges_exist = false;
            $tags_exist = false;
            if ($badges[0] != '' && $badges[0] != '*') $badges_exist = true;
            if ($tags[0] != '' && $tags[0] != '*') $tags_exist = true;
            if ($badges_exist) {
                $db = ADODB::connect();
                if ($info->admin) {
                    //This does not take into account permissions on topic_count, becuase Im admin
                    $query = "
                        SELECT
                                DISTINCT ti.*
                        FROM
                                tbl_tag_link tl
                                LEFT OUTER JOIN tbl_tag_item ti on tl.tag_id = ti.tag_id
                        WHERE ";
                        if ($tags_exist) {
                            $query .= "
                            tl.topic_id IN (
                                SELECT
                                    tl.topic_id
                                FROM
                                    tbl_tag_link tl
                                WHERE
                                    tl.tag_id IN (".implode(",", $tags).") 
                                GROUP BY
                                    tl.topic_id
                                HAVING 
                                    count(DISTINCT tl.tag_id) >= ".count($tags)."
                            ) ";
                        }
                        if ($badges_exist) {
                            if ($tags_exist) $query .= "AND ";
                            $query .= "
                            tl.topic_id IN (
                                SELECT
                                        bl.topic_id
                                FROM
                                        tbl_badge_link bl
                                WHERE
                                        bl.badge_id IN (".implode(",", $badges).") 
                                GROUP BY
                                        bl.topic_id
                                HAVING 
                                        count(DISTINCT bl.badge_id) >= ".count($badges)."
                            ) ";
                        }
                        if ($tags_exist) {
                            $query .= "
                            AND
                            tl.tag_id NOT IN (".implode(",", $tags).") ";
                        }
                        $query .= "ORDER BY ti.tag";
                } else {
                    //This uses a true topic count, taking into account your permissions
                    //Holy crap, this ones crazy!
                    $query = "
                        SELECT  DISTINCT
                            mainti.tag_id,
                            mainti.tag ";
                            /* I took counts off because this is total count, not filtered count
                            (
                                SELECT count(DISTINCT t.topic_id) FROM
                                tbl_topic t LEFT OUTER JOIN tbl_tag_link tl on t.topic_id = tl.topic_id
                                LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id
                                LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id
                                WHERE tl.tag_id = mainti.tag_id AND ((pl.perm_id = ".Config::STATIC_PERM_READ." AND pgl.user_id = $user_id) or t.created_by = $user_id)
                            ) as topic_count*/
                        $query .= "FROM
                            tbl_tag_link maintl
                            INNER JOIN tbl_tag_item mainti on maintl.tag_id = mainti.tag_id
                        WHERE ";
                        if ($tags_exist) {
                            $query .= "
                            maintl.topic_id IN (
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
                            ) ";
                        }
                        if ($badges_exist) {
                            if ($tags_exist) $query .= "AND ";
                            $query .="
                            maintl.topic_id IN (
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
                            ) ";
                        }
                        if ($tags_exist) {
                            $query .="
                            AND
                            maintl.tag_id NOT IN (".implode(",", $tags).")";
                        }
                        $query .= "ORDER BY mainti.tag";                    
                }
                $rs = $db->Execute($query);
                $tag_items = array();
                while (!$rs->EOF) {
                    $tag_item = new Tbl_tag;
                    $tag_item->tag_id = $rs->fields['tag_id'];
                    $tag_item->tag = $rs->fields['tag'];
                    $tag_item->image = $rs->fields['image'];
                    $tag_item->default_topic_id = $rs->fields['default_topic_id'];
                    $tag_items[] = $tag_item; //add this badge_item to array of Tbl_badge
                    $rs->MoveNext();
                }
                return $tag_items;
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    /*
     function get_tags_with_links($topic_id) Tbl_tag
     Gets all tags, with a flag column (selected) set if tag is linked to given $topic_id
     mReschke 2010-08-26
    */
    public static function get_tags_with_links($topic_id) {
        try {
            $db = ADODB::connect();
            //Get tags in this topoic
            if ($topic_id == '') $topic_id = 0;
            $rs = $db->Execute("
                SELECT
                    bi.*,
                    CASE WHEN bl.topic_id IS NULL THEN 0 ELSE 1 END as selected
                FROM
                    tbl_tag_item bi
                    LEFT OUTER JOIN tbl_tag_link bl on bi.tag_id = bl.tag_id AND bl.topic_id=$topic_id
                ORDER BY
                    selected DESC, bi.tag ASC
            ");
            $tag_items = array();
            while (!$rs->EOF) {
                $tag_item = new Tbl_tag;
                $tag_item->tag_id = $rs->fields['tag_id'];
                $tag_item->tag = $rs->fields['tag'];
                $tag_item->image = $rs->fields['image'];
                $tag_item->default_topic_id = $rs->fields['default_topic_id'];
                $tag_item->topic_count = $rs->fields['topic_count'];
                $tag_item->selected = $rs->fields['selected'];
                $tag_items[] = $tag_item; //add this tag_item to array of Tbl_tag
                $rs->MoveNext();
            }
            return $tag_items;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }     
    }

    /*    
     function update_tag($tag_id, $tag_name, $image, $topic_count) tag_id or -1 if exists
     Updates one tag_item
     Returns the same tag_id, unless you tried to update to a tag name that already exists, if so, returns -1
     mReschke 2012-10-01
    */
    public static function update_tag($tag_id, $tag_name, $image, $default_topic_id, $topic_count) {
        try {
            if ($tag_id > 0) {
                $db = ADODB::connect();
				if ($image == '') {
					$image_query = "NULL";
				} else {
					$image_query = "'".ADODB::dbclean($image)."'";
				}

                //Clean Tag Name
                $tag_name = Tag::clean_tag($tag_name);

                //Check if group exists (when cant change grou name to existing users)
                $query = "SELECT tag_id FROM tbl_tag_item WHERE tag='".ADODB::dbclean($tag_name)."' AND tag_id <> ".ADODB::dbclean($tag_id);
                $row = $db->GetRow($query);
                if (isset($row['tag_id'])) {
                    //Error: Tried to update to a tag name that already exists
                    return -1;
                }
                
                if (!$default_topic_id || $default_topic_id == 0) $default_topic_id = 'null';
                
                $query = "
                    UPDATE tbl_tag_item SET
                        tag='".ADODB::dbclean($tag_name)."',
                        image=$image_query,
                        default_topic_id=".ADODB::dbclean($default_topic_id);
                    if ($topic_count) {
                        $query .= ",topic_count=".ADODB::dbclean($topic_count);
                    }
                    $query .= " WHERE
                        tag_id = $tag_id
                ";
                $rs = $db->Execute($query);
                return $tag_id;
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }  
    }


    /*
     function update_tag_image($tag_id, $image) null
     Updates the tag image filename
     mReschke 2012-10-01
    */
    public static function update_tag_image($tag_id, $image) {
        try {
            if ($tag_id > 0 && $image != '') {
                $db = ADODB::connect();
                $query = "UPDATE tbl_tag_item SET image='".ADODB::dbclean($image)."' WHERE tag_id=$tag_id";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }

    /*
     function delete_tag($tag_id)
     Deletes one tag (and its image file), will cascade delete all tag links
     mReschke 2012-10-28
    */
    public static function delete_tag($tag_id) {
        try {
            if ($tag_id > 0) {
                $tbl_tag = Tbl_tag::get_tag($tag_id);

                $db = ADODB::connect();
                $query = "DELETE FROM tbl_tag_item WHERE tag_id=$tag_id";
                $rs = $db->Execute($query);

                //Delete Image
                if ($tbl_tag->image) {
                    if (file_exists(Page::get_abs_base().'/web/image/'.$tbl_tag->image)) {
                        unlink(Page::get_abs_base().'/web/image/'.$tbl_tag->image);
                    }
                }
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }

    /*
     function delete_tag_links($topic_id) null
     Deletes ALL tags from this topic (and subtracts from topic_count)
     mReschke 2010-08-29
    */
    public static function delete_tag_links($topic_id) {
        try {
            $db = ADODB::connect();
            
            //Subtract topic_count for each tag
            $query = "
                UPDATE tbl_tag_item SET topic_count = topic_count - 1 WHERE tag_id IN (
                    SELECT tag_id FROM tbl_tag_link WHERE topic_id = $topic_id
                )
            ";
            $rs = $db->Execute($query);
            
            //Delete all badge links for this topic
            $query = "DELETE FROM tbl_tag_link WHERE topic_id=$topic_id";
            $rs = $db->Execute($query);

        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    /*
     function insert_tag_link($topic_id, $tag_id) null
     Inserts one topic/tag link into tbl_tag_link (and adds to topic_count)
     mReschke 2010-08-29
    */
    public static function insert_tag_link($topic_id, $tag_id) {
        try {
            if ($topic_id > 0 && $tag_id > 0) {
                //Insert topic/tag link
                $db = ADODB::connect();
                $query = "INSERT INTO tbl_tag_link (topic_id, tag_id) VALUES ($topic_id, $tag_id)";
                $rs = $db->Execute($query);
                
                //Add one to topic_count
                Tbl_tag::update_topic_count($tag_id, 1);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    /*
     function update_topic_count($tag_id, $num)
     Updates this tags topic_count ($num can be 1 or -1)
     mReschke 2010-09-09
    */
    public static function update_topic_count($tag_id, $num) {
        try {
            $db = ADODB::connect();
            $query = "UPDATE tbl_tag_item SET topic_count = topic_count + $num WHERE tag_id = $tag_id";
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }    
    
    /*
     function insert_tag_item($tagname) int new tag_id
     Inserts one new tag item (does not link tag to any topic)
     Only insert IF NOT EXIST
     mReschke 2010-08-29
    */
    public static function insert_tag_item($tagname, $image=null, $default_topic_id=0) {
        try {
            $new_tag_id = 0;
            
            $newtagname = '';
            #$tagname = eregi_replace(" ", "", $tagname);
            $words = str_word_count($tagname, 1, ".");
            foreach ($words as $word) {
                $newtagname .= $word;
            }
            $tagname = $newtagname;

            //Clean Tag Name
            $tagname = ADODB::dbclean(Tag::clean_tag($tagname));
            
            if ($tagname != '') {
                $db = ADODB::connect();
                
                //Check if tag exists first
                $tag = new Tbl_tag;
                $tag = Tbl_tag::get_tag($tagname);
                
                if (!isset($tag->tag_id)) {
                    if (!$default_topic_id || $default_topic_id == 0) $default_topic_id = 'null';
                    if (is_null($image)) {
                        $query = "INSERT INTO tbl_tag_item (tag, default_topic_id, topic_count) VALUES ('$tagname', $default_topic_id, 0)";
                    } else {
                        $query = "INSERT INTO tbl_tag_item (tag, default_topic_id, image, topic_count) VALUES ('$tagname', $default_topic_id, '$image', 0)";
                    }
                    $rs = $db->Execute($query);
                    $new_tag_id = $db->Insert_ID();
                } else {
                    $new_tag_id = $tag->tag_id;
                }
            }
            return $new_tag_id;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }       
    }
}
