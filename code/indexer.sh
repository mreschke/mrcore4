#!/bin/bash

#mrcore4 indexer start script
#mReschke 2010-09-14

accesscode='test'
options=''
	#topic=x (will only index one topic and its comments)
	#fullindex=1 (will reset all indexed dates and run full index)
url='http://mrcore4.examp.e.com/admin/indexer'
log='/var/www/mrcore4/web/admin/indexer_log.html'

wget -O - -q -t 1 $url?accesscode=${accesscode}$options > $log

