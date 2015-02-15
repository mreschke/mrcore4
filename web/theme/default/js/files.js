
/*
 mReschke 2011-06-04
*/
function folder_click_ajax(path, ins) {
    http_object = get_http_object();
    if (http_object != null) {
        http_object.open("GET", path);
        http_object.send(null);
        //http_object.onreadystatechange = ajax_output('file_outer_div', 'innerHTML');
        http_object.onreadystatechange = function() {
            if (http_object.readyState == 4) {
                document.getElementById('files_outer_div' + ins).innerHTML = http_object.responseText
                //alert('Path: ' + path + ', ins: ' + ins);
            }            
        }
    }
}

/*
 mReschke 2011-06-04
*/
function toggle_menu(menu, ins, mouseover) {
    file = document.getElementById('files_menu_file' + ins);
    edit = document.getElementById('files_menu_edit' + ins);
    view = document.getElementById('files_menu_view' + ins);
    clicked = null;
    
    anyopen = false;
    if (file.style.display == 'block') anyopen = true;
    if (edit.style.display == 'block') anyopen = true;
    if (view.style.display == 'block') anyopen = true;
    
    document.getElementById('files_menu_file_txt' + ins).style.background = '';
    document.getElementById('files_menu_edit_txt' + ins).style.background = '';
    document.getElementById('files_menu_view_txt' + ins).style.background = '';
    
    selected_bgcolor = '#E5ECF9';
    
    if (!mouseover || (mouseover && anyopen)) {
        if (menu == 'file') {
            clicked = file;
            edit.style.display = 'none';
            view.style.display = 'none';
            document.getElementById('files_menu_file_txt' + ins).style.background = selected_bgcolor;
            /*document.getElementById('files_menu_file_txt').style.borderLeft = '1px solid #8DC342';*/
        } else if (menu == 'edit') {
            clicked = edit;
            file.style.display = 'none';
            view.style.display = 'none';
            document.getElementById('files_menu_edit_txt' + ins).style.background = selected_bgcolor;
            document.getElementById('files_menu_edit_txt' + ins).style.display = 'hidden';

        } else if (menu == 'view') {
            clicked = view;
            file.style.display = 'none';
            edit.style.display = 'none';
            document.getElementById('files_menu_view_txt' + ins).style.background = selected_bgcolor;
        } else if (menu == 'hideall') {
            file.style.display = 'none';
            edit.style.display = 'none';
            view.style.display = 'none';
        }
        
        
        //Toggle Clicked
        if (clicked) {
            if (clicked.style.display == 'block' && !mouseover) {
                clicked.style.display = 'none';
            } else {
                clicked.style.display = 'block';
                just_opened_file_menu = true;
            }
        }
        if (mouseover && anyopen) {
            just_opened_file_menu = false;
        }
    }
    
    return true;
}

/*
 mReschke 2011-06-10
*/
function open_upload_dialog(ins) {
    javascript:toggle_dialog('files_upload_div')
    document.getElementById('hid_upload_instance').value = ins;
}

