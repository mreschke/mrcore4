<?php eval(Page::load_code('part/popup/debug')) ?>
<div id='m_debug_error_div' class='m_dialog'>
    <table width='100%'>
        <tr>
            <td>
                <div class='m_dialog_title'>Error Information</div>
                <div class='m_dialog_close'>
                    <a href="javascript:toggle_dialog('m_debug_error_div')">
                        <img src='<?php echo Page::get_url('close.png') ?>' title='Close' alt='Close' border='0' ?>
                    </a>
                </div>                
            </td>
        </tr>
        <tr>
            <td align='left' valign='top'>
                <div class='m_dialog_content'>
                    <? if (Config::EMAIL_ERROR != ''): ?>
                        <div class='help'>
                            NOTE: These errors have been emailed to the system developer.<br /><br />
                            If these issues are not resolved soon please contact the system administrator at '<? echo Config::EMAIL_ADMIN ?>' or the system developer at '<? echo Config::EMAIL_DEV ?>'.
                        </div>
                        <hr />
                    <? endif ?>
                    
                    <?php for ($i=0; $i < count($view->debug_error['header']); $i++): ?>
                        <div class='m_debug_error_header'><?php echo $view->debug_error['header'][$i] ?></div>
                        <div class='m_debug_error_location'><?php echo $view->debug_error['location'][$i] ?></div>
                        <div class='m_debug_error_data'><?php echo $view->debug_error['data'][$i] ?></div>
                    <?php endfor ?>
                </div>
            </td>
        </tr>
    </table>
</div>