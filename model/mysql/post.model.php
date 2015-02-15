<?php
require_once 'adodb.php';



/*
 class Tbl_post
 Database Layer for db.tbl_post
 mReschke 2010-08-06
*/
class Tbl_post {
    public $post_id;
    public $post_uuid;
    public $topic_id;
    public $topic_idTbl_topic;
    public $created_by;
    public $created_byTbl_user;
    public $created_on;
    public $updated_by;
    public $updated_byTbl_user;
    public $updated_on;
    public $is_comment;
    public $has_exec;
    public $has_html;
    public $deleted;
    public $hidden;
    public $uuid_enabled;
    public $title;
    public $body;
    public $view_count;
    public $comment_count;
    
    public static function get_post(Info $info, $post_id) {
        try {
            $db = ADODB::connect();
            $query = "
                SELECT
                        p.*, ts.view_count, ts.comment_count
                FROM
                        tbl_post p
                        LEFT OUTER JOIN tbl_topic_stat ts on p.topic_id = ts.topic_id
                WHERE
                        p.post_id=$post_id
            ";
            if (!$info->admin) $query .= " AND p.deleted=0";

            $row = $db->GetRow($query);
            $post = new Tbl_post;
            if (1==2) {$this->created_byTbl_user = new Tbl_user;} //For Komodo autocomplete
            if (1==2) {$this->updated_byTbl_user = new Tbl_user;} //For Komodo autocomplete
            if (1==2) {$this->topic_idTbl_topic = new Tbl_topic;} //For Komodo autocomplete
            if (isset($row['post_id'])) {
                $post->post_id = $row['post_id'];
                $post->post_uuid = $row['post_uuid'];
                $post->topic_id = $row['topic_id'];
                if ($row['is_comment'] == 1) {
                    $post->topic_idTbl_topic = Tbl_topic::get_topic($post->topic_id);
                }
                $post->created_by = $row['created_by'];
                $post->created_byTbl_user = Tbl_user::get_user($row['created_by']);
                $post->created_on = $row['created_on'];
                $post->updated_by = $row['updated_by'];
                $post->updated_byTbl_user = Tbl_user::get_user($post->updated_by);
                $post->updated_on = $row['updated_on'];
                $post->is_comment = $row['is_comment'];
                $post->has_exec = $row['has_exec'];
                $post->has_html = $row['has_html'];
                $post->deleted = $row['deleted'];
                $post->hidden = $row['hidden'];
                $post->uuid_enabled = $row['uuid_enabled'];
                $post->title = $row['title'];
                $post->body = $row['body'];
                $post->view_count = $row['view_count'];
                $post->comment_count = $row['comment_count'];
            }
            return $post;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }

    /*
     function get_comments() array of Tbl_post
     Gets all comments for this topic
     mReschke 2010-08-24
    */
    public static function get_comments($topic_id, $show_deleted=false) {
        try {
            $db = ADODB::connect();
            if ($show_deleted) {
                $rs = $db->Execute("SELECT * FROM tbl_post WHERE topic_id=$topic_id AND is_comment=1 ORDER BY created_on ASC");            
            } else {
                $rs = $db->Execute("SELECT * FROM tbl_post WHERE topic_id=$topic_id AND is_comment=1 and deleted=0 ORDER BY created_on ASC");
            }
            
            $posts = array();
            while (!$rs->EOF) {
                #print $rs->fields[0].' '.$rs->fields['first_name'].'<BR>';
                $post = new Tbl_post;
                $post->post_id = $rs->fields['post_id'];
                $post->topic_id = $rs->fields['topic_id'];
                $post->created_by = $rs->fields['created_by'];
                $post->created_byTbl_user = Tbl_user::get_user($post->created_by);
                $post->created_on = $rs->fields['created_on'];
                $post->updated_by = $rs->fields['updated_by'];
                $post->updated_byTbl_user = Tbl_user::get_user($post->updated_by);
                $post->updated_on = $rs->fields['updated_on'];
                $post->is_comment = $rs->fields['is_comment'];
                $post->has_exec = $rs->fields['has_exec'];
                $post->has_html = $rs->fields['has_html'];
                $post->deleted = $rs->fields['deleted'];
                $post->title = $rs->fields['title'];
                $post->body = $rs->fields['body'];
                $posts[] = $post; //add this post to array of tbl_post
                $rs->MoveNext();
            }
            return $posts;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }   
    }
    
    /*
     function get_unindexed_posts()
     Gets all undeleted posts (comments too) that have been updated/created after its index
     So all posts needing re-indexed
     mReschke 2010-09-14
    */
    public static function get_unindexed_posts() {
        try {
            $db = ADODB::connect();
            $query = "SELECT * FROM tbl_post WHERE deleted=0 AND updated_on > indexed_on";
            $rs = $db->Execute($query);
            $posts = array();
            while (!$rs->EOF) {
                $post = new Tbl_post;
                $post->post_id = $rs->fields['post_id'];
                $post->topic_id = $rs->fields['topic_id'];
                $post->title = $rs->fields['title'];
                $post->body = $rs->fields['body'];
                $posts[] = $post; //add this post to array of tbl_post
                $rs->MoveNext();
            }
            return $posts;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }       
    }
    
    public static function get_topic(Info $info, $topic_id) {
        try {
            $db = ADODB::connect();
            $query = "
                SELECT
                    post_id
                FROM
                    tbl_post
                WHERE
                    topic_id=$topic_id
                    AND is_comment=0
            ";
            if (!$info->tbl_user->perm_admin) $query .= " AND deleted=0"; //admins can read deleted topics
            $row = $db->GetRow($query);
            $post = new Tbl_post;
            if (isset($row['post_id'])) {
                $post = Tbl_post::get_post($info, $row['post_id']);
            }
            return $post;
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
    
    /*
     function update_post(Tbl_post $tbl_post)
     Updates post (post can be topic or comment)
     Will auto update tbl_topic if is topic
     mReschke 2010-08-06
    */
    public static function update_post(Tbl_post $tbl_post) {
        try {
            $db = ADODB::connect();
            $tbl_post->body = Parser::pre_save($tbl_post->body);

            //Type Conversions
            $tbl_post->hidden = ADODB::boolint($tbl_post->hidden);
            $tbl_post->uuid_enabled = ADODB::boolint($tbl_post->uuid_enabled);
            $tbl_post->has_exec = ADODB::boolint($tbl_post->has_exec);
            $tbl_post->has_html = ADODB::boolint($tbl_post->has_html);

            $query = "
                UPDATE tbl_post SET
                    created_by=".ADODB::dbclean($tbl_post->created_by).",
                    created_on='".ADODB::dbclean($tbl_post->created_on)."',
                    updated_by=".ADODB::dbclean($tbl_post->updated_by).",
                    updated_on='".ADODB::dbclean($tbl_post->updated_on)."',
                    has_exec=".ADODB::dbclean($tbl_post->has_exec).",
                    has_html=".ADODB::dbclean($tbl_post->has_html).",
                    hidden=".ADODB::dbclean($tbl_post->hidden).",
                    uuid_enabled=".ADODB::dbclean($tbl_post->uuid_enabled).",
                    title='".ADODB::dbclean($tbl_post->title)."',
                    body='".ADODB::dbclean($tbl_post->body)."'
                WHERE
                    post_id = ".$tbl_post->post_id
            ;
            $rs = $db->Execute($query);
            
            //If the post is a topic, update Tbl_topic too
            if (!$tbl_post->is_comment) {
                Tbl_topic::update_topic($tbl_post);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }    
    }
    
    /*
     function insert_post(Tbl_post $tbl_post) int new topic_id or post_id
     Inserts a new post (topic or comment) into tbl_post
     If post is a topic, then returns the new topic_id
     If post is a comment, then returns the new post_id
     mReschke 2010-08-29
    */
    public static function insert_post(Tbl_post $tbl_post) {
        try {
            $db = ADODB::connect();

            //Type Conversions
            $tbl_post->is_comment = ADODB::boolint($tbl_post->is_comment);
            $tbl_post->hidden = ADODB::boolint($tbl_post->hidden);
            $tbl_post->uuid_enabled = ADODB::boolint($tbl_post->uuid_enabled);
            $tbl_post->has_exec = ADODB::boolint($tbl_post->has_exec);
            $tbl_post->has_html = ADODB::boolint($tbl_post->has_html);

            //New MD5 post_uuid
            #$tbl_post->post_uuid = md5($tbl_post->title.rand(1000,99999999).ADODB::dbnow().$info->user_id.$info->tbl_user->alias);
            $tbl_post->post_uuid = md5(uniqid(rand(), true));
            
            //Insert the new topic first (if not comment)
            if (!$tbl_post->is_comment) {
                $topic_id = Tbl_topic::insert_topic($tbl_post);
            } else {
                $topic_id = $tbl_post->topic_id;
            }
            $tbl_post->topic_id = $topic_id;
            
            $tbl_post->body = Parser::pre_save($tbl_post->body);
            
            $query = "
                INSERT INTO tbl_post (
                    post_uuid, topic_id, created_by, created_on, updated_by,
                    updated_on, is_comment, has_exec, has_html, hidden, uuid_enabled, title, body
                ) VALUES (
                    '".ADODB::dbclean($tbl_post->post_uuid)."',
                    ".ADODB::dbclean($tbl_post->topic_id).",
                    ".ADODB::dbclean($tbl_post->created_by).",
                    '".ADODB::dbclean($tbl_post->created_on)."',
                    ".ADODB::dbclean($tbl_post->updated_by).",
                    '".ADODB::dbclean($tbl_post->updated_on)."',
                    ".ADODB::dbclean($tbl_post->is_comment).",
                    ".ADODB::dbclean($tbl_post->has_exec).",
                    ".ADODB::dbclean($tbl_post->has_html).",
                    ".ADODB::dbclean($tbl_post->hidden).",
                    ".ADODB::dbclean($tbl_post->uuid_enabled).",
                    '".ADODB::dbclean($tbl_post->title)."',
                    '".ADODB::dbclean($tbl_post->body)."'
                )
            ";
            $rs = $db->Execute($query);
            $post_id = $db->Insert_ID();
            
            //Return topic_id if not topic, or post_id if new post
            if (!$tbl_post->is_comment) {
                return $tbl_post->topic_id;
            } else {
                return $post_id;
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }    
    }
    
    /*
     function update_index($post_id, $title, $body, $tags array)
     Update the the post search index table (tbl_post_index) for this posts title,body,tags
     If $title is_null then just delete all index for this post_id
     mReschke 2010-09-09
    */
    public static function update_index($post_id, $title=null, $body=null, $badges=array(), $tags=array()) {
        try {
            if ($post_id > 0) {
                $db = ADODB::connect();
                
                //Delete all indexed words for this topic
                $rs = $db->Execute("DELETE FROM tbl_post_index WHERE post_id=$post_id");
                
                if (!is_null($title)) {
                    
                    //Insert each word into tbl_post_index for each word in the post title,body,tags
                    foreach (Indexer::get_words($title, $body, $badges, $tags) as $word => $weight) {
                        $rs = $db->Execute("INSERT INTO tbl_post_index (post_id, word, weight) VALUES ($post_id, '$word', $weight)");
                    }                    
                }
                
                //Update indexed date
                $db->Execute("UPDATE tbl_post SET indexed_on = '".ADODB::dbnow()."' WHERE post_id=$post_id");
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }     
    }
    
    /*
     function delete_comment($post_id) 
     Sets the deleted FLAG in tbl_post for this post_id
     Does NOT take care of updating user/topic comment_counts
     mReschke 2010-09-09
     */
    public static function delete_comment(Info $info, $post_id) {
        try {
            if ($post_id > 0) {
                $db = ADODB::connect();
                #$query = "DELETE FROM tbl_post WHERE post_id=$post_id AND is_comment=1";
                $query = "
                    UPDATE
                        tbl_post
                    SET
                        deleted=1,
                        updated_by=".$info->user_id.",
                        updated_on='".ADODB::dbnow()."'
                    WHERE
                        post_id=$post_id
                ";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                      
    }

    /*
     function delete_uncomment($post_id) 
     Sets the deleted FLAG to false in tbl_post for this post_id
     Does NOT take care of updating user/topic comment_counts
     mReschke 2013-10-12
     */
    public static function undelete_comment(Info $info, $post_id) {
        try {
            if ($post_id > 0) {
                $db = ADODB::connect();
                #$query = "DELETE FROM tbl_post WHERE post_id=$post_id AND is_comment=1";
                $query = "
                    UPDATE
                        tbl_post
                    SET
                        deleted=0,
                        updated_by=".$info->user_id.",
                        updated_on='".ADODB::dbnow()."'
                    WHERE
                        post_id=$post_id
                ";
                $rs = $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }                      
    }

    /*
     function get_post_lock($post_id)
     Get user and lock date if this post is locked
     mReschke 2012-11-10
     */
    public function get_post_lock($post_id) {
        try {
            if ($post_id > 0) {
                $db = ADODB::connect();
                $query = "
                    SELECT
                        u.user_id,
                        u.alias,
                        pl.locked_on
                    FROM
                        tbl_post_lock pl
                        INNER JOIN tbl_user u on pl.user_id = u.user_id
                    WHERE
                        pl.post_id = $post_id
                    LIMIT 1
                ";
                $row = $db->GetRow($query);
                return $row;
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }

    /*
     function lock_post($post_id, $user_id)
     Mark post as locked (being edited) by this user
     mReschke 2012-11-10
     */
    public function lock_post($post_id, $user_id) {
        try {
            if ($post_id > 0 && $user_id > 0) {
                $db = ADODB::connect();
                Tbl_post::unlock_post($post_id, $user_id);
                $query = "INSERT INTO tbl_post_lock (post_id, user_id, locked_on) VALUES ($post_id, $user_id, '".ADODB::dbnow()."')";
                $db->Execute($query);
            }
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }

    /*
     function unlock_post($id, $user_id, $is_topic_id=false)
     Unlock a post for this user, $id can be post_id (most efficient) or topic_id if $is_topic_id=true
     mReschke 2012-11-10
    */
    public function unlock_post($id, $user_id, $is_topic_id=false) {
        try {
            $db = ADODB::connect();
            if ($is_topic_id) {
                //Get post_id from topic_id
                $query = "SELECT post_id FROM tbl_post WHERE topic_id = $id";
                $id = $db->GetOne($query);
            }
            $query = "DELETE FROM tbl_post_lock WHERE post_id = $id AND user_id = $user_id";
            $db->Execute($query);
        } catch (exception $e) {
            Info::exception_handler($e, __file__, get_class($this), __FUNCTION__, __LINE__);
        }
    }
}

