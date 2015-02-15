<?php eval(Page::load_code('part/menu/badges')) ?>

<div class='m_<?php echo $left_right ?>_item'>
    <!-- <div class='m_<?php #echo $left_right ?>_header'>Browse Topics</div> -->
    <table width='100%' border='0'>
    <tbody align='left' valign='middle'>
    <?php foreach ($badge->tbl_badges_all as $badge->tbl_badge): ?>
        <?php if($badge->tbl_badge->topic_count > 0): ?>
            <tr>
                <td width='23'>
                    <div class='m_badge_line'>
                        <a href='<?php echo Page::get_url('search').'/*/'.$badge->tbl_badge->badge ?>'>
                            <img src='<?php echo Page::get_url($badge->tbl_badge->image, true) ?>' border='0' class='badge_small' />
                        </a>
                    </div>
                </td>
                <td>
                    <a href='<?php echo Page::get_url('search').'/*/'.$badge->tbl_badge->badge ?>'>
                        <?php echo $badge->tbl_badge->badge.' ('.$badge->tbl_badge->topic_count.')' ?>
                    </a>
                </td>
            </tr>
                <?php if(isset($search)): ?>
                    <?php if($badge->tbl_badge->badge_id == $search->Badge_ids[0]): ?>
                        <?php if(isset($badge->tbl_badges_related) || isset($tag->tbl_tags_related)): ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <div id='search_related_badgetag_menu_item'>
                                        <?php if(isset($badge->tbl_badges_related)): ?>
                                            <?php foreach ($badge->tbl_badges_related as $badge->tbl_badge): ?>
                                                <div class='m_<?php echo $left_right ?>_line'>
                                                    <a href='<?php echo Page::get_url('search').'/*/'.$search->badges.','.$badge->tbl_badge->badge ?>' title='Filter search results further by this badge'>
                                                        &raquo;<?php echo $badge->tbl_badge->badge ?>
                                                    </a>
                                                </div>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <?php if(isset($tag->tbl_tags_related)): ?>
                                            <?php foreach ($tag->tbl_tags_related as $tag->tbl_tag): ?>
                                                <div class='m_<?php echo $left_right ?>_line'>
                                                    <?php if($search->Tags[0] != ''): ?>
                                                        <a href='<?php echo Page::get_url('search').'/*/'.$search->badges.'/'.$search->tags.','.$tag->tbl_tag->tag ?>'>
                                                    <?php else: ?>
                                                        <a href='<?php echo Page::get_url('search').'/*/'.$search->badges.'/'.$search->tags.$tag->tbl_tag->tag ?>'>
                                                    <?php endif ?>
                                                        &raquo;<?php echo $tag->tbl_tag->tag ?>
                                                    </a>
                                                </div>
                                            <?php endforeach ?>
                                        <?php endif ?>                            
                                    </div>
                                </td>
                            </tr>
                        <?php endif ?>
                    <?php endif ?>
                <?php endif ?>
            </div>
        <?php endif ?>
    <?php endforeach ?>
    </tbody>
    </table>
</div>