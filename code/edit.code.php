<?php
eval(Page::load_class('topic'));
eval(Page::load_class('badge'));
eval(Page::load_class('tag'));
eval(Page::load_class('perm'));
eval(Page::load_class('edit'));
eval(Page::load_class('files'));

//Recognize Events
$event_save=$event_delete=$event_delete=$event_undelete=$event_cancel=false;
if (isset($_POST['btn_save'])) {
    $event_save = true;
} elseif (isset($_POST['btn_save_view'])) {
    $event_save = true;
    $event_save_view = true;
} elseif (isset($_POST['__EVENTTARGET']) && $_POST['__EVENTTARGET'] == 'btn_cancel') {
    $event_cancel = true;
} elseif (isset($_POST['__EVENTTARGET']) && $_POST['__EVENTTARGET'] == 'btn_delete') {
    $event_delete = true;
} elseif (isset($_POST['__EVENTTARGET']) && $_POST['__EVENTTARGET'] == 'btn_undelete') {
    $event_undelete = true;
} elseif (isset($_GET['backup'])) {
    $event_backup = true;
} elseif (isset($_POST['btnRemoveLock'])) {
    $event_remove_lock = true;
}



//Determine action by URL
$edit_topic=$edit_comment=$new_topic=$new_comment=$delete_something=$delete_topic=$delete_comment=false;
$var1 = strtolower(Page::get_variable(0));
$var2 = strtolower(Page::get_variable(1));
$var3 = strtolower(Page::get_variable(2));
$topic->topic_id = 0;
$topic->post_id = 0;

switch ($var1) {
    case 'topic':
        $edit_topic = true;
        $title = 'Edit Topic';
        $topic->topic_id = $var2;
        $edit->view_preview_hide = true;
        #$edit->view_files_hide = true; #Temp for production, no file view on edit
        break;
    case 'comment':
        $edit_comment = true;
        $title = 'Edit Comment';
        $topic->post_id = $var2;
        $edit->view_org_hide = true;
        $edit->view_perm_hide = true;
        $edit->view_files_hide = true;
        //$edit->view_admin_hide = true;
        $edit->view_button2_hide = true;
        break;
    case 'newtopic':
        $new_topic = true;
        $title = 'Create Topic';
        $edit->view_preview_hide = true;
        #$edit->view_files_hide = true; #Temp for production, no file view on edit
        break;
    case 'newcomment':
        $new_comment = true;
        $title = 'Add Comment';
        $topic->topic_id = $var2;
        $edit->view_org_hide = true;
        $edit->view_perm_hide = true;
        $edit->view_files_hide = true;
        //$edit->view_admin_hide = true;
        $edit->view_button2_hide = true;
        break;
    /*case 'delete' || 'undelete':
        $delete_something = true;
        if ($var2 == "comment") {
            $delete_comment = true;
            if ($var1 == "delete") {$title = 'Delete Comment';}else{$title = 'Unelete Comment';}
            $topic->post_id = $var3;
        } elseif ($var2 == "topic") {
            $delete_topic = true;
            if ($var1 == "delete") {$title = 'Delete Topic';}else{$title = 'Undelete Topic';}
            $topic->topic_id = $var3;
        }
        $edit->view_content_hide = true;
        $edit->view_preview_hide = true;
        $edit->view_org_hide = true;
        $edit->view_perm_hide = true;
        $edit->view_files_hide = true;
        $edit->view_admin_hide = true;
        $edit->view_button2_hide = true;
        break;*/
    default:
        Page::redirect(Page::get_url('redirect').'/critical');
}


/**
################################################################################
*/
//Get Post Data
if (($edit_topic || $new_comment || $delete_topic) && is_numeric($topic->topic_id) && $topic->topic_id > 0) {
    $topic->tbl_post = Tbl_post::get_topic($info, $topic->topic_id);
} elseif (($edit_comment || $delete_comment) && (is_numeric($topic->post_id) && $topic->post_id > 0)) {
    $topic->tbl_post = Tbl_post::get_post($info, $topic->post_id);
}
$topic->topic_id = $topic->tbl_post->topic_id;
$topic->post_id = $topic->tbl_post->post_id;

