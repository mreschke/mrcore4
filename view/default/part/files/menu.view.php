<div id='files_menu_div<? echo $files->instance ?>' class='files_menu_div'>
    <table id='files_menu_table' border='0'>
        <tbody align='left' valign='top'>
        <tr>
            <td width='5px'>
                <span class='a files_menu_txt' id='files_menu_file_txt<? echo $files->instance ?>' onclick="toggle_menu('file', <? echo $files->instance ?>, false);" onmouseover="toggle_menu('file', <? echo $files->instance ?>, true);">File</span>
                <div id='files_menu_file<? echo $files->instance ?>' class='files_menu files_menu_file'>
                    <? if($files->perm_write): ?>
                        <?# <div class='files_menu_item' onclick="toggle_menu('hideall', <? #echo $files->instance ? >);document.getElementById('files_upload_div').style.display='block';document.getElementById('files_upload_box').style.display='block';">Upload</div> ?>
                        <?# <div class='files_menu_item'><a href='<? #echo Page::get_url('upload').'/'.$files->path ? >' target='_blank'>Upload Files</a></div> ?>
                        <?# <div class='files_menu_item' onclick="window.open('<? #echo Page::get_url('upload').'/'.$files->path ? >','fileupload','width=900,height=700,scrollbars=1')">Upload Files</div> ?>
                        <? if ($files->instance != 99): ?>
                            <div class='files_menu_item' onclick="open_upload_dialog(<? echo $files->instance ?>);">Upload File</div>
                        <? endif ?>
                        <div class='files_menu_separator'></div>
                        <div class='files_menu_item' onclick="move_newfolder_menu(event, <? echo $files->instance ?>);">New Folder</div>
                        <div class='files_menu_item' onclick="move_newfile_menu(event, <? echo $files->instance ?>);">New Plain-Text File</div>
                        <div class='files_menu_separator'></div>
                        <?
                        $url = htmlentities(preg_replace('" "', '+', Page::get_url('files').'/'.$files->path.$files->ajax_url2));
                        ?>
                        <div class='files_menu_item'><a href="<?= $url ?>">Open as URL</a></div>
                    <? endif ?>
                    <? if($files->view == 'detail' || $files->view == 'detailpreview'): ?>
                        <?# <div class='files_menu_item'>Download Selected (zip)</div> ?>
                        <?# <div class='files_menu_item'>Download Selected (tar.gz)</div> ?>
                    <? endif ?>
                </div>
            </td>
            <td width='5px'>
                <span class='a files_menu_txt' id='files_menu_edit_txt<? echo $files->instance ?>' onclick="toggle_menu('edit', <? echo $files->instance ?>, false);" onmouseover="toggle_menu('edit', <? echo $files->instance ?>, true);">Edit</span>
                <div id='files_menu_edit<? echo $files->instance ?>' class='files_menu files_menu_edit'>
                    <? if($files->perm_write): ?>
                        <? if($files->view == 'detail' || $files->view == 'detailpreview'): ?>
                            <?# <div class='files_menu_item'>Cut</div> ?>
                            <?# <div class='files_menu_item'>Copy</div> ?>
                            <?# <div class='files_menu_item'>Paste</div> ?>
                            <?# <div class='files_menu_separator'></div> ?>
                        <? endif ?>
                    <? endif ?>
                    <? if($files->view == 'detail' || $files->view == 'detailpreview'): ?>
                        <?# <div class='files_menu_item'>Select All</div> ?>
                        <?# <div class='files_menu_item'>Select None</div> ?>
                    <? endif ?>
                    <? if($files->perm_write): ?>
                        <div class='files_menu_separator'></div>
                        <? if($files->view == 'detail' || $files->view == 'detailpreview'): ?>
                            <?# <div class='files_menu_item'>Delete Selected</div> ?>
                        <? endif ?>
                        <div class='files_menu_separator'></div>
                        <? if($files->view == 'detail' || $files->view == 'detailpreview'): ?>
                            <?# <div class='files_menu_item' title='Create preview/thumbs of selected images'>Process Selected Images</div> ?>
                        <? endif ?>
                        <div class='files_menu_item' title='Create preview/thumbs of all images in this folder (not subfolders)' onclick="folder_click_ajax('<? echo Page::get_url('ajax/files.ajax').'/'.$files->path.$files->ajax_url.'&process_images=1' ?>', <? echo $files->instance ?>);">Process All Images</div>
                        <div class='files_menu_item' title='Create preview/thumbs of all images in this folder (including subfolders)' onclick="folder_click_ajax('<? echo Page::get_url('ajax/files.ajax').'/'.$files->path.$files->ajax_url.'&process_images=1&process_images_recursive=1' ?>', <? echo $files->instance ?>);">Process All Images (Recursive)</div>
                    <? endif ?>
                </div>
            </td>
            <td>
                <span class='a files_menu_txt' id='files_menu_view_txt<? echo $files->instance ?>' onclick="toggle_menu('view', <? echo $files->instance ?>, false);" onmouseover="toggle_menu('view', <? echo $files->instance ?>, true);">View</span>
                <div id='files_menu_view<? echo $files->instance ?>' class='files_menu files_menu_view'>
                    <div class='files_menu_item' onclick="folder_click_ajax('<? echo Page::get_url('ajax/files.ajax').'/'.$files->path.$files->ajax_url.'&view=detail' ?>', <? echo $files->instance ?>);">Detail</div>
                    <div class='files_menu_item' onclick="folder_click_ajax('<? echo Page::get_url('ajax/files.ajax').'/'.$files->path.$files->ajax_url.'&view=detailpreview' ?>', <? echo $files->instance ?>);">Detail Preview</div>
                    <div class='files_menu_item' onclick="folder_click_ajax('<? echo Page::get_url('ajax/files.ajax').'/'.$files->path.$files->ajax_url.'&view=icon' ?>', <? echo $files->instance ?>);">Icons</div>
                    <div class='files_menu_item' onclick="folder_click_ajax('<? echo Page::get_url('ajax/files.ajax').'/'.$files->path.$files->ajax_url.'&view=preview' ?>', <? echo $files->instance ?>);">Preview</div>
                    <?# <div class='files_menu_item' onclick="folder_click_ajax('<? #echo Page::get_url('ajax/files.ajax').'/'.$files->path.$files->ajax_url.'&view=slideshow' ? >', <?#=echo $files->instance ? >);">Slideshow</div> ?>
                    <div class='files_menu_separator'></div>
                    <div class='files_menu_item' onclick="folder_click_ajax('<? echo Page::get_url('ajax/files.ajax').'/'.$files->path.$files->ajax_url.'&show_hidden=1' ?>', <? echo $files->instance ?>);">Show Hidden</div>
                    <div class='files_menu_item' onclick="folder_click_ajax('<? echo Page::get_url('ajax/files.ajax').'/'.$files->path.$files->ajax_url.'&show_hidden=0' ?>', <? echo $files->instance ?>);">Hide Hidden</div>
                    <div class='files_menu_separator'></div>
                    <? if($files->embed): ?>
                        <?# <div class='files_menu_item' onclick="window.open('<? #echo Page::get_url('files').'/'.$files->path ? >','filemanager','width=900,height=700,scrollbars=1')">Full Manager</div> ?>
                        <div class='files_menu_item' onclick="window.open('<? echo Page::get_url('files').'/'.$files->path ?>','_blank')">Full Manager</div>
                    <? endif ?>
                    <div class='files_menu_item' onclick="folder_click_ajax('<? echo Page::get_url('ajax/files.ajax').'/'.$files->path.$files->ajax_url.'&reset=1' ?>', <? echo $files->instance ?>);">Reset Defaults</div>
                    <? if(!$files->embed): ?>
                        <div class='files_menu_separator'></div>
                        <div class='files_menu_item'><a href='<?= Page::get_url("topic").'/'.$files->topic_id ?>' target='_blank'>View Topic</a></div>
                    <? endif ?>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>