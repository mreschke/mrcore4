<?php
$view->css[] = Page::get_url('master.css');
$view->js[] = Page::get_url('master.js');
eval(Page::load_code('part/files'));
load_files_code();
