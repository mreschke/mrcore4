<?php
eval(Page::load_class('rest')); //$rest variable initiated here
eval(Page::load_class('topic'));
eval(Page::load_class('badge'));
eval(Page::load_class('tag'));
eval(Page::load_class('helper/data'));
/*
 The topic RESTful API
 This API is for all things topic, since edit is in its own page, edit function are not included here
 So this is basically just the API to get a topic
 Official mRcore API Documentation: http://mreschke.com/edit/topic/255/mRcore4+API+Documentation
 API URL: http://mreschke.com/rest/v/topic/tid.format
 mReschke 2011-06-13
*/

//Populate $rest Class Instance with request data (via POST, GET, PUT)
$rest = new Rest;
$rest = Rest::process_request();


//Authenticate (will exit and return 401 Unauthorized on fail)
$info = $rest->authenticate();

//Parse Parameters specific to this API
$var1 = $rest->url_vars[0];
if ($rest->method == 'post') {
    //Variables passed by POST method
    $topic_id = $rest->request_vars['topic_id'];
} else {
    //Variables passed by GET method
    if (stristr($var1, ".")) {
        $var1 = explode(".", $rest->url_vars[0]);
        $topic_id = $var1[0];
        $rest->http_accept = $var1[1];
    } else {
        $topic_id = $var1;
    }
}


//Process the Request
$topic->tbl_post = Tbl_post::get_topic($info, $topic_id);
if (isset($topic->tbl_post->topic_id)) {
    $topic->topic_id = $topic->tbl_post->topic_id;
	
    //Permission Check
    $topic->perms = Tbl_perm::get_permissions($info, $topic->topic_id);
    if (!Topic::has_perm($topic, 'READ') && $topic->tbl_post->created_by != $info->tbl_user->user_id) {
        Rest::send_response(401); //401 Unauthorized
    }

    //Update topic view count (only if your not the creator)
    if ($topic->tbl_post->created_by != $info->tbl_user->user_id) {
        Tbl_topic::update_view_count($topic->topic_id);
        $topic->tbl_post->view_count += 1;
    }                
	
	//Create the Interface
	//Instead of using the direct return from get_topic we need to create our own class interface for the perfect XML/json structured return
	//This is an API interface, since we return our own class structure here, it doesn't matter what I change the actual model too, this
	//API won't break!  Its an interface to the model, not the model directly!
	$ret = null;
	$topic->tbl_topic = Tbl_topic::get_topic($topic->topic_id);
	$ret->topic_id = $topic->topic_id;
	$ret->post_id = $topic->tbl_post->post_id;
	$ret->title = $topic->tbl_post->title;
	$ret->teaser = $topic->tbl_topic->teaser;
	$ret->body = $topic->tbl_post->body;
	$ret->comments = Tbl_post::get_comments($topic->topic_id); //This needs its own interface sometime, currently uses direct model return :(
	$ret->badges = Tbl_badge::get_badges($info, $topic->topic_id); //This needs its own interface
	$ret->tags = Tbl_tag::get_tags($info, $topic->topic_id); //This needs its own interface
	$ret->view_count = $topic->tbl_post->view_count;
	$ret->comment_count = $topic->tbl_post->comment_count;
	$ret->deleted = $topic->tbl_post->deleted;
	$ret->created_by = $topic->tbl_post->created_by;
	$ret->created_byTbl_user = $topic->tbl_post->created_byTbl_user;
	$ret->created_on = $topic->tbl_post->created_on;
	$ret->updated_by = $topic->tbl_post->updated_by;
	$ret->updated_byTbl_user = $topic->tbl_post->updated_byTbl_user;
	$ret->updated_on = $topic->tbl_post->updated_on;

    //Remove Sensitive Data
	//Must remove any <auth>..</auth> and <priv>..</priv> content from topic body
	if (!$info->is_authenticated) {
		$topic->tbl_post->body = preg_replace('"<auth>.*?</auth>"sim', '', $topic->tbl_post->body);
        unset($ret->created_byTbl_user->email);
        unset($ret->updated_byTbl_user->email);
	}
	if ($info->is_authenticated && ($info->admin || $topic->tbl_post->created_by == $info->user_id)) {
	} else {
		$topic->tbl_post->body = preg_replace('"<priv>.*?</priv>"sim', '', $topic->tbl_post->body);
        $topic->tbl_post->body = preg_replace('"<php>.*?</php>"sim', '', $topic->tbl_post->body);
        $topic->tbl_post->body = preg_replace('"<phpw>.*?</phpw>"sim', '', $topic->tbl_post->body);
	}

    
    //Send Data in requested Format
    if (!$rest->request_vars['plaintext']) {
        if ($rest->format == 'json') {
            Rest::send_response(200, json_encode($ret), 'application/json');
        } elseif ($rest->format == 'xml') {
			$output = \Helper\Data::convert_rs_xml($ret, 'Topic', 'item');
	        Rest::send_response(200, $output, 'application/xml');			
        }
    } else {
        //Return plaintext of body
        Rest::send_response(200, $topic->tbl_post->body, 'text/plain');
    }
} else {
    //Topic Not Found
    Rest::send_response(404); //404 Not Found
}

