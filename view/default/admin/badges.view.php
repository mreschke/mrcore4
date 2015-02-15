<div id='bc'>
    <?=eval(Page::load_part('menu/admin'))?>
    <div id='bb'>
        <div id='subnavc'>
            <span class='subnava'><a href='<?=Page::get_url('admin/badges/new') ?>'>New Badge</a></span>
        </div>

        <table id='badges_table' border='0'>
            <tr>
                <th>&nbsp;</th>
                <th>ID</th>
                <th>Badge</th>
                <th>Topic</th>
                <th>Image</th>
                <th>Topic Count</th>
            </tr>
            <?php if($event_new): ?>
                <tr>
                    <td>
                        <input type='button' name='btn_cancel' onclick="window.location='<?php echo Page::get_url('admin/badges') ?>'" value='Cancel' />
                        <input type='button' name='btn_save' onclick='button_click(this)' value='Save' />
                    </td>
                    <td><?php echo $perm->tbl_perm_group->perm_group_id ?></td>
                    <td><input type='text' class='badges_badge_txt' name='txt_badge' value='' /></td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td><input type='text' class='badges_topic_count_txt' name='txt_topic_count' value='' /></td>
                </tr>
            <?php endif ?>
            <?php $i=0 ?>
            <?php foreach ($badge->tbl_badges_all as $badge->tbl_badge): ?>
                <?php $alt=''; if($i%2) $alt='_alt'; $i++; ?>
                <tr class='badges_row<?php echo $alt ?>'>
                    <?php if($event_edit && $badge->tbl_badge->badge_id == $badge_id): ?>
                        <td>
                            <input type='button' name='btn_delete' onclick="if(confirm_delete()) button_click(this);" value='Delete' />
                            <input type='button' name='btn_cancel' onclick="window.location='<?php echo Page::get_url('admin/badges') ?>'" value='Cancel' />
                            <input type='button' name='btn_save' onclick='button_click(this)' value='Save' />
                        </td>
                        <td><?php echo $badge->tbl_badge->badge_id ?></td>
                        <td><input type='text' class='badges_badge_txt' name='txt_badge' value='<?php echo $badge->tbl_badge->badge ?>' /></td>
                        <td><input type='text' class='badges_topic_txt' name='txt_default_topic_id' value='<?php echo $badge->tbl_badge->default_topic_id ?>' /></td>
                        <td align='center'>
                            <input type='hidden' name='txt_image' value='<?php echo $badge->tbl_badge->image ?>' />
                            <img src='<?php echo Page::get_url($badge->tbl_badge->image, true) ?>' border='0' alt='<?php echo $badge->tbl_badge->image ?>' class='badge_full' /><br />
                            <a href='#' onclick="document.getElementById('badges_upload_div').style.display='block';">Change</a>
                            <div id='badges_upload_div'>
                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                <input name="uploadedfile" type="file" id='badges_upload' /><br />
                                <input type="button" value="Cancel" onclick="document.getElementById('badges_upload').value='';document.getElementById('badges_upload_div').style.display='none';" />
                            </div>                        
                            
                        </td>
                        <td>
                            <input type='text' class='badges_topic_count_txt' name='txt_topic_count' value='' /><br />
                            <div class='help'>Leave blank to keep original</div>
                        </td>
                    <?php else: ?>
                        <td><a href='<?php echo Page::get_url('admin/badges').'/edit/'.$badge->tbl_badge->badge_id ?>'>Edit</a></td>
                        <td><?php echo $badge->tbl_badge->badge_id ?></td>
                        <td><?php echo $badge->tbl_badge->badge ?></td>
                        <td><?php echo $badge->tbl_badge->default_topic_id ?></td>
                        <td><img src='<?php echo Page::get_url($badge->tbl_badge->image, true) ?>' border='0' alt='<?php echo $badge->tbl_badge->image ?>' class='badge_full' /></td>
                        <td><?php echo $badge->tbl_badge->topic_count ?></td>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>        
        </table>
        
        <!--<div class='section_header01'>Testing htaccess</div>
        <section>
            <?
            /*eval(Page::load_code('htaccess'));
            $ht = new htaccess();
            $ht->setFPasswd("/nwq/admin/www/.htpasswd");
            $ht->setFHtaccess("/nwq/pub/.htaccess");
            $users = $ht->getUsers();
            echo count($users);*/
            ?>
        </section> -->

    </div>
</div>