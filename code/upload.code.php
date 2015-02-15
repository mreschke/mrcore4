<?php
#eval(Page::load_class('files'));
eval(Page::load_class('topic'));


//Get standalone URL variables (/upload/38)
$files->path = Page::get_variables(true);
$files->topic_id = Page::get_variable(0);

//Remove any real $_GET vars in URL
if (stristr($files->path, "?")) {
    $files->path = split("\?", $files->path);$files->path = $files->path[0];
    $files->topic_id = split("\?", $files->topic_id);$files->topic_id = $files->topic_id[0];
}

//Get Topic Information
$topic->tbl_post = Tbl_post::get_topic($info, $files->topic_id);

//Get Users Permissions for this topic
$topic->perms = Tbl_perm::get_permissions($info, $files->topic_id);

$files->path = urldecode($files->path);

//Now all variables are set, by URL if in standalone mode, or by preset $files-> variables if in include mode
if (isset($topic->tbl_post->post_id) && (count($topic->perms) > 0 || $topic->tbl_post->created_by==$info->user_id)) {

    //Get Permissions
    if ($info->admin || $topic->tbl_post->created_by==$info->user_id || Topic::has_perm($topic, "WRITE")) {
        $files->perm_write = true;
    }
    
    if ($files->perm_write) {
        if(is_dir(Config::FILES_DIR.'/'.$files->path)) {
            //Continue Uploading
            $view->css[] = Page::get_url('upload.css');
            $view->js[] = Page::get_url('uploader/jquery-1.3.2.js');
            $view->js[] = Page::get_url('uploader/swfupload.js');
            $view->js[] = Page::get_url('uploader/jquery.swfupload.js');
            /*$view->title = "Upload";*/
        } else {
            Page::redirect(Page::get_url('redirect').'/notopic/'.$files->topic_id);
        }
    } else {
        Page::redirect(Page::get_url('redirect').'/denied/'.$files->topic_id);
    }
} else {
    Page::redirect(Page::get_url('redirect').'/notopic/'.$files->topic_id);
}