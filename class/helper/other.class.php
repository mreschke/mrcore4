<?php
namespace Helper;

/*
 class \Helper\Other
 All other helper work functions (non model)
 mReschke 2010-08-06
*/
class Other {
    
    /*
     function print_obj($obj) string
     prints out all elements of an object (with recursion)
     mReschke 2011-01-22
    */
    public static function print_obj($obj, $name='NA', $recursion=false) {
        if (is_object($obj)) $obj_name = @get_class($obj);
        if (!$obj_name) $obj_name = $name;
        if (is_array($obj)) {
            if (\Helper\Other::is_assoc($obj)) {
				//This is specific to my application, remove AA named debug
                if ($recursion && $name == 'debug') $obj = array("Removed"=>"For Display");
                if ($recursion) $data .= "<tr><td align='right'>$name (AA):</td><td>";
                $data .= "<span class='master_debug_data_object_header'>Items of Associative Array: $obj_name</span>";
                $data .= "<table border='1' class='table_pad'><tbody align='left' valign='top'>";
                foreach($obj as $key => $value) {
                    $data .= \Helper\Other::print_obj($value, $key, true);
                }
            } else {
                
                if ($recursion) $data .= "<tr><td align='right'>$name (Array):</td><td>";
                $data .= "<span class='master_debug_data_object_header'>Items of Array: $obj_name</span>";
                $data .= "<table border='1' class='table_pad'><tbody align='left' valign='top'>";
                for ($i=0; $i < count($obj); $i++) {
                    $data .= \Helper\Other::print_obj($obj[$i], $i, true);	
                }
            }
            $data .= "</tbody></table>";
            if ($recursion) $data .= "</td></tr>";
            
        } elseif (is_object($obj)) {
            if ($recursion) $data .= "<tr><td align='right'>$name (Object):</td><td>";
            $data .= "<span class='master_debug_data_object_header'>Items of Object: $obj_name</span>";
            $data .= "<table border='1' class='table_pad'><tbody align='left' valign='top'>";
            $arr = get_object_vars($obj);
            while (list($item, $val) = each($arr)) {
                $data .= \Helper\Other::print_obj($val, $item, true);
            }
            $data .= "</tbody></table>";
            if ($recursion) $data .= "</td></tr>";
        } else {
            if ($recursion) {
                $data .= "<tr><td align='right'>$name:</td><td>$obj</td></tr>";		
            } else {
                $data .= "<div class='master_debug_data_object_header'>Items of Variable: $obj_name</div>";
                $data .= $obj;
            }
        }
        return $data;
    }
	
    /*
     function is_assoc($obj) string
     Check if array is an associative array
     mReschke 2011-01-22
    */	
    public static function is_assoc($array) {
        return (is_array($array) && (count($array)==0 || 0 !== count(array_diff_key($array, array_keys(array_keys($array))) )));
    }
	
    private static function print_obj_obsolete($obj, $additionalName='') {
        $arr = get_object_vars($obj);
        $data = "<span class='master_debug_data_object_header'>Items of object: ".get_class($obj)." ".$additionalName."</span>";
        $data .= "<table border='1' class='table_pad'><tbody align='left' valign='top'>";
        while (list($prop, $val) = each($arr)) {
            if (is_object($val)) {
                //Value is another object
                $data .= "<tr><td align='right'><b>$prop (Object):</b></td><td>".\Helper\Other::print_obj($val)."</td></tr>";
            } elseif (is_array($val)) {
                //Value is an array (possibly array of more objects)
                $data .= "<tr><td align='right'><b>$prop (Array of ".count($val)."):</b></td><td>";
                for ($i=0; $i < count($val); $i++) {
                    if (isset($val[$i])) {
                        if (is_object($val[$i])) {
                            $data .= \Helper\Other::print_obj($val[$i], "($i)");
                        } else {
                            if (Helper::is_assoc($val)) {
                                foreach($val as $key => $value) {
                                    $data .= "[$key] => $value<br />";
                                }
                            } else {
                                $data .= implode("<br />", $val);
                            }
                            break;
                        }
                    }
                }
                $data .= "</td></tr>";
                
            } else {
                //Value is just a variable
                $data .= "<tr><td align='right'><b>$prop:</b></td><td>$val</td></tr>";
            }
    	}
        $data .= "</tbody></table>";
        return $data;
    }
    
    /*
     function get_os() string
     Returns the users operating system type (Windows XP, Windows 7, Linux, Mac OS, ...)
     From http://www.geekpedia.com/code47_Detect-operating-system-from-user-agent-string.html
     mReschke 2011-05-22
    */
    public static function get_os() {
        $OSList = array
        (
            // Match user agent string with operating systems
            'Windows 3.11' => 'Win16',
            'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
            'Windows 98' => '(Windows 98)|(Win98)',
            'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
            'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
            'Windows Server 2003' => '(Windows NT 5.2)',
            'Windows Vista' => '(Windows NT 6.0)',
            'Windows 7' => '(Windows NT 7.0)',
            'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
            'Windows ME' => 'Windows ME',
            'Open BSD' => 'OpenBSD',
            'Sun OS' => 'SunOS',
            'Android' => 'Android',
            'Linux' => '(Linux)|(X11)',
            'Mac OS' => '(Mac_PowerPC)|(Macintosh)',
            'QNX' => 'QNX',
            'BeOS' => 'BeOS',
            'OS/2' => 'OS/2',
            'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
        );
        // Loop through the array of user agents and matching operating systems
        foreach($OSList as $CurrOS=>$Match)
        {
            // Find a match
            #if (eregi($Match, $_SERVER['HTTP_USER_AGENT'])) {
			if (preg_match("'".$Match."'", @$_SERVER['HTTP_USER_AGENT'])) {
                // We found the correct match
                break;
            }
        }
        // You are using Windows Vista
        #echo "You are using ".$CurrOS;
		return $CurrOS;
    }
    
    /*
     Gets the users browser name and version (like Firefox 6.0.1)
     mReschke 2011-09-10
    */
    public static function get_browser() {
        $browsers = array("firefox", "msie", "opera", "chrome", "safari",
                            "mozilla", "seamonkey", "konqueror", "netscape",
                            "gecko", "navigator", "mosaic", "lynx", "amaya",
                            "omniweb", "avant", "camino", "flock", "aol");

        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        foreach($browsers as $browser)
        {
            if (preg_match("#($browser)[/ ]?([0-9.]*)#i", $agent, $match))
            {
                $name = $match[1] ;
                $version = $match[2] ;
                break ;
            }
        }
        if (strlen($name) > 0) $name = strtoupper(substr($name, 0, 1)) . substr($name, 1);
        return $name.' '.$version;
    }
	
    /*
     simple trim string length if greater than $len, optional add ... if trimmed
     Final size will be $len, even with ...
     mReschke 2011-09-26
    */
    public static function trimlen($data, $len, $adddotdotdot=true) {
        $dot = '...';
        if (strlen($data) > $len) {
            if ($adddotdotdot) {
                $data = substr($data, 0, ($len-strlen($dot))).$dot;
            } else {
                $data = substr($data, 0, $len);    
            }
        }
        return $data;
    }	
    
}

