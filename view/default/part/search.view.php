<? eval(Page::load_code('part/search')) ?>
<? function load_search_view() { GLOBAL $search, $topic, $badge, $tag, $info, $view; ?>
<? if (!$search->embed): ?><div id='sc'><? endif ?>

<? if(isset($search->global_topic)): ?>
    <? echo Parser::parse_wiki($info, null, $search->global_topic) ?>
<? endif ?>

<? if(isset($search->global_topic_user)): ?>
    <? echo Parser::parse_wiki($info, null, $search->global_topic_user) ?>
<? endif ?>


<table>
    <tbody valign="top">
    <tr>
        <td class="badge_td"><? eval(Page::load_view('part/menu/badges')) ?></td>
        <td>
            <? if(isset($search->search_topic_body)): ?>
                <div id='search_badge_topic_body'>
                    <div id='topic_content'>
                        <div><div><div><div><div><div>
                        <? echo Parser::parse_wiki($info, null, $search->search_topic_body) ?>
                        </div></div></div></div></div></div>
                    </div>
                </div>
                
            <? endif ?>

            <? if(isset($topic->tbl_topics)): ?>

                <? if (!$search->embed): ?>
                    <? if ($search->Options['unread']): ?>
                        <!--<a href='<?= Page::get_url('search').'/markallread=1' ?>'>Mark All as Unread</a>-->
                    <? endif ?>

                    <div class='search_pager'>
                        <? if(!is_null($search->previous_url)): ?><a href='<? echo $search->previous_url ?>' class='search_pager_previous_a'>&laquo; Previous Page</a><? endif ?>
                        <? if(!is_null($search->next_url)): ?><a href='<? echo $search->next_url ?>' class='search_pager_next_a'>Next Page &raquo;</a><? endif ?>
                    </div>
                <? endif ?>

                <div class='sb'>
                    <? if ($search->Options['view'] == 'detail'): ?>
                        <? eval(Page::load_part('search/detail', false)) ?>
                    <? elseif ($search->Options['view'] == 'list'): ?>
                        <? eval(Page::load_part('search/list', false)) ?>
                    <? elseif ($search->Options['view'] == 'link'): ?>
                        <? eval(Page::load_part('search/link', false)) ?>
                    <? endif ?>
                    <? if (!$search->embed): ?>
                        <div class='search_pager'>
                            <? if(!is_null($search->previous_url)): ?><a href='<? echo $search->previous_url ?>' class='search_pager_previous_a'>&laquo; Previous Page</a><? endif ?>
                            <? if(!is_null($search->next_url)): ?><a href='<? echo $search->next_url ?>' class='search_pager_next_a'>Next Page &raquo;</a><? endif ?>
                        </div>
                    <? endif ?>
                </div>
                
                
            <? endif ?>
            <? if (!$search->embed): ?></div<? endif ?>

            <? } ?>
        </td>
    </tr>
</tbody>
</table>