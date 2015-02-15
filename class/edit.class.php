<?php
$edit = new Edit;

/*
 class Badge
 All badge work functions (non model)
 mReschke 2010-08-28
*/
class Edit {
    public $view_delete_hide;
    public $view_content_hide;
    public $view_preview_hide;
    public $view_org_hide;
    public $view_perm_hide;
    public $view_files_hide;
    public $view_admin_hide;
    public $view_button2_hide;
    public $users;
    

    /*
     function has_permission($topic_classs, $info, $new_topic, $new_comment, $edit_topic) boolean
     Check if user has permission to edit/create topic/comment
     mReschke 2010-09-02
    */
    public static function has_permission($topic_class, $info, $new_topic, $new_comment, $edit_topic, $edit_comment, $delete_topic, $delete_comment) {
        //Check Permissions
        $topic = new Topic;
        $topic = $topic_class;
        $granted = false;
        
        if ($info->tbl_user->perm_admin) {
            //Admin can do all
            $granted = true;
        } elseif ($new_topic && $info->tbl_user->perm_create) {
            //User allowed to create topic
            $granted = true;
        } elseif ($topic->tbl_post->created_by == $info->tbl_user->user_id && $info->tbl_user->user_id != Config::STATIC_ANONYMOUS_USER) {
            //The creator of this post can do all
            $granted = true;
        } elseif (($edit_comment || $delete_comment) && $topic->tbl_post->topic_idTbl_topic->created_by==$info->tbl_user->user_id) {
            //The creator of the TOPIC can edit any comment, even if its not their comment
            $granted = true;
        } elseif ($new_comment && Topic::has_perm($topic, 'COMMENT')) {
            //User allowed to comment on topic
            $granted = true;
        } elseif ($edit_topic && Topic::has_perm($topic, 'WRITE')) {
            //User allowed to edit topic
            $granted = true;
        } elseif ($delete_topic && Topic::has_perm($topic, 'WRITE')) {
            //User allowed to edit & therefore delete topic
            $granted = true;
        }
        return $granted;
    }
    
    /*
     function update_topic_perms($info, $topic_id, $_POST) null
     Edit page helper to update topic permissions from checkboxes
     $_POST is full $_POST, not just $_POST['something']
     Should only be called if $edit->view_perm_hide=false
     mReschke 2010-09-08
    */
    public static function update_topic_perms($info, $topic_id, $POST) {
        //Users can only edit perms groups they have access too
        //So instead of deleting all from tbl_perm_link, I only delete by their groups
        Tbl_perm::delete_perm_links($topic_id, $info->perm_groups);

        //Add all checked checkbox permissions to database
        //Checkbox values are x_x (permGroupID_permID);
        while (list($key, $value) = each($POST)) {
            if (substr($key, 0, 9) == 'chk_perm_') {
                $tmp = split("_", $value);
                $perm_group_id = $tmp[0];
                $perm_id = $tmp[1];
                Tbl_perm::insert_perm_link($topic_id, $perm_group_id, $perm_id);
            }
        }
    }
    
    /*
     function update_topic_badges($topic_class, $lst_badges) int of badges selected
     Edit page helper to update topic badges
     mReschke 2010-08-30
    */
    public static function update_topic_badges($topic_class, $lst_badges, $token_badges) {
        $topic = new Topic;
        $topic = $topic_class;

        //Delete all badge links
        Tbl_badge::delete_badge_links($topic->topic_id);
        
        //Add all badges from listbox and autocomplete box to unique array of IDs
        if (!isset($lst_badges)) $lst_badges = array();
        if (trim($token_badges) != '') $lst_badges = array_merge($lst_badges, split(",", $token_badges));
        $lst_badges = array_unique($lst_badges);
        
        //Add each badge to database
        foreach ($lst_badges as $badge) {
            Tbl_badge::insert_badge_link($topic->topic_id, $badge);
        }        
        
        return count($lst_badges);

    }
    
    /*
     function update_topic_tags($topic_class, $lst_tags, $txt_new_tag)
     Edit page helper to update topic tags
     mReschke 2010-08-30
    */
    public static function update_topic_tags($topic_class, $lst_tags, $token_tags, $txt_new_tag) {
        $topic = new Topic;
        $topic = $topic_class;
        
        //Delete all tag links
        Tbl_tag::delete_tag_links($topic->topic_id);
        
        //Add all tags from listbox and autocomplete box to unique array of IDs
        if (!isset($lst_tags)) $lst_tags = array();
        if (trim($token_tags) != '') $lst_tags = array_merge($lst_tags, split(",", $token_tags));
        $lst_tags = array_unique($lst_tags);
        
        //Add each tag to database
        foreach ($lst_tags as $tag) {
            Tbl_tag::insert_tag_link($topic->topic_id, $tag);
        }        
        
        //Insert and link up new tags
        $txt_new_tag = trim(strtolower($txt_new_tag));
        if ($txt_new_tag != '') {
            $new_tags = split(",", $txt_new_tag);
            for ($i=0; $i < count($new_tags); $i++) {
                $new_tag = trim($new_tags[$i]);
                $new_tag_id = Tbl_tag::insert_tag_item($new_tag);
                if ($new_tag_id > 0) {
                    //Tag inserted, now link this new tag to this topic
                    Tbl_tag::insert_tag_link($topic->topic_id, $new_tag_id);
                }
            }
        }        
    }
    
    public static function update_post_created_updated(Tbl_post &$tbl_post, $creator_id, $created_on, $updater_id, $updated_on) {
        if ($creator_id > -1) {
            //Creator has changed, subtract topic count from original creator and add topic count to new creator
            if ($tbl_post->is_comment) {
                Tbl_user::update_comment_count($tbl_post->created_by, -1);
                Tbl_user::update_comment_count($creator_id, 1);
            } else {
                Tbl_user::update_topic_count($tbl_post->created_by, -1);
                Tbl_user::update_topic_count($creator_id, 1);
            }
            $tbl_post->created_by = $creator_id;
        }
        if (strlen($created_on) == 19) $tbl_post->created_on = $created_on;
        if ($updater_id > -1) $tbl_post->updated_by = $updater_id;
        if (strlen($updated_on) == 19) $tbl_post->updated_on = $updated_on;
    }
    
}