if ((!$new_topic && !isset($topic->tbl_post->post_id))) {
    //Invalid URL
    Page::redirect(Page::get_url('redirect').'/notopic/'.$topic->topic_id);
}

//Get Users Permissions for this topic
if ($new_topic || $topic->tbl_post->created_by == $info->tbl_user->user_id) {
    $topic->perms = Tbl_perm::get_permissions($info, 0); //get all perm items
} else {
    $topic->perms = Tbl_perm::get_permissions($info, $topic->topic_id); //get perm items this user has access to for this topic
}

//Check edit permissions
if (!Edit::has_permission($topic, $info, $new_topic, $new_comment, $edit_topic, $edit_comment, $delete_topic, $delete_comment)) {
    //Edit Access Denied
    Page::redirect(Page::get_url('redirect').'/denied/'.$topic->topic_id);
} else {
    //Has permissions, but hide certian things
    if (!$info->admin) {
        //Hide admin functions
        $edit->view_admin_hide = true;
        
        //Hide permissions if not creator
        #hum, not sure i like this
        #if (!$new_topic && $topic->tbl_post->created_by != $info->tbl_user->user_id) {
            #$edit->view_perm_hide = true;
        #}

        //Hide permissions if user can not create
        if (!$info->tbl_user->perm_create) {
            $edit->view_perm_hide = true;  
        }

        if (!$delete_something) {
            //Deny edit if wiki has execution code but user cannot write execution code
            if ($topic->tbl_post->has_exec && !$info->tbl_user->perm_exec) {
                Page::redirect(Page::get_url('redirect').'/denied/'.$topic->topic_id);
            }

            //Deny edit if wiki has HTML code but user cannot write HTML code
            if ($topic->tbl_post->has_html && !$info->tbl_user->perm_html) {
                Page::redirect(Page::get_url('redirect').'/denied/'.$topic->topic_id);
            }
        }
    }
}

//Add title to newtopic from URL
if ($new_topic & Page::get_variable(1) != '') {
    $topic->tbl_post->title = urldecode(Page::get_variable(1));
}