/*
 mReschke 2011-06-04
*/
//function file_click(event, filesphp_url, filename, path, folder_click_ajax_url, ins) {
function file_click(event, popup_menu, ins, phpfile_url, ajax_url, ajax_get_vars, path, filename, ext, is_dir, is_archive, ajax_get_vars2) {
    full = path + '/' + filename;
    //alert(is_archive);
    if (popup_menu) {
        //When file clicked, popup the file context menu
        menu_div = document.getElementById('files_context_menu');
        opentab_item = document.getElementById('files_context_opentab');
        openwindow_item = document.getElementById('files_context_openwindow');
        openurl_item = document.getElementById('files_context_openurl');
        download_item = document.getElementById('files_context_download');
        //listarchive_item = document.getElementById('files_context_listarchive');
        rename_div = document.getElementById('files_rename');
        rename_item = document.getElementById('files_context_rename');
        btn_rename = document.getElementById('btn_rename');
        delete_item = document.getElementById('files_context_delete');
        
        menustate = menu_div.style.display;
        close_all_file_menus(ins);

        height = 500;
        width = 700;
    
        //Alter the context menu link URL's
        //open_item.onclick = function() {window.open(filesphp_url + '/' + file,'mywindow','width='+width+',height='+height+',scrollbars=1');}
        opentab_item.onclick = function() {window.open(phpfile_url + '/' + full, '_blank');}
        openwindow_item.onclick = function() {window.open(phpfile_url + '/' + full,'filewindow','width='+width+',height='+height+',scrollbars=1');}
        download_item.onclick = function() {window.open(phpfile_url + '/' + full + '?forcedownload=1','_blank');}
        //if (is_archive) {
        //    listarchive_item.style.display = 'block';
        //    listarchive_item.onclick = function() {
        //        alert('list');
        //    }
        //} else {
        //    listarchive_item.style.display = 'none';
        //}

        //Open as URL
        openurl_item.innerHTML = "<a href='" + phpfile_url + '/' + path + '/' + filename + "'>Open as URL</a><br /><div id='files_copyurl'>" + phpfile_url + '/' + path + '/' + filename + '</div>';


        
        //Rename
        if (rename_item != null) rename_item.onclick = function() {
            filename = urldecode(filename);
            move_rename_menu(event, popup_menu, ins, phpfile_url, ajax_url, ajax_get_vars, path, filename, ext, is_dir, is_archive);
            btn_rename.onclick = function() {
                folder_click_ajax(ajax_url + '/' + path + ajax_get_vars + '&renameold=' + filename + '&renamenew=' + document.getElementById('files_rename_text').value, ins);
                rename_div.style.display = 'none';
                return false;
            }
        }

        //Delete
        if (delete_item != null) delete_item.onclick = function() {
            if (confirm_delete()) {
                folder_click_ajax(ajax_url + '/' + path + ajax_get_vars + '&delete=' + filename, ins);
            }
        }
       
        //Display the context menu in the right location (under mouse)
        if (menustate == 'none' || !menustate) {
            move_context_menu(event, 'files_context_menu');    
        } else {
            menu_div.style.display = 'none'
        }
    } else {
        //When file clicked, open file direct (no context menu)
        window.open(phpfile_url + '/' + full, '_blank');
    }
}

