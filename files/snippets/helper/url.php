<?php
require_once 'common.php';

/*
 helper_url
 URL helpers
 mReschke 2012-10-29
*/
class helper_url {
	public $tmpdir;

	public function __construct() {
		$tmpdir = '/tmp/';
	}
	/*
	 function get_url($url, $postdata)l
	 Gets the raw HTML of a URL using curl
	 $postdata whould be assoc array
	 mReschke 2012-10-31
	*/
	public function get_url($url, $postdata) {
		$ch = curl_init();
		$cookie = tempnam("/tmp", "CURLCOOKIE");
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		if (isset($postdata)) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		}
		$html = curl_exec($ch);
		curl_close($ch);
		return $html;

		/*
        //Set Main Curl Options
        $libcurl = curl_init();
        $cookie = tempnam("/tmp", "CURLCOOKIE");
        curl_setopt($libcurl, CURLOPT_TIMEOUT, 60);
        curl_setopt($libcurl, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($libcurl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2
        curl_setopt($libcurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($libcurl, CURLOPT_FOLLOWLOCATION, 1); #There is another one of these below, sear
        #curl_setopt($curl, CURLOPT_HEADER, 1);
        */
	}

	/*
	 function get_pdf($url, $filename)
	 Use PDFMyURL to convert $url into a PDF stored at $filename.  $filename is a random file in /tmp
	 Returns random $filename of new saved PDF
	 mReschke 2012-10-31
	*/
	public function get_pdf($url, $filename=null) {
		if (!is_dir($this->tmpdir)) exec("mkdir -p '".$this->tmpdir."'");
		if (!isset($filename)) $filename = $this->tmpdir.rand(1000, 99999999).".pdf";
		exec("wget http://pdfmyurl.com/?url=$url -O $filename");
		return $filename;
	}
}