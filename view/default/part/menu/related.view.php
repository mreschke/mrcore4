<?php eval(Page::load_code('part/menu/related')) ?>

<div class='m_<?php echo $left_right ?>_item'>
    <div class='m_<?php echo $left_right ?>_header'>Related Topics</div>
    <table border='0' cellpadding='1' cellspacing='1' width='100%'>
        <tbody align='left' valign='middle'>
            <?php if(isset($topic->tbl_topics_related)): ?>
                <?php foreach($topic->tbl_topics_related as $topic->tbl_topic): ?>
                    <tr>
                        <td>
                            <a href='<?php echo Page::get_url('topic').'/'.$topic->tbl_topic->topic_id.'/'.urlencode($topic->tbl_topic->title) ?>'>
                                <div class='label_small'><?php echo \Helper\Other::trimlen($topic->tbl_topic->title, 35) ?></a>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>
</div>
