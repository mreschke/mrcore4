<?php
eval(Page::load_class('login'));

/*
 class Rest
 mRcore RESTful API Webservice functions (non model)
 Resources: http://www.gen-x-design.com/archives/create-a-rest-api-with-php/
 mReschke 2011-06-13
*/
class Rest {
	
    public $url;
    public $url_vars;
    public $request_vars;
	public $method;
    public $format;
    #public $data;

	public function __construct() {
        $this->url          = Page::get_url();
		$this->url_vars     = array();
        $this->request_vars = array();
		$this->method       = 'get';
        $this->format       = 'json';
        #$this->data         = '';
	}
    
	public static function process_request() {
        //Init New Rest Class Instance
        $rest = new Rest;
        
        //Get URL (full URL, ex: http://mreschke.com/rest/v1/topic/1.xml)
        $rest->url = Page::get_url();
        
        //Get URL Variables Array
        //These are NOT ?get&vars, they are tidy URL vars, ex: /rest/v1/topic/1.xml would have array[0] = 1.xml
        $rest->url_vars = Page::get_variables();

        //Get Verb (Method), get, post or put
        $rest->method = strtolower($_SERVER['REQUEST_METHOD']);
        
        //Get Format (comes from either URL or HTTP_ACCEPT header)
        if (preg_match('/\.xml/i', $rest->url)) {
            $rest->format = 'xml';
        } elseif (preg_match('/\.json/i', $rest->url)) {
            $rest->format = 'json';
        } else {
            //Format not specified in URL, try from passed HTTP_ACCEPT header
            if (strtolower($_SERVER['HTTP_ACCEPT']) == 'application/json') {
                $rest->format = 'json';
            } elseif (strtolower($_SERVER['HTTP_ACCEPT']) == 'application/xml') {
                $rest->format = 'xml';
            } else {
                //Format not specified in URL or in Accept header, default to json
                $rest->format = 'json';
            }
        }

		switch ($rest->method) {
			case 'get':
                //API being called via GET method
                $rest->request_vars = $_GET;
				break;
			case 'post':
                //API being called via POST method
				$rest->request_vars = $_POST;
				break;
			case 'put':
                // here's the tricky bit...
				// basically, we read a string from PHP's special input location,
				// and then parse it out into an array via parse_str... per the PHP docs:
				// Parses str  as if it were the query string passed via a URL and sets
				// variables in the current scope.
				parse_str(file_get_contents('php://input'), $rest->request_vars);
				break;
		}

		/*
        // set the raw data, so we can access it if needed (there may be
		// other pieces to your requests)
		if(isset($rest->request_vars['data'])) {
			// translate the JSON to an Object for use however you want
            $rest->data = $rest->request_vars['data'];
        } else {
            #$return_obj->setData($data);
		}
        */
        return $rest;
	}
    
