<table class='files_detail_table' border='0'>
    <? if(!$files->hide_header): ?>
        <thead align='left' valign='middle'>
        <tr class='files_th_tr'>
            <? if(!$files->hide_selection): ?>
                <td width='10'>&nbsp;</td>
            <? endif ?>
                <td width='16'>&nbsp;</td>
                <td>File</td>
            <? if (!$files->hide_columns): ?>
                <td width='75'>Size (KB)</td>
                <td width='40'>Type</td>
                <td width='140'>Created</td>
            <? endif ?>
        </tr>
        </thead>
    <? endif ?>
<tbody align='left' valign='middle'>

<?
//On file/folder click, do I show context menu or just open the file
$contextmenu = 1; if ($files->hide_contextmenu) $contextmenu = 0;

$i=0;
foreach ($files->files as $file_class) {
    $alt=''; if ($i%2) $alt='_alt'; $i++;
    $is_dir     = 0;  if ($file_class->is_dir) $is_dir = 1;
    $is_archive = 0;  if (in_array($file_class->ext, explode(",", Config::KNOWN_ARCHIVES))) $is_archive = 1;
    $onclickvars = "$files->instance, '".Page::get_url('files')."', '".Page::get_url('ajax/files.ajax')."', '".$files->ajax_url."', '".$files->path."', '".urlencode($file_class->filename)."', '".$file_class->ext."', $is_dir, $is_archive, '".$files->ajax_url2."'";
    
    if($file_class->is_file || ($file_class->is_dir && !$files->hide_subfolders)) {
        if(!$files->hide_background) {
            echo "<tr class='files_row${alt}'>";
        } else {
            echo "<tr>";
        }
        if(!$files->hide_selection) {
            echo "<td width='10'><input type='checkbox' name='chk_".$file_class->fullfile."' /></td>";
        }
        
        //The Icon Column
        echo "<td width='16' align='center'>";
            if($file_class->is_dir) {
                if ($files->view=='detailpreview') {
                    //Show Directory Icon for DetailPreview
                    echo "<div class='a' onclick=\"folder_click(event, ".$contextmenu.", $onclickvars);\">
                        <img src='".$file_class->icon2."' alt='icon' border='0' class='files_detail_thumbnail' />
                    </div>";
                } else {
                    //Show Directory Icon for Detail View
                    echo "<div onclick=\"folder_click(event, ".$contextmenu.", $onclickvars);\">
                        <img src='".$file_class->icon."' alt='icon' border='0' />
                    </div>";
                }
            } else {
                if ($files->view=='detailpreview') {
                    //Show File Icon for DetailPreview View
                    if ($file_class->type == 'image' && $file_class->filename_thumb != '') {
                        $thumbclass = ($file_class->image_height > 25) ? "class='files_detail_thumbnail'" : "";
                        echo "<div onclick=\"file_click(event, ".$contextmenu.", $onclickvars);\">
                            <img src='".Page::get_url('files').'/'.$file_class->path.'/'.$file_class->filename_thumb."' alt='preview' border='0' $thumbclass />
                        </div>";
                    } else {
                        echo "<div onclick=\"file_click(event, ".$contextmenu.", $onclickvars);\">
                            <img src='".$file_class->icon2."' alt='icon' border='0' class='files_detail_thumbnail' />
                        </div>";
                    }
                } else {
                    //Show File Icon for Detail View
                    echo "<div onclick=\"file_click(event, ".$contextmenu.", $onclickvars);\">
                        <img src='".$file_class->icon."' alt='icon' border='0' />
                    </div>";                        
                }
            }
        echo "</td>";



        #The File Name (link) Column
        echo "<td>";
            if($file_class->is_dir) {
                echo "<div class='a' onclick=\"folder_click(event, false, $onclickvars);\">".$file_class->filename."</div>";
            } else {
                #<!-- <a target='_blank' href='<? #echo Page::get_url('files').'/'.$file_class->path.'/'.$file_class->filename ? >'> -->
                echo "<div class='a' onclick=\"file_click(event, false, $onclickvars);\">".$file_class->filename."</div>";
            }
        echo "</td>";
        
        
        //The Remaining Size, Type, Created Columns
        if(!$files->hide_columns) {
            echo "<td width='75'>";
                if(is_numeric($file_class->size)) {
                    echo number_format(round($file_class->size / (1024)), 0, ".", ",");
                } else {
                    echo $file_class->size;
                }
            echo "</td>
            <td width='40'>".$file_class->ext."</td>
            <td width='140'>".$file_class->created."</td>";
        }
        echo "</tr>";
    }
}
?>
</tbody>
</table>