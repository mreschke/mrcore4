<?php eval(Page::load_code('part/menu/imap')) ?>

<div class='m_<?php echo $left_right ?>_item'>
    <div class='m_<?php echo $left_right ?>_header'>Unread Emails</div>
        <div class='m_<?php echo $left_right ?>_line'>
            <? foreach ($mail_headers as $header): ?>
                <? if ($header->subject != ''): ?>
                    * <? echo $header->subject ?> from <? echo $header->fromaddress ?><br />
                <? endif ?>
            <? endforeach ?>
        </div>
</div>