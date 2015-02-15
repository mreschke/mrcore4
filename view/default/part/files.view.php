<? eval(Page::load_code('part/files')) ?>
<? function load_files_view() { GLOBAL $files; ?>

<a name='fm<? echo $files->instance ?>'></a>
<div class='fc' id='files_outer_div<? echo $files->instance ?>'>
    <? if (!$files->embed) echo "<div class='fcfull'>" ?>
    <div class='fb'>
        <? if (!$files->embed) echo "<div class='fbfull'>" ?>
        <div class='files_outer_border'>
            
            <? if(!$files->hide_menu) eval(Page::load_part('files/menu', false)); ?>
            
            <? if(!$files->hide_nav && $files->found_dir): ?>
                <div class='files_nav'>
                    <? foreach($files->nav as $name => $nav): ?>
                        <? if($name != ''): ?>
                        /<span class='a' onclick="folder_click_ajax('<? echo Page::get_url('ajax/files.ajax').'/'.$nav.$files->ajax_url ?>', <? echo $files->instance ?>);">
                            <? echo $name ?>
                        </span>
                        <? endif ?>
                    <? endforeach ?>
                    <?# <div id='files_full' onclick="window.open('<?#=Page::get_url('files').'/'.$files->path ? >','_blank')" title='Open Full File Manager in New Tab'></div> ?>
                </div>
            <? else: ?>
                <?# <div id='files_full' onclick="window.open('<?#=Page::get_url('files').'/'.$files->path ? >','_blank')" title='Open Full File Manager in New Tab'></div> ?>
            <? endif ?>
            
            
            <? if(isset($files->files)): ?>    
                <div id="files_div">
                    <? if ($files->view == 'detail' || $files->view == 'detailpreview'): ?>
                        <? eval(Page::load_part('files/detail', false)) ?>
                    <? elseif ($files->view == 'detailpreview'): ?>
                        <? eval(Page::load_part('files/detailpreview', false)) ?>
                    <? elseif ($files->view == 'icon'): ?>
                        <? eval(Page::load_part('files/icon', false)) ?>
                    <? elseif ($files->view == 'preview'): ?>
                        <? eval(Page::load_part('files/preview', false)) ?>
                    <? endif ?>
                </div>
            <? endif ?>
            
        </div>
        <? if (!$files->embed) echo "</div>" ?>
    </div>



    <?# HIDDEN VARS FOR POSTBACKS ?>
    <input type='hidden' name='hid_instance' id='hid_instance' />
    <input type='hidden' name='hid_path<? echo $files->instance ?>' value='<? echo $files->path ?>' />
    <input type='hidden' name='hid_ajax_url<? echo $files->instance ?>' value='<? echo $files->ajax_url ?>' />
    
    <? if ($files->instance == 1 && $files->perm_write): ?>
    <div id='files_upload_div' class='m_dialog'>
        <table width='100%'>
            <tr>
                <td>
                    <div class='m_dialog_title' id='upload_dialog_title'>Upload Content</div>
                    <div class='m_dialog_close'>
                        <a href="javascript:toggle_dialog('files_upload_div')">
                            <img src='<?php echo Page::get_url('close.png') ?>' title='Close' alt='Close' border='0' ?>
                        </a>
                    </div>                
                </td>
            </tr>
            <tr>
                <td align='left' valign='top'>
                    <div class='m_dialog_content'>
                        <div class='help'>NOTICE: Uploading will cause a postback, if you are editing this topic please save it first.</div>
                        
                        <div class='section_header01'>Choose File:</div>
                        <div class='section01'>
                            <table class='table_pad'>
                                <tbody align='left' valign='middle'>
                                <tr>
                                    <th>File:</th>
                                    <td>
                                        <div id='files_upload_input'>
                                            <input type='hidden' name='hid_upload_instance' id='hid_upload_instance' />
                                            <input type='hidden' name='MAX_FILE_SIZE' value='<? echo Config::MAX_UPLOAD * 1024 * 1024 ?>' />
                                            <input name='uploadedfile' type='file' id='files_upload' />
                                        </div>
                                    </td>
                                    </div>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    
                        <div class='section_header01'>Image Options</div>
                        <div class='help'>Will only process images greater than <? echo Config::MAX_FILE_PREVIEW_SIZE ?> KB.</div>
                        <div class='section01'>
                            <table class='table_pad'>
                                <tbody align='left' valign='middle'>
                                <tr>
                                    <th>Process Images:</th>
                                    <td>
                                        <input type='checkbox' checked='checked' name='chk_process_image' id='chk_process_image' />
                                        <span class='help'>If upload is an Image file, automatically create image preview and thumbnail images (recommended)?</span>
                                    </td>
                                </tr><tr>
                                    <th>Process Archive Images:</th>
                                    <td>
                                        <input type='checkbox' checked='checked' name='chk_process_images' id='chk_process_images' />
                                        <span class='help'>
                                                If upload is an archive, and the archive contains Images, automatically create image preview and thumbnail images for every image in the archive?
                                                <br /><b>NOTICE:</b> Only applies if archive is chosen to be extracted below!
                                        </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class='section_header01'>Archive Options</div>
                        <div class='section01'>
                            <table class='table_pad'>
                                <tbody align='left' valign='middle'>
                                <tr>
                                    <th>Extract Archive:</th>
                                    <td>
                                        <input type='checkbox' checked='checked' name='chk_extract' id='chk_extract' />
                                        <span class='help'>If upload is an archive (compressed file), extract its contents to the server?<br /><b>Supported Archives:</b> <? echo preg_replace("/\,/i", ", ", Config::KNOWN_ARCHIVES) ?></span>
                                    </td>
                                </tr><tr>
                                    <th>Keep Archive:</th>
                                    <td>
                                        <input type='checkbox' checked='checked' name='chk_keep_compressed' id='chk_keep_compressed' />
                                        <span class='help'>If archive was extracted, keep the original compressed archive file?</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <br />
                        <table class='table_pad' width='200px'>
                            <tbody align='left' valign='middle'>
                            <tr>                        
                                <td>
                                    <input type='button' onclick='button_click(this)' name='btn_upload' id='btn_upload' value='Upload' />
                                </td>
                                <td align='right'><input type='submit' name='btn_cancel_upload' onclick="javascript:toggle_dialog('files_upload_div');return false" value='Cancel' /></td>
                            </tr><tr>
                                <td colspan='2' height='20px'></td>
                            </tr><tr>
                                <td colspan='2'>
                                    
                                </td>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <? endif ?>
    <? if (!$files->embed) echo "</div>" ?>
</div> <?# fc (entire div is ajaxed on folder click) ?>







<?# -------------------------------- ABSOLUTE POSITIONED DIVS -------------------------------- ?>
<? #if ((($files->embed && $files->instance == 1) || ($files->instance == 10 && !$_GET['loadedonce']) || (!$files->embed && $files->instance == 1)) && !$files->hide_contextmenu): ?>

<? if (!$files->embed): ?>

<?# AHH, this needs fixed, its duplicated in topic.view.php too ?>
<div id='files_context_menu'>
    <div class='files_menu_item' id='files_context_opentab'>Open In New Tab</div>
    <div class='files_menu_item' id='files_context_openwindow'>Open In New Window</div>
    <div class='files_menu_separator'></div>
    <div class='files_menu_item' id='files_context_openurl'>Open as URL</div>
    <div class='files_menu_separator'></div>
    <?# <a target='_blank' href='' id='files_context_open_a'>Open File</a></div> ?>
    <?# <div class='files_menu_item' id='files_context_listarchive'>List Archive Contents</div>?>
    <div class='files_menu_separator'></div>
    <div class='files_menu_item' id='files_context_download'>Download File</div>
    <? if($files->perm_write): ?>
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
    <? if($files->perm_write): ?>
        <div class='files_menu_item' id='folder_context_rename'>Rename Folder</div>
        <div class='files_menu_item' id='folder_context_delete'>Delete Folder</div>
    <? endif ?>
</div>
<?endif ?>




    <? if($files->perm_write): ?>
        <div id='files_rename'>
            <div id='files_rename_title'>Rename File:</div>
            <div><input type='text' id='files_rename_text' /></div>
            <div>
                <input type='submit' name='btn_rename' id='btn_rename' value='Rename' />
                <input type='submit' name='btn_rename_cancel' id='btn_rename_cancel' value='Cancel' onclick="document.getElementById('files_rename').style.display = 'none';return false;" />
            </div>
        </div>
    <? endif ?>
<? #endif ?>


<? if($files->perm_write): ?>
    <div id='files_newfolder<? echo $files->instance ?>' class='files_newfolder'>
        <div>Folder Name:</div>
        <div><input type='text' id='files_newfolder_text<? echo $files->instance ?>' class='files_newfolder_text' /></div>
        <div>
            <input type='submit' name='btn_newfolder' id='btn_newfolder' value='New Folder' onclick="create_folder('<? echo Page::get_url('ajax/files.ajax') ?>', '<? echo $files->ajax_url ?>', <? echo $files->instance ?>, '<? echo $files->path ?>');return false;" />
            <input type='submit' name='btn_newfolder_cancel' id='btn_newfolder_cancel' value='Cancel' onclick="document.getElementById('files_newfolder<? echo $files->instance ?>').style.display = 'none';return false;" />
        </div>    
    </div>

    <div id='files_newfile<? echo $files->instance ?>' class='files_newfile'>
        <div>File Name:</div>
        <div><input type='text' id='files_newfile_text<? echo $files->instance ?>' class='files_newfile_text' /></div>
        <div>
            <input type='submit' name='btn_newfile' id='btn_newfile' value='New File' onclick="create_file('<? echo Page::get_url('ajax/files.ajax') ?>', '<? echo $files->ajax_url ?>', <? echo $files->instance ?>, '<? echo $files->path ?>');return false;" />
            <input type='submit' name='btn_newfile_cancel' id='btn_newfile_cancel' value='Cancel' onclick="document.getElementById('files_newfile<? echo $files->instance ?>').style.display = 'none';return false;" />
        </div>    
    </div>
<? endif ?>

<? }