/*
 mReschke 2011-06-22
*/
function folder_click(event, popup_menu, ins, phpfile_url, ajax_url, ajax_get_vars, path, filename, ext, is_dir, is_archive, ajax_get_vars2) {
    //popup_menu = boolean, if true then show context menu, else perform default open action
    //ins = this filemanagers instance integer
    //phpfile_url = URL path to files.php file (ie: http://mreschke.com/file) has no / at end
    //ajax_url = URL path to files.ajax.php file (ie: http://mreschke.com/ajax/files.ajax) has no / at end
    //ajax_get_vars = GET vars of current instances settings (ie: ?instance=1&view=icon...)
    //ajax_get_vars2 is like ajax_get_vars but embed=1 and instance=x has been removed because its for linking as URL only
    //path = relative path from topicID, (ie: 251 or 251/foldername) has no / at end
    //filename = the filename of file just clicked (will be filename or foldername), no path, just filename
    //ext = the file extension of the file just clicked (will be empty if folder)
    if (popup_menu) {
        //When folder clicked, popup the folder context menu
        menu_div = document.getElementById('folder_context_menu');
        open_item = document.getElementById('folder_context_open');
        openurl_item = document.getElementById('folder_context_openurl');
        delete_item = document.getElementById('folder_context_delete');
        rename_div = document.getElementById('files_rename');
        rename_item = document.getElementById('folder_context_rename');
        btn_rename = document.getElementById('btn_rename');

        menustate = menu_div.style.display;
        close_all_file_menus(ins);
        
        //Open
        open_item.onclick = function() {
            //alert(ajax_url + '/' + path + '/' + filename + ajax_get_vars, ins);
            folder_click_ajax(ajax_url + '/' + path + '/' + filename + ajax_get_vars, ins);
        }

        //Open as URL (folder one includes all the ajax_get_vars to its the exact current look)
        openurl_item.innerHTML = "<a href='" + phpfile_url + '/' + path + '/' + filename + ajax_get_vars2 + "'>Open as URL</a><br /><div id='files_copyurl'>" + phpfile_url + '/' + path + '/' + filename + ajax_get_vars2 + '</div>';

        //Rename
        if (rename_item != null) rename_item.onclick = function() {
            filename = urldecode(filename);
            move_rename_menu(event, popup_menu, ins, phpfile_url, ajax_url, ajax_get_vars, path, filename, ext, is_dir, is_archive);
            btn_rename.onclick = function() {
                folder_click_ajax(ajax_url + '/' + path + ajax_get_vars + '&renameold=' + filename + '&renamenew=' + document.getElementById('files_rename_text').value, ins);
                rename_div.style.display = 'none';
                return false;
            }
        }

        //Delete
        if (delete_item != null) delete_item.onclick = function() {
            if (confirm_delete()) {
                folder_click_ajax(ajax_url + '/' + path + ajax_get_vars + '&delete=' + filename, ins);
            }
        }

        //Display the context menu in the right location (under mouse)
        if (menustate == 'none' || !menustate) {
            move_context_menu(event, 'folder_context_menu');    
        } else {
            menu_div.style.display = 'none';
        }    
    } else {
        //When folder clicked, open folder direct (no context menu)
        folder_click_ajax(ajax_url + '/' + path + '/' + filename + ajax_get_vars, ins);
    }

    
}

/*
 mReschke 2011-06-04
*/
function move_context_menu(event, id) {
    //Open file or folder context menu under cursor
    menu_div = document.getElementById(id);
    menu_div.style.left = get_mouse_x_position(event) + 'px';
    menu_div.style.top = get_mouse_y_position(event) + 'px';
    menu_div.style.display = 'block';
    just_opened_file_menu = true;
}

/*
 mReschke 2011-06-04
*/
function move_rename_menu(event, popup_menu, ins, phpfile_url, ajax_url, ajax_get_vars, path, filename, ext, is_dir, is_archive) {
    close_all_file_menus(ins);
    
    //Open and move Rename window under cursor
    rename_div = document.getElementById('files_rename');
    rename_txt = document.getElementById('files_rename_text');
    rename_div.style.left = get_mouse_x_position(event) + 'px';
    rename_div.style.top = get_mouse_y_position(event) + 'px';
    rename_div.style.display = 'block';
    just_opened_file_menu = true;
    
    rename_txt.focus();
    rename_txt.value = filename;
    rename_txt.select();
    
    //Set Title
    if (is_dir) {
        document.getElementById('files_rename_title').innerHTML = 'Rename Folder:';
    } else {
        document.getElementById('files_rename_title').innerHTML = 'Rename File:';
    }
}

/*
 mReschke 2011-06-04
*/
function move_newfolder_menu(event, ins) {
    close_all_file_menus(ins);
    
    //Open and move Rename window under cursor
    newfolder_div = document.getElementById('files_newfolder' + ins);
    newfolder_txt = document.getElementById('files_newfolder_text' + ins);
    newfolder_div.style.left = get_mouse_x_position(event) + 'px';
    newfolder_div.style.top = get_mouse_y_position(event) + 'px';
    newfolder_div.style.display = 'block';
    just_opened_file_menu = true;
    
    newfolder_txt.focus();
}

