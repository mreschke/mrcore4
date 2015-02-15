<?php
eval(Page::load_class('search'));
eval(Page::load_class('topic'));
eval(Page::load_class('badge'));
eval(Page::load_class('tag'));
eval(Page::load_class('user'));


function load_search_code() {
    GLOBAL $search, $topic, $badge, $tag, $info, $view;

    if (!$from_api) {
        $view->js[] = Page::get_url("jquery.dataTables.min.js");
        $view->js[] = Page::get_url("jquery.dataTables.fixedHeader.min.js");
        $view->js[] = Page::get_url('search.js');
        $view->css[] = Page::get_url('wiki.css');
        $view->css[] = Page::get_url('search.css');
        $view->title = 'Search';
    }

    #Note, all URLs (like badges, tags...) are Names, NOT IDS
    #So I will convert all names to IDS, keeping both string/arrays or names/ids


    # /search/query/badge/tag/options   
    # /search/query/badge/options
    # /search/query/options
    # /search/options

    if (!$from_api) {
        //Add Search query to recent cookie crumbs
        User::insert_recent($info, 0, strtoupper(substr(Page::get_page(), 1)).'/'.Page::get_variables(true));
    }

    //Get URI from actual browser URL or from a variable (for embed <search uri/xxx/xxx> mode)
    if (!isset($search->uri)) $search->uri = (substr(@$_SERVER['REQUEST_URI'], 1));

    //String, not arrays
    $search->query = urldecode(url_strip(Page::get_variable(0, $search->uri), $from_api));
    $search->badges = url_strip(Page::get_variable(1, $search->uri), $from_api);
    $search->tags = url_strip(Page::get_variable(2, $search->uri), $from_api);
    $search->options = url_strip(Page::get_variable(3, $search->uri), $from_api);

    if (stristr($search->query, "=")) {
        $search->options = $search->query;
        $search->query = null;
    } elseif (stristr($search->badges, "=")) {
        $search->options = $search->badges;
        $search->badges = null;
    } elseif (stristr($search->tags, "=")) {
        $search->options = $search->tags;
        $search->tags = null;
    }

    //Validate and Manipulate URL Input (and convert names to IDs, strings to arrays...)
    $search->query = urldecode(strtolower($search->query));
    if ($search->query == "" || $search->query == "*") {
        $search->query = null;
    }
    if ($search->badges == "" || $search->badges == "*") {
        $search->badges = null;
    } else {
        Search::convert_string_array($search->badges, $search->Badges, $search->badge_ids, $search->Badge_ids, 'badge');
    }
    if ($search->tags == "" || $search->tags == "*") {
        $search->tags = null;
    } else {
        Search::convert_string_array($search->tags, $search->Tags, $search->tag_ids, $search->Tag_ids, 'tag');
    }
    if ($search->options != "") {
        $search->Options = array();
        $Options = split(";", strtolower($search->options));
        $tmp = array();
        foreach ($Options as $option) {
            $tmp = split("=", $option);
            $search->Options[$tmp[0]] = $tmp[1];
        }
    }

    //HERE

    //Set Default View
    if (!isset($search->Options['view'])) $search->Options['view'] = 'detail';

    //Set $view->search_query for master.view.php
    $view->search_query = urldecode(implode("/", Page::get_variables()));

    //Get Topics filtered by permission and URL query
    //This is the main search query
    try {
        $topic->tbl_topics = Tbl_topic::get_topics($info, $search);
    } catch (exception $e) {
        //print "cool";
    }

    if (!$from_api) {
        //Get Related Badges (filtered by already selected badges/tags) for menu/badges
        $badge->tbl_badges_related = Tbl_badge::get_related_badges($info, $search->Badge_ids, $search->Tag_ids);

        //Get Related Tags (filtered by already selected badges/tags) for menu/badges
        $tag->tbl_tags_related = Tbl_tag::get_related_tags($info, $search->Badge_ids, $search->Tag_ids);


        //Set Paging Data ONLY for the View
        $page = 1;
        $page_size = Config::SEARCH_PAGE_SIZE;
        $result_count = count($topic->tbl_topics);
        $search->previous_url = null;
        $search->next_url = null;
        if (isset($search->Options['page'])) $page = $search->Options['page'];
        if (isset($search->Options['pagesize'])) $page_size = $search->Options['pagesize'];
        $url = Page::get_url();
        if (stristr($url, "page=")) {
            $url = preg_replace('";page=[0-9]"', '', $url);
            $url = preg_replace('"page=[0-9];"', '', $url);
            $url = preg_replace('"page=[0-9]"', '', $url);
            //$data = preg_replace('"\|\|"', '', $data); //Strip ||
        }
        if (!stristr($url, "=") && substr($url,-1) != "/") {
            $url .= "/";
        } elseif (stristr($url, "=")) {
            $url .= ";";
        }
        if ($page > 1) {
            $search->previous_url = $url."page=".($page -1);
        }
        if ($result_count == $page_size) {
            $search->next_url = $url."page=".($page + 1);
        }
        
        //If only viewing ONE badge, get the badges default topic to display as the search topic header
        if (!$search->embed) {
            if (count($search->Badge_ids) == 1) {
                $badge_default_topic_id = Tbl_badge::get_badge($search->Badge_ids[0])->default_topic_id;
                if ($badge_default_topic_id > 0) {
                    $search->search_topic_body = Tbl_post::get_topic($info, $badge_default_topic_id)->body;
                }
            }
        }

        //Get Global Topic Body
        if (!$search->embed && Config::GLOBAL_TOPIC) {
            $search->global_topic = Tbl_post::get_topic($info, Config::GLOBAL_TOPIC)->body;
        }

        //Get User Global Topic Body
        if ($info->tbl_user->global_topic_id > 0) {
            $search->global_topic_user = Tbl_post::get_topic($info, $info->tbl_user->global_topic_id)->body . $topic->tbl_post->body;
        }

    }



    //Make array of badges
    // Cannot mix and & or
    // * (means all)
    // 1,* (means 1 AND (2 or 3 or 4 or 5 or n))
    // 1|2 (means 1 or 2)
    // 1,2|3,2 (invalid)
    // 1,2,3 (means 1 and 2 and 3)



    if (!$from_api) {
        //Set left menu items
        #$view->menu_left_parts[] = 'menu/sites';
        if ($search->Options['hideleft']) $view->menu_left_hidden = true;
        if ($search->Options['hideright']) $view->menu_right_hidden = true;
    }
    $view->menu_left_parts[] = 'menu/badges';
}





