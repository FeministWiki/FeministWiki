#!/bin/sh

cd /var/www/fw/wiki/w

php maintenance/generateSitemap.php \
    --fspath=sitemap \
    --urlpath=w/sitemap \
    --skip-redirects
