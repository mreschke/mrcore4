<?php
if ($_GET['accesscode'] != Config::INDEXER_ACCESS_CODE) {
    exit('denied');
}
eval(Page::load_class('topic'));
eval(Page::load_class('badge'));
eval(Page::load_class('tag'));

GLOBAL $items;
$items =& $indexer->items;
$items = array();

$view->title = Config::APP_NAME." Indexer";
$view->css[] = Page::get_url('indexer.css');
//$view->js[] = Page::get_url('codepress/codepress.js');

//Fake super admin user credentials)
$info = new Info;
$info->tbl_user = new Tbl_user;
$info->tbl_user->user_id = Config::INDEXER_ADMIN_USER;
$info->tbl_user->perm_admin = 1;
$info->user_id = Config::INDEXER_ADMIN_USER;
$info->admin = 1;

#out("Indexing Started (".Page::get_url().")");
out("Indexing Started");

$indexer->topic_id = 0;
if (isset($_GET['topic'])) $indexer->topic_id = $_GET['topic'];

$indexer->fullindex = false;
if (isset($_GET['fullindex'])) $indexer->fullindex = true;


//Delete entire index if 'fullindex'
if ($indexer->fullindex) {
    out("fullindex flag found, resetting all indexed_on dates to 1900/01/01");
    $db = ADODB::connect();
    $query = "UPDATE tbl_post SET indexed_on = '1900/01/01 00:00:00'";
    $rs = $db->Execute($query);
}

//Index Single Topic
if (is_numeric($indexer->topic_id) && $indexer->topic_id > 0) {
    //Index single Topic and all comments (if not deleted)
    $indexer->header = "Indexing Single Topic";
    $topic->tbl_post = Tbl_post::get_topic($info, $indexer->topic_id);
    if (isset($topic->tbl_post)) {
        //Index Topic
        out("Indexing TID ".$topic->tbl_post->topic_id);
        Tbl_post::update_index(
            $topic->tbl_post->post_id,
            $topic->tbl_post->title,
            $topic->tbl_post->body,
            Tbl_badge::get_badges_array($indexer->topic_id),
            Tbl_tag::get_tags_array($indexer->topic_id)
        );
        
        //Index each comment in this topic
        $topic->tbl_post_comments = Tbl_post::get_comments($indexer->topic_id);
        foreach ($topic->tbl_post_comments as $topic->tbl_post_comment) {
            out("Indexing PID ".$topic->tbl_post_comment->post_id);
            Tbl_post::update_index(
                $topic->tbl_post_comment->post_id,
                $topic->tbl_post_comment->title,
                $topic->tbl_post_comment->body
            );
        }
    }
    
    
//Index All Topics and comments
} elseif (!isset($_GET['topic'])) {
    $indexer->header = "Indexing All Posts";
    
    //Index every undeleted topic and comment that has been updated or created after its index
    $topic->tbl_posts_unindexed = Tbl_post::get_unindexed_posts();
    if (count($topic->tbl_posts_unindexed) > 0) {
        foreach ($topic->tbl_posts_unindexed as $topic->tbl_post) {
            
            if ($topic->tbl_post->is_comment) {
                //Comment, so no badges/tags
                out("Indexing Comnt PID ".$topic->tbl_post->post_id);
                Tbl_post::update_index (
                    $topic->tbl_post->post_id,
                    $topic->tbl_post->title,
                    $topic->tbl_post->body
                );
            } else {
                //Topic
                out("Indexing Topic PID ".$topic->tbl_post->post_id);
                Tbl_post::update_index (
                    $topic->tbl_post->post_id,
                    $topic->tbl_post->title,
                    $topic->tbl_post->body,
                    Tbl_badge::get_badges_array($topic->tbl_post->topic_id),
                    Tbl_tag::get_tags_array($topic->tbl_post->topic_id)
                );
            }
        }
    } else {
        out("No posts to re-index");
    }
}
out("Indexing Complete");


function out($out) {
    GLOBAL $items;
    $items[] = ADODB::dbnow().' '.$out;
}

