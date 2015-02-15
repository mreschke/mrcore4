<?php

//Configure php.ini
#ini_set("display_errors","2"); //This will display all errors
#ini_set("session.auto_start","1");

//Configuration Constants Class
class Config {
	const DB_TYPE = 'mysql';
	const DB_NAME = 'mrcore4';
	const DB_SERVER = 'localhost';
	const DB_PORT = 3306;
	const DB_USER = 'root';
	const DB_PASS = 'password';
	const DEBUG = false;
	const USE_PAGE_EXTENSIONS = false;
	const WEB_BASE_URL = '//mrcore4.mreschke.net';
	const WEB_BASE_IMAGE_URL = '//mrcore4.mreschke.net'; //for parallel and cookieless domain optimizations
	const WEB_BASE_CSS_URL = '//mrcore4.mreschke.net'; //for parallel and cookieless domain optimizations
	const WEB_BASE_JS_URL = '//mrcore4.mreschke.net'; //for parallel and cookieless domain optimizations
	const WEB_HOST = 'mreschke.net';
	const WEB_BASE = '/';
	const ABS_BASE = '/var/www/mrcore4';
	const FILES_DIR = '/var/www/mrcore4/files';
	const APP_NAME = 'mRcore';
	const APP_CREATOR = 'mReschke';
	const APP_VERSION = '4.2';
	const APP_VERSION_DATE = '2013-09-10';
	const DEFAULT_TOPIC = 1;
	const HOME_URL = 1;
	const HOME_URL_NAME = 'Welcome to mReschke.com';
	const ABOUT_URL = '';
	const ABOUT_URL_NAME = '';
	const THEME = 'default';
	const TEASER_LEN = 500;
	const STATIC_PERM_GROUP_PUBLIC = 1;
	const STATIC_PERM_GROUP_USERS = 2;
	const STATIC_PERM_READ = 1;
	const STATIC_ANONYMOUS_USER = 1;
	const SEARCH_PAGE_SIZE = 25;
	const INDEXER_ACCESS_CODE = 'test';
	const INDEXER_ADMIN_USER = 2;
	const MAX_FILE_PREVIEW_SIZE = 20;
	const RECENT_MAX_TOPICS = 5;
	const RECENT_MAX_TITLE_LEN = 30;
	const DISPLAY_ERRORS = true;
	const EMAIL_ERROR = 'mail@example.com';
	const EMAIL_DEV = "mail@example.com"; 
	const EMAIL_ADMIN = 'mail@example.com';
	const MAX_UPLOAD = 1024;
	const NEW_TOPIC_TEMPLATE_TOPIC_ID = 6;
	const NEW_TOPIC_TEMPLATE_TAG_ID = 2;
	const ENABLE_WYSIWYG = false;
	const WIKI_HELP_TOPIC_ID = 3;
	const GLOBAL_TOPIC = 2;
	const LOGO_URL = '';
	const USERINFO_TOPIC = 4;
	const SEARCHBOX_TOPIC = 5;
	const CACHE_VERSION = null;
	
	//Archive Commands, extract and list known archives
	const KNOWN_ARCHIVES = 'tar,tar.gz,tgz,tar.bz2,zip,gz';
	const CMD_TAR = "/bin/tar -xf '%s' -C '%d'";
	const CMD_TARGZ = "/bin/tar -xzf '%s' -C '%d'";
	const CMD_TARBZ2 = "/bin/tar -xjf '%s' -C '%d'";
	const CMD_ZIP = "/usr/bin/unzip '%s' -d '%d'";
	const CMD_GZ = "/bin/gunzip '%s'";
	const CMD_TAR_LIST = "/bin/tar -tvf '%s'";
	

	public static function WEB_SUBFOLDERS() {
		return array('admin/','ajax/','rest/v1/');
	}
	
	public static function ERROR_REPORTING() {
		//If array() is empty, no error dialog will be displayed and no email sent
		//The E_NOTICE is super strict, not good for production
		#return array(E_ERROR, E_WARNING, E_PARSE, E_NOTICE);
		return array(E_ERROR, E_WARNING, E_PARSE);
	}    
}


