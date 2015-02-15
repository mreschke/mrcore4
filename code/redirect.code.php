<?php

$view->css[0] = Page::get_url('redirect.css');

$reason = strtolower(Page::get_variable(0));
if ($reason == 'notopic') {
    $view->title = "Error";
    $redirect_image = 'error.png';
    $redirect_message = 'Topic Not Found';
    $redirect_submessage = 'Topic ID: '.Page::get_variable(1).' was not found<br />Click the back button on your browser to return to the previous page.';
} elseif ($reason == 'timeout') {
    $view->title = "Session Time Out";
    $redirect_image = 'clock04.png';
    $redirect_message = 'Your session has expired';
    $redirect_submessage = 'Please sign in again or continue viewing as public';
} elseif ($reason == 'denied') {
    $view->title = "Access Denied";
    $redirect_image = 'denied.png';
    $redirect_message = "Permission Denied";
    $redirect_submessage = "Access rejected for user '".$info->tbl_user->alias."' to '".$_GET['ref']."'";
    $redirect_help = "Click the back button on your browser to return to the previous page.";
    $redirect_help .= "<div id='redirect_login'>Please <a href='".Page::get_url('login')."'>Login</a> with appropriate credentials to view this document.</div>";
} elseif ($reason == 'error') {
    $view->title = "Error";
    $redirect_image = 'error.png';
    $redirect_message = 'Unknown Error on page, check the URL and try again';
    $redirect_submessage = "An unknown error occured on '".$_GET['ref']."'";
    $redirect_help = 'Click the back button on your browser to return to the previous page.';
} elseif ($reason == 'critical') {
    $view->title = "Critical Error";
    $redirect_image = 'error_critical.png';
    $redirect_message = 'Critical Error on page, check the URL and try again';
    $redirect_submessage = "A critical error occured on '".$_GET['ref']."'";
    $redirect_help = 'Click the back button on your browser to return to the previous page.';
}
