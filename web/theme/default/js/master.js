/* Global Scope */
var open_menus = new Array();


/*
 Document OnLoad jQuery
 mReschke 2013-09-08
*/
$(document).ready(function() {
    just_opened_a_menu = false;
    muavatar_opened_once = false;
    msc_opened_once = false;
    tmbfolder_opened_once = false;


    /* Body Click */
    $(document).click(function( event ) {
        if (just_opened_a_menu) {
            //I just opened a menu, so don't close them all
            just_opened_a_menu = false;
        } else {
            close_open_menus();
        }
        //If this function exists (meaning I have loaded the filemanager.js file), then on page click also
        //call the filemanagers fm_on_body_click which will close any file context menus
        //if (typeof fm_on_body_click == 'function') {
        //    fm_on_body_click();
        //}
        //alert('click');
    });


    /* Master User Avatar Click */
    $('#muavatar').click(function () {
        if (toggle_div('muc')) {
            $('#muc').fadeIn('fast');
            open_menus.push('muc');
            just_opened_a_menu = true;
            if (muavatar_opened_once == false) {
                //Get User Info via AJAX
                $.post('/ajax/userinfo.ajax.php',
                {
                    key:'c44dbb976265f6d75756475bb7cbdee5'
                }
                , function (data, status) {
                    //alert("Data: " + data + "\nStatus: " + status);
                    $('#mub').html(data);
                });
                muavatar_opened_once = true; //flag to not load via ajax again
            }
        }
    });
    $('#muc').click(function () {
        just_opened_a_menu = true;
    });


    /* Master Search Click */
    $('#msmore').click(function () {
        search_toggle();
    });
    $('#msx').click(function () {
        search_toggle();
    });
    function search_toggle() {
        if (toggle_div('msc')) {
            $('#msc').fadeIn('fast');
            open_menus.push('msc');
            just_opened_a_menu = true;
            if (msc_opened_once == false) {
                //Get User Info via AJAX
                $.post('/ajax/search.ajax.php',
                {
                   key:'c44dbb976265f6d75756475bb7cbdee5'
                }
                , function (data, status) {
                    $('#msb').html(data);
                });
                msc_opened_once = true; //flag to not load via ajax again
            }
        }        
    }
    $('#msc').click(function () {
        just_opened_a_menu = true;
    });


    /* Topic Menu Folder Button Clicked */    
    $('#tmbfolderc').click(function () {
        just_opened_a_menu = true;
    })
    $('#files_context_menu').click(function () {
        just_opened_a_menu = true;
    });
    $('#folder_context_menu').click(function () {
        just_opened_a_menu = true;
    });
});


/*
 Close all open menus
 mReschke 2013-09-26
*/
function close_open_menus() {
    for (open_menu in open_menus) {
        $('#' + open_menus[open_menu]).fadeOut('fast');
    }
    open_menus = new Array();
}

/*
 Generic toggle_div function
 Returns true if menu is now OPEN
 mReschke 2013-09-26
*/
function toggle_div(div) {
    if ($('#'+div).is(":visible")) {
        $('#'+div).fadeOut('fast');
    } else {
        close_open_menus();
        $('#'+div).fadeIn('fast');
        open_menus.push(div);
        just_opened_a_menu = true;
        return true
    }
}


/* Topic Menu Folder Button Clicked */
function tmbfolder_click(topic_id) {
    if (toggle_div('tmbfolderc')) {
        $('#tmbfolderc').fadeIn('fast');
        open_menus.push('tmbfolderc');
        just_opened_a_menu = true;
        if (tmbfolder_opened_once == false) {
            //Get User Info via AJAX
            if ($('#files_context_menu'))
            $.post('/ajax/files.ajax.php/' + topic_id + '?instance=99&embed=1',
            {
               key:'c44dbb976265f6d75756475bb7cbdee5'
            }
            , function (data, status) {
                $('#tmbfolderb').html(data);
            });
            tmbfolder_opened_once = true; //flag to not load via ajax again
        }
    }        
}





/*
 function get_http_object()
 AJAX XMLHTTP function used in all sites ajax scripts
 OBSOLETE, use jquery $.get or $.post
 mReschke 2010-09-17
*/
var http_object = null;
function get_http_object() {
    //OBSOLETE AJAX, use jQuery $.get or $.post instead
    if (window.ActiveXObject) {
        return new ActiveXObject("Microsoft.XMLHTTP");
    } else if (window.XMLHttpRequest) {
        return new XMLHttpRequest();
    } else {
        alert("Your browser does not support AJAX.");
        return null;
    }
}

