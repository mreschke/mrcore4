# Mrcore4
A Wiki/CMS built with an mreschke custom PHP framework.


# Install

	cd /var/www
	git clone https://github.com/mreschke/mrcore4

	# Config
	cd /var/www/mrcore4/config
	cp sample.config.php config.php
	# Edit new config.php to your environment

	# Database
	# Create the mysql database yourself with proper credentials
	cd /var/www/mrcore4/install
	mysql --host=localhost -uroot -p mrcore4 < tables.sql
	mysql --host=localhost -uroot -p mrcore4 < fixtures.sql

	# Cron indexer
	Edit /var/www/mrcore4/code/indexer.sh to your url and log location
	@hourly /var/www/mrcore4/code/indexer.sh

	# Directories
	mkdir -p /var/www/mrcore4/files/{1,2,3,4,5,6,7}

	# Set apache to web/index.php, login to your new http location
	user/pass admin/admin



# License

The mrcore4 framework is open-sourced software licensed under the [MIT license](http://mreschke.com/license/mit)
