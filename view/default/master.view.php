<? eval(Page::load_code('master')) ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$view->title ?></title>
    <?# <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, user-scalable=yes" /> ?>

    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <meta http-equiv='Content-Style-Type' content='text/css' />
    <meta name='generator' content='mrcore 4.0' />
    <link rel='shortcut icon' href='<?=Config::WEB_BASE_URL?>/favicon.png' />
    <?# <meta name='keywords' content='mReschke mrcore4 linux bsd wiki blog it tech technology' /> ?>
    <?# <meta name='description' content='IT and Development Wiki' /> ?>
    <?# <link rel='apple-touch-icon' href='http://en.wikipedia.org/apple-touch-icon.png' /> ?>
    <?# <link rel='copyright' href='http://creativecommons.org/licenses/by-sa/3.0/' /> ?>
    <?# <link rel='alternate' type='application/atom+xml' title='Wikipedia Atom feed' href='/w/index.php?title=Special:RecentChanges&amp;feed=atom' /> ?>

<?
if(isset($view->css)) {
    foreach($view->css as $css) {
        echo "    <link rel='stylesheet' type='text/css' href='$css' />\r\n";
    }
}
if(isset($view->css_print)) {
    foreach($view->css_print as $css) {
        echo "    <link rel='stylesheet' type='text/css' media='print' href='$css' />\r\n";
    }
}
if(isset($view->js)) {
    foreach($view->js as $js) {
        echo "    <script language='javascript' src='$js' type='text/javascript'></script>\r\n";
    }
}
?>
</head>
<?# <body onload="document.getElementById('<? #echo $view->focus ? >').focus();return true;"> ?>
<body>
<?# onsubmit='return false;' ?>
<form name='masterform' enctype="multipart/form-data" method='post' <?=$view->form_attributes?>>
<input type='hidden' name='__EVENTTARGET' id='__EVENTTARGET' />
<input type='hidden' name='__EVENTARGUMENTS' id='__EVENTARGUMENTS' />
<a name='top'></a>

<div id='m_dialog_fade' class='m_dialog_fade' style='display:none;'></div>
<? if(isset($view->error)): ?><? eval(Page::load_part('popup/error')) ?><? endif ?>
<? if(isset($view->debug)): ?><? eval(Page::load_part('popup/debug')) ?><? endif ?>
<? if(isset($view->debug_error) && Config::DISPLAY_ERRORS): ?><? eval(Page::load_part('popup/debug_error')) ?><? endif ?>

<div id='mo'>
    <div id='mh'>
        <table id='mht'>
            <tbody align='left' valign='middle'>
            <tr>
                <? if (Config::LOGO_URL): ?>
                    <td width='1'>
                        <div id='ml'>
                            <div id='mlimg'>
                                <a href='<?=Config::WEB_BASE_URL?>'>
                                    <? if (preg_match('|//|', Config::LOGO_URL)): ?>
                                        <img src='<?=Config::LOGO_URL ?>' alt='logo' border='0' /> 
                                    <? else: ?>
                                        <img src='<?=Page::get_url(Config::LOGO_URL) ?>' alt='logo' border='0' />
                                    <? endif ?>
                                </a>
                            </div>
                        </div>
                    </td>
                <? endif ?>
                <td>
                    <div id='ms'>
                        <table id='mst'>
                            <tr>
                                <td>
                                    <table id='mst2'>
                                        <tr>
                                            <td width='1'>
                                                <div id='msnarrow'></div>
                                            </td><td>
                                                <?# <td valign='top'><input type='text' placeholder="Search..." autofocus onfocus="document.getElementById('m_search_text').select();document.forms['masterform'].onsubmit=function() {return false};" onblur="document.forms['masterform'].onsubmit=function() {return true};" onkeyup="change_search_submit_text();keyup_actions(this, event, 'm_search_submit')" name='txt_search' id='m_search_text' value='<?=$view->xsearch_query ? >' /></td> ?>
                                                <input type='search' autocomplete='off' id='msx' name='txt_search' placeholder="" onfocus="document.getElementById('msx').select();document.forms['masterform'].onsubmit=function() {return false};" onblur="document.forms['masterform'].onsubmit=function() {return true};" onkeyup="change_search_submit_text();keyup_actions(this, event, 'msbtn')" value='<?=$view->search_query ?>'/>
                                            </td><td width='1'>
                                                <div id='msmore'>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td><td width='1'>
                                    <?# <td valign='top'><input type='text' placeholder="Search..."  onkeyup="change_search_submit_text();keyup_actions(this, event, 'm_search_submit')" name='txt_search' id='m_search_text' value='<?=$view->search_query ? >' /></td> ?>
                                    <?# <td valign='top'><input type='submit' name='btn_search' value='Search' id='m_search_submit' /></td> ?>
                                    <?# <td valign='top'><a href='#' onclick="do_search('m_search_text');" id='m_search_submit'>Search</a></td> ?>
                                    <input type='button' id='msbtn' onclick="do_search('msx', '<?=Page::get_url('net') ?>', '<?=Page::get_url('search') ?>');" name='btn_search' value='GO' />
                                </td>
                            </tr>
                        </table>
                        <div id='msc'>
                            <div id='msb'>
                                <div class='ajax_loader'>Loading Search Box...</div>
                            </div>
                        </div>
                    </div>
                
                    <? #eval(Page::load_part('recent')) ?>

                </td><td align='right' width='1'>
                    <div id='mu'>
                        <? if ($info->is_authenticated): ?>
                            <div id='muavatar' title='<?=$info->tbl_user->email?>'>
                                <table><tr>
                                    <td><div id='muimg'>
                                        <img src='<?=Page::get_url($info->tbl_user->avatar, true)?>' alt='User' height='40px' width='40px' /></a>
                                    </div></td>
                                    <td><div id='mudownimg'></div></td>
                                </tr></table>
                            </div>
                        <? else: ?>
                            <input id='muloginbtn' type='button' value='SIGN IN' onclick="window.location='<?=Page::get_url('login')?>'" />
                        <? endif ?>
                        <div id='muc'>
                            <div id='mub'>
                                <div class='ajax_loader'>Loading User Info...</div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div id='mlinks'>
        <?
        if ($info->tbl_user->perm_admin && isset($view->debug)) {
            echo "<a href=\"javascript:toggle_dialog('m_debug_div')\">Debug</a>";
        }
        ?>
    </div>
    
    <div id='mb'>
        <div id='mbc'>
            <? eval(Page::load_view()) ?>
        </div>
    </div>

</div>
<a name='bottom'></a>
</form>
</body>
</html>