/**
################################################################################
*/
//Event Actions
if ($event_save) {
    //Load $post from input forms
    $previous_hidden = $topic->tbl_post->hidden;
    $topic->tbl_post->hidden = $_POST['chk_hidden'];
    $topic->tbl_post->uuid_enabled = $_POST['chk_public_sharing'];
    $topic->tbl_post->title = trim($_POST['txt_title']);
    $topic->tbl_post->body = $_POST['txt_body'];
    $topic->tbl_post->updated_by = $info->tbl_user->user_id;
    $topic->tbl_post->updated_on = ADODB::dbnow();
    if (trim($topic->tbl_post->body) == '') $topic->tbl_post->body = 'Empty Body';
    if (isset($_POST['chk_indexer'])) $index=true; else $index=false;

    //Determine if post contains execution code (<php></php>, <cmd>...)
    $topic->tbl_post->has_exec = false;
    if (preg_match('"<php>|<phpw>|<cmd "', $topic->tbl_post->body)) {
        $topic->tbl_post->has_exec = true;
    }

    //Determine if post contains HTML code
    $topic->tbl_post->has_html = false;
    if (preg_match('"<html>"', $topic->tbl_post->body)) {
        $topic->tbl_post->has_html = true;
    }

    if (!$info->admin) {
        //Remove execution code if user cannot write execution code
        if ($topic->tbl_post->has_exec && !$info->tbl_user->perm_exec) {
            $topic->tbl_post->body = preg_replace('"<php>|</php>|<phpw>|</phpw>|<cmd "', '<permission denied>', $topic->tbl_post->body);
            $topic->tbl_post->has_exec = false;
        }

        //Remove HTML code if user cannot write HTML
        if ($topic->tbl_post->has_html && !$info->tbl_user->perm_html) {
            $topic->tbl_post->body = preg_replace('"<html>|</html>"', '<permission denied>', $topic->tbl_post->body);
            $topic->tbl_post->has_html = false;
        }
    }

    
    if ($edit_topic) {
        //Override post created/updated (admin override)
        Edit::update_post_created_updated($topic->tbl_post, $_POST['lst_created_by'], $_POST['txt_created_on'], $_POST['lst_updated_by'], $_POST['txt_updated_on']);

        //Update topic
        Tbl_post::update_post($topic->tbl_post);
        
        //Update topic badges
        Edit::update_topic_badges($topic, $_POST['lst_badges'], $_POST['edit_tokenize_badge_txt']);
                    
        //Update topic tags
        Edit::update_topic_tags($topic, $_POST['lst_tags'], $_POST['edit_tokenize_tag_txt'], $_POST['txt_new_tag']);
        
        //Update topic permissions
        if (!$edit->view_perm_hide) {
            Edit::update_topic_perms($info, $topic->topic_id, $_POST);
        }
        
        //Unread topic
        Tbl_topic::unread_topic($topic->topic_id);
        
        //Update post search index
        if ($index) Tbl_post::update_index($topic->post_id, $topic->tbl_post->title, $topic->tbl_post->body, Tbl_badge::get_badges_array($topic->topic_id), Tbl_tag::get_tags_array($topic->topic_id));

        if ($event_save_view) Page::redirect(Page::get_url('topic').'/'.$topic->topic_id.'/'.urlencode($topic->tbl_post->title));
        
    } elseif ($edit_comment) {
        //Override post created/updated (admin override)
        Edit::update_post_created_updated($topic->tbl_post, $_POST['lst_created_by'], $_POST['txt_created_on'], $_POST['lst_updated_by'], $_POST['txt_updated_on']);
        
        //Update comment
        Tbl_post::update_post($topic->tbl_post);
        
        //Unread topic
        Tbl_topic::unread_topic($topic->topic_id);
        
        //Update post search index
        if ($index) Tbl_post::update_index($topic->post_id, $topic->tbl_post->title, $topic->tbl_post->body, Tbl_badge::get_badges_array($topic->topic_id), Tbl_tag::get_tags_array($topic->topic_id));
        
        if ($event_save_view) Page::redirect(Page::get_url('topic').'/'.$topic->topic_id.'#'.$topic->post_id);
        
    } elseif ($new_topic) {
        //Insert new Topic
        $topic->tbl_post->created_by = $info->tbl_user->user_id;
        $topic->tbl_post->created_on = ADODB::dbnow();
        $topic->tbl_post->is_comment = 0;

        //Override post created/updated (admin override) (comes before insert_post)
        Edit::update_post_created_updated($topic->tbl_post, $_POST['lst_created_by'], $_POST['txt_created_on'], $_POST['lst_updated_by'], $_POST['txt_updated_on']);

        //Insert New Topic
        $topic->topic_id = Tbl_post::insert_post($topic->tbl_post);

        
        //Update topic badges
        Edit::update_topic_badges($topic, $_POST['lst_badges'], $_POST['edit_tokenize_badge_txt']);
    
        //Update topic tags
        Edit::update_topic_tags($topic, $_POST['lst_tags'], $_POST['edit_tokenize_tag_txt'], $_POST['txt_new_tag']);
        
        //Update topic permissions
        if (!$edit->view_perm_hide) {
            Edit::update_topic_perms($info, $topic->topic_id, $_POST);
        }
        
        //Update users topic count
        Tbl_user::update_topic_count($info->tbl_user->user_id, 1);
        
        //Create topics file folder
        Files::create_file_folder($topic->topic_id);
        
        //Update post search index
        if ($index) Tbl_post::update_index($topic->post_id, $topic->tbl_post->title, $topic->tbl_post->body, Tbl_badge::get_badges_array($topic->topic_id), Tbl_tag::get_tags_array($topic->topic_id));
        
        if ($event_save_view) {
			Page::redirect(Page::get_url('topic').'/'.$topic->topic_id);
		} else {
			//Must refresh page to new topic URL or else will still be on /edit/newtopic (would create multiples on save)
			Page::redirect(Page::get_url('edit').'/topic/'.$topic->topic_id);
		}
        
    } elseif ($new_comment) {
        //Insert new comment
        $topic->tbl_post->created_by = $info->tbl_user->user_id;
        $topic->tbl_post->created_on = ADODB::dbnow();
        $topic->tbl_post->hidden = 0;
        $topic->tbl_post->is_comment = 1;
        
        //Override post created/updated (admin override) (comes before insert_post)
        Edit::update_post_created_updated($topic->tbl_post, $_POST['lst_created_by'], $_POST['txt_created_on'], $_POST['lst_updated_by'], $_POST['txt_updated_on']);

        //Insert new Comment
        $topic->post_id = Tbl_post::insert_post($topic->tbl_post);
        
        //Update topics comment count
        Tbl_topic::update_comment_count($topic->topic_id, 1);
        
        //Update users comment count
        Tbl_user::update_comment_count($info->tbl_user->user_id, 1);
        
        //Unread topic
        Tbl_topic::unread_topic($topic->topic_id);
        
        //Update post search index
        if ($index) Tbl_post::update_index($topic->post_id, $topic->tbl_post->title, $topic->tbl_post->body, Tbl_badge::get_badges_array($topic->topic_id), Tbl_tag::get_tags_array($topic->topic_id));
        
        if ($event_save_view) {
			Page::redirect(Page::get_url('topic').'/'.$topic->topic_id.'#'.$topic->post_id);
		} else {
			//Must refresh page to new topic URL or else will still be on /edit/newtopic (would create multiples on save)
			Page::redirect(Page::get_url('edit').'/comment/'.$topic->post_id);
		}
        
    } else {
        Page::redirect(Page::get_url('redirect').'/critical');
    }
    

} elseif ($event_delete || $event_undelete) { //Handles Delete & Undelete

    if ($edit_topic) {
        //Delete or Undelete Topic
        
        $num = -1; //Subtract one count
        if ($topic->tbl_post->deleted) $num = 1; //Undeleting, so add counts back
        
        //Subtract (add if undelete) from each users comment count And Delete its index
        $topic->tbl_post_comments = Tbl_post::get_comments($topic->topic_id);
        if (isset($topic->tbl_post_comments)){
            foreach ($topic->tbl_post_comments as $topic->tbl_post_comment) {
                Tbl_user::update_comment_count($topic->tbl_post_comment->created_by, $num);
                if (!$topic->tbl_post->deleted) {
                    //Deleting topic, so delete index too for this comment
                    Tbl_post::update_index($topic->tbl_post_comment->post_id); //Delete comments index
                }
            }
        }

        //Remove all permissions (makes it private)
        if (!$topic->tbl_post->deleted) {
            Tbl_perm::delete_all_perm_links($topic->topic_id);
        }
        
        //Subtract (add if undelete) one from users topic count
        Tbl_user::update_topic_count($topic->tbl_post->created_by, $num);
        
        //Subtract (add if undelete) one from each badges topic count
        $badge->tbl_badges_intopic = Tbl_badge::get_badges($info, $topic->topic_id);
        if (isset($badge->tbl_badges_intopic)) {
            foreach ($badge->tbl_badges_intopic as $badge->tbl_badge) {
                Tbl_badge::update_topic_count($badge->tbl_badge->badge_id, $num);
            }
        }

        //Subtract (add if undelete) one from each tags topic count
        $tag->tbl_tags_intopic = Tbl_tag::get_tags($info, $topic->topic_id);
        if (isset($tag->tbl_tags_intopic)) {
            foreach ($tag->tbl_tags_intopic as $tag->tbl_tag) {
                Tbl_tag::update_topic_count($tag->tbl_tag->tag_id, $num);
            }
        }
        
        if ($topic->tbl_post->deleted) {
            //Undelete Topic
            Tbl_topic::undelete_topic($info, $topic->topic_id);
            Page::redirect(Page::get_url('topic').'/'.$topic->topic_id);
        } else {
            //Delete Topic and Index
            Tbl_topic::delete_topic($info, $topic->topic_id); #which cascades all comments
            Tbl_post::update_index($topic->tbl_post->post_id);
            Page::redirect(Page::get_url('topic').'/'.Config::DEFAULT_TOPIC);
        }


    } elseif ($edit_comment) {
        
        if (!$topic->tbl_post->deleted) {
            //Delete Comment

            //Subtract one from users comment count
            Tbl_user::update_comment_count($topic->tbl_post->created_by, -1);
            
            //Subtract one from topics comment count
            Tbl_topic::update_comment_count($topic->topic_id, -1);
            
            //Delete Comment
            Tbl_post::delete_comment($info, $topic->post_id);
            
            //Delete comments index
            Tbl_post::update_index($topic->post_id); 
        
        } else {
            //Undelete Comment

            //Add one from users comment count
            Tbl_user::update_comment_count($topic->tbl_post->created_by, 1);
            
            //Add one from topics comment count
            Tbl_topic::update_comment_count($topic->topic_id, 1);
            
            //Unelete Comment
            Tbl_post::undelete_comment($info, $topic->post_id);
        }
        Page::redirect(Page::get_url('topic').'/'.$topic->topic_id);
    }


} elseif ($event_cancel) {
    if ($new_topic) {
        Page::redirect(Page::get_url('topic'));
    } else {
        if ($edit_comment || $new_comment) {
            Page::redirect(Page::get_url('topic').'/'.$topic->topic_id.'/'.urlencode($topic->tbl_post->title).'#'.$topic->tbl_post->post_id);
        } else {
            Page::redirect(Page::get_url('topic').'/'.$topic->topic_id.'/'.urlencode($topic->tbl_post->title));
        }
    }

    
} elseif ($event_backup) {
    if ($info->is_authenticated && ($topic->tbl_post->created_by == $info->user_id || $info->admin)) {
        $backup_dir = Config::FILES_DIR.'/'.$topic->topic_id.'/.sys/backups';
        $backup_file = date("Y-m-d").'_'.date("H-i-s").'_topic_'.$topic->topic_id.'_backup.txt';
        if (!is_dir($backup_dir)) exec("mkdir -p $backup_dir");
        $fp = fopen($backup_dir.'/'.$backup_file, 'w');
        fwrite($fp, $topic->tbl_post->body);
        fclose($fp);
        Page::redirect(Page::get_url('topic').'/'.$topic->topic_id);
    }
}



