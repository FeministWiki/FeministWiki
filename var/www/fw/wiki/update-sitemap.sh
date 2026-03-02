#!/bin/sh

cd /var/www/fw/wiki/w || exit

php maintenance/run.php \
	generateSitemap.php -q \
	--fspath=sitemap \
	--urlpath=w/sitemap \
	--skip-redirects
