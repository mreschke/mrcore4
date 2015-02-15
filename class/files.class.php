<?php
eval(Page::load_class('helper/file'));
#if (!isset($files)) $files = new Files();
$files = new Files();
GLOBAL $files;



/*
 class File
 All file work functions (non model)
 mReschke 2010-09-15
*/
class Files {
    public $path;        #relative to actual files folder, no / at beginning or end (example: 37 or 37/music or 37/music/songs)
    public $filename;    #just filename, no path
    public $filename_preview; # the image preview  640px file (usually .dscn3234_preview.jpg)
    public $filename_preview_small; # the image preview_smaller 250px file (usually .dscn3234_preview.jpg)
    public $filename_thumb; #the image thumbnail file (usually .dscn3234_thumb.jpg)
    public $fullfile;    #path and filename
    public $size;
    public $ext;
    public $topic_id;
    public $files;
    public $perm_read = false;
    public $perm_write = false;
    public $is_dir = false;
    public $is_file = false;
    public $type; //like image, text, pdf, music
    public $image_width;
    public $image_height;
    public $image_type;
    public $image_attr;
    public $found_dir = false;
    public $created;
    public $icon;
    public $icon2;
    public $nav;
    public $ajax_url;
    public $ajax_url2;
    public $instance;
    public $embed = false;
    
    public $filter;
    public $hide_header = false;
    public $hide_menu = false;
    public $hide_contextmenu = false;
    public $hide_subfolders = false;
    public $hide_nav = false;    
    public $show_hidden = false;
    public $hide_columns = false;
    public $hide_selection = false;
    public $hide_background = false;
    public $view = 'detail'; //detail, icon, slideshow
    

    public function __construct() {
        #if (!isset($files)) {
        #    $files = new Files;
        #}
    }
    
    public static function reset_default_view() {
        GLOBAL $files;
        
        $files->filter = null;
        $files->hide_header = false;
        $files->hide_menu = false;
        $files->hide_contextmenu = false;
        $files->hide_subfolders = false;
        $files->hide_nav = false;
        $files->show_hidden = false;
        $files->hide_columns = false;
        $files->hide_selection = false;
        $files->hide_background = false;
        $files->view = 'detail';
    }
    
    /*
     function create_file_folder($topic_id) bool success;
     Creates the topics files folder
     mReschke 2010-09-14
    */
    public static function create_file_folder($topic_id) {
        if (is_dir(Config::FILES_DIR)) {
            mkdir(Config::FILES_DIR.'/'.$topic_id) or die('Error creating directory');
            return true;
        }
        return false;
    }
    
    /*
     function process_image($path, $filename);
     Process (create preview and thumbnail) a single image
     mReschke 2011-06-26
    */
    public static function process_image($path, $filename) {
        //Path is HD absolute path (ie: /nwq/admin/www/data/mrcore_files/251) no / at end
        //Filename is just the filename to process
        if (file_exists($path.'/'.$filename)) {
            eval(Page::load_class('helper/image'));
            $finfo = pathinfo($path.'/'.$filename);
            $ext = $finfo["extension"];
            $filename_thumb = '.'.substr($filename, 0, -(strlen($ext)+1)).'_thumb.'.$ext;
            $filename_preview = '.'.substr($filename, 0, -(strlen($ext)+1)).'_preview.'.$ext;
            $filename_preview_small = '.'.substr($filename, 0, -(strlen($ext)+1)).'_preview_small.'.$ext;
            
            //Create Preview Image File
            \Helper\Image::resize_image($path.'/'.$filename, 640, 0, true, $path.'/'.$filename_preview,false);
            
            //Create Preview_small Image File
            \Helper\Image::resize_image($path.'/'.$filename, 200, 0, true, $path.'/'.$filename_preview_small,false);

            //Create Thumb Image File
            \Helper\Image::resize_image($path.'/'.$filename, 50, 0, true, $path.'/'.$filename_thumb,false);
        }
    }
    
    /*
     function process_images($path, $recursive = false)
     Process all images in this folder (optional subfolders) that are not already processed
     mReschke 2011-06-26
    */
    public static function process_images($path, $recursive = false) {
        //Path is HD absolute path (ie: /nwq/admin/www/data/mrcore_files/251) no / at end
        if (is_dir($path)) {
            if ($handle = opendir($path)) {
                while (false !== ($filename = readdir($handle))) {
                    $add = true;
                    
                    $finfo = pathinfo($path.'/'.$filename);
                    $ext = $finfo["extension"];
                    $size = filesize($path.'/'.$filename); //bytes
                    
                    if (round($size / 1024, 0) <= Config::MAX_FILE_PREVIEW_SIZE) $add = false; //File too small
                    if ($filename == "." || $filename == "..") $add = false;
                    if (substr($filename, 0, 1) == '.') {
                        if (preg_match("/_preview\./i", $filename)) $add = false;
                        if (preg_match("/_preview_small\./i", $filename)) $add = false;
                        if (preg_match("/_thumb\./i", $filename)) $add = false;
                    }
                    
                    if ($add) {
                        $filename_preview = '.'.substr($filename, 0, -(strlen($ext)+1)).'_preview.'.$ext;
                        $filename_preview_small = '.'.substr($filename, 0, -(strlen($ext)+1)).'_preview_small.'.$ext;
                        $filename_thumb = '.'.substr($filename, 0, -(strlen($ext)+1)).'_thumb.'.$ext;
                        if (!file_exists($path.'/'.$filename_preview) || !file_exists($path.'/'.$filename_thumb) || !file_exists($path.'/'.$filename_preview_small)) {
                            Files::process_image($path, $filename);
                        }
                    }
                }
            }
        }
    }

