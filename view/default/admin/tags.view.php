<div id='tagc'>
    <?=eval(Page::load_part('menu/admin'))?>
    <div id='tagb'> 
        <div id='subnavc'>
            <span class='subnava'><a href='<?=Page::get_url('admin/tags/new') ?>'>New Tag</a></span>
        </div>

        <table id='tags_table'>
            <tr>
                <th>&nbsp;</th>
                <th>ID</th>
                <th>Tag</th>
                <th>Topic</th>
                <th>Image</th>
                <th>Topic Count</th>
            </tr>
            <?php if($event_new): ?>
                <tr>
                    <td>
                        <input type='button' name='btn_cancel' onclick="window.location='<?php echo Page::get_url('admin/tags') ?>'" value='Cancel' />
                        <input type='button' name='btn_save' onclick='button_click(this)' value='Save' />
                    </td>
                    <td><?php echo $perm->tbl_perm_group->perm_group_id ?></td>
                    <td><input type='text' class='tags_tag_txt' name='txt_tag' value='' /></td>
                    <td>N/A</td>
                    <td><input type='text' class='tags_topic_count_txt' name='txt_topic_count' value='' /></td>
                </tr>
            <?php endif ?>
            <?php $i=0 ?>
            <?php foreach ($tag->tbl_tags_all as $tag->tbl_tag): ?>
                <?php $alt=''; if($i%2) $alt='_alt'; $i++; ?>
                <tr class='tags_row<?php echo $alt ?>'>
                    <?php if($event_edit && $tag->tbl_tag->tag_id == $tag_id): ?>
                        <td>
                            <input type='button' name='btn_delete' onclick="if(confirm_delete()) button_click(this);" value='Delete' />
                            <input type='button' name='btn_cancel' onclick="window.location='<?php echo Page::get_url('admin/tags') ?>'" value='Cancel' />
                            <input type='button' name='btn_save' onclick='button_click(this)' value='Save' />
                        </td>
                        <td><?php echo $tag->tbl_tag->tag_id ?></td>
                        <td><input type='text' class='tags_tag_txt' name='txt_tag' value='<?php echo $tag->tbl_tag->tag ?>' /></td>
                        <td><input type='text' class='tags_topic_txt' name='txt_default_topic_id' value='<?php echo $tag->tbl_tag->default_topic_id ?>' /></td>
                        <td align='center'>
                            <input type='hidden' name='txt_image' value='<?php echo $tag->tbl_tag->image ?>' />
    						<? if (isset($tag->tbl_tag->image)): ?>
    	                        <img src='<?php echo Page::get_url($tag->tbl_tag->image, true) ?>' border='0' alt='<?php echo $tag->tbl_tag->image ?>' class='tag_full' /><br />
    						<? endif ?>
                            <a href='#' onclick="document.getElementById('tags_upload_div').style.display='block';">Change</a>
                            <div id='tags_upload_div'>
                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                <input name="uploadedfile" type="file" id='tags_upload' /><br />
                                <input type="button" value="Cancel" onclick="document.getElementById('tags_upload').value='';document.getElementById('tags_upload_div').style.display='none';" />
                            </div>                        
                            
                        </td>
                        <td>
                            <input type='text' class='tags_topic_count_txt' name='txt_topic_count' value='' /><br />
                            <div class='help'>Leave blank to keep original</div>
                        </td>
                    <?php else: ?>
                        <td><a href='<?php echo Page::get_url('admin/tags').'/edit/'.$tag->tbl_tag->tag_id ?>'>Edit</a></td>
                        <td><?php echo $tag->tbl_tag->tag_id ?></td>
                        <td><?php echo $tag->tbl_tag->tag ?></td>
                        <td><?php echo $tag->tbl_tag->default_topic_id ?></td>
                        <td>
    						<? if (isset($tag->tbl_tag->image)): ?>
    							<img src='<?php echo Page::get_url($tag->tbl_tag->image, true) ?>' border='0' alt='<?php echo $tag->tbl_tag->image ?>' class='tag_full' />
    						<? endif ?>
    					</td>
                        <td><?php echo $tag->tbl_tag->topic_count ?></td>
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