/*
Config Help
-----------

DB_TYPE
	database type string used by php adodb5

DB_NAME
	database name
	
DB_SERVER
	database server, usually localhost
	
DB_PORT
	database port, usually 3306 for MySQL
	
DB_USER
	database username with full read/write access

DB_PASS
	database password for given username
	
DEBUG
	when true, mrcore4 app will spit out a bunch of debug content...
	
USE_PAGE_EXTENSIONS
	if true, all page redirects will include the .php (so http://hostname/article.php/5)
	if false, no extensions will be given (http://hostname/article/4)
	in order for no extensions to be used, your webserver must support it
	
WEB_BASE_URL
	full base url, used for stylesheets/javascript/images... would be the same as http://WEB_DOMAIN/WEB_BASE
	EXCEPT NO / at end
	
WEB_BASE_IMAGE_URL
	can be the same as WEB_BASE_URL if you don't have a separate image url
	my image.mreschke.com actually just points to mreschke.com, I simply do this because google chrome stated
	I should Parallelize downloads across hostnames, not for .js files though, js should be on same domain as main html
	
WEB_BASE_CSS_URL
	can be the same as WEB_BASE_URL if you don't have a separate image url
	my image.mreschke.com actually just points to mreschke.com, I simply do this because google chrome stated
	I should Parallelize downloads across hostnames, not for .js files though, js should be on same domain as main html

WEB_HOST
	web domain location of this app, do not use http or any /, example (hostname.com or mrcore4.hostname.com)
	Same as $_SERVER['HTTP_HOST']
	
WEB_BASE
	the base location of this app relative to your domain, so if you use http://hostname.com/mrcore4/article.php
	then the base would be /mrcore4/.  If you use http://hostname.com/article.php then the base would just be /
	always put a final / at the end

ABS_BASE
	The absolute path to the source code directory (no / at end) (not the /web folder, but above it with /class /view /model /web...)

FILES_DIR
	the absolute filesystem path to the files (topic uploads) folder, no / at end

APP_NAME
	this applications name, used in various places, mostly for display
	
APP_CREATOR
	this applications creator, used in various places, mostly for display
	
APP_VERSION
	this applications version, used in various places, mostly for display
	
APP_VERSION_DATE
	this applications version date, used in various places, mostly for display

DEFAULT_TOPIC
	Main topic, sometimes there is no where to redirect, so goes here, this is usually hour home page too

HOME_URL
	can be just a integer topicID, or '../search/' or 'http://somehome.com', etc...
	the topic for the home page

ABOUT_URL
	can be just a integer topicID, or '../search/' or 'http://somehome.com', etc...
	the topic for the about page
	IF = '' or null, then the About link will not show up on the master header

THEME
	the theme to use, used in view/THEME/...
	
TEASER_LEN
	the length of the teaser after all HTML and WIKI syntax has been removed
	
STATIC_PERM_READ
	the db.tbl_perm_item perm_id for the READ permission
	
STATIC_ANONYMOUS_USER
	the db.tbl_user user_id for the anonymous user
	
SEARCH_PAGE_SIZE
	the search page size

INDEXER_ACCESS_CODE
	the access code used by the indexer.php page (pass as ?accesscode=xxx)
	The URL accesscode variable must match this INDEXER_ACCESS_CODE to run
	
INDEXER_ADMIN_USER
	This user is used by the indexer and must have perm_admin=1 (super admin)
	
MAX_FILE_PREVIEW_SIZE
	In kilobytes
	In file 'Detail Preview' or 'Icon' view it will show the actual picture thumbnail
	as long as the image is <= this size.  AND if you upload an image using the filemanager
	it will create a thumnail if the file is > than this size and display that thumbnail instead

RECENT_MAX_TOPICS
	Max number of topics to remember and use in the recent cookie crumb navigation
	
RECENT_MAX_TITLE_LEN
	Max length of each topic title in the recent cookie crumb navigation

DISPLAY_ERRORS
	Display the 'Errors Found' link and dialog box, if false errors may still be handled and emailed depending on other config variables
	
EMAIL_ERROR
	If not "" then send each error instance details to this email address
	
EMAIL_DEV
	Developer email (comma separated)
	
EMAIL_ADMIN
	Administrators emails (comma separated)
	
MAX_UPLOAD
	Max upload size in MB
	
NEW_TOPIC_TEMPLATE_TOPIC_ID
	When you click 'Create Topic' this topic's source is added as the default "template" text
	Setting to 0 enabled the hard coded default topic template

NEW_TOPIC_TEMPLATE_TAG_ID
	All topics with this tag will be listed in the create topic template dropdown
	If NEW_TOPIC_TEMPLATE_TOPIC_ID is set it will also be included in this dropdown and the default template selected
	Setting to 0 will disable the template dropdown completely

ENABLE_WYSIWYG
	Enable custom text_wiki WYSIWYG editor

WIKI_HELP_TOPIC_ID
	Wiki syntax help documentation, links here on the edit topic page.

GLOBAL_TOPIC
	This topic ID is globally included with every page, even the search page

LOGO_URL
	Can be blank or plain logo.png to look in theme path or full URL

USERINFO_TOPIC
	This topic is displayed in each users avatar dropdown userinfo box

SEARCHBOX_TOPIC
	This topic is displayed in the main search box dropdown   

CACHE_VERSION
	If set will add ?v=x to all CSS and Javascript to force re-caching at will
*/
