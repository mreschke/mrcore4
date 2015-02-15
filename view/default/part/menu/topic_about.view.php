<?php eval(Page::load_code('part/menu/topic_about')) ?>

<div class='m_<?php echo $left_right ?>_item'>
    <div class='m_<?php echo $left_right ?>_header'>Topic Contributors</div>
    <!-- <div class='m_<?php #echo $left_right ?>_subheader'>Created by</div> -->
    <table width='100%'>
        <tbody align='left' valign='top'>
            <tr>
                <td width='10'>
                    <a href='<?php echo Page::get_url('profile')."/".$topic->tbl_post->created_byTbl_user->alias ?>'>
                        <img src='<?php echo Page::get_url($topic->tbl_post->created_byTbl_user->avatar, true) ?>' alt='creator avatar' border='0' class='avatar_medium' />
                    </a>
                </td>
                <td width='5'>&nbsp;</td>
                <td>
                    <table width='100%'>
                        <tr>
                            <td>
                                <a href='<?php echo Page::get_url('profile').'/'.$topic->tbl_post->created_byTbl_user->alias ?>'>
                                    <?php echo $topic->tbl_post->created_byTbl_user->alias ?>
                                </a>
                            </td>
                        </tr><tr>
                            <td><div class='label_small'><?php echo $topic->tbl_post->created_byTbl_user->first_name.' '.$topic->tbl_post->created_byTbl_user->last_name ?></div></td>
                        </tr><tr>
                            <td><div class='label_small'><?php echo $topic->tbl_post->created_byTbl_user->title ?></div></td>
                        <!--</tr><tr>
                            <td><div class='label_small'><?php #echo $topic->tbl_post->created_byTbl_user->email ?></div></td>-->
                        </tr><tr>
                            <td><div class='label_small'>Created: <?php echo date("M jS, Y", strtotime($topic->tbl_post->created_on)) ?></div></td>
                        <?php if($topic->tbl_post->created_by == $topic->tbl_post->updated_by): ?>
                            </tr><tr><td><div class='label_small'>Updated: <?php echo date("M jS, Y", strtotime($topic->tbl_post->updated_on)) ?></div></td>
                        <?php endif ?>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <?php if($topic->tbl_post->created_by != $topic->tbl_post->updated_by): ?>
        <!-- <div class='m_<?php #echo $left_right ?>_subheader'>Updated by</div> -->
        <table width='100%'>
            <tbody align='left' valign='top'>
                <tr>
                    <td width='10'>
                        <a href='<?php echo Page::get_url('profile')."/".$topic->tbl_post->updated_by ?>'>
                            <img src='<?php echo Page::get_url($topic->tbl_post->updated_byTbl_user->avatar, true) ?>' alt='updater avatar' border='0' class='avatar_medium' />
                        </a>
                    </td>
                    <td width='5'>&nbsp;</td>
                    <td>
                        <table width='100%'>
                            <tr>
                                <td>
                                    <a href='<?php echo Page::get_url('profile').'/'.$topic->tbl_post->updated_by ?>'>
                                        <?php echo $topic->tbl_post->updated_byTbl_user->alias ?>
                                    </a>
                                </td>
                            </tr><tr>
                                <td><div class='label_small'><?php echo $topic->tbl_post->updated_byTbl_user->first_name.' '.$topic->tbl_post->updated_byTbl_user->last_name ?></div></td>
                            </tr><tr>
                                <td><div class='label_small'><?php echo $topic->tbl_post->updated_byTbl_user->title ?></div></td>
                            <!--</tr><tr>
                                <td><div class='label_small'><?php #echo $topic->tbl_post->updated_byTbl_user->email ?></div></td>-->
                            </tr><tr>
                                <td><div class='label_small'>Updated: <?php echo date("M jS, Y", strtotime($topic->tbl_post->updated_on)) ?></div></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif ?>
</div>
