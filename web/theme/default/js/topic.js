/*
 Document OnLoad jQuery
 mReschke 2013-09-11
*/
$(document).ready(function() {
    //If body content is over 1200px then things start to look funky
    //So remove max-width and tweak a few display items
    adjust_large_width();

});

function adjust_large_width() {
    if ($('#tbt').width() > 1200) {
        $('#mh').css('border-right',  '1px solid #aaa');
        $('#mbc').css('margin',  '0 0 0 0');
        $('#tbh').css('box-shadow',  '0 0 0 0 #fff');
        $('#tbh').css('border-left',  '0');
        $('#tb').css('max-width',  'none');
        $('#tbt').css('border-left',  '0');
        $('#tcom').css('max-width',  'none');
        $('#tfoot').css('border-right',  '1px solid #aaa');
    }
}


/*
 function toggle_wiki_header(id)
 Collapse/Expand this Text_Wiki header content div
 mReschke 2011-04-12
*/
function toggle_wiki_header(id) {
    div = document.getElementById(id+"__content");
    link = document.getElementById(id+"__link");
    if (div.style.display == "none") {
        div.style.display = "";
        link.innerHTML = "[-]";
    } else {
        div.style.display = "none";
        link.innerHTML = "[+]";
    }
    adjust_large_width();
}

/*
 function toggle_wiki_headers(collapse)
 Collapse/Expand ALL Text_Wiki Headers and alter their +/- link status
 mReschke 2011-04-13
*/
function toggle_wiki_headers(collapse) {
    //Toggle Header Content Div Display
    var divs = document.getElementsByTagName("div");
    for (i=0; i < divs.length; i++) {
        id = divs[i].id;
        if (id.substring(id.length-9, id.length) == '__content') {
            if (collapse) {
                document.getElementById(id).style.display = 'none';
            } else {
                document.getElementById(id).style.display = 'block';
            }
        }
    }
    
    //Toggle Header [+]/[-] link display
    var hrefs = document.getElementsByTagName("span");
    for (i=0; i < hrefs.length; i++) {
        id = hrefs[i].id;
        if (id.substring(id.length-6, id.length) == '__link') {
            if (collapse) {
                document.getElementById(id).innerHTML = '[+]';
            } else {
                document.getElementById(id).innerHTML = '[-]';
            }
            
        }
    }
    adjust_large_width();    
}

/*
 resize_object(obj)
 used for <embed url>
 mReschke 2013-05-03
*/
function resize_embed_url(obj) {
    var height = document.documentElement.clientHeight;
    height -= document.getElementById(obj).offsetTop;
    height -= 165;
    if (height < 600) height = 600;
    document.getElementById(obj).style.height = height +"px";
}
