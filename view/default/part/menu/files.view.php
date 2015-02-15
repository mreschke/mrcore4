<?php eval(Page::load_code('part/menu/files')) ?>
<? if(isset($files->files)): ?>
    <div class='m_<? echo $left_right ?>_line'><?php load_files_view() ?></div>
<? endif ?>