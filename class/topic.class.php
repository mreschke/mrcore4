<?php
eval(Page::load_model('post'));
eval(Page::load_model('topic'));
eval(Page::load_class('parser'));
eval(Page::load_class('indexer'));
eval(Page::load_class('api'));

$topic = new Topic;
GLOBAL $topic;

/*
 class Topic
 All topic work functions (non model)
 mReschke 2010-08-06
*/
class Topic {
    public $tbl_post;
    public $tbl_topic;
    public $tbl_topics;
    public $tbl_topics_related;
    public $tbl_post_comment;
    public $tbl_post_comments;
    public $tbl_posts_unindexed;
    public $topic_id;
    public $post_id;
    public $perms;
    public $unread_count;
    
    function __construct() {
        $this->tbl_post = new Tbl_post;
        $this->tbl_topic = new Tbl_topic;
        $this->tbl_post_comment = new Tbl_post;
    }

    public static function unlock_last_edited_post(Info $info) {
        if (Page::get_page() != '/edit') {
            #echo $_SERVER['HTTP_REFERER']."<br />";
            $last_page = $_SERVER['HTTP_REFERER'];
            if (preg_match("'/edit/topic/(.*?)(/|$)'", $last_page, $matches)) {
                Tbl_post::unlock_post($matches[1], $info->user_id, true); #id is a topic_id
                #echo "Last Topic Edited was ".$last_topic_id;
            } elseif (preg_match("'/edit/comment/(.*?)(/|$)'", $last_page, $matches)) {
                Tbl_post::unlock_post($matches[1], $info->user_id); #id is a post_id
                #echo "Last Comment Edited was ".$last_topic_id;
            }
        }

    }
    
    /*
     function has_perm(Topic $topic, $short) as boolean
     True if user/topic has this permission short
     Users groups are already stored in $info->perm_groups
     Users perms for this topic are already stored in $topic->perms;
     If user is admin, all these will be true
     mReschke 2010-09-02
    */
    public static function has_perm(Topic $topic, $short) {
        $found = false;
        #if (isset($topic->perms)) {
            foreach ($topic->perms as $perm) {
                if (strtolower($perm) == strtolower($short)) {
                    $found = true;
                    break;
                }
            }
        #}
        return $found;
    }
    
    /*
     function create_teaser($data, $len) string
     Trims all HTML and WIKI from $data and substr to $len
     mReschke 2010-09-04
    */
    public static function create_teaser($data, $len) {
        //Remove Some Tags and Data First

        //This REMOVES all contents between the <xxx> and </xxx> tags
        //Different than a strip HTML which leavs the text just removes the tags
        #$data = preg_replace('"<html>(\n|.?)*</html>"', '', $data); //This one crashed sometimes, would just kill all script here
        #$data = preg_replace('"<php>.*?</php>"sim', '', $data); //Beautiful multi line strip
        $data = preg_replace('"<auth>.*?</auth>"sim', '', $data); //Beautiful multi line strip
        $data = preg_replace('"<priv>.*?</priv>"sim', '', $data); //Beautiful multi line strip
        $data = preg_replace('"<html>.*?</html>"sim', '', $data); //Beautiful multi line strip
        $data = preg_replace('"<php>.*?</php>"sim', '', $data); //Beautiful multi line strip
        $data = preg_replace('"<phpw>.*?</phpw>"sim', '', $data); //Beautiful multi line strip

        $start = stripos($data, "<teaser>");
        $end = stripos($data, "</teaser>");
        if (($start && $end) || substr($data,0, 8) == '<teaser>') {
            //Found <teaser>...</teaser>
            GLOBAL $info, $topic;
            $data = Parser::parse_wiki($info, $topic, substr($data, $start+8, $end-$start-8), false);
        } else {
            //No <teaser> defined, create teaser from stripped trimmed body
            $data = strip_tags($data); //Remove HTML if makeing generic teaser
            $data = preg_replace('"\[\[(.|\n)*?\]\]"', '', $data); //Strip [[xxxx]]
            $data = preg_replace('"\[(.|\n)*?\]"', '', $data); //Strip [xxxx]
            $data = preg_replace('"\(\((.|\n)*?\)\)"', '', $data); //Strip ((xxxx))
            $data = preg_replace('"\+(.|\n)*?\n"', '', $data); //Strip +xxx
            
            #$data = preg_replace('"\#\#(.|)*?\|"', '', $data); //Strip ##xxxx|
            #$data = preg_replace('"\|\|\~"', '', $data); //Strip ||~
            #$data = preg_replace('"\|\|"', '', $data); //Strip ||
            #$data = preg_replace('"\/\/"', '', $data); //Strip //
            #$data = preg_replace('"\*\*"', '', $data); //Strip **
            #$data = preg_replace('"\'\'\'"', '', $data); //Strip '''
            #$data = preg_replace('"\_\_"', '', $data); //Strip __
            
            #$preg = array(
            #    '"\*\*|"',
                
            $data = preg_replace('"\*\*|\'\'\'|\_\_|\/\/|\|\|\~|\|\||\#\#(.|)*?\|"', '', $data);
            
            $data = preg_replace('"\* "', '', $data); //Strip *space
            $data = preg_replace('"\#\#"', '', $data); //Strip ##
            $data = preg_replace('"\# "', '', $data); //Strip #space 
            $data = preg_replace('"\`\`"', '', $data); //Strip ``
            $data = preg_replace('"\{\{"', '', $data); //Strip {{
            $data = preg_replace('"\}\}"', '', $data); //Strip }}
            $data = preg_replace('" \\\"', '', $data); //Strip  \ (space \)
            $data = preg_replace('"\@\@"', '', $data); //Strip @@
            $data = preg_replace('"\-\-\-"', '', $data); //Strip ---
            $data = preg_replace('"\-\-\-\-"', '', $data); //Strip ----
            $data = preg_replace('"\+\+\+"', '', $data); //Strip +++
            $data = preg_replace('"\r\n"', ' ', $data); //Strip \r\n with space
            #$data = preg_replace('"= "', '', $data); //Strip center tags
            #$data = preg_replace('", ,"', '', $data); //Strip dual comma space comma

            if (strlen($data) >= $len) {
                $data = trim(substr($data, 0, $len)).'...';
            }
        }

        return $data;
    }
    
}