<? $i=0 ?>
<? foreach($topic->tbl_topics as $topic->tbl_topic): ?>
    <? $alt=''; if($i%2) $alt='_alt'; $i++; ?>
    <? if ($topic->tbl_topic->unread && $info->is_authenticated) $read='_unread'; else $read=''; ?>
    <? #if($info->tbl_user->perm_admin): ?>
        <!--<div class='search_row<? echo $alt.$read ?>' ondblclick="window.location='<?#=Page::get_url('edit').'/topic/'.$topic->tbl_topic->topic_id ?>';">-->
    <? #else: ?>
        <div class='search_row<? echo $alt.$read ?>'>
    <? #endif ?>
        <table width='100%' border='0'>
            <tbody align='left' valign='top'>
            <tr>
                <td align='center' width='1'>
                    <div class='search_left'>
                        <? foreach ($topic->tbl_topic->topic_idTbl_badges as $badge->tbl_badge): ?>
                            <div class='search_badge_item'>
                                <a href='<? echo Page::get_url('search').'/*/'.$badge->tbl_badge->badge ?>'>
                                    <img src='<? echo Page::get_url($badge->tbl_badge->image, true) ?>' title='<? echo $badge->tbl_badge->badge ?>' alt='<? echo $badge->tbl_badge->badge ?>' border="0" class="post-badge" />
                                    <!-- <div class='search_badge_text'><? #echo $badge->tbl_badge->badge ?></div> -->
                                </a>
                            </div>
                        <? endforeach ?>
                    </div>
                </td>
                <td>
                    <div class='search_center'>
                        <? if($topic->tbl_topic->deleted==0): ?>
                            <div class='search_title<? echo $read ?>'>
                                <a href='<? echo Page::get_url('topic').'/'.$topic->tbl_topic->topic_id.'/'.urlencode($topic->tbl_topic->title) ?>'<? if ($read): ?> title='Unread' <? endif ?>>
                                    <? echo $topic->tbl_topic->title ?>
                                </a>
                            </div>
                        <? else: ?>
                            <div class='search_title_deleted'>
                                <a href='<? echo Page::get_url('topic').'/'.$topic->tbl_topic->topic_id ?>'>
                                    <? echo $topic->tbl_topic->title ?> (DELETED)
                                </a>
                            </div>                                                                        
                        <? endif ?>
                        <div class='search_subtitle'>
                            <img src='<? echo Page::get_url('post.gif') ?>' alt='Post # <? echo $topic->tbl_post->post_id ?> permalink' border='0' />
                            Topic <b>#<? echo $topic->tbl_topic->topic_id ?></b> by <? echo $topic->tbl_topic->created_byTbl_user->alias ?>
                            on <? echo $topic->tbl_topic->created_on ?>.
                            <? if($topic->tbl_topic->created_on != $topic->tbl_topic->updated_on): ?>
                                Updated by <? echo $topic->tbl_topic->updated_byTbl_user->alias ?> on <? echo $topic->tbl_topic->updated_on ?>
                            <? endif ?>
                            (viewed <? echo number_format($topic->tbl_topic->view_count, 0, ".", ",") ?> times)
                        </div>
                        
                        <div class='search_teaser<? echo $read ?>'>
                            <? echo $topic->tbl_topic->teaser ?>
                        </div>
                        
                        <div class='search_tags'>
                            <? foreach($topic->tbl_topic->topic_idTbl_tags as $tag->tbl_tag): ?>
                                <a href='<? echo Page::get_url('search').'/*/*/'.$tag->tbl_tag->tag ?>' class='search_tag_link'>
                                    <? echo $tag->tbl_tag->tag.' ('.$tag->tbl_tag->topic_count.')' ?>
                                </a>
                            <? endforeach ?>
                        </div>
                    </div>
                </td>
                <td width='10'>
                    <div class='search_center_tags'>
                        <? foreach($topic->tbl_topic->topic_idTbl_tags as $tag->tbl_tag): ?>
                            <? if ($tag->tbl_tag->image): ?>
                                <a href='<? echo Page::get_url('search').'/*/*/'.$tag->tbl_tag->tag ?>' class='search_tag_link'>
                                    <img src='<? echo Page::get_url($tag->tbl_tag->image, true) ?>' title='<? echo $tag->tbl_tag->tag ?>' alt='<? echo $tag->tbl_tag->tag ?>' border='0' class="post-badge" />
                                </a>
                            <? endif ?>
                        <? endforeach ?>
                    </div>
                </td>
                <td width='110' align='center'>
                    <div class='search_right'>
                        <a href='<? echo Page::get_url('profile')."/".$topic->tbl_topic->created_byTbl_user->alias ?>'>
                            <img src='<? echo Page::get_url($topic->tbl_topic->created_byTbl_user->avatar, true) ?>' alt='creator avatar' border='0' class='avatar_small' /></a><br />
                        <a href='<? echo Page::get_url('profile')."/".$topic->tbl_topic->created_byTbl_user->alias ?>'>
                            <? echo $topic->tbl_topic->created_byTbl_user->alias ?>
                        </a>                            
                        <!--<div class='search_updated'>
                            Updated by <? #echo $topic->tbl_topic->updated_byTbl_user->alias ?>
                            on <? #echo $topic->tbl_topic->updated_on ?>
                        </div> -->
                        <div class='search_counts'>
                            Views: <? echo number_format($topic->tbl_topic->view_count, 0, ".", ",") ?><br />
                            Comments: <? echo number_format($topic->tbl_topic->comment_count, 0, ".", ",") ?>
                        </div>
                        <div class='search_permissions'>
                            <? if($info->is_authenticated): ?>
                                <? $perm->tbl_perm_groups_short_display = Tbl_perm::get_perm_groups_short_display($topic->tbl_topic->topic_id); ?>
                                <? if(count($perm->tbl_perm_groups_short_display) > 0): ?>        
                                    <table border='0' style='border-spacing: 1px;'>
                                        <tbody align='left' valign='middle'>
                                            <? foreach($perm->tbl_perm_groups_short_display as $perm->tbl_perm): ?>
                                                <tr>
                                                    <td align="right">
                                                        <? if($perm->tbl_perm->group == 'Public'): ?>
                                                            <span class='search_perm_group_public'>
                                                        <? else: ?>
                                                            <span class='search_perm_group'>
                                                        <? endif ?>
                                                    
                                                        <? echo $perm->tbl_perm->group ?>:
                                                        </span>
                                                    </td>
                                                    <td><span class='search_perm_short'><? echo $perm->tbl_perm->short ?></span></td>
                                                </tr>
                                            <? endforeach ?>
                                        </tbody>
                                    </table>
                                <? else: ?>
                                    <span class='search_perm_private'>Private</span>
                                <? endif ?>
                            <? endif ?>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

    </div>
<? endforeach ?>