    /*
     function authenticate() Info $info
     Validate the credentials for the REST request, either using Basic HTTP Authentication or HTTP Digest Authentication or nothing (anonymous)
     Resources: http://php.net/manual/en/features.http-auth.php
     mReschke 2011-06-13
    */
    public static function authenticate() {
        //Need to find out how to impliment oauth authentication (more secure) !!
        $realm = 'Restricted Webservice';
        $username = '';
        $password = '';
        $authenticated = true;
        $digest = array();
        
        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            //Using Digest Authentication
            $using_digest = true;
            $digest = Rest::convert_digest_text_to_array($_SERVER['PHP_AUTH_DIGEST']);
            $username = $digest['username'];
            if (strtolower($username) == 'anonymous') {
                //Logging in with anonymous user, switch to no authentication
                $using_digest = false;
                $using_anonymous = true;
            }
        } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
            //Using Basic Authentication
            $using_basic = true;
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
            if (strtolower($username == 'anonymous')) {
                //Logging in with anonymous user, switch to no authentication
                $using_basic = false;
                $using_anonymous = true;
            }
        } else {
            //No Authentication (anonymous)
            $using_anonymous = true;
            
        }
        
        /*
        //Prompt for Digest password if none passed
        if ($using_digest && empty($_SERVER['PHP_AUTH_DIGEST'])) {
            //No Digest Information passed (no login credentials)
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
            $authenticated = false;
        }
        */
        
        if ($using_anonymous) {
            //Using Anonymous Login, start new anonymous Info session
            Info::get_info($info);
            $info = $_SESSION['core'];

        } else {
            //Get User Information from tbl_user
            $tbl_user = Tbl_user::get_user($username);
            $database_password = Tbl_user::get_user_password($tbl_user->user_id);
            
            if ($tbl_user->user_id > 0) {
                if ($using_digest) {
                    //Validate Digest Authentication
                    $A1 = md5($digest['username'] . ':' . $realm . ':' . $database_password);
                    $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$digest['uri']);
                    $valid_response = md5($A1.':'.$digest['nonce'].':'.$digest['nc'].':'.$digest['cnonce'].':'.$digest['qop'].':'.$A2);
                    
                    if ($digest['response'] != $valid_response) {
                        Rest::send_response(401); //401 Unauthorized
                    }
                    
                } else {
                    //Validate Basic Authentication
                    if ($database_password != $password) {
                        Rest::send_response(401); //401 Unauthorized
                    }
                }
            } else {
                //User not found in database
                Rest::send_response(401); //401 Unauthorized
            }
            
            //Digest or Basic Authentication Successfull!
            //Load up new Info session for this user
            $info = Login::validate($username, $password);
        }
        return $info;
    }


	public static function send_response($status = 200, $body = '', $content_type = 'text/html') {
		$status_header = 'HTTP/1.1 ' . $status . ' ' . Rest::get_status_code_message($status);
		// set the status
		header($status_header);
		// set the content type
		header('Content-type: ' . $content_type);

		if($body != '') {
            // pages with body are easy
			// send the body
			echo $body;
			exit();
		} else {
            // we need to create the body if none is passed
			// create some body messages
			$message = '';

			// this is purely optional, but makes the pages a little nicer to read
			// for your users.  Since you won't likely send a lot of different status codes,
			// this also shouldn't be too ponderous to maintain
			switch($status) {
				case 401:
					$message = 'You must be authorized to view this page.';
					break;
				case 404:
					$message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
					break;
				case 500:
					$message = 'The server encountered an error processing your request.';
					break;
				case 501:
					$message = 'The requested method is not implemented.';
					break;
			}

			// servers don't always have a signature turned on (this is an apache directive "ServerSignature On")
			$signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

			// this should be templatized in a real-world solution
			$body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
						<html>
							<head>
								<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
								<title>' . $status . ' ' . Rest::get_status_code_message($status) . '</title>
							</head>
							<body>
								<h1>' . Rest::get_status_code_message($status) . '</h1>
								<p>' . $message . '</p>
								<hr />
								<address>' . $signature . '</address>
							</body>
						</html>';

			echo $body;
			exit();
		}
	}


	public static function get_status_code_message($status) {
		// these could be stored in a .ini file and loaded
		// via parse_ini_file()... however, this will suffice
		// for an example
		$codes = Array(
		    100 => 'Continue',
		    101 => 'Switching Protocols',
		    200 => 'OK',
		    201 => 'Created',
		    202 => 'Accepted',
		    203 => 'Non-Authoritative Information',
		    204 => 'No Content',
		    205 => 'Reset Content',
		    206 => 'Partial Content',
		    300 => 'Multiple Choices',
		    301 => 'Moved Permanently',
		    302 => 'Found',
		    303 => 'See Other',
		    304 => 'Not Modified',
		    305 => 'Use Proxy',
		    306 => '(Unused)',
		    307 => 'Temporary Redirect',
		    400 => 'Bad Request',
		    401 => 'Unauthorized',
		    402 => 'Payment Required',
		    403 => 'Forbidden',
		    404 => 'Not Found',
		    405 => 'Method Not Allowed',
		    406 => 'Not Acceptable',
		    407 => 'Proxy Authentication Required',
		    408 => 'Request Timeout',
		    409 => 'Conflict',
		    410 => 'Gone',
		    411 => 'Length Required',
		    412 => 'Precondition Failed',
		    413 => 'Request Entity Too Large',
		    414 => 'Request-URI Too Long',
		    415 => 'Unsupported Media Type',
		    416 => 'Requested Range Not Satisfiable',
		    417 => 'Expectation Failed',
		    500 => 'Internal Server Error',
		    501 => 'Not Implemented',
		    502 => 'Bad Gateway',
		    503 => 'Service Unavailable',
		    504 => 'Gateway Timeout',
		    505 => 'HTTP Version Not Supported'
		);
		return (isset($codes[$status])) ? $codes[$status] : '';
	}
    
    /*
     function convert_digest_text_to_array($txt)
     Converts the digest string $_SERVER['PHP_AUTH_DIGEST'] into an array
     Array keys: username,realm,nonce,uri,cnonce,nc,qop,response,opaque
     Digest String: //ex: username="testuser", realm="Restricted Webservoce", nonce="4df78c13963f5", uri="/api/topic/1.json", cnonce="MTQ4NTc3", nc=00000001, qop="auth", response="8bfaf97eeb991870558841b81e5a85a2", opaque="de7d27e200c0609db205b9a5900564b9"
     Resources: http://php.net/manual/en/features.http-auth.php
     mReschke 2011-06-13
    */
    public static function convert_digest_text_to_array($txt) {
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        $keys = implode('|', array_keys($needed_parts));
    
        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
    
        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }
        return $needed_parts ? false : $data;
    }    
}

