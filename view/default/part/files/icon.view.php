<table class='files_icon_table' border='0'><tbody><tr><td>
<?

//On file/folder click, do I show context menu or just open the file
$contextmenu = 1; if ($files->hide_contextmenu) $contextmenu = 0;

foreach ($files->files as $file_class) {
    $is_dir     = 0;  if ($file_class->is_dir) $is_dir = 1;
    $is_archive = 0;  if (in_array($file_class->ext, explode(",", Config::KNOWN_ARCHIVES))) $is_archive = 1;
    #$onclickvars = "$files->instance, '".Page::get_url('files')."', '".Page::get_url('ajax/files.ajax')."', '".$files->ajax_url."', '".$files->path."', '".urlencode($file_class->filename)."', '".$file_class->ext."', $is_dir,  $is_archive";
    $onclickvars = "$files->instance, '".Page::get_url('files')."', '".Page::get_url('ajax/files.ajax')."', '".$files->ajax_url."', '".$files->path."', '".urlencode($file_class->filename)."', '".$file_class->ext."', $is_dir, $is_archive, '".$files->ajax_url2."'";
    
    if($file_class->is_file || ($file_class->is_dir && !$files->hide_subfolders)) {
        echo "<div class='files_iconview_div'>";
            echo "<div class='files_iconview_icon'>";
                if($file_class->is_dir) { ##### ICON - DIR #####--image
                    echo "<div class='a' onclick=\"folder_click(event, false, $onclickvars);\">
                        <img src='".$file_class->icon2."' alt='icon' border='0' class='files_icon_thumbnail' />
                    </div>";
                    
                } else { ##### ICON - FILE #####--image
                    echo "<div onclick=\"file_click(event, false, $onclickvars);\">";
                        if ($file_class->type == 'image' && $file_class->filename_thumb != '') {
                            $thumbclass = ($file_class->image_height > 50) ? "class='files_icon_thumbnail'" : "";
                            echo "<img src='".Page::get_url('files').'/'.$file_class->path.'/'.$file_class->filename_thumb."' alt='preview' border='0' $thumbclass />";
                        } else {
                            echo "<img src='".$file_class->icon2."' alt='icon' border='0' class='files_icon_thumbnail' />";
                        }
                    echo "</div>";
                }
            echo "</div>";
            
            echo "<div class='files_iconview_filename'>";
                if($file_class->is_dir) {  ##### ICON - DIR #####--filename-text
                    echo "<div class='files_attr'>folder</div>";
                    echo "<div  class='a' onclick=\"folder_click(event, ".$contextmenu.", $onclickvars);\">".$file_class->filename."</div>";
                    
                } else { ##### ICON - FILE #####--filename-text
                    echo "<div class='files_attr'>";
                        echo number_format(round($file_class->size / (1024)), 0, ".", ",")."k";
                        if($file_class->type=='image') {
                            echo $file_class->image_width.'x'.$file_class->image_height;
                        }
                    echo "</div>";
                    echo "<div class='a' onclick=\"file_click(event, ".$contextmenu.", $onclickvars);\">".$file_class->filename."</div>";
                }
            echo "</div>";
        echo "</div>";
    }
}
?>
</td></tr></tbody></table>