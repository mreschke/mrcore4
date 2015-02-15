<?php
eval(Page::load_class('topic'));
eval(Page::load_class('badge'));
eval(Page::load_class('tag'));
eval(Page::load_class('user'));
eval(Page::load_part('files'));

$topic->topic_id = Page::get_variable(0);

if ($topic->topic_id <= 0) {
    //No topic defined in URL, get default topic (HOME PAGE)
    //$topic->topic_id = Config::DEFAULT_TOPIC;
    if (is_numeric(Config::HOME_URL)) {
        $topic->topic_id = Config::HOME_URL; //Home Page is a topic, load topic
    } else {
        Page::redirect(Config::HOME_URL); //Home Page is URL, redirect to URL;
    }
}

//Get topic (actually from tbl_post)
if (is_numeric($topic->topic_id)) {
    $topic->tbl_post = Tbl_post::get_topic($info, $topic->topic_id);

    if (!$view->viewmode_raw) {
        //Append User Global Topic Body
        if ($info->tbl_user->global_topic_id > 0 && $topic->topic_id != $info->tbl_user->global_topic_id) {
            $topic->tbl_post->body = Tbl_post::get_topic($info, $info->tbl_user->global_topic_id)->body . $topic->tbl_post->body;
        }

        //Append Global Topic Body - this one ends up comming first!
        if (Config::GLOBAL_TOPIC && $topic->topic_id != Config::GLOBAL_TOPIC) {
            $topic->tbl_post->body = Tbl_post::get_topic($info, Config::GLOBAL_TOPIC)->body . $topic->tbl_post->body;
        }
    }
}

