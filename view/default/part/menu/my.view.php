<?php eval(Page::load_code('part/menu/my')) ?>

<div class='m_<?php echo $left_right ?>_item'>
    <!-- <div class='m_<?php #echo $left_right ?>_header'>Tools</div> -->
    <?php if($info->tbl_user->perm_create || $info->admin): ?>
        <div class='m_<?php echo $left_right ?>_line'>
        	<a href='<?php echo Page::get_url('edit').'/newtopic' ?>'>
        		<img src='<? echo Page::get_url('post2.png') ?>' border='0' />
        		Create Topic
        	</a>
       	</div>
    <?php endif ?>
    <!-- <div class='m_<?php #echo $left_right ?>_line'><a href='<?php #echo Page::get_url('edit').'/newtopic' ?>'>Manage Subscriptions</a></div> -->
    <!-- <div class='m_<?php #echo $left_right ?>_line'><a href='<?php #echo Page::get_url('edit').'/newtopic' ?>'>Subscribed (x)</a></div> -->
    <div class='m_<?php echo $left_right ?>_line'>
    	<a href='<?php echo Page::get_url('search').'/unread=1' ?>'>
			<img src='<? echo Page::get_url('unread2.png') ?>' border='0' />
			Unread (<?php echo $topic->unread_count ?>)
    	</a>
    </div>
    <!-- <div class='m_<?php #echo $left_right ?>_line'><a href='<?php #echo Page::get_url('edit').'/newtopic' ?>'>My Topics</a></div> -->

	<hr />
   	
    <div class='m_<?php echo $left_right ?>_line'>
    	<? if ($info->tbl_user->perm_admin): ?>
    	<a href='<? echo Page::get_url('admin') ?>/users'>
    	<? else: ?>
    	<a href='<? echo Page::get_url('profile').'/'.$info->tbl_user->alias ?>'>
    	<? endif ?>
    		<img src='<? echo Page::get_url('myaccount.png') ?>' border='0' />
    		My Account
    	</a>
    </div>


	<? if ($info->tbl_user->perm_admin && isset($view->debug)): ?>
   		<div class='m_<?php echo $left_right ?>_line'>
            <a href="javascript:toggle_dialog('m_debug_div')">
				<img src='<? echo Page::get_url('debug.png') ?>' border='0' />
            	Debug
           	</a>
     	</div>
    <? endif ?>

</div>

