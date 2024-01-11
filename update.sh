#!/bin/sh

if ! [ "$(pwd)" = /root/repo/fw ];
then
    echo >&2 'Run this from /root/repo/fw!'
    exit 1
fi

find etc root var -type f -exec sh -c 'for file; do cp -a /"$file" "$file"; done' {} +

sed -ri 's/(ldap_password): "[^"]+"/\1: "[REDACTED]"/' etc/ejabberd/ejabberd.yml
sed -ri 's/(key|password|bindauth)="[^"]+"/\1="[REDACTED]"/' etc/inspircd/inspircd.conf
sed -ri "s/(SUBSCRIBE_FORM_SECRET) = '[^']+'/\1 = '[REDACTED]'/" etc/mailman/mm_cfg.py

(cd /root; tar -czf- pwd) \
  | openssl aes-256-cbc \
    -md sha512 \
    -pbkdf2 \
    -iter 100000 \
    -pass file:/root/pwd/meta \
    -in - \
    -out pwd.enc

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