if (isset($topic->tbl_post->topic_id)) {
    //Get Users Permissions for this topic
    $topic->perms = Tbl_perm::get_permissions($info, $topic->topic_id);

    //Get Topic Perm Groups and Shorts
    $perm->tbl_perm_groups_short_display = Tbl_perm::get_perm_groups_short_display($topic->topic_id);
    
    //Check read permissions
    if (!Topic::has_perm($topic, 'READ') && $topic->tbl_post->created_by != $info->tbl_user->user_id) {
        if (isset($_GET['uuid']) || count($info->uuids) > 0) {
            if ($_GET['uuid'] == $topic->tbl_post->post_uuid && $topic->tbl_post->uuid_enabled) {
                //Allow READ based on private uuid URL
                $topic->perms[] = 'READ';
                //Store this GUID in a session for use with the filemanager
                if (!in_array($_GET['uuid'], $info->uuids)) $info->uuids[] = $_GET['uuid'];
            } elseif (in_array($topic->tbl_post->post_uuid, $info->uuids)) {
                //Allow READ based on private uuid stored in session
                $topic->perms[] = 'READ';
            } else {
                Page::redirect(Page::get_url('redirect').'/denied/'.$topic->topic_id);     
            }
        } else {
            Page::redirect(Page::get_url('redirect').'/denied/'.$topic->topic_id); 
        }
    }
    
    //Redirect all topic/xx to topic/xx/topicname so search engines only get one URL
    //Shouldn't happen very often
    #if (Page::get_variable(1) == '') {
    #    Page::redirect(Page::get_url('topic').'/'.$topic->topic_id.'/'.$topic->tbl_post->title);
    #}

    //Get topic comments
    if ($_GET['deleted'] || $topic->tbl_post->deleted) $show_deleted = true;
    $topic->tbl_post_comments = Tbl_post::get_comments($topic->topic_id, $show_deleted);

    
    $view->title = $topic->tbl_post->title;
/*
    $view->header = "
        <table style='float:right;'><tr>";
        #if ($topic->tbl_post->deleted) {
            #$view->header .="<td><div id='topic_deleted_header'>".$topic->tbl_post->title." (DELETED TOPIC)</div></td>"  ;
        #} else {
            #$view->header .="<td>".$topic->tbl_post->title."</td>";
        #}
        
        $view->header .= "<td align='right'>
            <a href='#comments' class='m_bar_link'>
                <img src='".Page::get_url('bottom.gif')."' alt='down' border='0' />Comments (".intval($topic->tbl_post->comment_count).")
            </a>";
        #if ($info->is_authenticated) {
        #    $view->header .= "<a href='". Page::get_url('list')."' class='m_bar_link'>Subscribe</a>";
        #}            
        if ($topic->tbl_post->created_by == $info->user_id || Topic::has_perm($topic, 'WRITE')) {
            $view->header .= "<a href='". Page::get_url('edit')."/topic/".$topic->topic_id.'/'.urlencode($topic->tbl_post->title)."' class='m_bar_link'>Edit</a>";
            if ($topic->tbl_post->deleted) {
                $view->header .= "<a href='". Page::get_url('edit')."/undelete/topic/".$topic->topic_id."' class='m_bar_link'>Undelete</a>";    
            } else {
                $view->header .= "<a href='". Page::get_url('edit')."/delete/topic/".$topic->topic_id."' class='m_bar_link'>Delete</a>";
            }
        }
        if ($info->is_authenticated && ($topic->tbl_post->created_by == $info->user_id || $info->admin)) {
            $view->header .= "<a href='".Page::get_url('edit')."/topic/".$topic->topic_id."?backup=1' class='m_bar_link' title='Backup raw wiki code to this topics .backup hidden folder'>Backup</a>";
        }
    
        $view->header .= "</td>
            </tr></table>
    ";
*/
    
    //Insert topic into $info->recent for recent cookie crumb navigation
    User::insert_recent($info, $topic->topic_id, $topic->tbl_post->title);

    //Get Topic Badges
    if (!isset($badge->tbl_badges_intopic)) {
        //Get all badges in this topic
        $badge->tbl_badges_intopic = Tbl_badge::get_badges($info, $topic->topic_id);
    }

    //Get a list of tags in this topic_id (for tags just below the header)
    //Or for the soap icon if fixme tag is present
    if (!isset($tag->tbl_tags_intopic)) {
        $tag->tbl_tags_intopic = Tbl_tag::get_tags($info, $topic->topic_id);
    }
    
    //Set left menu items
    #$view->menu_left_parts[] = 'menu/badges';
    #$view->menu_left_parts[] = 'menu/sites';
    
    /*
    //Set right menu items for this topic page
    $view->menu_right_parts[] = 'menu/topic_about';
    $view->menu_right_parts[] = 'menu/topic_org';
    $view->menu_right_parts[] = 'menu/related';
    $view->menu_right_parts[] = 'menu/files';
    */
    
	#$view->menu_left_parts[] = 'menu/topic_org';
    #$view->menu_left_parts[] = 'menu/topic_about';
	#$view->menu_left_parts[] = 'menu/related';
    $view->menu_left_parts[] = 'menu/files';
    
    //Experimental IMAP connection (does work!)
    #if ($info->is_authenticated && $info->tbl_user->alias == 'mreschke') {
    #    $view->menu_left_parts[] = 'menu/imap';
    #}

    #$view->menu_left_hidden = true;
    
    //Load CSS and Javascript
    $view->js[] = Page::get_url("jquery.dataTables.min.js");
    $view->js[] = Page::get_url("jquery.dataTables.fixedHeader.min.js");
    $view->css[] = Page::get_url('files.css'); //Files CSS and JS because of menu/files 
    $view->js[] = Page::get_url('files.js');   //Files CSS and JS because of menu/files 
    $view->css[] = Page::get_url('wiki.css');
    $view->css[] = Page::get_url('topic.css'); //Should come after embeded files.css because it has overrides
    #$view->js[] = Page::get_url('codepress/codepress.js');
    $view->js[] = Page::get_url('datatables.js');
    $view->js[] = Page::get_url('topic.js');
    $view->css[] = Page::get_url('search.css'); #since you can embed <search> into a topic, need the search.css
    #$view->css_print[] = Page::get_url('topic_print.css');
    if (Topic::has_perm($topic, "WRITE")) {
        //Enable if you get the flash uploader to work
        #$view->js[] = Page::get_url('uploader/jquery-1.3.2.js');
        #$view->js[] = Page::get_url('uploader/swfupload.js');
        #$view->js[] = Page::get_url('uploader/jquery.swfupload.js');
    }

    
    //Update topic view count (only if your not the creator)
    if ($topic->tbl_post->created_by != $info->tbl_user->user_id) {
        Tbl_topic::update_view_count($topic->topic_id);
        $topic->tbl_post->view_count += 1;
    }
    
    //Update user/topic read status (except for anonymous user)
    if ($info->user_id != Config::STATIC_ANONYMOUS_USER) {
        Tbl_topic::read_topic($info->user_id, $topic->topic_id);
    }
    
    //Insert Tag topic content into body for any tags with a default_topic_id set
    foreach ($tag->tbl_tags_intopic as $tag->tbl_tag) {
        if ($tag->tbl_tag->default_topic_id) {
            $tmp = Tbl_post::get_topic($into, $tag->tbl_tag->default_topic_id);
            $topic->tbl_post->body = $tmp->body.$topic->tbl_post->body;
        }
    }

    //Get Post Locks
    if ($info->is_authenticated) {
        //Have to unlock all here.  Usually I unlock all on master page, but this is called before the master
        //So if I don't unlock here, then right when your done editing it will still think your still editing...
        if (!$_GET['skip_unlock']) {
            Topic::unlock_last_edited_post($info);
            $skip_unlock = true; #for master
        }
        $lock = Tbl_post::get_post_lock($topic->tbl_post->post_id);
        if ($lock) {
            $datetime1 = new DateTime(date("Y-m-d H:i:s"));
            $datetime2 = new DateTime($lock[2]);
            $interval = $datetime1->diff($datetime2);
            
            //Calc difference in minutes
            $ago = $interval->days * 24 * 60; #diff days in minutes
            $ago += $interval->h * 60; #plus diff hours in minutes
            $ago += $interval->i; #plus diff minutes

            //Delete lock if locked for more than 4 hours (240 minutes)
            if ($ago >= 240) {
                Tbl_post::unlock_post($topic->tbl_post->post_id, $lock[0]);
            } else {
                $lock_message = "This topic is currently being edited by $lock[1] as of $lock[2] ($ago minutes ago)";
            }
        }
    }
    
} else {
    unset($topic->topic_id);
    Page::redirect(Page::get_url('redirect').'/notopic/'.$topic->topic_id);
}
