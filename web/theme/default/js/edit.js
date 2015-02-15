/*
 function resize_edit_textarea() null
 resizes the exit_body_textarea element
 mReschke 2010-08-27
*/
function resize_edit_textarea() {
    var height = document.documentElement.clientHeight;
    height -= document.getElementById('edit_body_textarea').offsetTop;
    //height -= 165; //Your pages top header height offset
    
    //Offsets
    wysiwyg_height = height -155;
    iframe_height = height - 185;
    textarea_height = height - 185;
    //setTimeout("alert('hi')", 5);
    if (document.getElementById('edit_body_wysiwyg')) {
        document.getElementById('edit_body_wysiwyg').style.height = wysiwyg_height +"px";    
    }
    if (document.getElementById('edit_body_iframe')) {
        document.getElementById('edit_body_iframe').style.height = iframe_height +"px";    
    }
    if (document.getElementById('edit_body_textarea')) {
        document.getElementById('edit_body_textarea').style.height = textarea_height +"px";
    }

}


/*
 function set_carrot_range(formname,sel_start,sel_end) null
 fornmae should be a textarea formname
 this sets the carrot position or selection to start,end
 mReschke 2010-08-29
*/
function set_carrot_range(formname, sel_start, sel_end) {
    txt = document.getElementById(formname);
    if (txt.setSelectionRange) { 
        txt.focus(); 
        txt.setSelectionRange(sel_start, sel_end);
    } else if (txt.createTextRange) { 
        var range = txt.createTextRange(); 
        range.collapse(true); 
        range.moveEnd('character', sel_end); 
        range.moveStart('character', sel_start); 
        range.select(); 
    } 
}


// ###################### Replace tabs in textarea ###################### //
// From: http://www.webdeveloper.com/forum/showthread.php?t=32317
// modified by mReschke 2010-09-03 to maintain scroll bar height, previously
// when you hit tab, it would maintain the carrot position, but would scroll to the top
// so when you hit tab, I record the .scrollTop then restore it after replace_selection()
// Call with, <textarea onkeydown='return catch_tab(this,event)'>
// Also added code where shift+enter will click the 'Save and View' button!
var previous_code;
function catch_tab(item, e) {
    if(navigator.userAgent.match("Gecko")){
        c=e.which;
    }else{
        c=e.keyCode;
    }
    
    //Pick up shift+enter (or shift, then enter right after will trigger it too)
    if (c == 16) {
        previous_code = c;
    } else if (c != 13) {
        previous_code = 0;
    }
    if (c == 13 && previous_code == 16) {
        //Shift+enter pressed, save and view topic
        document.getElementById('edit_save_view_submit').click();
        return false;
    }
    
    if(c==9){
        scroll_height = document.getElementById('edit_body_textarea').scrollTop;
        replace_selection(item,String.fromCharCode(9));
        document.getElementById('edit_body_textarea').focus();
        document.getElementById('edit_body_textarea').scrollTop = scroll_height;
        return false;
    }
    return true;
}
function replace_selection (input, replaceString) {
    if (input.setSelectionRange) {
        var selectionStart = input.selectionStart;
        var selectionEnd = input.selectionEnd;
        input.value = input.value.substring(0, selectionStart)+ replaceString + input.value.substring(selectionEnd);
        if (selectionStart != selectionEnd){
            set_selection_range(input, selectionStart, selectionStart + replaceString.length);
        }else{
            set_selection_range(input, selectionStart + replaceString.length, selectionStart + replaceString.length);
        }
    }else if (document.selection) {
        var range = document.selection.createRange();
        if (range.parentElement() == input) {
            var isCollapsed = range.text == '';
            range.text = replaceString;
             if (!isCollapsed)  {
                range.moveStart('character', -replaceString.length);
                range.select();
            }
        }
    }
}
function set_selection_range(input, selectionStart, selectionEnd) {
    if (input.setSelectionRange) {
        input.focus();
        input.setSelectionRange(selectionStart, selectionEnd);
    } else if (input.createTextRange) {
        var range = input.createTextRange();
        range.collapse(true);
        range.moveEnd('character', selectionEnd);
        range.moveStart('character', selectionStart);
        range.select();
    }
}

function validate() {
    title = document.getElementById('edit_title_text');
    badges = document.getElementById('edit_badges_select');
    badges_tokenize = document.getElementById('edit_tokenize_badge');
    tags = document.getElementById('edit_tags_select');
    tags_tokenize = document.getElementById('edit_tokenize_tag');
    tags_text = document.getElementById('edit_tag_text');
    validated = false;
    
    if (!title.value) {
        alert('Please enter a title');
        title.focus();
    } else if (!badges.value && !badges_tokenize.value) {
        alert('Please select at least one badge');
        badges.focus();
    } else if (!tags.value && !tags_tokenize.value && !tags_text.value) {
        alert('Please select or create at least one tag');
        tags.focus();
    } else {
        validated = true;
    }
    
    return validated;
}

/*
 function change_template()
 fired when the template dropdown selection is changed
 mReschke 2013-05-01
*/
var template_id = 0;
function change_template() {
    template = document.getElementById('sel_template');
    if (confirm('CAUTION: Changing templates will overwrite your new body text below with that of the template.  You will lose any changes. Are you sure you want to continue?') == true) {
        template_id = template.value;
        document.masterform.submit();
    } else {
        //If they hit cancel we have to change the selection back to what it was
        template.value = template_id;
    }
    
}
