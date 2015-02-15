<? foreach($topic->tbl_topics as $topic->tbl_topic): ?>
	<div>
		<a href='<? echo Page::get_url('topic').'/'.$topic->tbl_topic->topic_id.'/'.urlencode($topic->tbl_topic->title) ?>'<? if ($read): ?> title='Unread' <? endif ?>>
			<?= $topic->tbl_topic->title ?>
		</a>
	</div>
<? endforeach ?>
