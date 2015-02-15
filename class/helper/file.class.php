<?php
namespace Helper;

/*
 class \Helper\File
 All file helper work functions (non model)
 mReschke 2011-09-08
*/
class File {
    
    /*
     Delete files/dirs with wildcards
     mReschke 2010-09-22
    */
    public static function unlink_wildcards($path,$match){
       static $deld = 0, $dsize = 0;
       $dirs = glob($path."*");
       $files = glob($path.$match);
       foreach($files as $file){
          if(is_file($file)){
             $dsize += filesize($file);
             unlink($file);
             $deld++;
          }
       }

       foreach($dirs as $dir){
          if(is_dir($dir)){
             $dir = basename($dir) . "/";
             rfr($path.$dir,$match); //? rfr is referer? recursion?
          }
       }
       return "$deld files deleted with a total size of $dsize bytes";
    }

    /*
     function get_extension($string)
     Parses out the .extsion from a plain string.  If you were wanting the extension of an actual file use finfo
     mReschke 2012-10-26
    */
    public static function get_extension($string) {
        $ext ='';
        $tmp = explode('.', $string);
        if (count($tmp) > 1) $ext = $tmp[count($tmp)-1];
        return $ext;
    }
    
    /*
     function rm_dir($path)
     Recursive delete directory
     mReschke 2011-06-09
    */
    public static function rm_dir($dir) {
        if ($dir != '/' && is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        //Delete subdir (recusion)
                        \Helper\File::rm_dir($dir."/".$object);
                    } else {
                        //Delete regular file
                        unlink($dir."/".$object);
                    }
                }
            }
            if(is_array($object) || is_object($object)) @reset($object);
            
            //Delete final top-level dir
            rmdir($dir) ;
        }
    }
    
    /*
     function write_file($data, file)
     Simple helper to write (overwrite mode) a $file with $data
     mReschke 2011-07-21
    */
    public static function write_file($data, $file='/tmp/test.txt') {
        $fh = fopen($file, 'w');
        fwrite($fh, $data);
        fclose($fh);        
    }

    /*
     mReschke 2011-08-21
     function clean_filename($filename, $trim_spaces = true, $prefix_date = false, $prefix_time = false)
     Remove invalid filesystem characters from filename (optional prefixes with date/time)
    */
    public function clean_filename($filename, $trim_spaces = true, $prefix_date = false, $prefix_time = false) {
        #[ ] / \ = + < > : ; " , * .
        $filename = preg_replace('"\["', '', $filename);
        $filename = preg_replace('"\]"', '', $filename);
        $filename = preg_replace('"\{"', '', $filename);
        $filename = preg_replace('"\}"', '', $filename);
        $filename = preg_replace('"\\\"', '', $filename);
        $filename = preg_replace('"\/"', '', $filename);
        $filename = preg_replace('"\<"', '', $filename);
        $filename = preg_replace('"\>"', '', $filename);
        $filename = preg_replace('"\;"', '', $filename);
        $filename = preg_replace('"\;"', '', $filename);
        $filename = preg_replace('"\""', '', $filename);
        $filename = preg_replace('"\'"', '', $filename);
        $filename = preg_replace('"\,"', '', $filename);
        $filename = preg_replace('"\!"', '', $filename);
        $filename = preg_replace('"\@"', '', $filename);
        $filename = preg_replace('"\#"', '', $filename);
        $filename = preg_replace('"\$"', '', $filename);
        $filename = preg_replace('"\%"', '', $filename);
        $filename = preg_replace('"\^"', '', $filename);
        $filename = preg_replace('"\&"', '', $filename);
        $filename = preg_replace('"\*"', '', $filename);
        $filename = preg_replace('"\("', '', $filename);
        $filename = preg_replace('"\)"', '', $filename);
        $filename = preg_replace('"\="', '', $filename);
        $filename = preg_replace('"\+"', '', $filename);
        if ($trim_spaces) $filename = preg_replace('" "', '', $filename);
        
        //Kinda worked, but sometimes now
        #$filename = preg_replace('/[^0-9a-zа-яіїё\`\~\!\@\#\$\%\^\*\(\)\; \,\.\'\/\_\-\ ]/i', ' ',$filename);
        
        if ($prefix_time) {
            $now = date("H-i-s");
            $filename = $now.'_'.$filename;
        }
        if ($prefix_date) {
            $now = date("Y-m-d");
            $filename = $now.'_'.$filename;
        }
        return $filename;
    }
    
}