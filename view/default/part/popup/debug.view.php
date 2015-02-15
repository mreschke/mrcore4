<?php eval(Page::load_code('part/popup/debug')) ?>

<div id='m_debug_div' class='m_dialog'>
    <table width='100%'>
        <tr>
            <td>
                <div class='m_dialog_title'>Debug Information</div>
                <div class='m_dialog_close'>
                    <a href="javascript:toggle_dialog('m_debug_div')">
                        <img src='<?php echo Page::get_url('close.png') ?>' title='Close' alt='Close' border='0' ?>
                    </a>
                </div>                
            </td>
        </tr>
        <tr>
            <td align='left' valign='top'>
                <div class='m_dialog_content'>
                    <?php for ($i=0; $i < count($view->debug['header']); $i++): ?>
                        <div class='m_debug_header'><?php echo $view->debug['header'][$i] ?></div>
                        <div class='m_debug_location'><?php echo $view->debug['location'][$i] ?></div>
                        <div class='m_debug_data'><?php echo $view->debug['data'][$i] ?>
                        </div>
                    <?php endfor ?>
                </div>
            </td>
        </tr>
    </table>
</div>