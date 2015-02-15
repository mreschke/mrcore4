<?php
$view->css[] = Page::get_url('net.css');
$view->js[] = Page::get_url('net.js');
/*$view->header = "
    <table width='100%'><tr>
    <td>".$view->title."</td>
    <td align='right'>
        <a href='".Page::get_url('net').'/http://google.com'."' class='m_bar_link'>google</a>
        <a href='".Page::get_url('net').'/http://mreschke.com/mrcore/mrticles/Home'."' class='m_bar_link'>mrcore3</a>
    </td>
    </tr></table>
";*/
/*$view->title = '';*/

$loc = Page::get_variables(true);
if (trim($loc) == '') {
    $loc = 'http://google.com';
}

//Set left menu items
#$view->menu_left_parts[] = 'menu/sites';

#Hide left and right menus
#$view->menu_left_hidden = true;
#$view->menu_right_hidden = true;

