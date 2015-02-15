<?php eval(Page::load_code('part/popup/error')) ?>

<div id='m_error_div'></div>
<div id='m_error_placement'>
    <div id='m_error_box'>
        <table width='100%'>
            <tr>
                <td align='right' valign='top'>
                    <? if (!$view->error_close_hide): ?>
                    <a href='#' onclick="document.getElementById('m_error_placement').style.display='none';document.getElementById('m_error_div').style.display='none';<?php echo $view->error_close ?>">
                        <img src='<?php echo Page::get_url('close.png') ?>' title='Close' alt='Close' border='0' ?>
                    </a>
                    <? endif ?>
            </tr>
            <tr>
                <td align='center' valign='middle'>
                    <table border='0'>
                        <tr>
                            <td align='center' valign='top'><img src='<?php echo Page::get_url('error.png') ?>' id='m_error_image' alt='error image' border='0' /></td>
                        </tr><tr>
                            <td align='center' valign='top' height='100'><div id='m_error_label'><?php echo $view->error ?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>