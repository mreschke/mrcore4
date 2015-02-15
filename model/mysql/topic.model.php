<?php
require_once 'adodb.php';

/*
 class Tbl_topic
 Database Layer for db.tbl_topic and db.tbl_topic_stat
 mReschke 2010-08-05
*/
class Tbl_topic {
    public $topic_id;
    public $topic_idTbl_badges;
    public $topic_idTbl_tags;
    public $created_by;
    public $created_byTbl_user;
    public $created_on;
    public $updated_by;
    public $updated_byTbl_user;
    public $updated_on;
    public $deleted;
    public $title;
    public $teaser;
    public $view_count;
    public $comment_count;
    #public $result_count;
    public $unread;
    
    public static function get_topic($topic_id) {
        try {
            $db = ADODB::connect();
            $query = "
                SELECT
                        t.*, ts.view_count, ts.comment_count
                FROM
                        tbl_topic t
                        -- LEFT OUTER JOIN tbl_topic_stat ts on t.topic_id = ts.topic_id
                WHERE
                        t.topic_id=$topic_id
                        AND t.deleted=0;
            ";
            $row = $db->GetRow($query);
            $topic = new Tbl_topic;
            
            if (1==2) {$this->created_byTbl_user = new Tbl_user;} //For Komodo autocomplete*/
            if (1==2) {$this->updated_byTbl_user = new Tbl_user;} //For Komodo autocomplete
            if (isset($row['topic_id'])) {
                $topic->topic_id = $row['topic_id'];
                $topic->created_by = $row['created_by'];
                $topic->created_byTbl_user = Tbl_user::get_user($row['created_by']);
                $topic->created_on = $row['created_on'];
                $topic->updated_by = $row['updated_by'];
                $topic->updated_byTbl_user = Tbl_user::get_user($topic->updated_by);
                $topic->updated_on = $row['updated_on'];
                $topic->deleted = $row['deleted'];
                $topic->title = $row['title'];
                $topic->teaser = $row['teaser'];
            }
            return $topic;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }      
    }
    
    public static function update_topic(Tbl_post $tbl_post) {
        try {
            $db = ADODB::connect();
            $query = "
                UPDATE tbl_topic SET
                    created_by = ".ADODB::dbclean($tbl_post->created_by).",
                    created_on = '".ADODB::dbclean($tbl_post->created_on)."',
                    updated_by = ".ADODB::dbclean($tbl_post->updated_by).",
                    updated_on = '".ADODB::dbclean($tbl_post->updated_on)."',
                    hidden = ".ADODB::dbclean($tbl_post->hidden).",
                    title = '".ADODB::dbclean($tbl_post->title)."',
                    teaser = '".ADODB::dbclean(Topic::create_teaser($tbl_post->body, Config::TEASER_LEN))."'
                WHERE
                    topic_id = ".$tbl_post->topic_id
            ;
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }    
    }
    
    /*
     function read_topic($user_id, $topic_id)
     Mark this topic as read by this user
     mReschke 2010-09-26
    */
    public function read_topic($user_id, $topic_id) {
        try {
            $db = ADODB::connect();
            $query = "INSERT IGNORE INTO tbl_read VALUES ($user_id, $topic_id)";
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }              
    }
    