/**
################################################################################
*/
//Load the Tag Autocomplete Tokenized Stuff
//Found http://loopj.com/2009/04/25/jquery-plugin-tokenizing-autocomplete-text-entry/
//mReschke 2010-10-05
$view->css[] = Page::get_url('token-input.css');
#$view->js[] = Page::get_url('jquery-1.4.2.min.js');
#$view->js[] = Page::get_url('jquery.min.js');
$view->js[] = Page::get_url('jquery.tokeninput.js');
$view->title = $title.' '.$topic->tbl_post->title;
$view->header = '';



//Set File Stuff
if (!$new_topic) {
    $files->path = $files->topic_id = $topic->topic_id;
    if (is_dir(Config::FILES_DIR.'/'.$topic->topic_id)) {
        eval(Page::load_code('part/files'));
        $files->embed = 1;
        load_files_code();
    } else {
        $files->path = null;
    }
}


#$view->css[] = Page::get_url('wiki.css');
$view->css[] = Page::get_url('edit.css');
if (Config::ENABLE_WYSIWYG) {
    $view->js[] = Page::get_url('wysiwyg.js');
}
$view->js[] = Page::get_url('edit.js');




//Set new Defaults or load Selected Template
if ($new_topic) {
    //Load up Topic Templates Array
    if (Config::NEW_TOPIC_TEMPLATE_TAG_ID > 0) {
        $templates = array();
        if (Config::NEW_TOPIC_TEMPLATE_TOPIC_ID > 0) {
            $templates[Config::NEW_TOPIC_TEMPLATE_TOPIC_ID] = "Default Topic Template";
        }
        if (Config::NEW_TOPIC_TEMPLATE_TAG_ID > 0) {
            $templates += Tbl_topic::get_quick_topics_by_tag(Config::NEW_TOPIC_TEMPLATE_TAG_ID);
        }
    }

    //Set body based on template
    if (isset($_POST['sel_template'])) {
        $topic->tbl_post->body = Tbl_post::get_topic($info, $_POST['sel_template'])->body;
    } elseif (Config::NEW_TOPIC_TEMPLATE_TOPIC_ID > 0) {
        //Set the source of this default topic as the body template
        $topic->tbl_post->body = Tbl_post::get_topic($info, Config::NEW_TOPIC_TEMPLATE_TOPIC_ID)->body;
    } else {
        //No default topic template topicID set, use this default
        $topic->tbl_post->body = 'Default body text';
    }
} elseif ($new_comment) {
    //Default title of new Comment
    $topic->tbl_post->title = "Re: ".$topic->tbl_post->title;
    $topic->tbl_post->body = "";
}

