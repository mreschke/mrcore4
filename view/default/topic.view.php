<div id='tc'>
<? if ($topic->tbl_post->created_by==$info->tbl_user->user_id || Topic::has_perm($topic, 'WRITE')): ?>
    <div id='tb'>
    <?# <div id='tb' ondblclick="window.location='<?#=echo Page::get_url('edit').'/topic/'.$topic->topic_id.'/'.urlencode($topic->tbl_post->title)  ? >';"> ?>
    <?# <div id='tb' ondblclick="if(this.contentEditable=='true'){this.contentEditable='false';}else{this.contentEditable='true'}" > ?>
<? else: ?>
    <div id='tb'>
<? endif ?>
        <div id='tbh'>
            <a name='<? echo $topic->tbl_post->post_id ?>'></a>
            
            <table border='0' width='100%'>
                <tr>
                    <td align='left' valign='middle' width='1'>
                        <div class='tbadges'>
                            <? if(isset($badge->tbl_badges_intopic)): ?>
                                <? foreach($badge->tbl_badges_intopic as $badge->tbl_badge): ?>
                                    <div>
                                        <a href='<? echo Page::get_url('search').'/*/'.$badge->tbl_badge->badge ?>'>
                                            <img src='<? echo Page::get_url($badge->tbl_badge->image, true) ?>' title='<? echo $badge->tbl_badge->badge ?>' alt='<? echo $badge->tbl_badge->badge ?>' class='badge_small' />
                                        </a>
                                    </div>
                                <? endforeach ?>
                            <? endif ?>
                        </div>
                    </td>
                    <td valign='middle'>

                        <table width='100%'>
                            <tbody align='left' valign='middle'>
                            <tr>
                                <td>
                                    <div id='ttitle'>
                                        <? if ($topic->tbl_post->deleted): ?>
                                            <div class='deleted'><?=$topic->tbl_post->title ?> (DELETED)</div>
                                        <? else: ?>
                                            <?=$view->title ?>
                                        <? endif ?>
                                    </div>
                                </td>
                                <td>
                                    <div id='tm'>
                                        <?= $view->menu ?>
                                        <? if ($info->is_authenticated && ($topic->tbl_post->created_by == $info->user_id || Topic::has_perm($topic, 'WRITE'))): ?>
                                            <div class='tmb tmbleft' id='tmbedit' onclick="window.location='<?=Page::get_url('edit').'/topic/'.$topic->topic_id.'/'.urlencode($topic->tbl_post->title)?>'">
                                                Edit
                                            </div>
                                            <? if (!$view->viewmode_app): ?>
                                            <div class='tmb tmbright' id='tmbbackup' onclick="window.location='<?=Page::get_url('edit').'/topic/'.$topic->topic_id.'?backup=1'?>'">
                                                Backup
                                            </div>
                                            <? endif ?>
                                        <? endif ?>

                                        <? if (!$view->viewmode_app): ?>
                                            <div class='tmb tmbleft' id='tmbinfo' onclick="toggle_div('tmbinfoc')">
                                                <div class='tmbdropdownimg'>View</div>
                                                <div id='tmbinfoc' class='tmbdropdown'>
                                                    <div><a href='#bottom' title='View Info Footer Below'>Info</a></div>
                                                    <div><a href='<?=Page::get_url('topic').'/'.$topic->topic_id.'?simple' ?>' target='_blank' title='Hide Site Layout'>Simple</a></div>
                                                    <div><a href='<?=Page::get_url('topic').'/'.$topic->topic_id.'?raw' ?>' target='_blank' title='Hide Site Layout and CSS/Javascript'>Raw</a></div>
                                                    <div><a href='<?=Page::get_url('rest').'/v1/topic/'.$topic->topic_id.'?plaintext=1' ?>' target='_blank' title='Unparsed Wiki Source Code'>Source</a></div>
                                                    
                                                </div>
                                            </div>
                                            <div class='tmb tmbright' id='tmbfolder' onclick="tmbfolder_click('<?=$topic->topic_id?>');">
                                                <div id='tmbfolderimg'>
                                                    <div class='tmbdropdownimg'></div>
                                                </div>
                                            </div>
                                        <? endif ?>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <? if (!$view->viewmode_app): ?>
                            <div id='tmbfolderc'>
                                <div id='tmbfolderb'>
                                    <div class='ajax_loader'>Loading Files...</div>
                                </div>
                            </div>

                            <? if ($lock_message): ?>
                                <div id='tlock_message'><?= $lock_message ?></div>
                            <? endif ?>
                        <? endif ?>
                    </td>
                </tr>
            </table>
        </div>


        <table id='tbt'>
            <tr>
                <td>
                    <div id='tbwiki'>
                        <div><div><div><div><div><div>
                        <?= $wiki ?>
                        </div></div></div></div></div></div>
                    </div>
                </td>
            </tr>
        </table>

    </div>


    <?# ############################################################################### ?>
    <? if (!$view->viewmode_app): ?>
    <div id="tcom">
        <a name='comments'></a>
            <div class='tcomadd'>
            <table width='100%'>
                <tr>
                    <td>
                        <? if($topic->tbl_post->created_by==$info->tbl_user->user_id || Topic::has_perm($topic, 'COMMENT')): ?>
                            <a href='<? echo Page::get_url('edit').'/newcomment/'.$topic->topic_id ?>'>
                                <img src='<? echo Page::get_url('comment.png') ?>' alt='comment' />Add Comment
                            </a>
                        <? endif ?>
                    </td>
                    <td align='right'>
                        <a href='#top' Title='Top of Page'>
                            <img src='<? echo Page::get_url('top.png') ?>' alt='top' />
                        </a>
                    </td>
                </tr>
            </table>
            </div>
        

        <? if(isset($topic->tbl_post_comments)): ?>
            <? $i=0 ?>
            <? foreach($topic->tbl_post_comments as $topic->tbl_post_comment): ?>
                <? $alt=''; if($i%2) $alt='alt'; $i++; ?>
                <a name='<? echo $topic->tbl_post_comment->post_id ?>'></a>
                <? if($info->tbl_user->perm_admin || $topic->tbl_post->created_by==$info->tbl_user->user_id || ($topic->tbl_post_comment->created_by==$info->tbl_user->user_id) && $topic->tbl_post_comment->created_by!=Config::STATIC_ANONYMOUS_USER): ?>
                    <div class='<? echo 'tcomb'.$alt ?>' ondblclick="window.location='<? echo Page::get_url('edit').'/comment/'.$topic->tbl_post_comment->post_id ?>';">
                <? else: ?>
                    <div class='<? echo 'tcomb'.$alt ?>'>
                <? endif ?>            
                    <table border='0' width='100%'>
                        <tr>
                            <td valign='top'>
                                <div class='tcomtitle'>
                                    <? if ($topic->tbl_post_comment->deleted): ?>
                                        <div class='deleted'><?=$topic->tbl_post_comment->title ?></div>
                                    <? else: ?>
                                        <?=$topic->tbl_post_comment->title ?>
                                    <? endif ?>
                                </div>
                                <div class='tcomsubtitle'>
                                    <a href='#<? echo $topic->tbl_post_comment->post_id ?>' title='Post #<? echo $topic->tbl_post_comment->post_id ?> permalink'>
                                        <img src='<? echo Page::get_url('post.gif') ?>' alt='postID: <? echo $topic->tbl_post_comment->post_id ?>' />
                                    </a>
                                    by <? echo $topic->tbl_post_comment->created_byTbl_user->alias ?>
                                    on <? echo $topic->tbl_post_comment->created_on ?>
                                </div>
                                
                                <div class='tcomc'>
                                    <? echo Parser::parse_wiki($info, $topic, $topic->tbl_post_comment->body) ?>
                                </div>

                            </td>
                            <td width='150' valign='top'>
                                <table border='0' width='150'>
                                    <tr>
                                        <td align='center'>
                                            <div class='tcomabout'>
                                                <div>
                                                    <a href='<? echo Page::get_url('profile')."/".$topic->tbl_post_comment->created_by ?>'>
                                                        <img src='<? echo Page::get_url($topic->tbl_post_comment->created_byTbl_user->avatar, true) ?>' alt='creator avatar' class='avatar_small' />
                                                    </a>
                                                </div>
                                                <a href='<? echo Page::get_url('profile')."/".$topic->tbl_post_comment->created_by ?>'>
                                                    <? echo $topic->tbl_post_comment->created_byTbl_user->alias ?>
                                                </a>
                                                <div class='tcomuserstats'>
                                                    <div>Topics: <? echo number_format($topic->tbl_post_comment->created_byTbl_user->topic_count, 0, ".", ",") ?></div>
                                                    <div>Comments: <? echo number_format($topic->tbl_post_comment->created_byTbl_user->comment_count, 0, ".", ",") ?></div>
                                                    <div>Member Since</div>
                                                    <? echo $topic->tbl_post_comment->created_byTbl_user->created_on ?>
                                                </div>
                                                <div class='tcomcontrols'>
                                                    <? if($info->tbl_user->perm_admin || $topic->tbl_post->created_by==$info->tbl_user->user_id || ($topic->tbl_post_comment->created_by==$info->tbl_user->user_id) && $topic->tbl_post_comment->created_by!=Config::STATIC_ANONYMOUS_USER): ?>
                                                        <div><a href='<? echo Page::get_url('edit').'/comment/'.$topic->tbl_post_comment->post_id ?>'>Edit Post</a></div>
                                                        <?# <a href='<? echo Page::get_url('edit').'/delete/comment/'.$topic->tbl_post_comment->post_id ? >'>Delete Post</a> ?>
                                                    <? endif ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <div class='tcomtopleft'>
                        <div class='tcomsubtitle'>
                            <hr />
                            <a href='#<? echo $topic->tbl_post_comment->post_id ?>' title='Top of this comment'>
                                <img src='<? echo Page::get_url('post.gif') ?>' alt='postID: <? echo $topic->tbl_post_comment->post_id ?>' /> 
                            </a>
                            by <? echo $topic->tbl_post_comment->created_byTbl_user->alias ?>
                            on <? echo $topic->tbl_post_comment->created_on ?>
                            <a href='#comments' title='Top of all comments'>
                                <img src='<? echo Page::get_url('top.gif') ?>' alt='top' />
                            </a>
                            <div class='tcomtopright'>
                                <a href='#top' Title='Top of Page'>
                                    <img src='<? echo Page::get_url('top.png') ?>' alt='top' />
                                </a>
                            </div>
                            <? if($topic->tbl_post_comment->updated_on != $topic->tbl_post_comment->created_on): ?>
                                <? if ($topic->tbl_post_comment->updated_by != $topic->tbl_post_comment->created_by): ?>
                                    updated by <? echo $topic->tbl_post_comment->updated_byTbl_user->alias ?>
                                    on <? echo $topic->tbl_post_comment->updated_on ?>
                                <? else: ?>
                                    update on <? echo $topic->tbl_post_comment->updated_on ?>
                                <? endif ?>
                            <? endif ?>
                        </div>
                    </div>
                </div>
            <? endforeach ?>
            
            
            
            <? if($topic->tbl_post->comment_count > 0 && ($topic->tbl_post->created_by==$info->tbl_user->user_id || Topic::has_perm($topic, 'COMMENT'))): ?>
                <div class='tcomadd'>
                <table width='100%'>
                    <tr>
                        <td>
                            <a href='<? echo Page::get_url('edit').'/newcomment/'.$topic->topic_id ?>'>
                                <img src='<? echo Page::get_url('comment.png') ?>' alt='comment' />Add Comment
                            </a>
                        </td>
                        <td align='right'>
                            <a href='#top' Title='Top of Page'>
                                <img src='<? echo Page::get_url('top.png') ?>' alt='top' />
                            </a>
                        <? if (1==2): ?>                            
                            <a href='<? echo Page::get_url('edit').'/newcomment/'.$topic->topic_id ?>'>
                                <img src='<? echo Page::get_url('comment.png') ?>' alt='comment' />Add Comment
                            </a>
                        <? endif ?>
                        </td>
                    </tr>
                </table>
                </div>
            <? endif ?>
        <? endif ?>
    </div>
    <? endif ?>
</div>






<?# ############################################################################### ?>
</div> <?# end master #mbc, so I can have footer go all the way to edge, without 30px pasdding of #mbc ?>
<? if (!$view->viewmode_app): ?>
<div id='tfoot'>
    <div id='tfootc'>
        <table border='0' width='100%'>
            <tr>
                <td align='left' valign='middle' width='1'>
                    <div class='tbadges'>
                        <? if(isset($badge->tbl_badges_intopic)): ?>
                            <? foreach($badge->tbl_badges_intopic as $badge->tbl_badge): ?>
                                <div>
                                    <a href='<? echo Page::get_url('search').'/*/'.$badge->tbl_badge->badge ?>'>
                                        <img src='<? echo Page::get_url($badge->tbl_badge->image, true) ?>' title='<? echo $badge->tbl_badge->badge ?>' alt='<? echo $badge->tbl_badge->badge ?>' class='badge_small' />
                                    </a>
                                </div>
                            <? endforeach ?>
                        <? endif ?>
                    </div>
                </td>
                <td valign='middle'>
                    <div id='tfoottitle'><? echo $view->title ?></div>
                    
                    <div class='tsubtitle' title='Last updated by <? echo $topic->tbl_post->updated_byTbl_user->alias ?> on <? echo $topic->tbl_post->updated_on ?>'>
                        <a href='#<? echo $topic->tbl_post->post_id ?>' title='Post #<? echo $topic->tbl_post->post_id ?> permalink'>
                            <img src='<? echo Page::get_url('post.gif') ?>' alt='Post # <? echo $topic->tbl_post->post_id ?> permalink' />
                        </a>
                        Topic <b>#<? echo $topic->topic_id ?></b> by <? echo $topic->tbl_post->created_byTbl_user->alias ?>
                        on <? echo $topic->tbl_post->created_on ?>.
                        <? if($topic->tbl_post->created_on != $topic->tbl_post->updated_on): ?>
                            Updated by <? echo $topic->tbl_post->updated_byTbl_user->alias ?> on <? echo $topic->tbl_post->updated_on ?>
                        <? endif ?>
                        (viewed <? echo number_format($topic->tbl_post->view_count, 0, ".", ",") ?> times)
                    </div>

                    <div class='ttags'>
                        <? if (isset($tag->tbl_tags_intopic)): ?>
                            <? foreach ($tag->tbl_tags_intopic as $tag->tbl_tag): ?>
                                <a href='<? echo Page::get_url('search').'/*/*/'.$tag->tbl_tag->tag ?>' class='ttag'>
                                    <? echo $tag->tbl_tag->tag.' ('.$tag->tbl_tag->topic_count.')' ?>
                                </a>
                            <? endforeach; ?>
                        <? endif ?>
                    </div>
                </td>
                <td align='left' valign='middle' width='10'>
                    <div class='ttagimages'>
                        <? foreach ($tag->tbl_tags_intopic as $tag->tbl_tag): ?>
                            <? if ($tag->tbl_tag->image): ?>
                                <div>
                                    <a href='<? echo Page::get_url('search').'/*/*/'.$tag->tbl_tag->tag ?>' class='search_tag_link'>
                                        <img src='<? echo Page::get_url($tag->tbl_tag->image, true) ?>' title='<? echo $tag->tbl_tag->tag ?>' alt='<? echo $tag->tbl_tag->tag ?>' class='badge_medium' />
                                    </a>
                                </div>
                            <? endif ?>
                        <? endforeach ?>
                    </div>
                </td>
                <td align='right' valign='middle' width='10'>
                    <div class='tdetails'>
                        <table border='0'>
                            <tr>
                                <td align='center'>
                                    <?php if($info->is_authenticated): ?>
                                        <div class='tdetailsperms'>Permissions</div>
                                        <?php if(count($perm->tbl_perm_groups_short_display) > 0): ?>        
                                            <table border='0' style='border-spacing: 1px;'>
                                                <tbody align='left' valign='middle'>
                                                    <?php foreach($perm->tbl_perm_groups_short_display as $perm->tbl_perm): ?>
                                                        <tr>
                                                            <td align="right" valign='top'>
                                                                <?php if($perm->tbl_perm->group == 'Public'): ?>
                                                                    <span class='tperm_group_public'>
                                                                <?php else: ?>
                                                                    <span class='tperm_group'>
                                                                <?php endif ?>
                                                            
                                                                <?php echo $perm->tbl_perm->group ?>:
                                                                </span>
                                                            </td>
                                                            <td><span class='tperm_short'><?php echo $perm->tbl_perm->short ?></span></td>
                                                        </tr>
                                                    <?php endforeach ?>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <span class='tperm_private'>Private</span>
                                        <?php endif ?>
                                    <?php endif ?>

                                </td>
                                <td>
                                    <div style='margin-left:10px;font-size: 10px'>
                                    <a href='<? echo Page::get_url('profile')."/".$topic->tbl_post->created_byTbl_user->alias ?>'><img src='<? echo Page::get_url($topic->tbl_post->created_byTbl_user->avatar, true) ?>' alt='creator' class='avatar_small' /></a><br />
                                    <a href='<? echo Page::get_url('profile')."/".$topic->tbl_post->created_byTbl_user->alias ?>'>
                                        <? echo $topic->tbl_post->created_byTbl_user->alias ?>
                                    </a>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                </td>
            </tr>
        </table>
    </div>
