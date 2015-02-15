<?php
eval(Page::load_class('login'));

/*$view->title = "";*/
$view->css[0] = Page::get_url('login.css');
#$view->onload = "onload=\"set_focus('login_username_text', true)\"";
$view->use_form = true;


$referrer = $_POST['txt_referrer'];
if ($referrer == '') $referrer = $_SERVER['HTTP_REFERER'];


if (isset($_POST['__EVENTTARGET']) && $_POST['__EVENTTARGET'] == 'btn_login') {
    $info_tmp = Login::validate($_POST['txtUsername'], $_POST['txtPassword']);
    if ($info_tmp->is_authenticated) {
        //Redirect
        if (preg_match('"'.Config::WEB_BASE_URL.'"', $referrer)) {
            //Redirect to last used page on this site
            if (preg_match('"redirect/denied"', $referrer)) {
                //Referrer was from a access denied page, parse out the original denied URL then redirect to that after login
                $referrer = substr($referrer, strpos($referrer, "?ref=")+5);
            } elseif (preg_match('"/login"', $referrer)) {
                //Referrer was from itself (login page), so goto home page
                $referrer = Page::get_url('topic').'/'.Config::DEFAULT_TOPIC;
            }
            Page::redirect($referrer);
        } else {
            Page::redirect(Page::get_url('topic').'/'.Config::DEFAULT_TOPIC);
        }
        
        exit();
        #echo "<meta http-equiv=\"refresh\" content=\"3;".$_SERVER['REQUEST_URI']."\">";
    } else {
        $view->message = "Invalid username or password";
    }
    
} else {
    //Loading login page, clear session
    unset($_SESSION['core']);
    
    if (Page::get_variable(0) == 'signout') {
        Page::redirect(Page::get_url('login').'/signedout'); //refresh after killing session
    }
    if (Page::get_variable(0) == 'signedout') {
        $view->message = "<span style='color:green;'>Thanks for signing out</span>";
    }
}

//Set left menu items
/*$view->menu_left_hidden = true;*/
#$view->menu_left_parts[] = 'menu/sites';



/*    
require_once '../model/mysql/adodb.php';

$db = ADODB::connect();
$rs = $db->Execute("SELECT * FROM tbl_user");
#printf($rs);
while (!$rs->EOF) {
         print $rs->fields[0].' '.$rs->fields['first_name'].'<BR>';
         $rs->MoveNext();
}

#$echo $row['email'];

#$rs =& $rs->Execute($sql);
#$array = adodb_getall($rs);
#var_dump($array);
 */