<?php
eval(Page::load_class('rest')); //$rest variable initiated here
eval(Page::load_class('search'));
eval(Page::load_class('topic'));
eval(Page::load_class('helper/data'));
#eval(Page::load_class('badge'));
#eval(Page::load_class('tag'));

/*
 The search RESTful API
 This API is for all things search
 Official mRcore API Documentation: http://mreschke.com/edit/topic/255/mRcore4+API+Documentation
 API URL: http://mreschke.com/rest/v/search/xxxx.format
 mReschke 2011-06-14
*/

//Populate $rest Class Instance with request data (via POST, GET, PUT)
$rest = new Rest;
$rest = Rest::process_request();


//Authenticate (will exit and regurn 401 Unauthorized on fail)
$info = $rest->authenticate();

//Parse Parameters specific to this API
//Not needed here, since existing search.code.php does it all!

//Process the Request
//Since my existing search.code.php already does all the
//URL parsing work, I'll just call that here
//Oh Dang man! Yes!!! This populates $topic->tbl_topics !!!
//This ($from_api) help make my existing search.code.php more efficient by skipping a few things
$from_api = true; 
eval(Page::load_code('search')); 

//Remove Sensitive Data
foreach ($topic->tbl_topics as $topic->tbl_topic) {
    #$topic->tbl_topic->created_byTbl_user = null;
    #$topic->tbl_topic->updated_byTbl_user = null;
}

//Send Data in requested Format
if (!$rest->request_vars['plaintext']) {
    if ($rest->format == 'json') {
        Rest::send_response(200, json_encode($topic->tbl_topics), 'application/json');
    } elseif ($rest->format == 'xml') {
        $output = \Helper\Data::convert_rs_xml($topic->tbl_topics, 'Results', 'item');
        Rest::send_response(200, $output, 'application/xml');
    }
} else {
    //Return plaintext of body
    $raw = "";
    foreach ($topic->tbl_topics as $topic->tbl_topic) {
        $header = "#".$topic->tbl_topic->topic_id;
        $header .= " ".$topic->tbl_topic->title;
        $header .= " (by ".$topic->tbl_topic->created_byTbl_user->alias;
        $header .= " on ".$topic->tbl_topic->created_on.")\n";
        $raw .= $header;
        for ($i=0; $i <= strlen($header)-2; $i++) {
            $raw .= "-";
        }
        $raw .= "\n".$topic->tbl_topic->teaser;
        $raw .= "\n\n\n";
    }
    if ($raw == '') $raw = "No Topics Found\n\n";
    $found = count($topic->tbl_topics);
    $footer = "\n|| Found $found Topics, thanks ".$info->tbl_user->alias." for using the mRcore4 RESTful API!!! ||\n";
    $line = '';
    for ($i=0; $i <= strlen($footer)-5; $i++) {
        $line1 .= "=";
        $line2 .= "=";
    }
    $raw .= " ".$line1.$footer." ".$line2;
    
    
    
    Rest::send_response(200, $raw, 'text/html');
}
#foreach ($topic->tbl_topics as $topic->tbl_topic) {
#    echo $topic->tbl_topic->title."<br />";    
#}
