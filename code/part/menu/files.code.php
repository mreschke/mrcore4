<?php

#Display Options
Files::reset_default_view();
$files->embed = true;
$files->path = $topic->topic_id;
$files->hide_header = true;
$files->hide_menu = true;
$files->hide_contextmenu = true;
$files->hide_subfolders = false;
$files->hide_nav = false;
$files->show_hidden = false;
$files->hide_columns = true;
$files->hide_selection = true;
$files->hide_background = false;
$files->view = 'detail';

load_files_code();


