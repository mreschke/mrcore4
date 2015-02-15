<? if ($info->admin): ?>
    <? eval(Page::load_code('part/menu/admin')) ?>

    <div class='navc'>
        <div class='navb'>
            <span class='nava'>
            	<a href='<? echo Page::get_url('admin/users') ?>'>
            		<!--<img src='<? #echo Page::get_url('manage_users.png') ?>' border='0' />-->
            		Users
            	</a>
            </span>
            <span class='nava'>
            	<a href='<? echo Page::get_url('admin/groups') ?>'>
        			<!--<img src='<? #echo Page::get_url('manage_groups.png') ?>' border='0' />-->
            		Groups
            	</a>
            </span>
            <span class='nava'>
            	<a href='<? echo Page::get_url('admin/badges') ?>'>
            		<!--<img src='<? #echo Page::get_url('manage_badges.png') ?>' border='0' />-->
            		Badges
            	</a>
        	</span>
            <span class='nava'>
            	<a href='<? echo Page::get_url('admin/tags') ?>'>
            		<!--<img src='<? #echo Page::get_url('manage_tags.png') ?>' border='0' />-->
            		Tags
            	</a>
            </span>
            
            <span class='nava'>
            	<a href='<? echo Page::get_url('net').'/'.Config::WEB_BASE_URL.'/admin/indexer_log.html' ?>'>
            		<!--<img src='<? #echo Page::get_url('manage_index.png') ?>' border='0' />-->
            		Indexer Log
            	</a>
            </span>
        </div>
    </div>
<? endif ?>