</div>
<? endif ?>


<?# Here instead of files.view because of the new files dropdown button (ajax), needs to be here for mouse menu positioning ?>
<?# this is a mess, it needs fixed, some menus are per instance, some are only one... #?>
<?# need to make all menus come from server side or something else ?>
<?# also duplicated in edit.view ?>
<div id='files_context_menu'>
    <div class='files_menu_item' id='files_context_opentab'>Open In New Tab</div>
    <div class='files_menu_item' id='files_context_openwindow'>Open In New Window</div>
    <div class='files_menu_separator'></div>
    <div class='files_menu_item' id='files_context_openurl'>Open as URL</div>
    <div class='files_menu_separator'></div>
    <?# <a target='_blank' href='' id='files_context_open_a'>Open File</a></div> ?>
    <?# <div class='files_menu_item' id='files_context_listarchive'>List Archive Contents</div> ?>
    <div class='files_menu_separator'></div>
    <div class='files_menu_item' id='files_context_download'>Download File</div>
    <? #if($files->perm_write): ?>
    <? if (Topic::has_perm($topic, "WRITE")): ?>
        <div class='files_menu_separator'></div>
        <?# <div class='files_menu_item'>Replace File with Upload</div> ?>
        <div class='files_menu_item' id='files_context_rename'>Rename File</div>
        <div class='files_menu_item' id='files_context_delete'>Delete File</div>
    <? endif ?>
</div>

<div id='folder_context_menu'>
    <div class='files_menu_item' id='folder_context_open'>Open</div>
    <div class='files_menu_separator'></div>
    <div class='files_menu_item' id='folder_context_openurl'>Open as URL</div>
    <div class='files_menu_separator'></div>
    <?# <div class='files_menu_item' id='folder_context_download'>Download Folder (as .zip)</div> ?>
    <? #if($files->perm_write): ?>
    <? if (Topic::has_perm($topic, "WRITE")): ?>
        <div class='files_menu_item' id='folder_context_rename'>Rename Folder</div>
        <div class='files_menu_item' id='folder_context_delete'>Delete Folder</div>
    <? endif ?>
</div>