/*
 function on_page_refresh()
 On every page load (page refresh) run this function
 mReschke 2012-10-26
*/
function body_onload() {
    //On every page load, start the session timer
}


/*
 Get cookie value
 http://www.perlscriptsjavascripts.com/js/cookies.html
*/
function getCookie(w){
    cName = "";
    pCOOKIES = new Array();
    pCOOKIES = document.cookie.split('; ');
    for(bb = 0; bb < pCOOKIES.length; bb++){
        NmeVal  = new Array();
        NmeVal  = pCOOKIES[bb].split('=');
        if(NmeVal[0] == w){
            cName = unescape(NmeVal[1]);
        }
    }
    return cName;
}

/*
 Get all cookies and their values
 http://www.perlscriptsjavascripts.com/js/cookies.html
*/
function printCookies(w){
    cStr = "";
    pCOOKIES = new Array();
    pCOOKIES = document.cookie.split('; ');
    for(bb = 0; bb < pCOOKIES.length; bb++){
        NmeVal  = new Array();
        NmeVal  = pCOOKIES[bb].split('=');
        if(NmeVal[0]){
            cStr += NmeVal[0] + '=' + unescape(NmeVal[1]) + '; ';
        }
    }
    return cStr;
}

/*
 Save cookie
 http://www.perlscriptsjavascripts.com/js/cookies.html
*/
function setCookie(name, value, expires, path, domain, secure){
    document.cookie = name + "=" + escape(value) + "; ";
    
    if(expires){
        expires = setExpiration(expires);
        document.cookie += "expires=" + expires + "; ";
    }
    if(path){
        document.cookie += "path=" + path + "; ";
    }
    if(domain){
        document.cookie += "domain=" + domain + "; ";
    }
    if(secure){
        document.cookie += "secure; ";
    }
}


/*
 function button_click(this item) null
 I do not use <input type='submit'> anywhere in mrcore4, I use <input type='button'>
 which does not cause a postback. I do this for misc reasons, mainly becuase when I hit
 return in a text box, I want to control what happens, defaultly the 'submit' button will be
 clicked, which I don't want.  This way, I can have multiple buttons on a page, and they
 get clicked differently depending on what textbox I hit enter on.  Example, when in the login
 page, if Im in the master search textbox and hit enter, it searches instead of posting btn_login
 If Im in the password textbox and I hit enter, it logs in, instead of searching...
 When you click a button, call this button_click(this) which sets __EVENT* hidden fields
 on the master page containing the buttons name (not ID) and value, so 'btn_save' and 'Save'
 Then on the .code.php side, instead of doing $_POST['btn_save'] I do $_POST['__EVENTTARGET'] == 'btn_save'
 Ya Dig?
 mReschke 2010-09-03
*/
function button_click(item) {
    //item is this, so onclick="button_click(this)"
    document.getElementById('__EVENTTARGET').value = item.name;
    document.getElementById('__EVENTARGUMENTS').value = item.value;
    var theForm;
    if (window.navigator.appName.toLowerCase().indexOf("netscape") > -1) {
        theForm = document.forms["masterform"];
    } else {
        theForm = document.masterform    
    }
    theForm.submit();
    //document.masterform.submit();
}
    


function do_search(txt, net_url, search_url) {
    //net_url and search_url are from php Page::get_url('net'), which do NOT have / at end
    txt = document.getElementById(txt);
    //alert(txt.value);
    if (txt.value.substring(0,1) == '>') {
        if (txt.value.substring(1,2) == 'g' && txt.value.substring(2,3) == ' ') {
            //query = '>g xxx' which is search google
            query = txt.value.substring(3);
            window.location = net_url + '/http://www.duckduckgo.com/?q=' + query;
        } else if (txt.value.substring(1,2) == 'g' && txt.value.substring(2,3) == 'o' && txt.value.substring(3,4) == ' ') {
            //query = '>go xxx' which is goto website
            query = txt.value.substring(4);
            if (query.substring(0,8) != 'https://' && query.substring(0,7) != 'http://') {
                query = 'http://' + query;
            }
            window.location = net_url + '/' + query;
        } else {
            btn.value = 'Advanced Mode';
            btn_width = 110;
        }
    } else {
        //window.location = search_url + '/' + txt.value.toLowerCase();
        window.location = search_url + '/' + txt.value;
    }
    return false;
}