    /*
     function unread_topic($topic_id)
     Mark topic as unread for all users
     mReschke 2010-09-26
    */
    public function unread_topic($topic_id) {
        try {
            $db = ADODB::connect();
            $query = "DELETE FROM tbl_read WHERE topic_id=$topic_id";
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                     
    }
    
    /*
     function get_unread_count(Info $info) int unread_count
     Gets integer topic unread count for user
     mReschke 2010-09-26
    */
    public static function get_unread_count(Info $info) {
        try {
            $db = ADODB::connect();
            //NOTE: Using a NOT IN is FASTER than using a left join
            //See http://blog.sqlauthority.com/2008/04/22/sql-server-better-performance-left-join-or-not-in/
            if ($info->admin) {
                //This does not take into account permissions on topic_count, becuase Im admin
                $query = "SELECT count(topic_id) as unread_count FROM tbl_topic WHERE deleted=0 AND topic_id NOT IN (SELECT topic_id FROM tbl_read WHERE user_id=$info->user_id)";
            } else {
                //This uses a true topic count, taking into account your permissions
                $query = "
                    SELECT
                        count(DISTINCT t.topic_id) as unread_count
                    FROM
                        tbl_topic t
                        LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id
                        LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id
                    WHERE
                        t.deleted = 0
                        AND ((pl.perm_id=".Config::STATIC_PERM_READ." AND pgl.user_id=$info->user_id) OR t.created_by=$info->user_id)
                        AND t.topic_id NOT IN (
                            SELECT topic_id FROM tbl_read WHERE user_id=$info->user_id
                        )
                ";
            }
            $row = $db->GetRow($query);
            $unread_count = 0;
            if (isset($row['unread_count'])) {
                $unread_count = $row['unread_count'];
            }
            return $unread_count;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }    
    }
    
    /*
     function insert_topic($tbl_pos) int new topic_id
     Inserts one new topic into tbl_topic
     Should always be called only from Tbl_post::insert_post()
     mReschke 2010-08-26
    */
    public static function insert_topic($tbl_post) {
        try {
            $post = new Tbl_post;
            $post = $tbl_post;
            $db = ADODB::connect();
            $query = "
                INSERT INTO tbl_topic (
                    created_by, created_on, updated_by,
                    updated_on, hidden, title, teaser
                ) VALUES (
                    ".ADODB::dbclean($post->created_by).",
                    '".ADODB::dbclean($post->created_on)."',
                    ".ADODB::dbclean($post->updated_by).",
                    '".ADODB::dbclean($post->updated_on)."',
                    ".ADODB::dbclean($post->hidden).",
                    '".ADODB::dbclean($post->title)."',
                    '".ADODB::dbclean(Topic::create_teaser($post->body, Config::TEASER_LEN))."'
                )
            ";
            $rs = $db->Execute($query);
            return $db->Insert_ID();
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }         
    }
    
    /*
     function get_topic_ids_array() array of topic_ids
     Gets all topic_ids in array, used for Text_Wiki FreeLink
     mReschke 2010-09-26
    */
    public static function get_topic_ids_array() {
        try {
            $db = ADODB::connect();
            $query = "SELECT topic_id, title FROM tbl_topic WHERE deleted=0";
            $rs = $db->Execute($query);
            $topic_ids = array();
            while (!$rs->EOF) {
                #$topic_ids[] = $rs->fields['topic_id'];
                $topic_ids[$rs->fields['topic_id']] = $rs->fields['title'];
                $rs->MoveNext();
            }
            return $topic_ids;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    /*
     function update_view_count($topic_id) 
     Updates topic view count
     mReschke 2010-09-01
    */
    public static function update_view_count($topic_id) {
        try {
            $query = "INSERT INTO tbl_topic_stat VALUES ($topic_id,1,0) ON DUPLICATE KEY UPDATE view_count=view_count + 1";
            $db = ADODB::connect();
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                
    }
    
    /*
     function update_comment_count($topic_id, $num) null
     Updates topic comment count ($num can be 1 or -1)
     mReschke 2010-09-01
    */
    public static function update_comment_count($topic_id, $num) {
        try {
            $query = "INSERT INTO tbl_topic_stat VALUES ($topic_id,0,1) ON DUPLICATE KEY UPDATE comment_count=comment_count + $num";
            $db = ADODB::connect();
            $rs = $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                
    }
    
    /*
     function delete_topic($topic_id) null
     Sets the deleted FLAG = 1 for this topic_id in tbl_topic and tbl_post by topic_id
     Does NOT take care of updating user/topic comment_counts or badge/topic counts
     mReschke 2010-09-09
     */
    public static function delete_topic(Info $info, $topic_id) {
        try {
            if ($topic_id > 0) {
                $db = ADODB::connect();
                //Delete topic
                $query = "
                    UPDATE
                        tbl_topic
                    SET
                        deleted=1,
                        updated_by=".$info->user_id.",
                        updated_on='".ADODB::dbnow()."'
                    WHERE
                        topic_id=$topic_id
                ";
                $rs = $db->Execute($query);
                
                //Delete topic comments
                $query = "
                    UPDATE
                        tbl_post
                    SET
                        deleted=1,
                        updated_by=".$info->user_id.",
                        updated_on='".ADODB::dbnow()."'
                    WHERE
                        topic_id=$topic_id
                        AND deleted=0
                    ";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                        
    }
    
    /*
     function undelete_topic($topic_id) null
     Sets the deleted FLAG = 0 for this topic_id in tbl_topic and tbl_post by topic_id
     Does NOT take care of updating user/topic comment_counts or badge/topic counts
     mReschke 2010-09-12
     */
    public static function undelete_topic(Info $info, $topic_id) {
        try {
            if ($topic_id > 0) {
                $db = ADODB::connect();
                //Undelete topic
                $query = "
                    UPDATE
                        tbl_topic
                    SET
                        deleted=0,
                        updated_by=".$info->user_id.",
                        updated_on='".ADODB::dbnow()."'
                    WHERE
                        topic_id=$topic_id
                ";
                $rs = $db->Execute($query);
                
                //Undelete all topic comments
                $query = "
                    UPDATE
                        tbl_post
                    SET
                        deleted=0,
                        updated_by=".$info->user_id.",
                        updated_on='".ADODB::dbnow()."'
                    WHERE
                        topic_id=$topic_id
                ";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                        
    }    

    /*
     function get_related_topics($user_id, $perm_admin, $topic_id) array of partial Tbl_topic
     Returns partial array of Tbl_topics with similar badges and topics
     Weighted by badge_count and topic_count
     pass $user_id because it only finds topics you have READ access too!
     Array only contains topic_id, title, badge_count, topic_count
     mReschke 2010-09-04
    */
    public static function get_related_topics($user_id, $perm_admin, $topic_id) {
        //Wow, super nice query here.  Only finds and weights topics $user_id has READ access too
        //So joins in all perm/group tables.  Unless your admin
        try {
            $db = ADODB::connect();
            if ($perm_admin) {
                //User is admin, no need to filter related topics by permission
                $query = "
                    SELECT
                            DISTINCT
                            t.topic_id,
                            t.title,
                            count(DISTINCT bl.badge_id) as badge_count,
                            count(DISTINCT tl.tag_id) as tag_count
                    FROM
                            tbl_topic t
                            LEFT OUTER JOIN tbl_badge_link bl on t.topic_id = bl.topic_id AND bl.badge_id IN (SELECT badge_id FROM tbl_badge_link WHERE topic_id = $topic_id)
                            LEFT OUTER JOIN tbl_tag_link tl on t.topic_id = tl.topic_id AND tl.tag_id IN (SELECT tag_id FROM tbl_tag_link WHERE topic_id = $topic_id)
                    WHERE
                            t.deleted = 0
                            AND t.topic_id <> $topic_id
                            AND (bl.badge_id IS NOT NULL OR tl.tag_id IS NOT NULL)
                    GROUP BY
                            t.topic_id
                    ORDER BY
                            count(DISTINCT bl.badge_id) DESC,
                            count(DISTINCT tl.tag_id) DESC
                    LIMIT 20
                ";
            } else {
                //This one joins in all the perm/group tables to filter related topics by those that $user_id has READ access too! Oh, sexified sexyness from the brain of mReschke!
                $query = "
                    SELECT
                            DISTINCT
                            t.topic_id,
                            t.title,
                            count(DISTINCT bl.badge_id) as badge_count,
                            count(DISTINCT tl.tag_id) as tag_count
                    FROM
                            tbl_topic t
                            LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id and pl.perm_id = ".Config::STATIC_PERM_READ."
                            LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id
                            LEFT OUTER JOIN tbl_badge_link bl on t.topic_id = bl.topic_id AND bl.badge_id IN (SELECT badge_id FROM tbl_badge_link WHERE topic_id = $topic_id)
                            LEFT OUTER JOIN tbl_tag_link tl on t.topic_id = tl.topic_id AND tl.tag_id IN (SELECT tag_id FROM tbl_tag_link WHERE topic_id = $topic_id)
                    WHERE
                            (pgl.user_id = $user_id OR t.created_by = $user_id)
                            AND t.deleted = 0
                            AND t.topic_id <> $topic_id
                            AND (bl.badge_id IS NOT NULL OR tl.tag_id IS NOT NULL)
                    GROUP BY
                            t.topic_id
                    ORDER BY
                            count(DISTINCT bl.badge_id) DESC,
                            count(DISTINCT tl.tag_id) DESC
                    LIMIT 20
                ";
            }
            $rs = $db->Execute($query);
            $topics = array();
            while (!$rs->EOF) {
                $topic = new Tbl_topic;
                $topic->topic_id = $rs->fields['topic_id'];
                $topic->title = $rs->fields['title'];
                $topic->badge_count = $rs->fields['badge_count'];
                $topic->tag_count = $rs->fields['tag_count'];
                $topics[] = $topic; //add this to array
                $rs->MoveNext();
            }
            return $topics;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }            
    }
    
    /*
     function get_topics($user_id, $perm_admin, $query, $badges, $tags) array of tbl_topic
     Main Search function, gets topics based on a wide variety of search options
     mReschke 2010-09-04
    */
    public static function get_topics(Info $info, Search $search) {
        try {
            GLOBAL $view;
            $db = ADODB::connect();
            
            $select=$from=$where=$orderby=$groupby=$having=null;
            if (!is_null($search->query)) {
                $words = array_values(Indexer::stem_text($search->query));
                $word_count = count($words);
                if ($word_count <= 0) return;
            }
            
            //Main SELECT
            //$select = "DISTINCT t.topic_id, t.created_by, t.created_on, t.updated_by, t.updated_on, t.deleted, t.title, t.teaser, ts.view_count, ts.comment_count ";
            $select = "DISTINCT t.*, ts.view_count, ts.comment_count, CASE WHEN r.user_id IS NULL THEN 1 ELSE 0 END as unread ";
            #if (isset($search->query)) $select .= ", COUNT(*) as cnt, SUM(weight) ";

            //Main FROM
            $from = "
                tbl_topic t
                LEFT OUTER JOIN tbl_topic_stat ts on t.topic_id = ts.topic_id
                LEFT OUTER JOIN tbl_read r on t.topic_id = r.topic_id AND r.user_id=$info->user_id ";
            if ($info->admin) {
                //User is admin, no need to filter by permission

            } else {
                //This one joins in all the perm/group tables to filter topics by those that $user_id has READ access too!
                $from .= "
                    LEFT OUTER JOIN tbl_perm_link pl on t.topic_id = pl.topic_id and pl.perm_id = ".Config::STATIC_PERM_READ."
                    LEFT OUTER JOIN tbl_perm_group_link pgl on pl.perm_group_id = pgl.perm_group_id ";
                $where .= "AND (pgl.user_id = $info->user_id OR t.created_by = $info->user_id) ";
            }
            
            //Badges Filter
            if (isset($search->badge_ids)) {
                $from .= "LEFT OUTER JOIN tbl_badge_link bl on t.topic_id = bl.topic_id ";
                if (preg_match('/,|;/', $search->badge_ids)) {
                    //Multiple badges use AND if separated by , and OR if separated by ;
                    if (preg_match('/,/', $search->badge_ids)) {
                        //Having makes the IN statement an AND instead of an OR
                        $having .= "AND count(DISTINCT badge_id) = ".count($search->Badge_ids)." ";
                        $tmpids = $search->badge_ids;
                    } else {
                        $tmpids = implode(',', $search->Badge_ids);
                    }
                    $where .= "AND bl.badge_id IN ($tmpids) ";
                    $groupby = "t.topic_id ";
                } else {
                    //Single Badge
                    if ($search->badge_ids != '' && $search->badge_ids != '*') {
                        $where .= "AND bl.badge_id IN ($search->badge_ids) ";
                    }
                }
            }
            
            //Tags Filter
            if (isset($search->tag_ids)) {
                $from .= "LEFT OUTER JOIN tbl_tag_link tl on t.topic_id = tl.topic_id ";
                if (preg_match('/,|;/', $search->tag_ids)) {
                    //Multiple tags use AND if separated by , and OR if separated by ;
                    if (preg_match('/,/', $search->tag_ids)) {
                        //Having makes the IN statement an AND instead of an OR
                        $having .= "AND count(DISTINCT tag_id) = ".count($search->Tag_ids)." ";
                        $tmpids = $search->tag_ids;
                    } else {
                        $tmpids = implode(',', $search->Tag_ids);
                    }
                    $where .= "AND tl.tag_id IN ($tmpids) ";
                    $groupby = "t.topic_id ";
                } else {
                    //Single Tag
                    if ($search->tag_ids != '' && $search->tag_ids != '*') {
                        $where .= "AND tl.tag_id IN ($search->tag_ids) ";
                    }
                }
            }

            //Query Filter
            //Comes after badge or tag filter because overrites $groupby
            if (isset($search->query)) {
                if ($word_count > 0) {
                    #$from .= "
                    #    LEFT OUTER JOIN tbl_post p on t.topic_id = p.topic_id
                    #    LEFT OUTER JOIN tbl_post_index pi on p.post_id = pi.post_id ";
                    $from .= "
                        INNER JOIN (
                            SELECT
                                tmp.topic_id,
                                tmp.cnt,
                                sum(weight) as weight
                            FROM (
                                SELECT
                                    p.topic_id,
                                    p.post_id,
                                    COUNT(*) AS cnt,
                                    SUM(weight) as weight
                                FROM
                                    tbl_post_index pi
                                    INNER JOIN tbl_post p on pi.post_id = p.post_id
                                WHERE (";
                                foreach ($words as $word) {
                                    if (stristr($search->query, "*")) {
                                        //Using like '%word%'
                                        //NOTE, this causes the weight to be inaccurate
                                        //And you must add a >= to 'WHERE cnt=>$word_count'
                                        //Order of weight will be invalid, results may vary
                                        $from.= "pi.word like '%$word%' OR ";
                                    } else {
                                        //Using = 'word'
                                        //This is accurate as far as weight
                                        $from.= "pi.word = '$word' OR ";    
                                    }
                                    
                                }
                                $from = substr($from, 0, -4).")
                                GROUP BY
                                    p.topic_id,
                                    p.post_id ";
                                if (!stristr($search->query, "|") && !stristr(urldecode($search->query), " or ")) {
                                    #if (stristr($search->query, "&") || stristr($search->query, " and ")) {
                                    //We only want an AND if there are multiple words
                                    if (stristr($search->query, " ")) {
                                        //makes it an AND statment instead of OR
                                        if (stristr($search->query, "*")) {
                                            //See like note above (inaccurate weight results)
                                            $from .= "HAVING cnt>=$word_count ";
                                        } else {
                                            //Regular, accurate weight results
                                            $from .= "HAVING cnt=$word_count ";
                                        }
                                        
                                    }
                                }
                                $from .= "
                            ) tmp
                            GROUP BY
                                tmp.topic_id,
                                tmp.cnt
                        ) i on t.topic_id = i.topic_id ";
                        
                    #$where .= "AND (";
                    #foreach ($words as $word) {
                    #    //$where .= "pi.word='$word' OR ";
                    #    $where .= "pi.word = '$word' OR ";
                    #}
                    #$where = substr($where, 0, -4).') ';
                    #$where .= "AND p.is_comment=0 ";
                    #$where .= "AND (pi.word='linux' or pi.word='feed') ";
                    #$where .= "AND (".implode(' OR ', array_fill(0, $word_count, 'pi.word=?')).")";
                    #$groupby = "t.topic_id, t.created_by, t.created_on, t.updated_by, t.updated_on, t.deleted, t.title, t.teaser, ts.view_count, ts.comment_count ";
                    $orderby = "cnt DESC, weight DESC, " ;
                    #if (!stristr($search->query, "|") && !stristr(urldecode($search->query), " or ")) {
                    #if (stristr($search->query, "&") || stristr($search->query, " and ")) {
                    #    //We only want an AND if there are multiple words
                    #    if (stristr($search->query, " ")) {
                    #        //makes it an AND statment instead of OR
                    #        $having .= "AND cnt>=$word_count ";
                    #    }
                    #}
                    #if ($word_count > 1) $having .= "AND cnt2<>1";
                }
            }
            
            //Option Deleted
            if ($info->admin && $search->Options['deleted'] == 1) {
                $where .= "AND t.deleted=1 ";
            } else {
                $where .= "AND t.deleted=0 ";
            }

            //Option Hidden
			if ($search->Options['unread'] != 1) {
	            if ($search->Options['hidden'] == 1) {
	                $where .= "AND t.hidden=1 ";
	            } else {
	                $where .= "AND t.hidden=0 ";
	            }
			}
            
            //Option Unread
            if ($search->Options['unread'] == 1) {
                #$where .= "AND t.topic_id NOT IN (SELECT topic_id FROM tbl_read WHERE user_id=$info->user_id) ";
                $where .= "AND r.user_id IS NULL ";
            }

            
            //Order by
            $orderby .= "t.created_on DESC, ";
            
            
            //Build the Final Query
            $sql = "SELECT $select FROM $from ";
            if (isset($where)) {
                $where = substr($where, 4); //remove beginning AND
                $sql .= "WHERE $where ";
            }
            if (isset($groupby)) {
                $sql .= "GROUP BY $groupby ";
            }
            if (isset($having)) {
                $having = substr($having, 4); //remove beginning AND
                $sql .= "HAVING $having ";
            }
            if (isset($orderby)) {
                $orderby = substr($orderby, 0, -2); //remove last ,space
                $sql .= "ORDER BY $orderby ";
            }

            //Paging
            if ($search->Options['view'] != 'link' && $search->Options['view'] != 'list') {
                $page = 0;
                $page_size = Config::SEARCH_PAGE_SIZE;
                if (isset($search->Options['page'])) $page = $search->Options['page'] -1;
                if (isset($search->Options['pagesize'])) $page_size = $search->Options['pagesize'];
                $page = $page * $page_size;
                $sql .= "LIMIT ".$page . ", $page_size";
            }
            
            if (Config::DEBUG) $view->add_debug("Search Query", "topic.model.php get_topics()", $sql);
            $rs = $db->Execute($sql);
            $topics = array();
            while (!$rs->EOF) {
                $topic = new Tbl_topic;
                $topic->topic_id = $rs->fields['topic_id'];
                $topic->topic_idTbl_badges = Tbl_badge::get_badges($info, $topic->topic_id);
                $topic->topic_idTbl_tags = Tbl_tag::get_tags($info, $topic->topic_id);
                $topic->created_by = $rs->fields['created_by'];
                $topic->created_byTbl_user = Tbl_user::get_user($rs->fields['created_by']);
                $topic->created_on = $rs->fields['created_on'];
                $topic->updated_by = $rs->fields['updated_by'];
                $topic->updated_byTbl_user = Tbl_user::get_user($topic->updated_by);
                $topic->updated_on = $rs->fields['updated_on'];
                $topic->deleted = $rs->fields['deleted'];
                $topic->title = $rs->fields['title'];
                $topic->teaser = $rs->fields['teaser'];
                $topic->view_count = $rs->fields['view_count'];
                $topic->comment_count = $rs->fields['comment_count'];
                $topic->unread = $rs->fields['unread'];
                $topics[] = $topic; //add this to array
                #try {
                    $rs->MoveNext();
                    #throw new Exception('asdf');
                #} catch (Exception $e) {
                 //echo "hi";
                    #throw new Exception('asdf');
                #}
            }
            return $topics;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
            //throw new Exception("ha");
            //Page::redirect(Page::get_url('redirect').'/error');
        }
    }
    
    /*
     function get_quick_topics_by_tag($tag_id) assoc array of topic_id/title
     Quick query of only assoc array of topic_id/title of all topics linked to the given $tag_id
     mReschke 2013-05-01
    */
    public static function get_quick_topics_by_tag($tag_id) {
        try {
            $db = ADODB::connect();
            $query = "
                SELECT
                    t.topic_id,
                    t.title
                FROM
                    tbl_topic t
                    INNER JOIN tbl_tag_link tl on t.topic_id = tl.topic_id
                WHERE
                    t.deleted=0
                    AND tl.tag_id=$tag_id
            ";
            $rs = $db->Execute($query);
            $topics = array();
            while (!$rs->EOF) {
                $topics[$rs->fields['topic_id']] = $rs->fields['title'];
                $rs->MoveNext();
            }
            return $topics;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    
}
