<div id='muinfo'>
	<table border='0' width='100%'>
		<tbody align='left' valign='top'>
		<tr>
			<td width='1'>
				<img src='<?=Page::get_url($info->tbl_user->avatar, true)?>' class='avatar_full' />
			</td><td>
				<div id='muinfotext'>
					<div>
						<? if ($info->tbl_user->user_topic_id > 0): ?>
							<a href='<?=Page::get_url('topic/'.$info->tbl_user->user_topic_id)?>'>
								<b><?=$info->tbl_user->first_name?> <?=$info->tbl_user->last_name?></b>
							</a>
						<? else: ?>
							<b><?=$info->tbl_user->first_name?> <?=$info->tbl_user->last_name?></b>
						<? endif ?>
					</div>
					<div><?=$info->tbl_user->email?></div>
					<div id='muinfotexta'>
						<table>
							<tr>
								<td><a href='<?=Page::get_url('profile/'.$info->tbl_user->alias)?>'>Account</a></td>
								<td><a href='<?=Page::get_url('login/signout')?>'>Sign Out</a></td>
							</tr><tr>
						    	<td><a href='<?=Page::get_url('search').'/unread=1' ?>'>Unread (<?=Tbl_topic::get_unread_count($info)?>)</a></td>
								<td>
									<? if($info->tbl_user->perm_create || $info->admin): ?>
    									<a href='<?=Page::get_url('edit/newtopic')?>'>New Topic</a>
									<? endif ?>
								</td>
							</tr>
						</table>

					</div>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
</div>
<div id='mutopic'>
    <div><div><div><div><div><div>
    <?= $wiki ?>
    </div></div></div></div></div></div>
</div>
