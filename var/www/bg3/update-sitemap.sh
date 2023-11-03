#!/bin/sh

cd /var/www/bg3

php w/maintenance/generateSitemap.php \
    --fspath=sitemap \
    --urlpath=sitemap \
    --skip-redirects \
    --compress=no

mv sitemap/sitemap-index-bg3wiki.xml sitemap.xml
