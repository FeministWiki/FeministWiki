#!/bin/sh

if ! [ "$(pwd)" = /root/repo ];
then
    echo >&2 'Run this from /root/repo!'
    exit 1
fi

find etc root var -type f -exec sh -c 'for file; do cp /"$file" "$file"; done' {} +

sed -ri 's/ldap_password: "[^"]+"/ldap_password: "[REDACTED]"/' etc/ejabberd/ejabberd.yml
sed -ri 's/(key|password)="[^"]+"/\1="[REDACTED]"/' etc/inspircd/inspircd.conf

echo
echo '~~~~~~~~~~~~~~~~~~~~~'
echo '~~ !!! WARNING !!! ~~'
echo '~~~~~~~~~~~~~~~~~~~~~'
echo
echo 'Do not forget to redact sensitive information from the repo.'
echo
echo 'Use "git diff" and go through all new content to ensure that'
echo 'no passwords, hash salts, or other secret tokens are included.'
echo