/*
 function change_search_submit_text() null
 alters the master search button text based on the search textbox content
 mReschke 2010-08-27
*/
function change_search_submit_text() {
    txt = document.getElementById('msx');
    btn = document.getElementById('msbtn');
    //txt_width_css = 141; //must match master.css
    btn_width_css = 65; //must match master.css
    btn_width = btn_width_css;
    if (txt.value.substring(0,1) == '>') {
        if (txt.value.substring(1,2) == 'g' && txt.value.substring(2,3) != 'o') {
            btn.value = 'Search the Web';
            btn_width = 110;
        } else if (txt.value.substring(1,2) == 'g' || txt.value.substring(2,3) == 'o') {
            btn.value = 'Goto Website';
            btn_width = 90;
        } else {
            btn.value = 'Advanced Mode';
            btn_width = 110;
        }
    } else {
        btn.value = 'GO';
    }
    //txt_width = txt_width_css - (btn_width - btn_width_css);
    btn.style.width = btn_width + 'px';
    //txt.style.width = txt_width + 'px';
}

/*
 function keyup_actions(e, submit_element)
 Used for onkeyup of a text box (onkeydown failed)
 If they hit the enter key (13) then click the submit_element
 If they hit ESC then clear the textbox contents...
 NOTE, onkeydown & onkeyup recognizes ESC, TAB,... while onkeypress does not
 But onkeydown kept resetting the value back to original when I hit ESC, so onkeyup works
 mReschke 2010-09-03
*/
function keyup_actions(item, e, submit_element) {
    if(navigator.userAgent.match("Gecko")){
        c=e.which;
    }else{
        c=e.keyCode;
    }
    //13 = ENTER
    //27 = ESC
    //9 = TAB
    //16 = SHIFT
    //17 = CTRL
    //18 = alt
    //alert(c);
    if (submit_element) {
        if(c==13){
            document.getElementById(submit_element).click();
            return false;
        }
    }
    if (c==27) {
        document.getElementById(item.id).value = '';
    }
    return false;    
}

function set_focus(element, selected) {
    document.getElementById(element).focus();
    if (selected)
        document.getElementById(element).select();
}

function trim(stringToTrim) {
    if (stringToTrim) {
        return stringToTrim.replace(/^\s+|\s+$/g,"");
    } else {
        return stringToTrim;
    }
}

/*
 function confirm_delete() boolean
 Generic delete confirmation script
 mReschke 2010-09-23
*/
function confirm_delete() {
    if (confirm("Are you sure you want to delete this item?")) {
        return true;
    } else {
        return false;
    }
}

/*
 function toggle_dialog()
 Used to toggle the main master popup divs
 mReschke 2011-04-02
*/
function toggle_dialog(dialog_div_id) {
    fade = document.getElementById('m_dialog_fade');
    dialog = document.getElementById(dialog_div_id);
    if (fade.style.display == 'none') {
        fade.style.display = 'block';
        dialog.style.display = 'block';
    } else {
        fade.style.display = 'none';
        dialog.style.display = 'none';
    }
}

/*
 mReschke 2011-08-21
 function get_mouse_position()
 Gets the mouse X position
*/
function get_mouse_x_position(event) {
	//Example, to set a div menu_div position by the mouse use
    //menu_div.style.left = get_mouse_x_position(event) + 'px';
    //menu_div.style.top = get_mouse_y_position(event) + 'px';
	var posx = 0;
	//if (!e) var e = window.event;
    e = event || window.event;
	if (e.pageX) {
		posx = e.pageX;
		posy = e.pageY;
	} else if (e.clientX) 	{
		posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
	}
    return posx
}


/*
 mReschke 2011-08-21
 function get_mouse_position()
 Gets the mouse Y position
*/
function get_mouse_y_position(event) {
	//Example, to set a div menu_div position by the mouse use
    //menu_div.style.left = get_mouse_x_position(event) + 'px';
    //menu_div.style.top = get_mouse_y_position(event) + 'px';
	var posy = 0;
	//if (!e) var e = window.event;
    e = event || window.event;
	if (e.pageY) 	{
		posy = e.pageY;
	} else if (e.clientY) 	{
		posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
	}
    return posy
}

/*
 function urldecode(url)
 URL decode %xx and +
 mReschke 2012-10-26
*/
function urldecode(url) {
  return decodeURIComponent(url.replace(/\+/g, ' '));
}

/*
 function urlencode(url)
 URL encode %xx and +
 mReschke 2012-10-26
*/
function urlencode(url) {
  return encodeURIComponent(url).replace(/%20/g, '+');
}
