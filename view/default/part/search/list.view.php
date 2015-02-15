<table id='search_list' class='datatable' width='100%'>
	<thead>
		<th></th>
		<th>ID</th>
		<th>Title</th>
		<th>Creator</th>
		<th>Created</th>
		<th>Updator</th>
		<th>Updated</th>
		<th>Views</th>
		<th>Comments</th>
		<th>Permissions</th>
	</thead>
	<tbody>
		<? $i=0 ?>
		<? foreach($topic->tbl_topics as $topic->tbl_topic): ?>
			<tr>
				<td>
                    <? foreach ($topic->tbl_topic->topic_idTbl_badges as $badge->tbl_badge): ?>
                        <a href='<? echo Page::get_url('search').'/*/'.$badge->tbl_badge->badge ?>'>
                            <img src='<? echo Page::get_url($badge->tbl_badge->image, true) ?>' title='<? echo $badge->tbl_badge->badge ?>' alt='<? echo $badge->tbl_badge->badge ?>' border='0' style='height:16px' />
                        </a>
                    <? endforeach ?>
                </td>
                <td><?= $topic->tbl_topic->topic_id ?></td>
				<td>
					<a href='<? echo Page::get_url('topic').'/'.$topic->tbl_topic->topic_id.'/'.urlencode($topic->tbl_topic->title) ?>'<? if ($read): ?> title='Unread' <? endif ?>>
						<? echo $topic->tbl_topic->title ?>
					</a>
				</td>
				<td><?= $topic->tbl_topic->created_byTbl_user->alias ?></td>
				<td><?= $topic->tbl_topic->updated_on ?></td>
				<td><?= $topic->tbl_topic->updated_byTbl_user->alias ?></td>
		        <td><?= $topic->tbl_topic->updated_on ?></td>
				<td><?= number_format($topic->tbl_topic->view_count, 0, ".", ",") ?></td>
				<td><?= number_format($topic->tbl_topic->comment_count, 0, ".", ",") ?></td>
				<td>
	                <? if($info->is_authenticated): ?>
	                    <? $perm->tbl_perm_groups_short_display = Tbl_perm::get_perm_groups_short_display($topic->tbl_topic->topic_id); ?>
	                    <? if(count($perm->tbl_perm_groups_short_display) > 0): ?>        
                            <? foreach($perm->tbl_perm_groups_short_display as $perm->tbl_perm): ?>
                                <? if($perm->tbl_perm->group == 'Public'): ?>
                                    <span class='search_perm_group_public'>
                                <? else: ?>
                                    <span class='search_perm_group'>
                                <? endif ?>
                            
                                <? echo $perm->tbl_perm->group ?>:</span><span class='search_perm_short'><? echo $perm->tbl_perm->short ?></span>
                                
                            <? endforeach ?>
	                    <? else: ?>
	                        <span class='search_perm_private'>Private</span>
	                    <? endif ?>
	                <? endif ?>
				</td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>