/*
 mReschke 2011-06-04
*/
function create_folder(ajax_url, ajax_get_vars, ins, path) {
    //ajax_url is like http://mreschke.com/ajax/files.ajax
    //ajax_get_vars is the current view, like ?embed=1&filter=&hide_header=1 ...
    //path is like 54/existing/folder
    newfolder_div = document.getElementById('files_newfolder' + ins);
    newfolder_txt = document.getElementById('files_newfolder_text' + ins);
    f = newfolder_txt.value;
    
    //Validate New Folder Name
    validated = false
    if (f == 'test-fixmeadsfasdfasdf') {
        alert('Error');
    } else {
        validated = true;
    }
    if (validated) {
        newpath = path; //like 54/existing/folder/newfolderhere
        //alert(ajax_url + '/' + newpath + ajax_get_vars);
        folder_click_ajax(ajax_url + '/' + newpath + ajax_get_vars + '&createfolder=' + newfolder_txt.value, ins);
        newfolder_div.style.display = 'none';
    }
    
    return false; //ignore button click
}

/*
 mReschke 2012-10-26
*/
function move_newfile_menu(event, ins) {
    close_all_file_menus(ins);
    
    //Open and move Rename window under cursor
    newfile_div = document.getElementById('files_newfile' + ins);
    newfile_txt = document.getElementById('files_newfile_text' + ins);
    newfile_div.style.left = get_mouse_x_position(event) + 'px';
    newfile_div.style.top = get_mouse_y_position(event) + 'px';
    newfile_div.style.display = 'block';
    just_opened_file_menu = true;
    
    newfile_txt.focus();
}

/*
 mReschke 2012-10-26
*/
function create_file(ajax_url, ajax_get_vars, ins, path) {
    //ajax_url is like http://mreschke.com/ajax/files.ajax
    //ajax_get_vars is the current view, like ?embed=1&filter=&hide_header=1 ...
    //path is like 54/existing/folder
    newfile_div = document.getElementById('files_newfile' + ins);
    newfile_txt = document.getElementById('files_newfile_text' + ins);
    f = newfile_txt.value;
    
    //Validate New File Name
    validated = false
    if (f == 'test-fixmeadsfasdfasdf') {
        alert('Error');
    } else {
        validated = true;
    }
    if (validated) {
        newpath = path; //like 54/existing/folder/newfile.txt
        //alert(ajax_url + '/' + newpath + ajax_get_vars);
        folder_click_ajax(ajax_url + '/' + newpath + ajax_get_vars + '&createfile=' + newfile_txt.value, ins);
        newfile_div.style.display = 'none';
    }
    
    return false; //ignore button click
}


/*
 mReschke 2011-06-26
*/
function close_all_file_menus(ins) {
    //Close all open menus
    if (document.getElementById('files_context_menu') != null) { //else no files HTML found on page
        document.getElementById('files_context_menu').style.display = 'none';
        document.getElementById('folder_context_menu').style.display = 'none';
        for (i = 1; i <=10; i++) {
            //I don't know how many instances there are, so try 10
            if (document.getElementById('files_menu_file' + i) != null) document.getElementById('files_menu_file' + i).style.display = 'none';
            if (document.getElementById('files_menu_edit' + i) != null) document.getElementById('files_menu_edit' + i).style.display = 'none';
            if (document.getElementById('files_menu_view' + i) != null) document.getElementById('files_menu_view' + i).style.display = 'none';
            
            if (document.getElementById('files_menu_file_txt' + i) != null) document.getElementById('files_menu_file_txt' + i).style.background = '';
            if (document.getElementById('files_menu_edit_txt' + i) != null) document.getElementById('files_menu_edit_txt' + i).style.background = '';
            if (document.getElementById('files_menu_view_txt' + i) != null) document.getElementById('files_menu_view_txt' + i).style.background = '';
        }
    }
}

/*
 mReschke 2011-06-04
*/
function on_body_click() {
    //Hide any context menus
    if (just_opened_file_menu) {
        //I just opened a menu, so don't close them all
        just_opened_file_menu = false;
    } else {
        close_all_file_menus();
    }
}
document.onclick = on_body_click;
var just_opened_file_menu = false;
