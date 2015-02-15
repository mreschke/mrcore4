<?php
eval(Page::load_class('login'));
eval(Page::load_class('topic'));
eval(Page::load_class('files'));
GLOBAL $view;



#if (!isset($files->instance)) load_code(1);
#load_files_code();
function load_files_code() {
    
    GLOBAL $info, $view, $files, $topic;
    $files->instance += 1;
    
    //Determine to use hid vars (determine if postback)
    $is_postback = false;
    if ($_POST['__EVENTTARGET'] == 'btn_upload') {
        $event_upload = true;
        $is_postback = true;
    }
    
    if ($is_postback) {
        if ($_POST['hid_ajax_url'.$files->instance] != '') {
            //Convert get string into $_GET vars for use below
            $get = $_POST['hid_ajax_url'.$files->instance];
            if (substr($get, 0, 1) == '?') $get = substr($get, 1);
            $_GET = array();
            $sets = explode("&", $get);
            foreach ($sets as $set) {
                $keyval = explode("=", $set);
                $key = $keyval[0];
                $val = $keyval[1];
                $_GET[$key] = $val;
                #echo $key .'='.$val.'<br />';
            }
        }
    }

    //Convert $_GET variables into $files-> options
    if ($_GET['instance'] > 0) $files->instance = $_GET['instance'];
    if ($_GET['embed'] == 1) $files->embed = true;
    if (!isset($_GET['reset'])) {
        if ($_GET['filter'] != '') $files->filter = $_GET['filter'];
        if ($_GET['hide_header'] == 1) $files->hide_header = true;
        if ($_GET['hide_menu'] == 1) $files->hide_menu = true;
        if ($_GET['hide_contextmenu'] == 1) $files->hide_contextmenu = true;
        if ($_GET['hide_nav'] == 1) $files->hide_nav = true;
        if ($_GET['hide_subfolders'] == 1) $files->hide_subfolders = true;
        if ($_GET['show_hidden'] == 1) $files->show_hidden = true;
        if ($_GET['hide_columns'] == 1) $files->hide_columns = true;
        if ($_GET['hide_selection'] == 1) $files->hide_selection = true;
        if ($_GET['hide_background'] == 1) $files->hide_background = true;
        if (isset($_GET['view'])) {
            $files->view = $_GET['view'];
        } elseif (!isset($files->view)) {
            $files->view = 'detail'; //default view
        }
    } else {
        Files::reset_default_view();
    }

    //Load Display CSS and JS
    $view->css[] = Page::get_url('files.css');
    $view->js[] = Page::get_url('files.js');

    if (!$files->embed) $view->css[] = Page::get_url('files_full.css');

    //This can be called standalone from URL
    //Or included from script (variables are NOT set through URL)
    if (!isset($files->path) && !isset($files->topic_id)) {
        //Vars not set, so using standalone via $_GET
        //Get standalone URL variables (/files/38/test.txt OR /files/user:pass/38/test.txt)
        $files->path = Page::get_variables(true);
        $files->topic_id = Page::get_variable(0);
        
        
        //Remove any real $_GET vars in URL
        if (stristr($files->path, "?")) {
            $files->path = explode("\?", $files->path);$files->path = $files->path[0];
            $files->topic_id = explode("\?", $files->topic_id);$files->topic_id = $files->topic_id[0];
        }
        
        //User Override (using /file/user+pass/38/test.txt)
        if (stristr($files->topic_id, "+")) {
            $userpass = $files->topic_id;
            $files->path = substr($files->path, stripos($files->path, "/") + 1);
            if (stristr($files->path, "/")) {
                $files->topic_id = substr($files->path, 0, stripos($files->path, "/"));
            } else {
                $files->topic_id = $files->path;
            }
            
            //Alter credentials
            $user = explode("+", $userpass); $user = $user[0];
            $pass = explode("+", $userpass); $pass = $pass[1];
            //$info = new Info;
            $info = Login::validate($user, $pass);
            if (!$info->is_authenticated) {
                exit('Credential Override Failed');
            }
        }
        
        //Symbolic link check
        if (!is_numeric($files->topic_id)) {
            //Usually this is a number, example mreschke.com/files/184/somefile.txt
            //But sometimes I want to make a symbolic link in linux to say 282, so that mreschke.com/files/lfs/somefile.txt
            //actually points to 282.  So if the topic_id is NOT numeric then its probably a softlink I made
            //I need to determine if it is a softlink if so get the actual path it pionts to (which would be the real topic_id integer)
            
            if (is_link($files->fullfile = Config::FILES_DIR.'/'.$files->topic_id)) {
                $files->topic_id = readlink($files->fullfile = Config::FILES_DIR.'/'.$files->topic_id);
            }
        }
    
        //Get Topic Information for standalone
        $topic->tbl_post = Tbl_post::get_topic($info, $files->topic_id);
    
        //Get Users Permissions for this topic for standalone
        $topic->perms = Tbl_perm::get_permissions($info, $files->topic_id);
        
       
    }
    
    
    //Set Path Info
    $files->path = urldecode($files->path);
    if ($is_postback) $files->path = $_POST['hid_path'.$files->instance];
    $files->fullfile = Config::FILES_DIR.'/'.$files->path;
    
    //Now all variables are set, by URL if in standalone mode, or by preset $files-> variables if in include mode
    if (isset($topic->tbl_post->post_id) && (count($topic->perms) > 0 || $topic->tbl_post->created_by==$info->user_id || isset($_GET['uuid']) || count($info->uuids) > 0)) {
    
        //Get Permissions
        if ($info->admin || $topic->tbl_post->created_by==$info->user_id || Topic::has_perm($topic, "WRITE")) {
            $files->perm_write = true;
        }
        if ($info->admin || $topic->tbl_post->created_by==$info->user_id || Topic::has_perm($topic, "READ")) {
            $files->perm_read = true;
        } elseif (isset($_GET['uuid'])) {
            if ($_GET['uuid'] == $topic->tbl_post->post_uuid && $topic->tbl_post->uuid_enabled) {
                //Allow READ based on private uuid URL
                $files->perm_read = true;
                //Store this GUID in a session for use with the filemanager
                if (!in_array($_GET['uuid'], $info->uuids)) $info->uuids[] = $_GET['uuid'];
            }
        } elseif (in_array($topic->tbl_post->post_uuid, $info->uuids)) {
            //Allow READ based on private uuid stored in session
            $files->perm_read = true;
        }

        
        //Deny .sys/ folder files if not creator or admin
        if (preg_match("/\/\.sys\//i", $files->fullfile)) {
            $files->perm_read = false;
            $files->perm_write = false;
            if ($topic->tbl_post->created_by == $info->user_id || $info->admin) {
                $files->perm_read = true;
                $files->perm_write = true;
            }
        }

        //Create New Folder
        if (isset($_GET['createfolder']) && $files->perm_write) {
            //the file manager is being loaded through ajax with a folder path as usual, but we have
            //a &createfolder=newfoldername $_GET variable, so create that folder, then display the same folder you were in
            Files::create_folder($files);
        }

        //Create New File
        if (isset($_GET['createfile']) && $files->perm_write) {
            Files::create_file($files);
        }
        
        //Upload
        if ($event_upload && $_FILES['uploadedfile']['name'] != '' && $files->perm_write) {
            if ($files->instance == $_POST['hid_upload_instance']) { //wow tricky business here, or else would upload to 1st instance path
                Files::upload_file($files);
            }
        }
        
        //Process All Images
        if (isset($_GET['process_images']) && $files->perm_write) {
            //Process all images in this directory (NOT recursive) that are not already processed
            //Process means create the small preview and smaller thumb file for each image
            if (isset($_GET['process_images_recursive'])) {
                Files::process_images($files->fullfile, true);
            } else {
                Files::process_images($files->fullfile);
            }
        }
                
        //Delete File
        if (isset($_GET['delete']) && $files->perm_write) {
            Files::delete_file($files);
        }

        //Rename File
        if (isset($_GET['renameold']) && $files->perm_write) {
            Files::rename_file($files);
        }

        
        
        
        //Load $files->files array with current directory files
        if ($files->perm_read) {
            //$files->path is a DIRECTORY, so view its contents
            if (is_dir($files->fullfile)) {
                if ($handle = opendir($files->fullfile)) {
                    $files->files = array();
                    while (false !== ($thisfile = readdir($handle))) {
                        $add = true;
                        
                        if ($thisfile == "." || $thisfile == "..") $add = false;
                        if (substr($thisfile, 0, 1) == '.' && !$files->show_hidden) $add = false;
                        //Hide .sys folder if no permissions
                        if ($thisfile == '.sys' && $files->show_hidden) {
                            $add = false;
                            if ($info->admin || $topic->tbl_post->created_by == $info->user_id) $add = true;
                        }
                        
                        if ($add) {
                            $file_class = new Files;
                            $file_class->path = $files->path;
                            $file_class->filename = $thisfile;
                            $file_class->fullfile = Config::FILES_DIR.'/'.$files->path.'/'.$thisfile;
                            
                            //Is Directory
                            if (is_dir($file_class->fullfile)) {
                                $file_class->is_dir = true;
                                $file_class->ext = "Folder";
                                $file_class->size = '-';
                                $file_class->icon = Page::get_url('filetypes/folder.png');
                                $file_class->icon2 = Page::get_url('filetypes02/Folder.png');
                                $files->found_dir = true;

                                
                            //Is File
                            } else {
                                $add_file = true;
                                if (isset($files->filter)) {
                                    #if (!eregi($files->filter, $file_class->fullfile)) $add_file = false;
                                    if (preg_match($files->filter, $file_class->fullfile)) $add_file = false;
                                }
                                
                                if ($add_file) {
                                    $file_class->is_file = true;
                                    $file_class->size = filesize($file_class->fullfile);
                                    $finfo = pathinfo($file_class->fullfile);
                                    $real_ext = $finfo["extension"];
                                    $file_class->ext = strtolower($real_ext);
                                    $file_class->created = date("Y-m-d H:i:s", filemtime($file_class->fullfile));
                                    if (file_exists(Page::get_abs_base().'/web/theme/'.Config::THEME.'/images/filetypes/'.strtolower($file_class->ext).'.png')) {
                                        $file_class->icon = Page::get_url('filetypes/'.strtolower($file_class->ext).'.png');
                                    } else {
                                        $file_class->icon = Page::get_url('filetypes/file.png');
                                    }
                                    if (file_exists(Page::get_abs_base().'/web/theme/'.Config::THEME.'/images/filetypes02/'.strtoupper($file_class->ext).'.png')) {
                                        $file_class->icon2 = Page::get_url('filetypes02/'.strtoupper($file_class->ext).'.png');
                                    } else {
                                        $file_class->icon2 = Page::get_url('filetypes02/Default.png');
                                    }                                    
                                    //Get Type
                                    $image = array('jpg','png','gif','tif');
                                    if (in_array($file_class->ext, $image)) {
                                        //Image
                                        if ($file_class->size > Config::MAX_FILE_PREVIEW_SIZE*1024) {
                                            $preview = '.'.substr($file_class->filename, 0, strlen($file_class->filename) - strlen($real_ext) - 1)."_preview.".$real_ext;
                                            $preview_small = '.'.substr($file_class->filename, 0, strlen($file_class->filename) - strlen($real_ext) - 1)."_preview_small.".$real_ext;
                                            $thumb = '.'.substr($file_class->filename, 0, strlen($file_class->filename) - strlen($real_ext) - 1)."_thumb.".$real_ext;
                                            if (file_exists(Config::FILES_DIR.'/'.$files->path.'/'.$preview)) $file_class->filename_preview = $preview;
                                            if (file_exists(Config::FILES_DIR.'/'.$files->path.'/'.$preview)) $file_class->filename_preview_small = $preview_small;
                                            if (file_exists(Config::FILES_DIR.'/'.$files->path.'/'.$thumb)) $file_class->filename_thumb = $thumb;
                                        } else {
                                            $file_class->filename_preview = $file_class->filename_preview_small = $file_class->filename_thumb = $file_class->filename;
                                        }
                                        #if ($file_class->filename_preview == '') $file_class->filename_preview = $file_class->filename;
                                        #if ($file_class->filename_thumb == '') $file_class->filename_thumb = $file_class->filename;
                                        list($file_class->image_width, $file_class->image_height, $file_class->image_type, $file_class->image_attr) = getimagesize($file_class->fullfile);
                                        $file_class->type = 'image';
                                    }
                                }
                            }
                            $files->files[] = $file_class; //Add this single file class to files array
                        }
                        #if (!is_numeric(ereg_replace("/", "", $files->path))) {
                        if (!is_numeric(preg_replace('"/"', '', $files->path))) {
                            $files->found_dir = true;
                        }
                        
                    }
                    if (count($files->files) > 0) {
                        //Sort
                        array_multisort($files->files);
                    } else {
                        $files->files = null;
                    }
                }
                closedir($handle);
                
                //Build the Navigation Links (nav will not have / at end)
                $files->nav = array();
                $tmp = explode("/", $files->path);
                for ($i=0; $i < count($tmp); $i++) {
                    for ($j=0; $j <= $i; $j++) {
                        $files->nav[$tmp[$i]] .= $tmp[$j].'/';
                    }
                    //Remove last / to correct any double // issues on display
                    if (substr($files->nav[$tmp[$i]], -1) == '/') $files->nav[$tmp[$i]] = substr($files->nav[$tmp[$i]], 0, -1);
                }
                
            //$files->path is a FILE, so downloads is contents
            } elseif (file_exists($files->fullfile)) {
                
                
                $finfo = pathinfo($files->fullfile);
                //Note, if file is test.tar.gz pathinfos extension is just gz
                $files->ext = strtolower($finfo["extension"]);
                $files->filename = $finfo["filename"];
                if ($files->ext != '') $files->filename .= '.'.$files->ext;
                $files->size = filesize($files->fullfile);


                if ((strtolower($files->ext) == 'wiki' && !isset($_GET['text'])) || isset($_GET['wiki'])) {
                    //Wiki parse this file and view in browser

                    echo "<!DOCTYPE html><html lang='en'><head><title>".$files->filename."</title>";
                    echo "<link rel='stylesheet' type='text/css' href='".Page::get_url('datatables.css')."' />";
                    echo "<link rel='stylesheet' type='text/css' href='".Page::get_url('master.css')."' />";
                    echo "<link rel='stylesheet' type='text/css' href='".Page::get_url('files.css')."' />";
                    echo "<link rel='stylesheet' type='text/css' href='".Page::get_url('wiki.css')."' />";
                    echo "<link rel='stylesheet' type='text/css' href='".Page::get_url('master_print.css')."' media='print' />";
                    echo "<script language='javascript' src='".Page::get_url('jquery.min.js')."' type='text/javascript'></script>";
                    echo "<script language='javascript' src='".Page::get_url('jquery.dataTables.min.js')."' type='text/javascript'></script>";
                    echo "<script language='javascript' src='".Page::get_url('jquery.dataTables.fixedHeader.min.js')."' type='text/javascript'></script>";
                    echo "<script language='javascript' src='".Page::get_url('files.js')."' type='text/javascript'></script>";
                    echo "<script language='javascript' src='".Page::get_url('codepress/codepress.js')."' type='text/javascript'></script>";
                    echo "<script language='javascript' src='".Page::get_url('topic.js')."' type='text/javascript'></script>";
                    echo "<script language='javascript' src='".Page::get_url('master.js')."' type='text/javascript'></script>";
                    echo "<style>body { background: #ffffff url(''); margin: 5px 20px 5px 5px }</style>";

                    echo "</head><body>";
                    #echo "<div style='margin-left: 15px; margin-right: 15px'>";
                    Parser::parse_wiki($info, $topic, file_get_contents($files->fullfile));
                    #echo "</div>";
                    echo "</body></html>";
                    exit();


                } else {

                
                    //See a list of mime types at http://www.vijayjoshi.org/blog/resources/content_types.txt
                    $content_types = array (
                        '3gpp'    => 'video/3gpp',
                        '3gp'     => 'video/3gpp',
                        'asf'     => 'video/x-ms-asf',
                        'asx'     => 'video/x-ms-asf',
                        //'avi'     => 'video/x-msvideo',
                        'bz2'     => 'application/x-bzip',
                        'class'   => 'application/octet-stream',
                        'css'     => 'text/css',
                        'dtd'     => 'text/xml',
                        'xml'     => 'text/xml',
                        'dvi'     => 'application/x-dvi',
                        'fli'     => 'video/fli',
                        'flc'     => 'video/fli',
                        'gif'     => 'image/gif',
                        'gz'      => 'application/x-gzip',
                        'htm'     => 'text/html',
                        'html'    => 'text/html',
                        'jpeg'    => 'image/jpeg',
                        'jpg'     => 'image/jpeg',
                        'js'      => 'text/javascript',
                        'm3u'     => 'audio/x-mpegurl',
                        'mov'     => 'video/quicktime',
                        'mp3'     => 'audio/mpeg',
                        //'mp4'     => 'video/mp4',
                        //'mpeg'    => 'video/mpeg',
                        //'mpg'     => 'video/mpeg',
                        //'mpv2'    => 'video/x-mpeg2',
                        //'mp2ve'   => 'video/x-mpeg2',
                        'ogg'     => 'audio/x-wav',
                        'pac'     => 'application/x-ns-proxy-autoconfig',
                        'pdf'     => 'application/pdf',
                        'png'     => 'image/png',
                        'ps'      => 'application/postscript',
                        'qt'      => 'video/quicktime',
                        'rv'      => 'video/vnd.rn-realvideo',
                        'sig'     => 'application/pgp-signature',
                        'spl'     => 'application/futuresplash',
                        'swf'     => 'application/x-shockwave-flash',
                        'tar'     => 'application/x-tar',
                        'tar.bz2' => 'application/x-bzip-compressed-tar',
                        'tar.gz'  => 'application/x-tgz',
                        'tgz'     => 'application/x-tgz',
                        'tbz'     => 'application/x-bzip-compressed-tar',
                        'torrent' => 'application/x-bittorrent',
                        'wav'     => 'audio/x-wav',
                        'wax'     => 'audio/x-ms-wax',
                        'wm'      => 'video/x-ms-wm',
                        'wma'     => 'audio/x-ms-wma',
                        'wmp'     => 'video/x-ms-wmp',
                        #'wmv'     => 'video/x-ms-wmv',
                        'wvx'     => 'video/x-ms-wvx',
                        'xbm'     => 'image/x-xbitmap',
                        'xpm'     => 'image/x-xpixmap',
                        'xwd'     => 'image/x-xwindowdump',
                        'zip'     => 'application/zip',
                        'txt'     => 'text/plain',
                        'text'    => 'text/plain',
                        'vb'      => 'text/plain',
                        'vbs'     => 'text/plain',
                        'aspx'    => 'text/plain',
                        'asc'     => 'text/plain',
                        'ascx'    => 'text/plain',
                        'c'       => 'text/plain',
                        'conf'    => 'text/plain',
                        'config'  => 'text/plain',
                        'php'     => 'text/plain',
                        'sh'      => 'text/plain',
                        'py'      => 'text/plain',
    					'pl'      => 'text/plain',
                        'wiki'    => 'text/plain',
                    );
                    
                    //Because pathinfo extension is just gz in test.tar.gz
                    //I need to check the largest possible extension in the $content_types array first
                    $ext_found = false;
                    $content_type = null;
                    $period_count = count_chars($files->filename);
                    $period_count = $period_count[46];
                    if ($period_count >= 2) {
                        $tmp = explode("\.", $files->filename);
                        $ext = $tmp[count($tmp)-2].'.'.$tmp[count($tmp)-1];
                        
                    }
                    foreach ($content_types as $type_ext => $type) {
                        if ($type_ext == $ext) {
                            $content_type = $type;
                            $ext_found = true;
                            break;
                        }
                    }
                    if (!$ext_found) {
                        foreach ($content_types as $type_ext => $type) {
                            if ($type_ext == $files->ext) {
                                $content_type = $type;
                                $ext_found = true;
                                break;
                            }
                        }
                    }

                    //If url is ?text=1 or just ?text then force as text/plain
                    if (isset($_GET['text'])) {
                        $ext_found = true;
                        $content_type = 'text/plain';  
                    } 
                    
                    //Instead of Opening file in browser, force download prompt
                    if (isset($_GET['forcedownload'])) $force_download = true;
                    
                    if ($ext_found && !$force_download) {
                        // Inline Stream
                        $headers = apache_request_headers();
                        header("Content-type: $content_type");
                        header('Content-Disposition: inline; filename="'.$files->filename.'"');

                        // Checking if the client is validating his cache and if it is current.
                        if (isset($headers['If-Modified-Since']) && (strtoupper($headers['If-Modified-Since']) == strtoupper(gmdate('D, d M Y H:i:s', filemtime($files->fullfile)).' GMT'))) {
                            // Client's cache IS current, so we just respond '304 Not Modified'.
                            header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($files->fullfile)).' GMT', true, 304);
                        } else {
                            // Image not cached or cache outdated, we respond '200 OK' and output the image.
                            header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($files->fullfile)).' GMT', true, 200);
                        }
                        header("Cache-control: public"); //required for If-Modified-Since header to exist from browser

                    } else {
                        //Force Download
                        header("Content-type: application/octet-stream");
                        header('Content-Disposition: attachment; filename="'.$files->filename.'"');
                        header("Cache-control: private");
                    }
                    header("Content-length: $files->size");

    				#AH! Finally, took forever on google to find this.
    				#If you use this php downloader to download a file, you cannot browse my site
    				#While the file is being downloaded, and you cannot obviously download another file at the same time
    				#The whole site is frozen for that user/browser until the file is done.  If you open a different browser then the
    				#site works.  This is because the session file is locked to prevent concurrent writes.  So opening a different browser
    				#obviously gets a new session so the sites works again.  To solve this problem, all you have to do is call session_write_close()
    				#which tricks the session into thinking the page is done, so it unlocks the session file allowing for further site browsing!!!
    				#See http://stackoverflow.com/questions/1610168/downloading-files-with-php-only-downloading-one-at-a-time

    				#Trick PHP into thinking this page is done, so it unlocks the session file to allow for further site navigation and downloading
    				session_write_close();

    				#Read the file into the stream (download the file)
    				readfile($files->fullfile);
                    exit();
                }
                
            } else {
                //Path not found, return 404
                header("HTTP/1.0 404 Not Found");
                ?>
                <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
                <html><head>
                <title>404 Not Found</title>
                <link rel='shortcut icon' href='/favicon.ico' />
                </head><body>
                <h1>Not Found</h1>
                <p>The requested URL <? echo $_SERVER['REQUEST_URI'] ?> was not found on this server.</p>
                </body></html>
                <?
                #echo 'Directory or File Not Found: '.$files->fullfile;
				exit();
            }
        } else {
            //No read access
            Page::redirect(Page::get_url('redirect').'/denied/'.$topic->topic_id);
            #echo 'Directory or File Not Found (2): '.$files->fullfile;
			exit();
        }
    
        //Load Uploader CSS & Javascript
        //if ($files->perm_write) {
            //User has write permissions to this topic, allow file uploads
            //swfuploader from http://webdeveloperplus.com/jquery/multiple-file-upload-with-progress-bar-using-jquery/
            #$view->js[] = Page::get_url('uploader/jquery-1.3.2.js');
            #$view->js[] = Page::get_url('uploader/swfupload.js');
            #$view->js[] = Page::get_url('uploader/jquery.swfupload.js');
        //}
    
    } else {
        //No data, probably invalid URL or topic not found, or permission denied
        Page::redirect(Page::get_url('redirect').'/denied/'.$topic->topic_id);
        #echo 'Directory or File Not Found (3): '.$files->fullfile;
        exit();
    }
    
    
    //Create $_GET URL of $files-> options (get URL used for ajax)
    ##############################################################
    #$files->ajax_url = "?instance=$files->instance&embed=$files->embed&filter=$files->filter&hide_header=$files->hide_header&hide_menu=$files->hide_menu&hide_contextmenu=$files->hide_contextmenu&hide_subfolders=$files->hide_subfolders&hide_nav=$files->hide_nav&&show_hidden=$files->show_hidden&hide_columns=$files->hide_columns&hide_selection=$files->hide_selection&hide_background=$files->hide_background&view=$files->view";
    //Only add to the string if parameter is set, this keeps the string miniumal for URL linking
    $files->ajax_url = "";
    if (strtolower($files->view) != 'detail') $files->ajax_url .= "&view=$files->view";
    #if ($files->view) $files->ajax_url .= "&view=$files->view";
    if ($files->embed && $files->instance) $files->ajax_url .= "&instance=$files->instance";
    if ($files->embed) $files->ajax_url .= "&embed=1";
    if ($files->filter) $files->ajax_url .= "&filter=$files->filter";
    if ($files->hide_header) $files->ajax_url .= "&hide_header=$files->hide_header";
    if ($files->hide_menu) $files->ajax_url .= "&hide_menu=$files->hide_menu";
    if ($files->hide_contextmenu) $files->ajax_url .= "&hide_contextmenu=$files->hide_contextmenu";
    if ($files->hide_subfolders) $files->ajax_url .= "&hide_subfolders=$files->hide_subfolders";
    if ($files->hide_nav) $files->ajax_url .= "&hide_nav=$files->hide_nav";
    if ($files->show_hidden) $files->ajax_url .= "&show_hidden=$files->show_hidden";
    if ($files->hide_columns) $files->ajax_url .= "&hide_columns=$files->hide_columns";
    if ($files->hide_selection) $files->ajax_url .= "&hide_selection=$files->hide_selection";
    
    //Trim embed and instance for good looking URLs - no need for these if used as a link
    $files->ajax_url2 = $files->ajax_url;
    $files->ajax_url2 = preg_replace("'&instance=[0-9][0-9]'", '', $files->ajax_url2);
    $files->ajax_url2 = preg_replace("'&instance=[0-9]'", '', $files->ajax_url2);
    $files->ajax_url2 = preg_replace("'&embed=1'", '', $files->ajax_url2);
    $files->ajax_url2 = preg_replace("'&loadedonce=1'", '', $files->ajax_url2);

    $files->ajax_url = preg_replace('"^&"', '?', $files->ajax_url);
    $files->ajax_url2 = preg_replace('"^&"', '?', $files->ajax_url2);
    
    if ($is_postback) $files->ajax_url = $_POST['hid_ajax_url'.$files->instance];
    if (!$files->ajax_url) $files->ajax_url .= "?"; //required dont move, allows first get variable to still have & in front for the view menus...    

    
    //Add Debug Information
    if (Config::DEBUG) {
        $vars = "
            \$files->topic_id: ".$files->topic_id."<br />
            \$files->path: ".$files->path."<br />
        ";
        if (isset($pass)) {
            $vars .= "Credential Override: $user<br />";
        }
        $vars .= "User: ".$info->tbl_user->alias." ($info->user_id)<br />";
        $vars .= "Admin: $info->admin<br />";
        #$vars .= "Users Perms:<br />";
        #foreach ($topic->perms as $perm->tbl_perm) {
        #    $vars.= "&nbsp;&nbsp;$perm->tbl_perm<br />";
        #}

        $view->add_debug("Variables", "files.code.php", $vars);
    }

}

function objSort(&$objArray,$indexFunction,$sort_flags=0) {
    $indices = array();
    foreach($objArray as $obj) {
        $indeces[] = $indexFunction($obj);
    }
    return array_multisort($indeces,$objArray,$sort_flags);
}

function getIndex($obj) {
    return $obj->getPosition();
}


// Mimic apache_request_headers if servers doesn't support it
// See http://php.net/manual/en/function.apache-request-headers.php
// I had to add $arh_key = strtolower($arh_key); to fix it
if( !function_exists('apache_request_headers') ) {
    function apache_request_headers() {
      $arh = array();
      $rx_http = '/\AHTTP_/';
      foreach($_SERVER as $key => $val) {
        if( preg_match($rx_http, $key) ) {
          $arh_key = preg_replace($rx_http, '', $key);
          $arh_key = strtolower($arh_key);
          $rx_matches = array();
          // do some nasty string manipulations to restore the original letter case
          // this should work in most cases
          $rx_matches = explode('_', $arh_key);
          if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
            foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
            $arh_key = implode('-', $rx_matches);
          }
          $arh[$arh_key] = $val;
        }
      }
      return( $arh );
    }
}
