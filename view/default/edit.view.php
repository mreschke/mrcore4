<div id='ec'>
    <div id='eb'>
    <?php if(!$edit->view_content_hide): ?>
        <table width='100%'>
            <tr>
                <td>
                    <table width='100%' style='margin-bottom: 10px'>
                        <tr>
                            <td valign='middle' style='white-space:nowrap'>
                                <input type='text' name='txt_title' onkeyup="keyup_actions(this, event)" value="<?php echo htmlentities($topic->tbl_post->title) ?>" id='edit_title_text' />
                            </td>
                            <? if ($new_topic && Config::NEW_TOPIC_TEMPLATE_TAG_ID > 0): ?>
                            <td valign='middle' width="100%">
                                <select name='sel_template' id='sel_template' onfocus="template_id=this.value" onchange="change_template()">
                                    <? foreach ($templates as $template_id => $template): ?>
                                        <? if ($template_id == $_POST['sel_template']): ?>
                                            <option value="<?= $template_id ?>"selected="selected"><?= $template ?></option>
                                        <? else: ?>
                                            <option value="<?= $template_id ?>"><?= $template ?></option>
                                        <? endif ?>
                                    <? endforeach ?>
                                </select>
                            </td>
                            <? endif ?>

                            <td width="50">
                                <div style='margin-top:-5px; color:#666'>
                                    <input type='checkbox' id='chk_wrap' checked='checked' onclick="
                                        if (this.checked) {
                                            document.getElementById('edit_body_textarea').setAttribute('style', 'word-wrap: break-word');
                                        } else {
                                            document.getElementById('edit_body_textarea').setAttribute('style', 'word-wrap: normal');
                                        }
                                        resize_edit_textarea();
                                    " />Wrap
                                </div>
                            </td>

                            <td align='right' valign='middle' width='50'>
                                <div class='a' style='width:75px' onclick="javascript:window.open(
                                    '<? echo Page::get_url('topic').'/'.Config::WIKI_HELP_TOPIC_ID ?>?viewmode=simple', '',
                                    'height=700,width=900,resizable=1,scrollbars=1');">
                                    Wiki Help
                                    <img src='<? echo Page::get_url('newwindow.png') ?>' border='0' />
                                </div>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr><tr>
                <td>
                    <? if (file_exists(Config::ABS_BASE."/web/theme/".Config::THEME."/wysiwyg.html")): ?>
                        <textarea name='txt_body' class='wysiwyg /theme/<?php echo Config::THEME ?>/wysiwyg.html' rows='0' cols='0' id='edit_body_textarea' onkeydown='return catch_tab(this,event)'><?php echo $topic->tbl_post->body ?></textarea>
                    <? else: ?>
                        <textarea name='txt_body' class='wysiwyg /theme/default/wysiwyg.html' rows='0' cols='0' id='edit_body_textarea' onkeydown='return catch_tab(this,event)'><?php echo $topic->tbl_post->body ?></textarea>
                    <? endif ?>
                    <!-- <textarea name='txt_body' class='wysiwyg wysiwyg.html' rows='0' cols='0' id='edit_body_textarea'><?php #echo $edit->view_body ?></textarea> -->
                    <!--<textarea name="demo" class="wysiwyg wysiwyg.html"><?php #echo $edit->view_body ?></textarea> -->
                </td>
            </tr>
        </table>
        <div class='edit_buttons'>
            <table width='100%'>
                <tr>
                    <td width='33%' valign='top'><input type='submit' onclick='return validate();' name='btn_save' value='Save' class='edit_save_submit' title='Save the post and continue editing' /></td>
                    <td width='33%' valign='top' align='center'><input type='submit' onclick='return validate()' name='btn_save_view' value='Save & View' class='edit_save_view_submit' id='edit_save_view_submit' title='Save and view the post (Shift+Enter while typing in the body)' /></td>
                    <td width='33%' valign='top' align='right'><input type='button' onclick='button_click(this)' name='btn_cancel' value='Cancel' class='edit_cancel_submit' title='Cancel all changes since last save and view post' /></td>
                </tr>
            </table>
        </div>
    <?php endif ?>


    <?php if(!$edit->view_files_hide): ?>
        <div class='section_header01'>Files</div>
        <?php if ($new_topic): ?>
            <div class='help'>You must save this topic before you can upload files</div>
        <?php elseif(!is_null($files->path)): ?>
            <?php if ($files->perm_writeXXXXXXXXXXXXXXXX): ?>
                <script type="text/javascript">
                    $(function(){
                        $('#swfupload-control').swfupload({
                            upload_url: '<?php echo Config::WEB_BASE.'upload-file.php?path='.$files->path ?>',
                            file_post_name: 'uploadfile',
                            file_size_limit : "1048576", //In KB
                            //file_types : "*.jpg;*.png;*.gif",
                            file_types : "*.*",
                            //file_types_description : "Image files",
                            file_types_description : "All Files",
                            file_upload_limit : 100,
                            flash_url : '<?php echo Config::WEB_BASE.'theme/'.Config::THEME ?>/js/uploader/swfupload.swf',
                            button_placeholder : $('#button')[0],
                            button_image_url : '<?php echo Config::WEB_BASE.'theme/'.Config::THEME ?>/images/wdp_buttons_upload_114x29.png',
                            button_width : 114,
                            button_height : 29,
                            //button_image_url : '/theme/devel/images/XPButtonUploadText_61x22.png',
                            //button_image_url : '/theme/devel/images/upload.png',
                            //button_width : 32,
                            //button_height : 32,
                            debug: false
                        })
                            .bind('fileQueued', function(event, file){
                                var listitem='<li id="'+file.id+'" >'+
                                    'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
                                    '<div class="progressbar" ><div class="progress" ></div></div>'+
                                    '<p class="status" >Pending</p>'+
                                    '<span class="cancel" >&nbsp;</span>'+
                                    '</li>';
                                $('#log').append(listitem);
                                $('li#'+file.id+' .cancel').bind('click', function(){
                                    var swfu = $.swfupload.getInstance('#swfupload-control');
                                    swfu.cancelUpload(file.id);
                                    $('li#'+file.id).slideUp('fast');
                                });
                                // start the upload since it's queued
                                $(this).swfupload('startUpload');
                            })
                            .bind('fileQueueError', function(event, file, errorCode, message){
                                alert('Size of the file '+file.name+' is greater than limit');
                            })
                            .bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
                                $('#queuestatus').text('Files Selected: '+numFilesSelected+' / Queued Files: '+numFilesQueued);
                            })
                            .bind('uploadStart', function(event, file){
                                $('#log li#'+file.id).find('p.status').text('Uploading...');
                                $('#log li#'+file.id).find('span.progressvalue').text('0%');
                                $('#log li#'+file.id).find('span.cancel').hide();
                            })
                            .bind('uploadProgress', function(event, file, bytesLoaded){
                                //Show Progress
                                var percentage=Math.round((bytesLoaded/file.size)*100);
                                $('#log li#'+file.id).find('div.progress').css('width', percentage+'%');
                                $('#log li#'+file.id).find('span.progressvalue').text(percentage+'%');
                            })
                            .bind('uploadSuccess', function(event, file, serverData){
                                var item=$('#log li#'+file.id);
                                item.find('div.progress').css('width', '100%');
                                item.find('span.progressvalue').text('100%');
                                var pathtofile='<a href="<?php echo Page::get_url('files').'/'.$files->path.'/' ?>'+file.name+'" >view &raquo;</a>';
                                item.addClass('success').find('p.status').html('Done!!! | '+pathtofile);
                            })
                            .bind('uploadComplete', function(event, file){
                                // upload has completed, try the next one in the queue
                                $(this).swfupload('startUpload');
                            })
                    }); 
                </script>
                <div id="swfupload-control">
                    <input type="button" id="button" />
                    <p id="queuestatus" ></p>
                    <ol id="log"></ol>
                </div>
            <?php endif ?>
            
            <?php eval(Page::load_part('files')); load_files_view(); ?>
        <?php else: ?>
            Error: No file directory found for this topic in <?php echo Config::FILES_DIR ?>
        <?php endif ?>
    <?php endif ?>

    
    <?php if(!$edit->view_org_hide): ?>
        <script type="text/javascript">        
        $(document).ready(function() {
            $("#edit_tokenize_badge").tokenInput("/ajax/getbadge.ajax", {
                hintText: "Autocomplete Badges",
                noResultsText: "No results",
                searchingText: "Searching..."
            });
            
            $("#edit_tokenize_tag").tokenInput("/ajax/gettag.ajax", {
                hintText: "Autocomplete Tags",
                noResultsText: "No results",
                searchingText: "Searching..."
            });

        });
        </script>    
        <div class='section_header01'>Organization</div>
        <div class='help'>Please help us categorize this new post by selecting the appropriate badge and tag, or by creating new tags if needed.</div>
        <div class='section01'>
            <table cellpadding='2' cellspacing='2'>
                <tr>
                    <td>
                        <table width='100%'>
                            <tr>
                                <td><div class='section_header02'>Badges</div></td>
                                <td width='20'>&nbsp;</td>
                                <td><div class='section_header02'>Tags</div></td>
                                <td width='20'>&nbsp;</td>
                                <td><div class='section_header02'>Create Tags</div></td>
                            </tr><tr>
                                <td valign='top'>
                                    <div class='nice_border'>
                                        <select name='lst_badges[]' size='15' multiple='multiple' id='edit_badges_select'>
                                            <?php foreach ($badge->tbl_badges_all_selected as $badge->tbl_badge): ?>
                                                <?php if ($badge->tbl_badge->selected): ?>
                                                    <option selected='selected' value='<?php echo $badge->tbl_badge->badge_id ?>'><?php echo $badge->tbl_badge->badge ?></option>
                                                <?php else: ?>
                                                    <option value='<?php echo $badge->tbl_badge->badge_id ?>'><?php echo $badge->tbl_badge->badge ?></option>                                            
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <input type="text" id="edit_tokenize_badge" name="edit_tokenize_badge_txt" />
                                </td>
                                <td>&nbsp;</td>
                                <td valign='top'>
                                    <div class='nice_border'>
                                        <select name='lst_tags[]' size='15' multiple='multiple' id='edit_tags_select'>
                                            <?php foreach ($tag->tbl_tags_all_selected as $tag->tbl_tag): ?>
                                                <?php if ($tag->tbl_tag->selected): ?>
                                                    <option selected='selected' value='<?php echo $tag->tbl_tag->tag_id ?>'><?php echo $tag->tbl_tag->tag ?></option>
                                                <?php else: ?>
                                                    <option value='<?php echo $tag->tbl_tag->tag_id ?>'><?php echo $tag->tbl_tag->tag ?></option>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <input type="text" id="edit_tokenize_tag" name="edit_tokenize_tag_txt" />
                                </td>
                                <td>&nbsp;</td>
                                <td valign='top'>
                                    <div class='help'>If the tag you want does not already exist, you can create it here.  <b>Please keep tags short.  Tags are converted to lowercase and spaces removed.</b> You can create multiple tags here by separating them with a comma.</div>
                                    <br />
                                    <div class='label'>New Tags (comma separated)</div>
                                    <div class='nice_border' id='edit_tag_text_div'>
                                        <input type='text' name='txt_new_tag' id='edit_tag_text' />
                                    </div>
                                    <div class='section_header02' style='margin-top: 20px'>Topic Visibility</div>
                                    <div class='help'>Hidden topics won't be visible during normal browser/search operations.  Used for system or site type pages/topics.  This is not a security mechanism, see post permissions for that.</div>
                                    <input type='checkbox' name='chk_hidden' <? if($topic->tbl_post->hidden) { echo "checked=checked"; } ?> id='chk_hidden'>Hidden
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>        
        </div>
    <?php endif ?>

    
    
    <?php if(!$edit->view_perm_hide): ?>
        <div class='section_header01'>Permissions</div>
        <div class='section01'>
            <table class='table_pad'>
                <tbody align='left' valign='top'>
                <tr>
                    <td>
                        <div class='section_header02'>Group Level Permissions</div>
                        <div class='section01'>
                            <div id='edit_perm_div'>
                                <table border='0' class='table_pad vtable'>
                                    <tr>
                                        <th>&nbsp</th>
                                        <? foreach ($topic->perms as $perm_item): ?>
                                            <th ><?=$perm_item ?></th>
                                        <? endforeach ?>
                                    </tr>
                                    <? foreach ($perm->tbl_perm_groups as $perm->tbl_perm_group): ?>
                                        <? if ($perm->tbl_perm_group->selected): ?>
                                        <tr>
                                            <td><?=$perm->tbl_perm_group->group ?></td>
                                            <? foreach ($topic->perms as $perm_item): ?>
                                                <? foreach ($perm->tbl_perms_all_selected as $perm->tbl_perm_selected): ?>
                                                    <? if ($perm->tbl_perm_selected->group_name == $perm->tbl_perm_group->group && $perm->tbl_perm_selected->short == $perm_item): ?>
                                                        <td>
                                                            <input
                                                                   type='checkbox'
                                                                   name='chk_perm_<?=$perm->tbl_perm_selected->perm_group_id .'_'.$perm->tbl_perm_selected->perm_id ?>'
                                                                   value='<?=$perm->tbl_perm_selected->perm_group_id .'_'.$perm->tbl_perm_selected->perm_id ?>'
                                                                   <?php if ($perm->tbl_perm_selected->selected): ?>
                                                                   checked='checked'
                                                                   <?php endif ?>
                                                            />
                                                        </td>
                                                        <? break ?>
                                                    <? endif ?>
                                                <? endforeach ?>
                                            <? endforeach ?>
                                        </tr>
                                        <? endif ?>
                                    <? endforeach ?>
                                </table>
                            </div>
                        </div>
                    </td><td>

                        <div class='section_header02'>Public Sharing URL</div>
                        <div class='section01'>
                            <div class='help'>
                                    This link will allow anyone to READ this document regardless of the topics permissions.
                                    This allow the sharing of private topics to the public.
                                    This method is slightly more secure than simply making the topic public and is the best option for giving out sensitive topics to a few public individuals.
                                    This URL will NOT require the user to login and therefore may be a security risk.
                                    Use caution when giving this link to the public. 
                                    <p>Any file attached to this topic can also be appended with <b>?uuid=<? echo $topic->tbl_post->post_uuid ?></b> to give public access.</p>
                            </div>
                            <p>
                                <input type='checkbox' name='chk_public_sharing' <? if ($topic->tbl_post->uuid_enabled): ?> checked='checked' <? endif ?> /> Enable Public Sharing URL<br />
                                <?
                                $publicurl = Page::get_url('topic').'/'.$topic->topic_id.'/'.urlencode($topic->tbl_post->title).'?uuid='.$topic->tbl_post->post_uuid;
                                if (!preg_match("'^http'i", $publicurl)) $publicurl = 'http:'.$publicurl;
                                ?>
                                <a href='<?=$publicurl?>' target='_blank'><?=$publicurl?></a>
                            </p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
    <?php endif ?>



    <? #if(!$edit->view_delete_hide): ?>
    <? if (!$new_topic): ?>
        <div class='section_header01'>Delete/Undelete</div>
        <div class='section01'>
            <div class='help'>
                This will delete the post.  When posts are deleted they are simply marked as deleted but never actually deleted.  You can view all deleted topics by searching for 'deleted=1'.  The topics files are preserved and so are its comments.  These deleted topics can be undeleted at anytime.  When posts are deleted their permissions are deleted too, so the document becomes private.
                <br /><br />
            </div>
            <? if ($topic->tbl_post->deleted): ?>
                <input type='button' name='btn_undelete' value='Undelete Post' onclick='button_click(this)' />
            <? else: ?>
                <input type='button' name='btn_delete' value='Delete Post' onclick='if (confirm_delete()) button_click(this); else return false;' />
            <? endif ?>
        </div>
    <? endif ?>


    


    <?php if(!$edit->view_admin_hide): ?>
        <div class='section_header01'>Admin Only</div>
        <div class='section01'>
            <div class='section_header02'>Created and Updated Overrides</div>
            <div class='section01'>
                <table class='table_pad'>
                <tbody>
                    <tr>
                        <td align='right'><b>Created By:</b></td>
                        <td>
                            <select name='lst_created_by' id='edit_created_by_select'>
                                <option selected='selected' value='-1'></option>
                                <?php foreach ($edit->users as $alias => $user_id): ?>
                                    <option value='<?php echo $user_id ?>'><?php echo $alias ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                        <td width='10'>&nbsp</td>
                        <td align='right'><b>Updated By:</b></td>
                        <td>
                            <select name='lst_updated_by' id='edit_updated_by_select'>
                                <option selected='selected' value='-1'></option>
                                <?php foreach ($edit->users as $alias => $user_id): ?>
                                    <option value='<?php echo $user_id ?>'><?php echo $alias ?></option>
                                <?php endforeach ?>
                            </select>                            
                        </td>
                    </tr>
                    <tr>
                        <td align='right' valign='top'><b>Created On:</b></td>
                        <td>
                            <input type='text' name='txt_created_on' id='edit_created_on_text' maxlength='19' />
                            <div class='help'>Ex: YYYY/MM/DD HH:MM:SS</div>
                        </td>
                        <td width='10'>&nbsp</td>
                        <td align='right' valign='top'><b>Updated On:</b></td>
                        <td>
                            <input type='text' name='txt_updated_on' id='edit_updated_on_text' maxlength='19' />
                            <div class='help'>Ex: YYYY/MM/DD HH:MM:SS</div>
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>
            
            <div class='section_header02'>Indexer</div>
            <div class='section01'>
                <div class='help'>Posts are not indexed (searchable) when saved.  The indexer script runs on a schedule
                on this server, usually a few times a day.  If you want this post to be indexed now, check the box below.</div>
                <input type='checkbox' name='chk_indexer' id='edit_indexer_checkbox' />Index Post on Save
            </div>
            
        </div>
    <?php endif ?>


    <?php if(!$edit->view_button2_hide): ?>
        <div class='edit_buttons'>
            <table width='100%'>
                <tr>
                    <td width='33%' valign='top'><input type='submit' onclick='return validate()' name='btn_save'  value='Save' class='edit_save_submit' title='Save the post and continue editing' /></td>
                    <td width='33%' valign='top' align='center'><input type='submit' onclick='return validate()' name='btn_save_view' value='Save & View' class='edit_save_view_submit' title='Save and view the post' /></td>
                    <td width='33%' valign='top' align='right'><input type='button' onclick='button_click(this)' name='btn_cancel' value='Cancel' class='edit_cancel_submit' title='Cancel all changes since last save and view post' /></td>

                </tr>
            </table>
        </div>
    <?php endif ?>

    </div>
</div>



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


<script type="text/javascript">
    //This will resize the edit textarea to your screen onload and onresize
    //document.edit_save_submit.onfocus=resize_edit_textarea;
    <? if($info->os != 'Android'): ?>
        window.onload=resize_edit_textarea;
    <? endif ?>
    
    <?php if($new_topic): ?>
        set_focus('edit_title_text');
    <?php else: ?>
        document.getElementById('edit_body_textarea').focus();
        //set_carrot_range('edit_body_textarea', 0, 100);
    <?php endif ?>
    
    <? if($info->os != 'Android'): ?>
        window.onresize = resize_edit_textarea;
    <? endif ?>
</script>    

