<div id='rc'>
    <div id='rb'>
        <table width='100%'>
            <tr>
                <td width='5px'>
                    <img src='<?php echo Page::get_url(@$redirect_image) ?>' id='redirect_image' alt='redirect image' border='0' />
                </td>
                <td width='10'>&nbsp;</td>
                <td valign='middle'>
                    <div id='redirect_message'><?php echo @$redirect_message ?></div>
                    <div id='redirect_submessage'><?php echo @$redirect_submessage ?></div>
                    <div id='redirect_help'><?php echo @$redirect_help ?></div>
                </td>
            </tr>
        </table>
    </div>
</div>