#echo "<hr />Below is only words<hr />";
#echo 
#$words = str_word_count($data, 1, "!@#$%^&*()_+-=,./<>?;':\"`~");
#foreach ($words as $word) {
#    echo "$word ";
#}



/*
$view->header = '
    <table border='0' cellpadding='0' cellspacing='0' width='100%'><tr>
    <td>'.$topic->tbl_post->title.'</td>
    <td align='right'>
        <a href='#comments' class='master_bar_link'>
            <img src=''.Page::get_url('bottom.gif').'' alt='down' border='0' />Comments ('.$topic->tbl_post->comment_count.')
        </a>';

//Set left menu items
$view->menu_left_parts[] = 'menu/sites';

//Set right menu items for this topic page
$view->menu_right_parts[] = 'menu/topic_about';
$view->menu_right_parts[] = 'menu/topic_org';
$view->menu_right_parts[] = 'menu/related';
*/    
    
    
/*
 function url_strip($url, $from_api)
 Quick function to strip .xml and .json from search string if from API call
  mReschke 2011-06-13
*/
function url_strip($url, $from_api) {
    if ($from_api) {
        //The API URL always has a .format (xml or json)
        //At the end, so mreschke.com/rest/v1/search/.xml
        //But The search will treat the .format as the search query
        //I don't want this, so lets get rid of it
        $url = preg_replace('/\.xml|\.json/i', '', $url);
    }
    return $url;
}