if (!$edit->view_org_hide) {
    //Get a list of ALL tags with a flag column set on the badges linked to this $topic_id
    $tag->tbl_tags_all_selected = Tbl_tag::get_tags_with_links($topic->topic_id);
    
    //Get a list of ALL badges with a flag column set on the badges linked to this $topic_id
    $badge->tbl_badges_all_selected = Tbl_badge::get_badges_with_links($topic->topic_id);
}

if (!$edit->view_perm_hide) {
    //Get perm/groups user has access too, with a column flag if group/perm selected for this $topic_id
    $perm->tbl_perms_all_selected = Tbl_perm_custom_all_selected::get_groups_and_perms_with_links($topic->topic_id, $info->tbl_user->user_id);
    $perm->tbl_perm_groups = Tbl_perm::get_groups($info->user_id);
}

if (!$edit->view_admin_hide) {
    //Get array of enabled users for created/updated by select boxes
    $edit->users = Tbl_user::get_users_array();
}

//Get Post Locks
$lock = Tbl_post::get_post_lock($topic->post_id);
if (!$delete_something) {
    if ($lock) {
        if ($event_remove_lock) { 
            Tbl_post::unlock_post($topic->post_id, $lock[0]);
            Page::redirect(Page::get_url('edit/topic').'/'.$topic->topic_id.'/'.urlencode($topic->tbl_post->title));
        } elseif (!isset($_POST['__EVENTTARGET']) && !$event_save) {
            $datetime1 = new DateTime(date("Y-m-d H:i:s"));
            $datetime2 = new DateTime($lock[2]);
            $interval = $datetime1->diff($datetime2);
            $hours   = $interval->format('%h');
            $minutes = $interval->format('%i');
            $ago = $hours * 60 + $minutes;

            $view->error = "
                This Post is Locked.<br /><br />It is currently being edited by $lock[1]<br />as of $lock[2] ($ago minutes ago)
                <p><input type='submit' name='btnRemoveLock' value='Force Remove Lock' /></p>
            ";
            $view->error_close = "window.location = '".$_SERVER['HTTP_REFERER']."?skip_unlock=1ag'";
            #$view->error_close_hide = true;
        }
    } else {
        //Lock Post
        if (!$edit_comment && !$new_comment && !$delete_comment && !$delete_topic) {
            Tbl_post::lock_post($topic->post_id, $info->user_id);
        }
    }
}