    /*
     mReschke 2011-06-04
    */
    public static function upload_file(files $files) {
        //Delete any previous avatars
        //Helper::unlink_wildcards(Page::get_abs_base().'/web/image/', 'avatar_user'.$user->user_id.'*');
        
        $filename = basename( $_FILES['uploadedfile']['name']);
        $ext = strtolower(substr($filename, strripos($filename, ".")+1));
        $newfilename = $filename;
        $newfullfile = $files->fullfile.'/'.$newfilename;
        if (file_exists($newfullfile)) {
            $newfilename = rand(9999, 999999).'-'.$filename;
            $newfullfile = $files->fullfile.'/'.$newfilename;
        }
        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $newfullfile)) {
            //Upload Success
            $file_size = filesize($newfullfile);
            $delfile = true; $cmd = null;
            if ($_POST['chk_extract']) {
                if ($ext == 'zip') {
                    $cmd = Config::CMD_ZIP;
                } elseif ($ext == 'gz') {
                    if (substr(strtolower($filename), -6) == 'tar.gz') {
                        $cmd = Config::CMD_TARGZ;
                    } else {
                        $cmd = Config::CMD_GZ;
                    }
                } elseif ($ext == 'bz2') {
                    if (substr(strtolower($filename), -7) == 'tar.bz2') {
                        $cmd = Config::CMD_TARBZ2;
                    }                            
                } elseif ($ext == 'tgz') {
                    $cmd = Config::CMD_TARGZ;
                } else {
                    //No custom extension found
                    $delfile = false;
                }
                if (isset($cmd)) {
                    $cmd = preg_replace("/\%s/i", $newfullfile, $cmd); //Replace %s with source file
                    $cmd = preg_replace("/\%d/i", $files->fullfile.'/', $cmd); //Replace %d with destination directory
                    #echo "<br />Running: $cmd<br />";
                    exec($cmd);
                    if ($delfile && $_POST['chk_keep_compressed'] == false) unlink($newfullfile); //Delete original file if required
                }
            }
            
            if ($_POST['chk_process_image']) {
                $known_images = array('jpg', 'jpeg', 'gif', 'png');
                if (in_array($ext, $known_images) && round($file_size / 1024, 0) > Config::MAX_FILE_PREVIEW_SIZE) {
                    Files::process_image($files->fullfile, $newfilename);
                };
            }
        }
    }

    /*
     mReschke 2011-06-04
    */
    public static function delete_file(files $files) {
        $filename = urldecode($_GET['delete']);
        $delpath = $files->fullfile.'/'.$filename;
        if (is_dir($delpath)) {
            \Helper\File::rm_dir($delpath);
        } else {
            $finfo = pathinfo($delpath);
            $ext = $finfo["extension"];
            $filename_preview = '.'.substr($filename, 0, -(strlen($ext)+1)).'_preview.'.$ext;
            $filename_preview_small = '.'.substr($filename, 0, -(strlen($ext)+1)).'_preview_small.'.$ext;
            $filename_thumb = '.'.substr($filename, 0, -(strlen($ext)+1)).'_thumb.'.$ext;
            if (file_exists($delpath)) unlink($delpath);
            if (file_exists($files->fullfile.'/'.$filename_preview)) unlink($files->fullfile.'/'.$filename_preview);
            if (file_exists($files->fullfile.'/'.$filename_preview_small)) unlink($files->fullfile.'/'.$filename_preview_small);
            if (file_exists($files->fullfile.'/'.$filename_thumb)) unlink($files->fullfile.'/'.$filename_thumb);
        }
    }

    /*
     mReschke 2012-10-26
    */
    public static function rename_file(files $files) {
        $path = $files->fullfile.'/';
        $old = $_GET['renameold'];
        $oldfull = $path.$old;
        $new = \Helper\File::clean_filename($_GET['renamenew'], false);
        $newfull = $path.$new;
        if (!file_exists($newfull)) {
            if (is_file($oldfull)) {
                $finfo = pathinfo($oldfull);
                $ext = $finfo["extension"];
                $noext = substr($old, 0, -(strlen($ext)+1));
                $old_preview = $path.'.'.$noext.'_preview.'.$ext;
                $old_preview_small = $path.'.'.$noext.'_preview_small.'.$ext;
                $old_thumb = $path.'.'.$noext.'_thumb.'.$ext;

                if (file_exists($old_preview)) {
                    $ext = \Helper\File::get_extension($new);
                    $noext = substr($new, 0, -(strlen($ext)+1));
                    $new_preview = $path.'.'.$noext.'_preview.'.$ext;
                    $new_preview_small = $path.'.'.$noext.'_preview_small.'.$ext;
                    $new_thumb = $path.'.'.$noext.'_thumb.'.$ext;

                    rename($old_preview, $new_preview);
                    if (file_exists($old_preview_small)) rename($old_preview_small, $new_preview_small);
                    if (file_exists($old_thumb)) rename($old_thumb, $new_thumb);
                }
            }
            rename($oldfull, $newfull);
            #\Helper\File::write_file("Rename '$old' to '$new'"); #TESTING
        }
    }

    /*
     mReschke 2011-06-04
    */
    public static function create_folder(files $files) {
        mkdir($files->fullfile.'/'.$_GET['createfolder']);
    }

    /*
     mReschke 2012-10-26
    */
    public static function create_file(files $files) {
        mkdir($files->fullfile.'/'.$_GET['createfolder']);
        \Helper\File::write_file('', $files->fullfile.'/'.$_GET['createfile']);
    }
    
}
