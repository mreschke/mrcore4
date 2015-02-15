<?
if (isset($info->recent)) {
    echo "<div id='topic_recent_div'>";
        echo "<span id='topic_recent_text'></span>";
        foreach ($info->recent as $id => $title) {
            $urltitle = $title;
            $i == 0;
            if (strlen($title) > Config::RECENT_MAX_TITLE_LEN) $title = substr($title, 0, Config::RECENT_MAX_TITLE_LEN).'..';
            
            if($id != $topic->topic_id) {
                #if($i > 0) echo ">"
                if ($i > 0) echo "<img src='".Page::get_url('smileys/icon_right.png')."' />";
                if ($id == 0) {
                    echo "<a href='".Page::get_url('search').'/'.substr($urltitle, 7)."' title='$urltitle' class='topic_recent'>
                        ".urldecode($title)."
                    </a>";
                } else {
                    echo "<a href='".Page::get_url('topic').'/'.$id.'/'.urlencode($urltitle)."' title='$urltitle' class='topic_recent'>
                        $title
                    </a>";
                }
                $i++;
            }
        }
    echo "</div>";
}
?>