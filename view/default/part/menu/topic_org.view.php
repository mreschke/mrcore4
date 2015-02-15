<?php eval(Page::load_code('part/menu/topic_org')) ?>

<div class='m_<?php echo $left_right ?>_item'>
    <div class='m_<?php echo $left_right ?>_header'>Topic Organization</div>
    <!-- <div class='m_<?php #echo $left_right ?>_subheader'>Topic Badges</div> -->
    <table width='100%'>
        <tbody align='left' valign='middle'>
            <?php if(isset($badge->tbl_badges_intopic)): ?>
                <?php foreach($badge->tbl_badges_intopic as $badge->tbl_badge): ?>
                    <tr>
                        <td width='5px' align='center'>
                            <div class='m_badge_line'>
                                <a href='<?php echo Page::get_url('search').'/*/'.$badge->tbl_badge->badge ?>'>
                                    <img src='<?php echo Page::get_url($badge->tbl_badge->image, true) ?>' alt='<?php echo $badge->tbl_badge->badge ?>' border='0' class='badge_full' />
                                </a>
                            </div>
                        </td>
                        <td>
                            <a href='<?php echo Page::get_url('search').'/*/'.$badge->tbl_badge->badge ?>'>
                                <?php echo $badge->tbl_badge->badge.' ('.$badge->tbl_badge->topic_count.')' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if(isset($tag->tbl_tags_intopic)): ?>
                    <?php foreach($tag->tbl_tags_intopic as $tag->tbl_tag): ?>
                        <tr>
                            <td>
                            <td>
                                <div class='m_<?php echo $left_right ?>_line'>
                                    <a href='<?php echo Page::get_url('search').'/*/*/'.$tag->tbl_tag->tag ?>'>
                                        <?php echo $tag->tbl_tag->tag.' ('.$tag->tbl_tag->topic_count.')' ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>                
            <?php endif ?>
        </tbody>
    </table>
    
    <?
    /*
    <div class='m_<?php #echo $left_right ?>_subheader'>Topic Tags</div>
    <table border='0' width='100%'>
        <tbody align='left' valign='middle'>
            <?php if(isset($tag->tbl_tags_intopic)): ?>
                <?php foreach($tag->tbl_tags_intopic as $tag->tbl_tag): ?>
                    <tr>
                        <td>
                            <div class='m_<?php echo $left_right ?>_line'>
                                <a href='<?php echo Page::get_url('search').'/xxx*xxx/xxx*xxx/'.$tag->tbl_tag->tag ?>'>
                                    <?php echo $tag->tbl_tag->tag.' ('.$tag->tbl_tag->topic_count.')' ?>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>
    */
    ?>

    <?php if($info->is_authenticated): ?>
        <div class='m_<?php echo $left_right ?>_subheader'>Topic Permissions</div>
        <?php if(count($perm->tbl_perm_groups_short_display) > 0): ?>        
            <table border='0' style='border-spacing: 3px;'>
                <tbody align='left' valign='middle'>
                    <?php foreach($perm->tbl_perm_groups_short_display as $perm->tbl_perm): ?>
                        <tr>
                            <td align="right" valign='top'>
                                <?php if($perm->tbl_perm->group == 'Public'): ?>
                                    <span class='perm_group_public'>
                                <?php else: ?>
                                    <span class='perm_group'>
                                <?php endif ?>
                            
                                <?php echo $perm->tbl_perm->group ?>:
                                </span>
                            </td>
                            <td><span class='perm_short'><?php echo $perm->tbl_perm->short ?></span></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php else: ?>
            <span class='perm_private'>Private</span>
        <?php endif ?>
    <?php endif ?>

<?
/*
    <div class='m_<?php echo $left_right ?>_subheader'>Share</div>
    <div class="addthis_toolbox addthis_default_style" style='display:none;'>
        <div class="addthis_toolbox">
            <div class="two_column">
                <div class="column1">
                    <a class="addthis_button_email">Email</a>
                </div>
                <div class="column2">
                    <a class="addthis_button_print">Print</a>
                </div>
                <div class="clear"></div>
                <div class="top">
                </div>
                <div class="column1">
                    <a class="addthis_button_twitter">Twitter</a>
                    <a class="addthis_button_facebook">Facebook</a>
                    <a class="addthis_button_myspace">MySpace</a>
                </div>
                <div class="column2">
                    <a class="addthis_button_delicious">Delicous</a>
                    <a class="addthis_button_stumbleupon">Stumble</a>
                    <a class="addthis_button_digg">Digg</a>
                </div>
                <div class="clear"></div>
                <div class="more">
                    <a class="addthis_button_expanded">More Destinations...</a>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4caa51503a0bcc04"></script>
    
    <center>
        <div class="addthis_toolbox addthis_default_style">
        <table width='100%'>
            <tbody align='left' valign='center'>
            <tr height='25'>
                <td width='80'>
                    <a class="addthis_button_email" title="Email"></a>
                    <a class="addthis_button_print" title="Print"></a>
                    <a class="addthis_button_gmail" title="Send with Gmail"></a>
                </td>
                <td rowspan='3'>
                    <img src="http://qrcode.kaywa.com/img.php?s=2&d=<?php echo urlencode(Page::get_url('topic').'/'.$topic->topic_id) ?>" alt="qrcode" title="Scan me with your phone!" />
                </td>
            </tr><tr height='25'>
                <td>
                    <a class="addthis_button_twitter" title="Tweet This"></a>
                    <a class="addthis_button_facebook" title="Share to Facebook"></a>
                    <a class="addthis_button_myspace" title="Share to MySpace"></a>
                </td>
            </tr><tr height='25'>
                <td>
                    <a class="addthis_button_stumbleupon" title="StumbleUpon"></a>
                    <a class="addthis_button_digg" title="Digg This"></a>
                    <a class="addthis_button_expanded" title="More Choices"></a>
                </td>
            </tr>
            </tbody>
        </table>
        </div>
    </center>
*/
    ?>
</div>
